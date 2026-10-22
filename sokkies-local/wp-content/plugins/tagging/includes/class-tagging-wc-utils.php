<?php
defined('ABSPATH') || exit;
/**
 * This file is copied from stape wordpress plugin
 * @package 
 */
class Tagging_WC_Utils
{
	protected static $instance = null;

	public static function get_setting_name($name)
	{
		if (defined('ICL_LANGUAGE_CODE')) {
			return $name . '_' . ICL_LANGUAGE_CODE;
		}
		return $name;
	}

	final public static function instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function get_product_categories($category_id)
	{
		$crumbs = get_term_parents_list(
			$category_id,
			'product_cat',
			array(
				'link' => false,
			)
		);

		return rtrim($crumbs, '/');
	}

	public static function json_encode($data, $options = 0, $depth = 512)
	{
		return wp_json_encode($data, $options, $depth);
	}

	public function enabled_wc()
	{
		return function_exists('WC');
	}

	public function get_data_layer_item($product, array $args = array())
	{
		$args = wp_parse_args(
			$args,
			array(
				'add_categories' => true,
				'add_variations' => true,
				'quantity' => 1,
				'list_id' => '',
				'list_name' => '',
				'affiliation' => get_bloginfo('name'),
				'location_id' => '',
			)
		);

		$result = array(
			'item_id'    					=> (string)$product->get_id(),
			'item_sku' 						=> (string)(method_exists($product, 'get_sku') && $product->get_sku() !== null ? $product->get_sku() : ''),
			'item_name'  					=> (string)$product->get_title(),
			'affiliation'					=> (string)$args['affiliation'],
			'discount'						=> (float)($product->get_sale_price() ? $product->get_regular_price() - $product->get_sale_price() : 0),
			'item_brand' 					=> (string)$this->get_product_brand($product->get_id()),
			'item_list_id'				=> (string)$args['list_id'],
			'item_list_name'			=> (string)$args['list_name'],
			'price'      					=> (float)$this->formatted_price(wc_get_price_including_tax($product)),
			'quantity'						=> (int)$args['quantity'],
			// Additional custom fields
			'price_excluding_vat' => (float)$this->formatted_price(wc_get_price_excluding_tax($product)),
		);

		$is_business_vertical_enabled = get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_BUSINESS_VERTICAL_ENABLED));

		if ($is_business_vertical_enabled) {
			$result['google_business_vertical']	= (string)get_option(Tagging_WC_Utils::get_setting_name(TAGGING_SETTINGS_BUSINESS_VERTICAL));
		}


		if (true === $args['add_variations']) {
			if ('variation' === $product->get_type()) {
				$labels = $this->get_product_variations($product);
				if (!empty($labels)) {
					foreach ($labels as $i => $label) {
						$index = $i === 0 ? 'item_variant' : 'item_variant' . ($i + 1);
						$result[$index] = (string)$label;
					}
				}
			}
		}

		if (true === $args['add_categories']) {
			$category_ids = $product->get_category_ids();
			if ($product->get_parent_id() > 0) {
				$parent_product = wc_get_product($product->get_parent_id());
				if (!empty($parent_product)) {
					$category_ids = $parent_product->get_category_ids();
				}
			}

			if (!empty($category_ids[0])) {
				$breadcrumbs = $this->get_product_categories($category_ids[0]);
				if (!empty($breadcrumbs)) {
					$categories = explode('/', $breadcrumbs);
					for ($i = 0; $i < min(count($categories), 5); $i++) {
						$index = $i === 0 ? 'item_category' : 'item_category' . ($i + 1);
						$result[$index] = (string)trim($categories[$i]);
					}
				}
			}
		}

		return $result;
	}

	function is_first_time_customer($email)
	{
		$args = array(
			'email' => $email,
			'return' => 'ids',
			'limit' => 2,
		);

		$orders = wc_get_orders($args);

		if (count($orders) > 1) {
			// Customer has previous orders
			return false;
		} else {
			// No previous orders found, first-time customer
			return true;
		}
	}

	public function get_order_data_layer_items($items)
	{
		$index  = 1;
		$result = array();
		foreach ($items as $item_loop) {
			$product = $item_loop->get_product();

			if (!($product instanceof WC_Product)) {
				$result[] = array();
				continue;
			}

			$array             = $this->get_data_layer_item($product);
			$array['quantity'] = intval($item_loop->get_quantity());
			$array['index']    = $index++;

			$result[] = $array;
		}

		return $result;
	}


	public function get_cart_data_layer_items($cart)
	{
		$index  = 1;
		$result = array();
		foreach ($cart as $product_loop) {
			$product = $product_loop['data'];

			$array             = $this->get_data_layer_item($product);
			$array['quantity'] = intval($product_loop['quantity']);
			$array['index']    = $index++;

			$result[] = $array;
		}

		return $result;
	}


	public function get_data_layer_user_data()
	{
		if (!(WC()->customer instanceof WC_Customer)) {
			return array();
		}
		$customer = new WC_Customer(WC()->customer->get_id());

		$fields = array(
			'customer_id'             => $customer->get_id(),
			'customer_email'          => $customer->get_email(),
			'customer_name'           => $customer->get_first_name() . ' ' . $customer->get_last_name(),
			'customer_first_name'     => $customer->get_first_name(),
			'customer_last_name'      => $customer->get_last_name(),
			'customer_phone'          => $customer->get_billing_phone(),
			'customer_address'        => join(' ', array($customer->get_billing_address_1(), $customer->get_billing_address_2())),
			'customer_city'           => $customer->get_billing_city(),
			'customer_state'          => $customer->get_billing_state(),
			'customer_zip'            => $customer->get_billing_postcode(),
			'customer_country'        => $customer->get_billing_country(),
			'shipping_first_name'     => $customer->get_shipping_first_name(),
			'shipping_last_name'      => $customer->get_shipping_last_name(),
			'shipping_company'        => $customer->get_shipping_company(),
			'shipping_address'        => join(' ', array($customer->get_shipping_address_1(), $customer->get_shipping_address_2())),
			'shipping_postcode'       => $customer->get_shipping_postcode(),
			'shipping_country'        => $customer->get_shipping_country(),
			'shipping_state'          => $customer->get_shipping_state(),
			'shipping_city'           => $customer->get_shipping_city(),
		);

		if (method_exists($customer, 'get_shipping_phone')) {
			$fields['shipping_phone'] = $customer->get_shipping_phone();
		}

		return $fields;
	}

	public function get_order_user_data($order)
	{
		if (!($order instanceof WC_Order)) {
			return array();
		}

		$data = array(
			'customer_id'         => $order->get_customer_id(),
			'customer_email'      => $order->get_billing_email(),
			'customer_name'       => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
			'customer_first_name' => $order->get_billing_first_name(),
			'customer_last_name'  => $order->get_billing_last_name(),
			'customer_phone'      => $order->get_billing_phone(),
			'customer_address'    => join(' ', array($order->get_billing_address_1(), $order->get_billing_address_2())),
			'customer_city'       => $order->get_billing_city(),
			'customer_state'      => $order->get_billing_state(),
			'customer_zip'        => $order->get_billing_postcode(),
			'customer_country'    => $order->get_billing_country(),
			'shipping_first_name' => $order->get_shipping_first_name(),
			'shipping_last_name'  => $order->get_shipping_last_name(),
			'shipping_company'    => $order->get_shipping_company(),
			'shipping_address'    => join(' ', array($order->get_shipping_address_1(), $order->get_shipping_address_2())),
			'shipping_postcode'   => $order->get_shipping_postcode(),
			'shipping_country'    => $order->get_shipping_country(),
			'shipping_state'      => $order->get_shipping_state(),
			'shipping_city'       => $order->get_shipping_city(),
			'is_first_time_buy'   => $this->is_first_time_customer($order->get_billing_email()),

			'billing_first_name'  => $order->get_billing_first_name(),
			'billing_last_name'   => $order->get_billing_last_name(),
			'billing_address'     => join(' ', array($order->get_billing_address_1(), $order->get_billing_address_2())),
			'billing_postcode'    => $order->get_billing_postcode(),
			'billing_country'     => $order->get_billing_country(),
			'billing_state'       => $order->get_billing_state(),
			'billing_city'        => $order->get_billing_city(),
			'billing_email'       => $order->get_billing_email(),
			'billing_phone'       => $order->get_billing_phone(),
		);

		if (method_exists($order, 'get_shipping_phone')) {
			$data['shipping_phone'] = $order->get_shipping_phone();
		}

		$user = $order->get_user();
		if ($user instanceof WP_User) {
			$data['email']      = $user->user_email;
			$data['first_name'] = $user->first_name;
			$data['last_name']  = $user->last_name;
		}

		return $data;
	}

	public function is_new_customer($customer_id)
	{
		if (!function_exists('WC')) {
			return null;
		}

		$customer = new WC_Customer($customer_id);
		if (!($customer instanceof WC_Customer)) {
			return null;
		}

		return $customer->get_order_count() === 1;
	}

	public function get_data_layer_categories($category_ids)
	{
		$result = array();
		if (empty($category_ids[0])) {
			return $result;
		}

		$breadcrumbs = $this->get_product_categories($category_ids[0]);
		if (empty($breadcrumbs)) {
			return $result;
		}

		$categories     = explode('/', $breadcrumbs);
		$category_index = 1;
		foreach ($categories as $category_name) {
			$index            = $category_index > 1 ? 'item_category' . $category_index : 'item_category';
			$result[$index] = esc_attr($category_name);

			if ($category_index >= 5) {
				return $result;
			}
			$category_index++;
		}
		return $result;
	}

	public function get_product_brand($product_id)
	{
		$taxonomy = apply_filters('gtm_trytagging_product_brand_taxonomy', false);
		if (false === $taxonomy) {
			return '';
		}

		$terms = wp_get_post_terms(
			$product_id,
			$taxonomy,
			array(
				'orderby' => 'parent',
				'order'   => 'ASC',
			)
		);

		if (empty($terms)) {
			return '';
		}
		if (empty($terms[0])) {
			return '';
		}
		if (!is_a($terms[0], 'WP_Term')) {
			return '';
		}

		return $terms[0]->name;
	}

	public function get_product_variations($product)
	{
		$labels     = array();
		$attributes = $product->get_variation_attributes();
		foreach ($attributes as $key => $value) {
			if (false === strstr($key, 'attribute_pa_')) {
				$labels[] = $value;
				continue;
			}

			$taxonomy = str_replace('attribute_', '', $key);
			if (!taxonomy_exists($taxonomy)) {
				continue;
			}

			$term = get_term_by('slug', $value, $taxonomy);
			if (is_a($term, 'WP_Term')) {
				$labels[] = $term->name;
			}
		}
		return $labels;
	}

	public function is_enable_user_data()
	{
		return function_exists('WC') && (WC()->customer instanceof WC_Customer);
	}

	public function formatted_price($price): float
	{
		$price = floatval($price);
		if (function_exists('wc_format_decimal')) {
			$price = wc_format_decimal($price, wc_get_price_decimals());
		} else {
			$price = number_format($price, 2, '.', '');
		}
		return floatval($price);
	}

	public function get_cart_total()
	{
		if (get_option('woocommerce_prices_include_tax')) {
			return (float)number_format($this->formatted_price(WC()->cart->get_total('edit')), 2, '.', '');
		} else {
			return (float)number_format($this->formatted_price(WC()->cart->get_cart_contents_total()), 2, '.', '');
		}
	}

	public static function set_session($name, $value)
	{
		self::set_cookie(
			array(
				'name'     => $name,
				'value'    => $value,
				'secure'   => false,
				'samesite' => '',
			)
		);
	}

	public static function get_session($name, $default = null)
	{
		if (!isset($_COOKIE[$name])) {
			return $default;
		}

		return filter_input(INPUT_COOKIE, $name, FILTER_DEFAULT);
	}

	public static function javascript_delete_cookie($name)
	{
		$options = self::get_default_cookie_options();
?>
		<script>
			document.cookie = '<?php echo esc_attr($name); ?>=; max-age=-1; path=<?php echo esc_attr($options['path']); ?>; domain=<?php echo esc_attr($options['domain']); ?>;';
		</script>
<?php
	}
	public static function set_cookie($args)
	{
		$args = wp_parse_args(
			$args,
			self::get_default_cookie_options()
		);

		if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
			$name  = $args['name'];
			$value = $args['value'];

			unset($args['name']);
			unset($args['value']);

			setcookie(
				$name,
				$value,
				$args
			);
		} else {
			setcookie(
				$args['name'],
				$args['value'],
				$args['expires'],
				$args['path'],
				$args['domain'],
				$args['secure'],
				$args['httponly']
			);
		}
	}

	private static function get_default_cookie_options()
	{
		return array(
			'name'     => '',
			'value'    => '',
			'expires'  => 0,
			'path'     => '/',
			'domain'   => '.' . wp_parse_url(home_url(), PHP_URL_HOST),
			'secure'   => true,
			'httponly' => false,
			'samesite' => 'lax',
		);
	}

	public function get_item($product)
	{
		if (!($product instanceof WC_Product)) {
			return array();
		}

		$array          = Tagging_WC_Utils::instance()->get_data_layer_item($product);
		$array['index'] = 1;

		return $array;
	}

	/**
	 * Get all variations of a variable product for frontend use
	 * @param WC_Product $product The variable product
	 * @return array Array of variations with same structure as get_data_layer_item
	 */
	public function get_product_variations_data($product)
	{
		if (!($product instanceof WC_Product)) {
			return array();
		}

		// Only process variable products
		if ($product->get_type() !== 'variable') {
			return array();
		}

		try {
			$variations_data = array();
			$variations = $product->get_available_variations();

			foreach ($variations as $variation_data) {
				$variation_id = $variation_data['variation_id'];
				$variation = wc_get_product($variation_id);

				if ($variation && $variation instanceof WC_Product) {
					$variation_item = $this->get_data_layer_item($variation);
					$variations_data[$variation_id] = $variation_item;
				}
			}

			return $variations_data;
		} catch (Exception $e) {
			return array();
		}
	}
}
