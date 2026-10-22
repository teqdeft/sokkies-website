<?php
/**
 * Schemas Template.
 *
 * @package Schema Pro
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'BSF_AIOSRS_Pro_Schema_Video_Object' ) ) {

	/**
	 * AIOSRS Schemas Initialization
	 *
	 * @since 1.0.0
	 */
	class BSF_AIOSRS_Pro_Schema_Video_Object {

		/**
		 * Render Schema.
		 *
		 * @param  array<string, mixed> $data Meta Data.
		 * @param  array<string, mixed> $post Current Post Array.
		 * @return array<string, mixed>
		 */
		public static function render( array $data, array $post ): array {
			$schema = array();

			$schema['@context'] = 'https://schema.org';
			$schema['@type']    = 'VideoObject';

			$schema['name'] = ! empty( $data['name'] ) && is_string( $data['name'] ) ? wp_strip_all_tags( $data['name'] ) : null;

			$schema['description'] = ! empty( $data['description'] ) && is_string( $data['description'] ) ? wp_strip_all_tags( $data['description'] ) : null;

			if ( isset( $data['orgnization-name'] ) && is_string( $data['orgnization-name'] ) && ! empty( $data['orgnization-name'] ) ) {
				$schema['publisher']['@type'] = 'Organization';
				$schema['publisher']['name']  = wp_strip_all_tags( $data['orgnization-name'] );
			}

			if ( isset( $data['site-logo'] ) && is_array( $data['site-logo'] ) && ! empty( $data['site-logo'] ) ) {
				$schema['publisher']['@type'] = 'Organization';
				$schema['publisher']['logo']  = BSF_AIOSRS_Pro_Schema_Template::get_image_schema( $data['site-logo'], 'ImageObject' );
			}

			if ( isset( $data['image'] ) && is_array( $data['image'] ) && ! empty( $data['image'] ) ) {
				$schema['thumbnailUrl'] = BSF_AIOSRS_Pro_Schema_Template::get_image_schema( $data['image'], 'URL' );
			}

			$schema['uploadDate'] = ! empty( $data['upload-date'] ) && is_string( $data['upload-date'] ) ? wp_strip_all_tags( $data['upload-date'] ) : null;

			$schema['duration'] = ! empty( $data['duration'] ) && is_string( $data['duration'] ) ? wp_strip_all_tags( $data['duration'] ) : null;

			if ( isset( $data['content-url'] ) && is_string( $data['content-url'] ) && ! empty( $data['content-url'] ) ) {
				$schema['contentUrl'] = esc_url( $data['content-url'] );
			}

			if ( isset( $data['embed-url'] ) && is_string( $data['embed-url'] ) && ! empty( $data['embed-url'] ) ) {
				$schema['embedUrl'] = esc_url( $data['embed-url'] );
			}

			$schema['expires'] = ! empty( $data['expires-date'] ) && is_string( $data['expires-date'] ) ? wp_strip_all_tags( $data['expires-date'] ) : null;

			if ( isset( $data['interaction-count'] ) && is_string( $data['interaction-count'] ) && ! empty( $data['interaction-count'] ) ) {
				$schema['interactionStatistic']['@type']                    = 'InteractionCounter';
				$schema['interactionStatistic']['interactionType']['@type'] = 'WatchAction';
				$schema['interactionStatistic']['userInteractionCount']     = wp_strip_all_tags( $data['interaction-count'] );
			}

			if ( isset( $data['thumbnail-url'] ) && is_string( $data['thumbnail-url'] ) && ! empty( $data['thumbnail-url'] ) ) {
				$schema['thumbnailUrl'] = esc_url( $data['thumbnail-url'] );
			}

			if ( isset( $data['clip'] ) && is_array( $data['clip'] ) && ! empty( $data['clip'] ) ) {
				$clip_index = 0;
				foreach ( $data['clip'] as $value ) {
					if ( ! is_array( $value ) ) {
						continue;
					}

					$clip_name         = isset( $value['clip-name'] ) && is_string( $value['clip-name'] ) && '' !== $value['clip-name'] ? wp_strip_all_tags( $value['clip-name'] ) : '';
					$clip_url          = isset( $value['clip-url'] ) && is_string( $value['clip-url'] ) && '' !== $value['clip-url'] ? esc_url( $value['clip-url'] ) : '';
					$clip_start_offset = isset( $value['clip-start-offset'] ) && is_string( $value['clip-start-offset'] ) && '' !== $value['clip-start-offset'] ? wp_strip_all_tags( $value['clip-start-offset'] ) : '';
					$clip_end_offset   = isset( $value['clip-end-offset'] ) && is_string( $value['clip-end-offset'] ) && '' !== $value['clip-end-offset'] ? wp_strip_all_tags( $value['clip-end-offset'] ) : '';

					// Skip clips that have no meaningful data.
					if ( empty( $clip_name ) && empty( $clip_url ) ) {
						continue;
					}

					$clip_data          = array();
					$clip_data['@type'] = 'Clip';
					$clip_data['name']  = $clip_name;

					if ( ! empty( $clip_start_offset ) ) {
						$clip_data['startOffset'] = $clip_start_offset;
					}
					if ( ! empty( $clip_end_offset ) ) {
						$clip_data['endOffset'] = $clip_end_offset;
					}
					if ( ! empty( $clip_url ) ) {
						$clip_data['url'] = $clip_url;
					}

					$schema['hasPart'][ $clip_index ] = $clip_data;
					++$clip_index;
				}
			}

			if ( isset( $data['seekto-action-start-offset'] ) && is_string( $data['seekto-action-start-offset'] ) && ! empty( $data['seekto-action-start-offset'] ) && isset( $data['seekto-action-target'] ) && is_string( $data['seekto-action-target'] ) && ! empty( $data['seekto-action-target'] ) ) {
				$schema['potentialAction']['@type']             = 'SeekToAction';
				$schema['potentialAction']['target']            = esc_url( $data['seekto-action-target'] ) . '?t={seek_to_second_number}';
				$schema['potentialAction']['startOffset-input'] = 'required name=seek_to_second_number';
			}

			if ( isset( $data['regions-allowed'] ) && is_string( $data['regions-allowed'] ) && ! empty( $data['regions-allowed'] ) ) {
				$schema['regionsAllowed'] = wp_strip_all_tags( $data['regions-allowed'] );
			}

			if ( isset( $data['is-live-broadcast'] ) && ! empty( $data['is-live-broadcast'] ) && 'false' !== $data['is-live-broadcast'] ) {
				$schema['publication']['@type']           = 'BroadcastEvent';
				$schema['publication']['isLiveBroadcast'] = true;
				$schema['publication']['startDate']       = isset( $data['start-date'] ) && is_string( $data['start-date'] ) ? wp_strip_all_tags( $data['start-date'] ) : null;
				$schema['publication']['endDate']         = isset( $data['end-date'] ) && is_string( $data['end-date'] ) ? wp_strip_all_tags( $data['end-date'] ) : null;
			}

			return apply_filters( 'wp_schema_pro_schema_video_object', $schema, $data, $post );
		}

	}
}


