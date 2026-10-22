<?php
/**
 * Expivi SVG Conversion Request
 *
 * @package Expivi/SVG
 */

defined( 'ABSPATH' ) || exit;

require_once XPV_ABSPATH . 'classes/svg/class-expivi-svg-conversion-controller.php';

/**
 * Data structure for results/progress regarding print ready files.
 */
class Expivi_SVG_Conversion_Request {
	/**
	 * Identifier of request.
	 *
	 * @var string $hash
	 */
	public $hash = '';

	/**
	 * Status of request.
	 *
	 * @var string $status
	 */
	public $status = Expivi_SVG_Conversion_Controller::STATUS_IN_PROGRESS;

	/**
	 * Results of request.
	 *
	 * @var array $files
	 */
	public $files = array();
}
