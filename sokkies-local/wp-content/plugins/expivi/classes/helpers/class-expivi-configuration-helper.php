<?php
/**
 * Expivi Configuration Helper
 *
 * @package Expivi/Helpers
 */

defined( 'ABSPATH' ) || exit;

trait Expivi_Configuration_Helper {

	/**
	 * Retrieves the configuration hash from the configuration object
	 *
	 * @param mixed $configuration The configuration array.
	 *
	 * @return string
	 */
	public function get_configuration_hash( $configuration ): string {
		$configuration_unslashed = wp_unslash( $configuration );
		$configured_products     = json_decode( $configuration_unslashed, true );

		return $this->get_hash_from_configured_products( $configured_products );
	}

	/**
	 * Retrieves and creates the configuration share link.
	 *
	 * @param mixed  $configuration The configuration array.
	 * @param mixed  $permalink The base url from the request.
	 * @param string $directory Fixed set upload directory.
	 * @return string
	 */
	public function get_share_link_url( $configuration, $permalink, string $directory ): string {
		$hash     = $this->get_configuration_hash( $configuration );
		$sub_dir  = XPV()->fs->combine( $directory, $hash );
		$hash_dir = XPV()->fs->combine( xpv_upload_dir(), $sub_dir );

		if ( ! XPV()->fs->exists( $hash_dir ) ) {
			XPV()->fs->mkdir( $hash_dir );
		}

		if ( ! XPV()->fs->exists( "$hash_dir/configuration.json" ) ) {
			XPV()->fs->write(
				'configuration.json',
				$configuration,
				true,
				$hash_dir
			);
		}
		return esc_url( add_query_arg( 'configuration', $hash, $permalink ) );
	}

	/**
	 * Generate an MD5 based on all data without UUIDs
	 *
	 * @param array $configured_products The configured products.
	 *
	 * @return string
	 */
	private function get_hash_from_configured_products( array $configured_products ): string {
		$props_to_delete = array(
			'uuid',
			'parent_uuid',
			'bundle_uuid',
			'temporary_uid',
			'thumbnail',
		);

		foreach ( $configured_products as &$configured_product ) {
			foreach ( $props_to_delete as $prop ) {
				if ( isset( $configured_product[ $prop ] ) ) {
					unset( $configured_product[ $prop ] );
				}
			}
		}

		return md5( wp_json_encode( $configured_products ) );
	}

	/**
	 * Generate configuration hash using data from articles and the product price.
	 *
	 * @param mixed $configured_product A single configured product.
	 * @param float $price Price of the product.
	 * @param array $meta_data Extra data for the hash.
	 *
	 * @return string
	 */
	private function get_config_hash( $configured_product, float $price, array $meta_data = array() ): string {
		$arr = array(
			'articles' => $configured_product['articles'],
			'price'    => $price,
		);

		return sha1( wp_json_encode( array_merge( $arr, $meta_data ) ) );
	}
}
