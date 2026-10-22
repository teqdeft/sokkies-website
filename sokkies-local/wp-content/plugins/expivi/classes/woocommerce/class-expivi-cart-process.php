<?php
/**
 * Expivi Cart Process
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class to provide add-to-cart-process variables.
 */
class Expivi_Cart_Process {
	public const PROCESS_SINGLE            = 1;
	public const PROCESS_GROUPED           = 2;
	public const PROCESS_GROUPED_AND_PRICE = 3;
	public const PROCESS_MULTIPLE          = 4;
}
