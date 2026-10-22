<?php
/**
 * Dataset Schema.
 *
 * @package Schema Pro
 * @since 2.12.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BSF_AIOSRS_Pro_Schema_Dataset' ) ) {

	/**
	 * AIOSRS Schemas Initialization
	 *
	 * @since 2.12.0
	 */
	class BSF_AIOSRS_Pro_Schema_Dataset {

		/**
		 * Render Schema.
		 *
		 * @param  array<string, mixed> $data Meta Data.
		 * @param  array<string, mixed> $post Current Post Array.
		 * @return array<string, mixed>
		 *
		 * Note: The wp_schema_pro_schema_dataset filter exposes the assembled
		 * schema array to third-party code. All string values added via this
		 * filter will be output inside a <script type="application/ld+json">
		 * block using JSON_UNESCAPED_SLASHES. Filter callbacks MUST sanitize any
		 * values they inject (e.g. wp_strip_all_tags, esc_url) to prevent
		 * </script> injection.
		 */
		public static function render( array $data, array $post ): array {
			$schema = array();

			$schema['@context'] = 'https://schema.org';
			$schema['@type']    = 'Dataset';

			// name (required) — maps to post_title by default.
			$schema['name'] = ! empty( $data['name'] ) ? wp_strip_all_tags( (string) $data['name'] ) : null;

			// description (required) — maps to post_content by default.
			$schema['description'] = ! empty( $data['description'] ) ? wp_strip_all_tags( (string) $data['description'] ) : null;

			// url — maps to post_permalink by default.
			if ( ! empty( $data['url'] ) ) {
				$schema['url'] = esc_url( (string) $data['url'] );
			}

			// sameAs — canonical dataset page, e.g. in a public repository.
			if ( ! empty( $data['same-as'] ) ) {
				$schema['sameAs'] = esc_url( (string) $data['same-as'] );
			}

			// identifier — DOI or Compact Identifier.
			$schema['identifier'] = ! empty( $data['identifier'] ) ? wp_strip_all_tags( (string) $data['identifier'] ) : null;

			// license — Google expects a URL to a known license.
			if ( ! empty( $data['license'] ) ) {
				$schema['license'] = esc_url( (string) $data['license'] );
			}

			// keywords — comma-separated string.
			$schema['keywords'] = ! empty( $data['keywords'] ) ? wp_strip_all_tags( (string) $data['keywords'] ) : null;

			// version.
			$schema['version'] = ! empty( $data['version'] ) ? wp_strip_all_tags( (string) $data['version'] ) : null;

			// creator — Person node (maps to author_name + author_url by default).
			if ( ! empty( $data['creator'] ) ) {
				$schema['creator']['@type'] = 'Person';
				$schema['creator']['name']  = wp_strip_all_tags( (string) $data['creator'] );
				if ( ! empty( $data['creator-url'] ) ) {
					$schema['creator']['url'] = esc_url( (string) $data['creator-url'] );
				}
			}

			// temporalCoverage — ISO 8601 date, date range, or open-ended range.
			$schema['temporalCoverage'] = ! empty( $data['temporal-coverage'] ) ? wp_strip_all_tags( (string) $data['temporal-coverage'] ) : null;

			// spatialCoverage — a named place is valid per Google's guidelines.
			$schema['spatialCoverage'] = ! empty( $data['spatial-coverage'] ) ? wp_strip_all_tags( (string) $data['spatial-coverage'] ) : null;

			// distribution — DataDownload entries; rows without a download URL are skipped.
			if ( isset( $data['distribution'] ) && is_array( $data['distribution'] ) ) {
				foreach ( $data['distribution'] as $value ) {
					if ( ! isset( $value['download-url'] ) || empty( $value['download-url'] ) ) {
						continue;
					}
					$download = array(
						'@type'      => 'DataDownload',
						'contentUrl' => esc_url( (string) $value['download-url'] ),
					);
					if ( ! empty( $value['file-format'] ) ) {
						$download['encodingFormat'] = wp_strip_all_tags( (string) $value['file-format'] );
					}
					$schema['distribution'][] = $download;
				}
			}

			return apply_filters( 'wp_schema_pro_schema_dataset', $schema, $data, $post );
		}

	}
}
