<?php
/**
 * Expivi SVG Conversion Controller
 *
 * @package Expivi/SVG
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';
require_once XPV_ABSPATH . 'classes/svg/class-expivi-svg-conversion-request.php';

/**
 * Class to help creating print ready files of svg in product/order.
 */
class Expivi_SVG_Conversion_Controller extends Expivi_Template {
	public const FORMAT_PNG  = 'png';
	public const FORMAT_JPG  = 'jpg';
	public const FORMAT_WEBP = 'webp';
	public const FORMAT_TIFF = 'tiff';
	public const FORMAT_PDF  = 'pdf';
	public const FORMAT_SVG  = 'svg';

	public const ATTRIBUTE_PAYLOAD_VISIBLE = 'visible';
	public const ATTRIBUTE_PAYLOAD_ALL     = 'all';

	public const STATUS_IN_PROGRESS    = 'in_progress';
	public const STATUS_COMPLETE       = 'complete';
	public const STATUS_NOT_PROCESSING = 'not_processing';
	public const STATUS_FAILED         = 'failed';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Request.
		add_action( 'wp_ajax_expivi_svg_conversion_controller_request', array( $this, 'request_api' ) );
		add_action( 'wp_ajax_nopriv_expivi_svg_conversion_controller_request', array( $this, 'request_api' ) );

		// Check progress.
		add_action( 'wp_ajax_expivi_svg_conversion_controller_check_progress', array( $this, 'check_progress_api' ) );
		add_action( 'wp_ajax_nopriv_expivi_svg_conversion_controller_check_progress', array( $this, 'check_progress_api' ) );
	}

	/**
	 * Request print ready file.
	 * The request can either directly return a print ready file (if cached) or return a hash used for polling the progress.
	 * Either use $dpi or $resolution to define the resolution of the image.
	 *
	 * @param int    $configured_product_id Identifier of configuration saved in Expivi.
	 * @param int    $media_id Identifier of media that needs to be converted.
	 * @param ?int   $dpi Resolution of image (dots per inch).
	 * @param ?int   $resolution Resolution of image (width & height).
	 * @param string $format Format of the print ready file.
	 * @param string $attribute_payload Define which attributes should be used for svg converion.
	 *
	 * @return Expivi_SVG_Conversion_Request|bool Returns progress/results of print ready file or false if something went wrong.
	 */
	public function request(
		int $configured_product_id,
		int $media_id,
		?int $dpi = null,
		?int $resolution = null,
		$format = self::FORMAT_PNG,
		$attribute_payload = self::ATTRIBUTE_PAYLOAD_VISIBLE
	) {
		$request_body = array(
			'configured_product_id' => $configured_product_id,
			'media_id'              => $media_id,
			'hide_print_marks'      => false,
			'hide_placeholders'     => false,
			'attribute_payload'     => $attribute_payload,
		);
		if ( null !== $dpi ) {
			$request_body[] = array( 'dpi' => $dpi );
		}
		if ( null !== $resolution ) {
			$request_body[] = array( 'resolution' => $resolution );
		}

		$response = $this->call_api( 'POST', 'svg/conversion/request/' . $format, $request_body );
		if ( empty( $response ) || isset( $response['error'] ) ) {
			XPV()->log( 'Request SVG Conversion failed: ' . ( $response['error'] ?? 'Unknown' ), Expivi::ERROR );
			return false;
		}

		$result         = new Expivi_SVG_Conversion_Request();
		$result->hash   = $response['hash'];
		$result->status = $response['status'];
		$result->files  = $response['files'];

		return $result;
	}

	/**
	 * Ability to check progress using hash of requested conversion.
	 *
	 * @param string $hash Identifier of the process. This can be retrieved from 'request()'.
	 *
	 * @return Expivi_SVG_Conversion_Request|bool Returns progress/results of print ready file or false if something went wrong.
	 */
	public function check_progress( string $hash ) {
		if ( ! $hash ) {
			return false;
		}

		// Check for progress.
		$response = $this->call_api(
			'GET',
			'svg/conversion/check/' . $hash
		);

		// Check for errors.
		if ( empty( $response ) || isset( $response['error'] ) ) {
			XPV()->log( 'Request SVG Conversion progress check failed: ' . ( $response['error'] ?? 'Unknown' ), Expivi::ERROR );
			return false;
		}

		$result         = new Expivi_SVG_Conversion_Request();
		$result->hash   = $response['hash'];
		$result->status = $response['status'];
		$result->files  = $response['files'];

		return $result;
	}

	/**
	 * Process print ready file request for Ajax requests.
	 *
	 * @throws Exception Throw if something went wrong.
	 *
	 * @return void
	 */
	public function request_api() {
		if ( ! check_ajax_referer( 'xpv-svg-conversion', 'nonce' ) ) {
			throw new Exception( 'Invalid Nonce.' );
		}

		if (
			! isset( $_POST ) ||
			! isset( $_POST['configured_product_id'] ) ||
			! isset( $_POST['media_id'] )
		) {
			throw new Exception( 'Missing input data.' );
		}

		$configured_product_id = (int) $_POST['configured_product_id'];
		$media_id              = (int) $_POST['media_id'];
		$dpi                   = isset( $_POST['dpi'] ) ? (int) $_POST['dpi'] : null;
		$resolution            = isset( $_POST['resolution'] ) ? (int) $_POST['resolution'] : null;
		$format                = isset( $_POST['format'] ) ? (string) sanitize_key( wp_unslash( $_POST['format'] ) ) : self::FORMAT_PNG;
		$attribute_payload     = isset( $_POST['attribute_payload'] ) ? (string) sanitize_key( wp_unslash( $_POST['attribute_payload'] ) ) : self::ATTRIBUTE_PAYLOAD_VISIBLE;

		$results = $this->request(
			$configured_product_id,
			$media_id,
			$dpi,
			$resolution,
			$format,
			$attribute_payload
		);

		if ( false === $results ) {
			wp_send_json_error(
				array(
					'message' => 'Failed',
				),
				400
			);
			return;
		}

		wp_send_json_success(
			$results,
			200
		);
	}

	/**
	 * Check progress of print ready file request for Ajax requests.
	 *
	 * @throws Exception Throw if something went wrong.
	 *
	 * @return void
	 */
	public function check_progress_api() {
		if ( ! check_ajax_referer( 'xpv-svg-conversion', 'nonce' ) ) {
			throw new Exception( 'Invalid Nonce.' );
		}

		if ( ! isset( $_POST ) || ! isset( $_POST['hash'] ) ) {
			throw new Exception( 'Missing input data.' );
		}

		$hash = (string) sanitize_key( wp_unslash( $_POST['hash'] ) );

		$results = $this->check_progress( $hash );

		if ( false === $results ) {
			wp_send_json_error(
				array(
					'message' => 'Failed',
				),
				400
			);
			return;
		}

		wp_send_json_success(
			$results,
			200
		);
	}
}
