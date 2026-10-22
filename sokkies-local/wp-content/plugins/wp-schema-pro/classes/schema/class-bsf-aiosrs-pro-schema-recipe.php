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
if ( ! class_exists( 'BSF_AIOSRS_Pro_Schema_Recipe' ) ) {

	/**
	 * AIOSRS Schemas Initialization
	 *
	 * @since 1.0.0
	 */

	class BSF_AIOSRS_Pro_Schema_Recipe {
		
		/**
		 * Render Schema.
		 *
		 * @param  array<string, mixed> $data Meta Data.
		 * @param  array<string, mixed> $post Current Post Array.
		 * @return array<string, mixed>
		 */
		public static function render( array $data, array $post ): array {
			// Get timezone string from WordPress settings.
			$timezone_string = get_option( 'timezone_string' );

			// Validate timezone string.
			
			if ( is_string( $timezone_string ) && ! empty( $timezone_string ) && in_array( $timezone_string, timezone_identifiers_list(), true ) ) {
				// WordPress calculates offsets from UTC.
				// phpcs:ignore WordPress.DateTime.RestrictedFunctions.timezone_change_date_default_timezone_set
				date_default_timezone_set( $timezone_string );
			} else {
				// If the timezone string is empty or invalid, set a fallback timezone.
				date_default_timezone_set( 'UTC' );
				$timezone_string = 'UTC'; // Ensure $timezone_string is set to a valid timezone for later use.
			}

			$schema = array();

			$schema['@context'] = 'https://schema.org';
			$schema['@type']    = 'Recipe';

			$schema['name'] = isset( $data['name'] ) && is_string( $data['name'] ) ? wp_strip_all_tags( $data['name'] ) : null;

			if ( isset( $data['image'] ) && is_array( $data['image'] ) ) {
				$schema['image'] = BSF_AIOSRS_Pro_Schema_Template::get_image_schema( $data['image'] );
			}

			if ( isset( $data['reviewer-type'] ) && is_string( $data['reviewer-type'] ) ) {
				$schema['author']['@type'] = wp_strip_all_tags( $data['reviewer-type'] );
			} else {
				$schema['author']['@type'] = 'Person';
			}

			$schema['author']['name'] = isset( $data['author'] ) && is_string( $data['author'] ) ? wp_strip_all_tags( $data['author'] ) : null;

			$schema['description'] = isset( $data['description'] ) && is_string( $data['description'] ) ? wp_strip_all_tags( $data['description'] ) : null;

			$schema['prepTime'] = isset( $data['preperation-time'] ) && is_string( $data['preperation-time'] ) ? wp_strip_all_tags( $data['preperation-time'] ) : null;

			$schema['cookTime'] = isset( $data['cook-time'] ) && is_string( $data['cook-time'] ) ? wp_strip_all_tags( $data['cook-time'] ) : null;

			if ( isset( $data['recipe-yield'] ) && is_string( $data['recipe-yield'] ) ) {
				$schema['recipeYield'] = esc_html( $data['recipe-yield'] );
			}

			$schema['keywords'] = isset( $data['recipe-keywords'] ) && is_string( $data['recipe-keywords'] ) ? wp_strip_all_tags( $data['recipe-keywords'] ) : null;

			$schema['recipeCategory'] = isset( $data['recipe-category'] ) && is_string( $data['recipe-category'] ) ? wp_strip_all_tags( $data['recipe-category'] ) : null;

			$schema['recipeCuisine'] = isset( $data['recipe-cuisine'] ) && is_string( $data['recipe-cuisine'] ) ? wp_strip_all_tags( $data['recipe-cuisine'] ) : null;

			// Only add aggregateRating if rating or review-count have actual values (not empty, not 'none').
			$rating_value      = isset( $data['rating'] ) && is_string( $data['rating'] ) ? wp_strip_all_tags( $data['rating'] ) : '';
			$review_count      = isset( $data['review-count'] ) && is_string( $data['review-count'] ) ? wp_strip_all_tags( $data['review-count'] ) : '';

			// Check if either value is non-empty and not 'none' (case-insensitive).
			$has_rating = ! empty( $rating_value ) && 'none' !== strtolower( $rating_value );
			$has_review_count = ! empty( $review_count ) && 'none' !== strtolower( $review_count );

			if ( $has_rating || $has_review_count ) {
				$schema['aggregateRating']['@type'] = 'AggregateRating';

				if ( $has_rating ) {
					$schema['aggregateRating']['ratingValue'] = $rating_value;
				}
				if ( $has_review_count ) {
					$schema['aggregateRating']['reviewCount'] = $review_count;
				}
			}

			// Only add nutrition if it has an actual value (not empty, not 'none').
			if ( isset( $data['nutrition'] ) && is_string( $data['nutrition'] ) ) {
				$nutrition = wp_strip_all_tags( $data['nutrition'] );
				if ( ! empty( $nutrition ) && 'none' !== strtolower( $nutrition ) ) {
					$schema['nutrition']['@type']    = 'NutritionInformation';
					$schema['nutrition']['calories'] = $nutrition;
				}
			}

			if ( isset( $data['ingredients'] ) && is_string( $data['ingredients'] ) ) {
				$recipe_ingredients = explode( ',', $data['ingredients'] );
				foreach ( $recipe_ingredients as $key => $value ) {
					$schema['recipeIngredient'][ $key ] = wp_strip_all_tags( $value );
				}
			}

			if ( isset( $data['recipe-instructions'] ) && is_array( $data['recipe-instructions'] ) ) {
				foreach ( $data['recipe-instructions'] as $key => $value ) {
					if ( isset( $value['steps'] ) && is_string( $value['steps'] ) ) {
						$schema['recipeInstructions'][ $key ]['@type'] = 'HowToStep';
						$schema['recipeInstructions'][ $key ]['text']  = wp_strip_all_tags( $value['steps'] );

						// Only add name if it has a value.
						if ( isset( $value['name'] ) && is_string( $value['name'] ) ) {
							$step_name = wp_strip_all_tags( $value['name'] );
							if ( ! empty( $step_name ) && 'none' !== strtolower( $step_name ) ) {
								$schema['recipeInstructions'][ $key ]['name'] = $step_name;
							}
						}

						// Only add url if it has a value.
						if ( isset( $value['url'] ) && is_string( $value['url'] ) ) {
							$step_url = wp_strip_all_tags( $value['url'] );
							if ( ! empty( $step_url ) && 'none' !== strtolower( $step_url ) ) {
								$schema['recipeInstructions'][ $key ]['url'] = $step_url;
							}
						}

						if ( isset( $value['image'] ) && is_array( $value['image'] ) ) {
							$schema['recipeInstructions'][ $key ]['image'] = BSF_AIOSRS_Pro_Schema_Template::get_image_schema( $value['image'], 'URL' );
						}
					}
				}
			}

			if ( isset( $data['recipe-video'] ) && is_array( $data['recipe-video'] ) ) {
				foreach ( $data['recipe-video'] as $key => $value ) {
					// Only add video if video-name has an actual value (not empty, not 'none').
					$video_name = isset( $value['video-name'] ) && is_string( $value['video-name'] ) ? wp_strip_all_tags( $value['video-name'] ) : '';

					if ( ! empty( $video_name ) && 'none' !== strtolower( $video_name ) ) {
						$schema['video'][ $key ]['@type'] = 'VideoObject';
						$schema['video'][ $key ]['name']  = $video_name;

						// Only add description if it has a value.
						$video_desc = isset( $value['video-desc'] ) && is_string( $value['video-desc'] ) ? wp_strip_all_tags( $value['video-desc'] ) : '';
						if ( ! empty( $video_desc ) && 'none' !== strtolower( $video_desc ) ) {
							$schema['video'][ $key ]['description'] = $video_desc;
						}

						if ( isset( $value['video-image'] ) && is_array( $value['video-image'] ) ) {
							$schema['video'][ $key ]['thumbnailUrl'] = BSF_AIOSRS_Pro_Schema_Template::get_image_schema( $value['video-image'], 'URL' );
						}

						// Only add contentUrl if it has a value.
						if ( isset( $value['recipe-video-content-url'] ) && is_string( $value['recipe-video-content-url'] ) ) {
							$content_url = esc_url( $value['recipe-video-content-url'] );
							if ( ! empty( $content_url ) && 'none' !== strtolower( $value['recipe-video-content-url'] ) ) {
								$schema['video'][ $key ]['contentUrl'] = $content_url;
							}
						}

						// Only add embedUrl if it has a value.
						if ( isset( $value['recipe-video-embed-url'] ) && is_string( $value['recipe-video-embed-url'] ) ) {
							$embed_url = esc_url( $value['recipe-video-embed-url'] );
							if ( ! empty( $embed_url ) && 'none' !== strtolower( $value['recipe-video-embed-url'] ) ) {
								$schema['video'][ $key ]['embedUrl'] = $embed_url;
							}
						}

						// Only add duration if it has a value.
						$duration = isset( $value['recipe-video-duration'] ) && is_string( $value['recipe-video-duration'] ) ? wp_strip_all_tags( $value['recipe-video-duration'] ) : '';
						if ( ! empty( $duration ) && 'none' !== strtolower( $duration ) ) {
							$schema['video'][ $key ]['duration'] = $duration;
						}

						// Convert uploadDate to DateTime object and set the timezone.
						if ( isset( $value['recipe-video-upload-date'] ) && is_string( $value['recipe-video-upload-date'] ) ) {
							$upload_date_str = wp_strip_all_tags( $value['recipe-video-upload-date'] );
							if ( ! empty( $upload_date_str ) && 'none' !== strtolower( $upload_date_str ) ) {
								$upload_date = new DateTime( $upload_date_str );
								$upload_date->setTimezone( new DateTimeZone( is_string( $timezone_string ) ? $timezone_string : 'UTC' ) );
								$schema['video'][ $key ]['uploadDate'] = $upload_date->format( 'c' );
							}
						}

						// Use DateTime to handle timezone for 'expires'
						if ( isset( $value['recipe-video-expires-date'] ) && is_string( $value['recipe-video-expires-date'] ) ) {
							$expires_date_str = wp_strip_all_tags( $value['recipe-video-expires-date'] );
							if ( ! empty( $expires_date_str ) && 'none' !== strtolower( $expires_date_str ) ) {
								$expires_date = new DateTime( $expires_date_str );
								$expires_date->setTimezone( new DateTimeZone( is_string( $timezone_string ) ? $timezone_string : 'UTC' ) );
								$schema['video'][ $key ]['expires'] = $expires_date->format( 'c' );
							}
						}

						// Only add interactionCount if it has a value.
						$interaction_count = isset( $value['recipe-video-interaction-count'] ) && is_string( $value['recipe-video-interaction-count'] ) ? wp_strip_all_tags( $value['recipe-video-interaction-count'] ) : '';
						if ( ! empty( $interaction_count ) && 'none' !== strtolower( $interaction_count ) ) {
							$schema['video'][ $key ]['interactionCount'] = $interaction_count;
						}
					}
				}
			}

			return apply_filters( 'wp_schema_pro_schema_recipe', $schema, $data, $post );
		}
	}
}
