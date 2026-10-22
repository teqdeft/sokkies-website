<?php
/**
 * Expivi Social Sharing
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/helpers/class-expivi-configuration-helper.php';

/**
 * Class for social sharing.
 */
class Expivi_Social_Sharing extends Expivi_Template {

	use Expivi_Configuration_Helper;

	/**
	 * Path to share share directory.
	 *
	 * @var string
	 */
	private $social_share_dir;

	/**
	 * Expivi_Social_Sharing Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_expivi_social_sharing', array( $this, 'save_social_sharing' ) );
		add_action( 'wp_ajax_nopriv_expivi_social_sharing', array( $this, 'save_social_sharing' ) );

		// Load assets.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'expivi_scripts_loaded', array( $this, 'load_scripts' ), 10, 1 );

		// Define share share dir.
		$this->social_share_dir = XPV()->fs->combine( xpv_upload_dir( false ), XPV_SOCIAL_SHARING_DIR );

		// Create social share dir, if not available.
		if ( ! XPV()->fs->exists( $this->social_share_dir ) ) {
			XPV()->fs->mkdir( $this->social_share_dir );
		}

		// Add index file to avoid direct access to folder structure.
		$index_path = XPV()->fs->combine( $this->social_share_dir, 'index.php' );
		if ( ! XPV()->fs->exists( $index_path ) ) {
			XPV()->fs->write( 'index.php', '<?php // Silence is golden.', true, $this->social_share_dir );
		}
	}

	/**
	 * Save the Social Sharing configuration to disk.
	 *
	 * phpcs:disable WordPress.Security.ValidatedSanitizedInput
	 *
	 * @throws Exception Inner use of exception to help logging errors and create response.
	 */
	public function save_social_sharing() {
		try {
			if ( ! check_ajax_referer( 'xpv-social-sharing', 'nonce' ) ) {
				throw new Exception( 'Invalid Nonce.' );
			}

			if ( ! isset( $_POST['thumbnail'], $_POST['configuration'], $_POST['product_id'] ) ) {
				throw new Exception( 'Missing input data.' );
			}

			// TODO check if module is enabled.

			if ( ! $this->has_expivi_product( (int) $_POST['product_id'] ) ) {
				throw new Exception( 'Invalid Product.' );
			}

			$thumbnail = $_POST['thumbnail'];

			if ( ! xpv_base64_validate( $thumbnail ) ) {
				throw new Exception( 'Invalid thumbnail format.' );
			}

			$configuration = wp_unslash( $_POST['configuration'] );
			$hash          = $this->get_configuration_hash( $_POST['configuration'] );

			$sub_dir  = XPV()->fs->combine( XPV_SOCIAL_SHARING_DIR, $hash );
			$hash_dir = XPV()->fs->combine( xpv_upload_dir( false ), $sub_dir );

			$mimetype           = xpv_base64_get_image_type( $thumbnail );
			$image_extension    = explode( '/', $mimetype )[1];
			$thumbnail_filename = "thumbnail.$image_extension";

			if ( ! $this->valid_extension( $mimetype ) ) {
				throw new Exception( 'Invalid Thumbnail mimetype.' );
			}

			if ( ! XPV()->fs->exists( $hash_dir ) ) {
				XPV()->fs->mkdir( $hash_dir );
			}

			if ( ! XPV()->fs->exists( "$hash_dir/$thumbnail_filename" ) ) {
				XPV()->fs->write(
					$thumbnail_filename,
					base64_decode( explode( ',', $thumbnail )[1] ), // phpcs:ignore
					true,
					$hash_dir
				);
			}

			if ( ! XPV()->fs->exists( "$hash_dir/configuration.json" ) ) {
				XPV()->fs->write(
					'configuration.json',
					$configuration,
					true,
					$hash_dir
				);
			}

			wp_send_json_success(
				array(
					'hash'          => $hash,
					'thumbnail_url' => $thumbnail_filename ?
						XPV()->fs->combine( xpv_upload_url(), $sub_dir, $thumbnail_filename ) :
						null,
				)
			);
		} catch ( Exception $e ) {
			XPV()->log_exception( $e );

			wp_send_json_error(
				array( 'message' => $e->getMessage() ),
				500
			);
		}
	}

	/**
	 * Register scripts for social sharing.
	 */
	public function register_scripts() {
		$plugin_version = defined( 'WP_DEBUG' ) && true === WP_DEBUG ? gmdate( 'h:i:s' ) : XPV_VERSION;

		// Register scripts.
		if ( ! wp_script_is( 'expivi-social-sharing-script', 'registered' ) ) {
			wp_register_script( 'expivi-social-sharing-script', XPV()->plugin_url() . '/public/js/social-sharing.js', array( 'expivi-viewer-script' ), $plugin_version, false );
		}
	}

	/**
	 * Load scripts for social sharing.
	 *
	 * @param WC_Product $product Expivi Product.
	 */
	public function load_scripts( $product ) {
		if ( ! $product ) {
			return;
		}

		// Load scripts.
		if ( wp_script_is( 'expivi-social-sharing-script', 'registered' ) && ! wp_script_is( 'expivi-social-sharing-script', 'enqueued' ) ) {
			wp_enqueue_script( 'expivi-social-sharing-script' );

			$wp_product_id             = $product->get_id();
			$camera_position_thumbnail = $product->meta_exists( 'xpv_camera_position_thumbnail' ) ? $product->get_meta( 'xpv_camera_position_thumbnail' ) : false;

			wp_add_inline_script(
				'expivi-social-sharing-script',
				'const XPV_SOCIAL_SHARING = ' . wp_json_encode(
					array(
						'product_id'                => $wp_product_id,
						'permalink'                 => $product->get_permalink(),
						'nonce'                     => wp_create_nonce( 'xpv-social-sharing' ),
						'ajax_url'                  => admin_url( 'admin-ajax.php' ),
						'camera_position_thumbnail' => $camera_position_thumbnail,
					)
				)
			);

			wp_localize_script(
				'expivi-social-sharing-script',
				'XPV_SOCIAL_SHARING_LOCALIZE',
				array(
					'share_init_failed'          => __( 'Failed to initiate sharing', 'expivi' ),
					'share_module_not_available' => __( 'Share module not available yet.', 'expivi' ),
				)
			);
		}
	}

	/**
	 * Check if the given mimetype is valid for a thumbnail.
	 *
	 * @param string $mimetype The filename mimetype.
	 *
	 * @return bool
	 */
	private function valid_extension( string $mimetype ): bool {
		return in_array(
			$mimetype,
			array(
				'image/png',
				'image/jpg',
				'image/jpeg',
				'image/gif',
				'image/bmp',
				'image/wbmp',
				'image/tiff',
				'image/webp',
			),
			true
		);
	}
}
