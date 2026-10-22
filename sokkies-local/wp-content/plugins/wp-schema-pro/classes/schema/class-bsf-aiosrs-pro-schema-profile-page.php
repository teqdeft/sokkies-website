<?php
/**
 * ProfilePage Schema.
 *
 * @package Schema Pro
 * @since 2.12.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BSF_AIOSRS_Pro_Schema_Profile_Page' ) ) {

	/**
	 * AIOSRS Schemas Initialization
	 *
	 * @since 2.12.0
	 */
	class BSF_AIOSRS_Pro_Schema_Profile_Page {

		/**
		 * Allowed mainEntity @type values for server-side validation.
		 *
		 * @var array<string>
		 */
		private static $allowed_entity_types = array( 'Person', 'Organization' );

		/**
		 * Render Schema.
		 *
		 * @param  array<string, mixed> $data Meta Data.
		 * @param  array<string, mixed> $post Current Post Array.
		 * @return array<string, mixed>
		 *
		 * Note: The wp_schema_pro_schema_profile_page filter exposes the assembled
		 * schema array to third-party code. All string values added via this filter
		 * will be output inside a <script type="application/ld+json"> block using
		 * JSON_UNESCAPED_SLASHES. Filter callbacks MUST sanitize any values they
		 * inject (e.g. wp_strip_all_tags, esc_url) to prevent </script> injection.
		 */
		public static function render( array $data, array $post ): array {
			$schema = array();

			$schema['@context'] = 'https://schema.org';
			$schema['@type']    = 'ProfilePage';

			// dateCreated / dateModified — recommended by Google for ProfilePage.
			$schema['dateCreated']  = ! empty( $data['date-created'] ) ? wp_strip_all_tags( (string) $data['date-created'] ) : null;
			$schema['dateModified'] = ! empty( $data['date-modified'] ) ? wp_strip_all_tags( (string) $data['date-modified'] ) : null;

			// mainEntity — the Person or Organization this page profiles (required by Google).
			$entity = array();

			// mainEntity @type: server-side allowlist validation against defined choices.
			$entity_type     = ! empty( $data['schema-type'] ) ? wp_strip_all_tags( (string) $data['schema-type'] ) : 'Person';
			$entity['@type'] = in_array( $entity_type, self::$allowed_entity_types, true ) ? $entity_type : 'Person';

			// name (required) — maps to author_name by default.
			$entity['name'] = ! empty( $data['name'] ) ? wp_strip_all_tags( (string) $data['name'] ) : null;

			// alternateName — username or handle.
			$entity['alternateName'] = ! empty( $data['alternate-name'] ) ? wp_strip_all_tags( (string) $data['alternate-name'] ) : null;

			// description — maps to post_excerpt by default.
			$entity['description'] = ! empty( $data['description'] ) ? wp_strip_all_tags( (string) $data['description'] ) : null;

			// url — maps to post_permalink by default.
			if ( ! empty( $data['url'] ) ) {
				$entity['url'] = esc_url( (string) $data['url'] );
			}

			// image — maps to author_image by default.
			if ( ! empty( $data['image'] ) && is_array( $data['image'] ) ) {
				$entity['image'] = BSF_AIOSRS_Pro_Schema_Template::get_image_schema( $data['image'] );
			}

			// sameAs — social profile URLs from the repeater field.
			if ( isset( $data['add-url'] ) && is_array( $data['add-url'] ) ) {
				foreach ( $data['add-url'] as $key => $value ) {
					if ( isset( $value['same-as'] ) && ! empty( $value['same-as'] ) ) {
						$entity['sameAs'][ $key ] = esc_url( (string) $value['same-as'] );
					}
				}
			}

			$schema['mainEntity'] = $entity;

			return apply_filters( 'wp_schema_pro_schema_profile_page', $schema, $data, $post );
		}

	}
}
