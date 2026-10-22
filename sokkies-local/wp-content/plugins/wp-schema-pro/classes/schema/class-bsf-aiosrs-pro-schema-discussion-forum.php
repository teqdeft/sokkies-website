<?php
/**
 * DiscussionForumPosting Schema.
 *
 * @package Schema Pro
 * @since 2.12.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BSF_AIOSRS_Pro_Schema_Discussion_Forum' ) ) {

	/**
	 * AIOSRS Schemas Initialization
	 *
	 * @since 2.12.0
	 */
	class BSF_AIOSRS_Pro_Schema_Discussion_Forum {

		/**
		 * Render Schema.
		 *
		 * @param  array<string, mixed> $data Meta Data.
		 * @param  array<string, mixed> $post Current Post Array.
		 * @return array<string, mixed>
		 *
		 * Note: The wp_schema_pro_schema_discussion_forum filter exposes the
		 * assembled schema array to third-party code. All string values added via
		 * this filter will be output inside a <script type="application/ld+json">
		 * block using JSON_UNESCAPED_SLASHES. Filter callbacks MUST sanitize any
		 * values they inject (e.g. wp_strip_all_tags, esc_url) to prevent
		 * </script> injection.
		 */
		public static function render( array $data, array $post ): array {
			$schema = array();

			$schema['@context'] = 'https://schema.org';
			$schema['@type']    = 'DiscussionForumPosting';

			// headline — maps to post_title by default.
			$schema['headline'] = ! empty( $data['name'] ) ? wp_strip_all_tags( (string) $data['name'] ) : null;

			// text (required) — maps to post_content by default.
			$schema['text'] = ! empty( $data['description'] ) ? wp_strip_all_tags( (string) $data['description'] ) : null;

			// url (required) — maps to post_permalink by default.
			if ( ! empty( $data['url'] ) ) {
				$schema['url'] = esc_url( (string) $data['url'] );
			}

			// datePublished (required) — maps to post_date by default.
			$schema['datePublished'] = ! empty( $data['published-date'] ) ? wp_strip_all_tags( (string) $data['published-date'] ) : null;

			// dateModified — maps to post_modified by default.
			$schema['dateModified'] = ! empty( $data['modified-date'] ) ? wp_strip_all_tags( (string) $data['modified-date'] ) : null;

			// author (required) — Person node (maps to author_name + author_url).
			if ( ! empty( $data['author'] ) ) {
				$schema['author']['@type'] = 'Person';
				$schema['author']['name']  = wp_strip_all_tags( (string) $data['author'] );
				if ( ! empty( $data['author-url'] ) ) {
					$schema['author']['url'] = esc_url( (string) $data['author-url'] );
				}
			}

			// image — maps to featured_img by default.
			if ( ! empty( $data['image'] ) && is_array( $data['image'] ) ) {
				$schema['image'] = BSF_AIOSRS_Pro_Schema_Template::get_image_schema( $data['image'] );
			}

			// interactionStatistic — comment/reply count as an InteractionCounter.
			// A count of 0 is valid, so check for a numeric value instead of empty().
			if ( isset( $data['comment-count'] ) && is_numeric( $data['comment-count'] ) ) {
				$schema['interactionStatistic'] = array(
					'@type'                => 'InteractionCounter',
					'interactionType'      => 'https://schema.org/CommentAction',
					'userInteractionCount' => (int) $data['comment-count'],
				);
			}

			return apply_filters( 'wp_schema_pro_schema_discussion_forum', $schema, $data, $post );
		}

	}
}
