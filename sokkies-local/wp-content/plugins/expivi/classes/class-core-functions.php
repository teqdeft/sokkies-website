<?php
/**
 * Expivi core function
 *
 * @package Expivi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Function to retrieve items from array.
 *
 * @param array | mixed | object $arr Haystack.
 * @param ?string                $key Needle.
 * @param mixed                  $default_value Default value when key does not exists.
 */
function xpv_array_get( $arr, ?string $key, $default_value = null ) {
	if ( ! is_array( $arr ) ) {
		return $default_value;
	}

	if ( is_null( $key ) ) {
		return $default_value;
	}

	if ( array_key_exists( $key, $arr ) ) {
		return $arr[ $key ];
	}

	$key_pieces = explode( '.', $key );
	if ( count( $key_pieces ) > 0 ) {
		foreach ( $key_pieces as $segment ) {
			if ( array_key_exists( $segment, $arr ) ) {
				$arr = $arr[ $segment ];
			} else {
				return $default_value;
			}
		}
		return $arr;
	}

	return $default_value;
}

/**
 * Function to retrieve first key from array.
 *
 * @param array | mixed | object $arr Array of items.
 *
 * @return mixed | bool First key of array or false.
 */
function xpv_array_get_first_key( $arr ) {
	if ( ! is_array( $arr ) || empty( $arr ) ) {
		return false;
	}
	$result = false;

	foreach ( $arr as $key => $value ) {
		$result = $key;
		break;
	}
	return $result;
}

/**
 * Function to retrieve first value from array.
 *
 * @param array | mixed | object $arr Array of items.
 *
 * @return mixed|bool First value of array or false.
 */
function xpv_array_get_first_value( $arr ) {
	if ( ! is_array( $arr ) || empty( $arr ) ) {
		return false;
	}

	foreach ( (array) $arr as $value ) {
		return $value;
	}

	return false;
}

/**
 * Function to retrieve template.
 *
 * @param string $template_name Name of the template.
 * @param array  $args Array of data which is given to template.
 * @param string $template_path Path to template. Leave empty to take default route: 'templates/'.
 * @param string $default_path Default path to template when $template_path could not find template.
 */
function xpv_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	$cache_key = sanitize_key( implode( '-', array( 'template', $template_name, $template_path, $default_path, XPV_VERSION ) ) );
	$template  = (string) wp_cache_get( $cache_key, 'expivi' );

	if ( ! $template ) {
		$template = xpv_locate_template( $template_name, $template_path, $default_path );
		wp_cache_set( $cache_key, $template, 'expivi' );
	}

	$action_args = array(
		'template_name' => $template_name,
		'template_path' => $template_path,
		'located'       => $template,
		'args'          => $args,
	);

	if ( ! empty( $args ) && is_array( $args ) ) {
		if ( isset( $args['action_args'] ) ) {
			unset( $args['action_args'] );
		}
		extract( $args ); // @codingStandardsIgnoreLine
	}

	include $action_args['located'];
}

/**
 * Function to locate template.
 *
 * @param string      $template_name Name of the template.
 * @param string|bool $template_path Path to template. Leave empty to take default route: 'templates/'. False to ignore.
 * @param string      $default_path Default path to template when $template_path could not find template.
 *
 * @return ?string Path to located template.
 */
function xpv_locate_template( string $template_name, $template_path = '', string $default_path = '' ) {
	$overridable = false !== $template_path;

	if ( ! $template_path ) {
		$template_path = XPV()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = XPV()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = null;
	if ( $overridable ) {
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);
	}

	// Get default template from plugin: /templates.
	if ( ! $template ) {
		$template = trailingslashit( $default_path ) . $template_name;
	}

	// Return what we found.
	return $template;
}

/**
 * Function to retrieve template as HTML.
 *
 * @param string $template_name Name of the template.
 * @param array  $args Array of data which is given to template.
 * @param string $template_path Path to template. Leave empty to take default route: 'templates/'.
 * @param string $default_path Default path to template when $template_path could not find template.
 *
 * @return string HTML of template.
 */
function xpv_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	xpv_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Function to retrieve extension from filename.
 *
 * @param string $filename Name of file.
 *
 * @return string|boolean Extension of file or FALSE if not found.
 */
function xpv_filename_extension( $filename ) {
	$pos = strrpos( $filename, '.' );
	if ( false === $pos ) {
		return false;
	} else {
		$ext = substr( $filename, $pos + 1 );
		if ( strlen( $ext ) > 0 ) {
			return $ext;
		}
		return false;
	}
}

/**
 * This function will return the file path to the current theme directory.
 * Example: '/wordpress/wp-content/themes/twentytwenty'
 *
 * @return string|boolean File path to current theme (without trailing slash).
 */
function xpv_get_theme_dir() {
	if ( function_exists( 'get_template_directory' ) ) {
		return get_template_directory();
	}
	return false;
}

/**
 * This function will return the file path of the themes directory.
 * Example: '/wordpress/wp-content/themes'
 *
 * @return string|boolean File path to root of theme folder (without trailing slash).
 */
function xpv_theme_root_dir() {
	if ( function_exists( 'get_theme_root' ) ) {
		return get_theme_root();
	}
	return false;
}

/**
 * This function will return the url of the themes directory.
 * Example: '/wordpress/wp-content/themes'
 *
 * @return string|boolean Url to root of theme folder (without trailing slash).
 */
function xpv_theme_root_url() {
	if ( function_exists( 'get_theme_root_uri' ) ) {
		return get_theme_root_uri();
	}
	return false;
}

/**
 * Returns the current upload directory’s path (without subdir).
 * Example: '/wordpress/wp-content/uploads'
 *
 * @param boolean $refresh_cache Whether to refresh the cache.
 *
 * @return string|boolean File path to upload directory (without trailing slash).
 */
function xpv_upload_dir( $refresh_cache = false ) {
	if ( function_exists( 'wp_upload_dir' ) ) {
		$results = wp_upload_dir( null, false, $refresh_cache );
		if ( ! empty( $results ) ) {
			return $results['basedir'];
		}
	}
	return false;
}

/**
 * Returns the current upload url (without subdir).
 * Example: 'http://wordpress.com/wp-content/uploads'
 *
 * @param boolean $refresh_cache Whether to refresh the cache.
 *
 * @return string|boolean File path to upload url (without trailing slash).
 */
function xpv_upload_url( $refresh_cache = false ) {
	if ( function_exists( 'wp_upload_dir' ) ) {
		$results = wp_upload_dir( null, false, $refresh_cache );
		if ( ! empty( $results ) ) {
			return $results['baseurl'];
		}
	}
	return false;
}

/**
 * Get random hash (md5 of current time in microseceond).
 *
 * @return string
 */
function xpv_random_hash() {
	return md5( microtime( false ) );
}

/**
 * Simple function which checks if given base64 is valid.
 *
 * @param string $base64 A base64 string.
 *
 * @return boolean Wether the given base64 string is valid.
 */
function xpv_base64_validate( $base64 ) {
	return preg_match( '/^((data:(.*);base64,)?([a-zA-Z0-9\/\r\n+]*={0,2}))$/', $base64 ) === 1;
}

/**
 * Returns type from given base64 string.
 *
 * @param string $base64 A base64 string.
 *
 * @return string|bool Returns the type of base64. Example: 'image/jpeg'.
 */
function xpv_base64_get_image_type( $base64 ) {
	if ( strlen( $base64 ) === 0 ) {
		return false;
	}

	// Retrieve type from data using trustworthy php function.
	$data = explode( ',', $base64, 2 );
	if ( count( $data ) > 0 ) {
		try {
			// phpcs:ignore
			$info = getimagesizefromstring( base64_decode( $data[1] ) );
			if ( count( $info ) > 0 ) {
				return $info['mime'];
			}
		} catch ( Exception $ex ) {
			// Continue regardless of errors.
			unset( $ex );
		}
	}

	return false;
}
