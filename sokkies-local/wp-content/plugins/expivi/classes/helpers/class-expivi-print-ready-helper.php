<?php
/**
 * Print Ready Helper
 */

defined( 'ABSPATH' ) || exit;

/**
 * Helper class for Print Ready functionality.
 */
class Expivi_Print_Ready_Helper {
	/**
	 * Useful parser to grab the print ready file ids from a configured product.
	 *
	 * @param array $configured_product Configured product.
	 *
	 * @return array List of (unique) print ready file ids.
	 */
	public static function get_print_ready_file_id_from_configuration( array $configured_product ): array {
		$attributes = (array) $configured_product['attributes'];

		if ( ! $attributes ) {
			return array();
		}

		$print_ready_file_ids = array();

		foreach ( $attributes as $attribute ) {
			if ( ! $attribute['attribute_value'] ) {
				continue;
			}

			if ( $attribute['attribute_value']['print_ready_file_id'] ) {
				$print_ready_file_ids[] = (int) $attribute['attribute_value']['print_ready_file_id'];
			}
		}

		// Remove duplicates and return result.
		return array_unique( $print_ready_file_ids, SORT_NUMERIC );
	}
}
