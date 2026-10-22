<?php
/**
 * Expivi Template
 *
 * @package Expivi/WooCommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class to provide useful functions to render templates.
 */
class Expivi_Template {
	/**
	 * Call the Expivi API.
	 *
	 * @param string $method Method of API call (POST, GET, PUT, etc).
	 * @param string $endpoint Url of API.
	 * @param mixed  $data Body of API call.
	 * @param array  $headers Headers for API call.
	 *
	 * @return array|mixed|object
	 */
	protected function call_api( string $method, string $endpoint, $data = null, array $headers = array() ) {
		if ( '/' === $endpoint[0] ) {
			$endpoint = substr( $endpoint, 1 );
		}

		if ( ! is_string( $data ) && ! empty( $data ) ) {
			$data = wp_json_encode( $data );
		}

		try {
			$shop_locale = XPV()->get_locale();

			$request_header = array(
				'Authorization'   => 'Bearer ' . $this->get_setting( 'api_token', '' ),
				'Accept'          => 'application/json',
				'Content-Type'    => 'application/json',
				'Accept-Language' => $shop_locale,
			);

			if ( ! empty( $headers ) ) {
				$request_header = array_merge( $request_header, $headers );
			}

			$response = ( new WP_Http() )->request(
				$this->get_api_url() . $endpoint,
				array(
					'method'          => strtoupper( $method ),
					'headers'         => $request_header,
					'body'            => $data,
					'timeout'         => 60, // How long should we wait for a response (seconds)?
					'connect_timeout' => 60, // How long should we wait while trying to connect (seconds)?
				),
			);

			if ( is_wp_error( $response ) ) {
				XPV()->log(
					// phpcs:ignore
					print_r( 'Request failed with WP_Error: ' . $this->get_api_url() . $endpoint . ', Statuscode: ' . $response->get_error_code() . ', Message: ' . $response->get_error_message(), true ),
					Expivi::ERROR
				);

				return array();
			}

			if ( $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
				return json_decode( $response['body'], true );
			}

			XPV()->log(
				// phpcs:ignore
				print_r( 'Request failed: ' . $this->get_api_url() . $endpoint . ', Statuscode: ' . $response['response']['code'] . ', Message: ' . $response['response']['message'], true ),
				Expivi::ERROR
			);

			return array();
		} catch ( Exception $ex ) {
			XPV()->log_exception( $ex );
		}

		return array();
	}

	/**
	 * Function to retrieve expivi settings.
	 *
	 * @param string $setting Name of the setting.
	 * @param mixed  $default_value Default in case of undefined setting.
	 * @param string $group Name of setting group.
	 *
	 * @return mixed
	 */
	protected function get_setting( string $setting, $default_value = null, string $group = 'expivi-settings' ) {
		$settings = (array) get_option( $group );

		return array_key_exists( $setting, $settings ) ? $settings[ $setting ] : $default_value;
	}

	/**
	 * Function to retrieve expivi settings in product.
	 *
	 * @param ?WC_Product $product Instance of product.
	 * @param string      $setting Setting name which is saved in meta of product.
	 * @param mixed       $default_value Default value when retrieval of setting failed.
	 *
	 * @return mixed
	 */
	protected function get_product_setting( ?WC_Product $product, string $setting, $default_value ) {
		if ( $product && $product->meta_exists( 'expivi_id' ) ) {
			return $product->meta_exists( $setting ) ? $product->get_meta( $setting ) : $default_value;
		}

		return $default_value;
	}

	/**
	 * Retrieve API URL from settings.
	 *
	 * @return string API URL.
	 */
	protected function get_api_url(): string {
		$url = $this->get_setting( 'api_url', 'https://expivi.net/api/' );

		if ( substr( $url, -1 ) !== '/' ) {
			return $url . '/';
		}

		return $url;
	}

	/**
	 * Check if user has entered their API key.
	 */
	protected function has_api_key(): bool {
		return ! empty( $this->get_setting( 'api_token', '' ) );
	}

	/**
	 * Check if the current page is an Expivi product
	 *
	 * @param ?int $product_id The id of the product.
	 *
	 * @return bool
	 */
	public function has_expivi_product( ?int $product_id = null ): bool {
		$product = $this->get_product( $product_id );
		return $product && $product->meta_exists( 'expivi_id' );
	}

	/**
	 * Retrieve WC product.
	 *
	 * @param ?int $product_id The id of the product.
	 *
	 * @return ?WC_Product|false
	 */
	public function get_product( ?int $product_id = null ) {
		global $product;

		// Required to check whether $product is available
		// as manual retrieval is required during `wp_enqueue_scripts`.
		if ( ! is_a( $product, 'WC_Product' ) || $product_id ) {
			return wc_get_product( $product_id ?? get_the_ID() );
		}

		return $product;
	}
}
