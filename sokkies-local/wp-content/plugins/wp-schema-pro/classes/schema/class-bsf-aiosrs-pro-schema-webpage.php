<?php
/**
 * WebPage Schema.
 *
 * @package Schema Pro
 * @since 2.11.0
 */

if ( ! class_exists( 'BSF_AIOSRS_Pro_Schema_Webpage' ) ) {

	/**
	 * AIOSRS Schemas Initialization
	 *
	 * @since 2.11.0
	 */
	class BSF_AIOSRS_Pro_Schema_Webpage {

		/**
		 * Allowed @type values for server-side validation.
		 *
		 * @var array<string>
		 */
		private static $allowed_types = array( 'WebPage', 'CollectionPage', 'AboutPage', 'ContactPage' );

		/**
		 * Render Schema.
		 *
		 * @param  array<string, mixed> $data Meta Data.
		 * @param  array<string, mixed> $post Current Post Array.
		 * @return array<string, mixed>
		 *
		 * Note: The wp_schema_pro_schema_webpage filter exposes the assembled schema
		 * array to third-party code. All string values added via this filter will be
		 * output inside a <script type="application/ld+json"> block using JSON_UNESCAPED_SLASHES.
		 * Filter callbacks MUST sanitize any values they inject (e.g. wp_strip_all_tags,
		 * esc_url) to prevent </script> injection.
		 */
		public static function render( array $data, array $post ): array {
			$schema           = array();
			$general_settings = BSF_AIOSRS_Pro_Helper::$settings['wp-schema-pro-general-settings'];

			$schema['@context'] = 'https://schema.org';

			// @type: server-side allowlist validation against defined choices.
			$schema_type     = ! empty( $data['schema-type'] ) ? wp_strip_all_tags( (string) $data['schema-type'] ) : 'WebPage';
			$schema['@type'] = in_array( $schema_type, self::$allowed_types, true ) ? $schema_type : 'WebPage';

			// name (required) — maps to post_title by default.
			$schema['name'] = ! empty( $data['name'] ) ? wp_strip_all_tags( (string) $data['name'] ) : null;

			// url (required) — maps to post_permalink by default.
			if ( ! empty( $data['url'] ) ) {
				$schema['url'] = esc_url( (string) $data['url'] );
			}

			// description — maps to post_excerpt by default.
			$schema['description'] = ! empty( $data['description'] ) ? wp_strip_all_tags( (string) $data['description'] ) : null;

			// datePublished — maps to post_date by default.
			$schema['datePublished'] = ! empty( $data['published-date'] ) ? wp_strip_all_tags( (string) $data['published-date'] ) : null;

			// dateModified — maps to post_modified by default.
			$schema['dateModified'] = ! empty( $data['modified-date'] ) ? wp_strip_all_tags( (string) $data['modified-date'] ) : null;

			// inLanguage — maps to site language (BCP-47 format, e.g. en-US).
			$schema['inLanguage'] = ! empty( $data['in-language'] ) ? wp_strip_all_tags( (string) $data['in-language'] ) : null;

			// image / thumbnailUrl — maps to featured_img by default.
			if ( ! empty( $data['image'] ) && is_array( $data['image'] ) ) {
				$schema['image'] = BSF_AIOSRS_Pro_Schema_Template::get_image_schema( $data['image'] );
				if ( isset( $data['image'][0] ) && is_string( $data['image'][0] ) ) {
					$schema['thumbnailUrl'] = esc_url( $data['image'][0] );
				}
			}

			// author — Person node (maps to author_name + author_url).
			if ( ! empty( $data['author'] ) ) {
				$schema['author']['@type'] = 'Person';
				$schema['author']['name']  = wp_strip_all_tags( (string) $data['author'] );
				if ( ! empty( $data['author-url'] ) ) {
					$schema['author']['url'] = esc_url( (string) $data['author-url'] );
				}
			}

			// publisher — Organization. Build once with both name and logo to ensure
			// name is always present alongside logo (prevents an Organization node with
			// only a logo and no name, which fails schema.org validation).
			$publisher_name = ! empty( $data['orgnization-name'] ) ? wp_strip_all_tags( (string) $data['orgnization-name'] ) : null;

			if ( isset( $data['site-logo'] ) && ! empty( $data['site-logo'] ) ) {
				$schema['publisher']['@type'] = 'Organization';
				if ( $publisher_name ) {
					$schema['publisher']['name'] = $publisher_name;
				}
				$schema['publisher']['logo'] = BSF_AIOSRS_Pro_Schema_Template::get_image_schema( (array) $data['site-logo'], 'ImageObject2' );
			} else {
				// Resolve logo from global site settings first; fall back to theme custom logo.
				$logo_id = 0;
				if ( isset( $general_settings['site-logo'] ) && 'custom' === $general_settings['site-logo'] ) {
					$logo_id = isset( $general_settings['site-logo-custom'] ) ? (int) $general_settings['site-logo-custom'] : 0;
				}
				if ( ! $logo_id && function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
					$logo_id = (int) get_theme_mod( 'custom_logo' );
				}
				if ( $logo_id ) {
					// Add logo image size temporarily to generate the constrained logo crop.
					add_filter( 'intermediate_image_sizes_advanced', 'BSF_AIOSRS_Pro_Schema_Template::logo_image_sizes' );
					$logo_image = wp_get_attachment_image_src( $logo_id, 'aiosrs-logo-size' );
					if ( isset( $logo_image[3] ) && 1 !== $logo_image[3] ) {
						BSF_AIOSRS_Pro_Schema_Template::generate_logo_by_width( $logo_id );
						$logo_image = wp_get_attachment_image_src( $logo_id, 'aiosrs-logo-size' );
					}
					// Remove logo image size — must be symmetrically removed after use.
					remove_filter( 'intermediate_image_sizes_advanced', 'BSF_AIOSRS_Pro_Schema_Template::logo_image_sizes' );
					if ( is_array( $logo_image ) && ! empty( $logo_image[0] ) ) {
						$schema['publisher']['@type'] = 'Organization';
						if ( $publisher_name ) {
							$schema['publisher']['name'] = $publisher_name;
						}
						$schema['publisher']['logo'] = BSF_AIOSRS_Pro_Schema_Template::get_image_schema( $logo_image, 'ImageObject' );
					}
				} elseif ( $publisher_name ) {
					// No logo available — still emit name-only publisher if name is set.
					$schema['publisher']['@type'] = 'Organization';
					$schema['publisher']['name']  = $publisher_name;
				}
			}

			// isPartOf — links WebPage to WebSite using @id (JSON-LD node identifier).
			if ( ! empty( $data['is-part-of'] ) ) {
				$schema['isPartOf']['@type'] = 'WebSite';
				$schema['isPartOf']['@id']   = esc_url( (string) $data['is-part-of'] );
			}

			return apply_filters( 'wp_schema_pro_schema_webpage', $schema, $data, $post );
		}

	}
}
