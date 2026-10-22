<?php
/**
 * Expivi Admin Settings
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/woocommerce/class-expivi-template.php';

/**
 * Class to provide validation on products.
 */
class Expivi_Validation extends Expivi_Template {

	const BLACKLIST_CHECK_ATTRIBUTE_TYPES = array( 'text_input', 'text_to_image' );

	/**
	 * Expivi_Validation Constructor.
	 */
	public function __construct() {
		add_filter( 'expivi_product_validation', array( $this, 'blacklist_word_validation' ), 10, 3 );
	}

	/**
	 * Handling the validation for blacklisting words.
	 *
	 * @param bool       $is_valid              The validation result for chaining.
	 * @param WC_Product $wc_product            The WooCommerce product.
	 * @param array      $configured_product    The Configured product to validate.
	 * @return bool                             Whether the validation has passed.
	 */
	public function blacklist_word_validation( $is_valid, $wc_product, $configured_product ) {
		try {
			$blacklisted_words = mb_strtolower( str_replace( PHP_EOL, '', $this->get_setting( 'blacklisted_words', '' ) ), 'UTF-8' );
			if ( empty( $blacklisted_words ) || empty( $configured_product ) ) {
				return $is_valid;
			}

			$blacklisted_words = array_map( 'trim', explode( ',', $blacklisted_words ) );
			$response          = $this->has_api_key() ? $this->call_api( 'GET', 'catalogue/' . $configured_product['catalogue_id'] . '?include=attributes' ) : array();
			$attribute_types   = array();

			if ( ! empty( $response ) ) {
				foreach ( $response['included'] as $expivi_attribute ) {
					$attribute_types[ $expivi_attribute['id'] ] = $expivi_attribute['attributes']['type'];
				}
			}

			// Allow memory to be reclaimed.
			unset( $response );

			foreach ( $configured_product['configuration']['attributes'] as $attribute ) {
				if ( isset( $attribute_types[ $attribute['attribute_id'] ] ) && in_array( $attribute_types[ $attribute['attribute_id'] ], self::BLACKLIST_CHECK_ATTRIBUTE_TYPES, true ) ) {
					$value = mb_strtolower( trim( explode( '@', $attribute['attribute_value']['value'] )[0] ), 'UTF-8' );
					if ( in_array( $value, $blacklisted_words, true ) ) {
						// translators: %s Name of the attribute.
						wc_add_notice( sprintf( __( '%s contains a blacklisted word.', 'expivi' ), $attribute['attribute_name'] ), 'error' );
						$is_valid = false;
					}
				}
			}
		} catch ( Exception $ex ) {
			XPV()->log_exception( $ex );
		}

		return $is_valid;
	}
}
