<?php
/**
 * QAPage Schema.
 *
 * @package Schema Pro
 * @since 2.12.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BSF_AIOSRS_Pro_Schema_Qa_Page' ) ) {

	/**
	 * AIOSRS Schemas Initialization
	 *
	 * @since 2.12.0
	 */
	class BSF_AIOSRS_Pro_Schema_Qa_Page {

		/**
		 * Build a single Answer node from field values.
		 *
		 * @param  string $text Answer text.
		 * @param  mixed  $author Answer author name.
		 * @param  mixed  $upvote_count Answer upvote count.
		 * @param  mixed  $url Answer URL.
		 * @param  mixed  $date Answer published date.
		 * @return array<string, mixed>
		 */
		private static function build_answer( $text, $author = '', $upvote_count = '', $url = '', $date = '' ) {
			$answer = array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( (string) $text ),
			);

			if ( ! empty( $author ) ) {
				$answer['author'] = array(
					'@type' => 'Person',
					'name'  => wp_strip_all_tags( (string) $author ),
				);
			}

			// An upvote count of 0 is valid, so check for a numeric value instead of empty().
			if ( '' !== $upvote_count && is_numeric( $upvote_count ) ) {
				$answer['upvoteCount'] = (int) $upvote_count;
			}

			if ( ! empty( $url ) ) {
				$answer['url'] = esc_url( (string) $url );
			}

			if ( ! empty( $date ) ) {
				$answer['datePublished'] = wp_strip_all_tags( (string) $date );
			}

			return $answer;
		}

		/**
		 * Render Schema.
		 *
		 * @param  array<string, mixed> $data Meta Data.
		 * @param  array<string, mixed> $post Current Post Array.
		 * @return array<string, mixed>
		 *
		 * Note: The wp_schema_pro_schema_qa_page filter exposes the assembled
		 * schema array to third-party code. All string values added via this
		 * filter will be output inside a <script type="application/ld+json">
		 * block using JSON_UNESCAPED_SLASHES. Filter callbacks MUST sanitize any
		 * values they inject (e.g. wp_strip_all_tags, esc_url) to prevent
		 * </script> injection.
		 */
		public static function render( array $data, array $post ): array {
			$schema = array();

			$schema['@context'] = 'https://schema.org';
			$schema['@type']    = 'QAPage';

			// mainEntity — the Question this page is about (required by Google).
			$question = array( '@type' => 'Question' );

			// name (required) — maps to post_title by default.
			$question['name'] = ! empty( $data['question'] ) ? wp_strip_all_tags( (string) $data['question'] ) : null;

			// text — the full question body, maps to post_content by default.
			$question['text'] = ! empty( $data['question-text'] ) ? wp_strip_all_tags( (string) $data['question-text'] ) : null;

			// datePublished — maps to post_date by default.
			$question['datePublished'] = ! empty( $data['question-date'] ) ? wp_strip_all_tags( (string) $data['question-date'] ) : null;

			// author — Person node (maps to author_name by default).
			if ( ! empty( $data['question-author'] ) ) {
				$question['author'] = array(
					'@type' => 'Person',
					'name'  => wp_strip_all_tags( (string) $data['question-author'] ),
				);
			}

			// An upvote count of 0 is valid, so check for a numeric value instead of empty().
			if ( isset( $data['question-upvote-count'] ) && '' !== $data['question-upvote-count'] && is_numeric( $data['question-upvote-count'] ) ) {
				$question['upvoteCount'] = (int) $data['question-upvote-count'];
			}

			// answerCount (required) — computed from the answers actually output.
			$answer_count = 0;

			// acceptedAnswer — built from the accepted answer fields.
			if ( ! empty( $data['accepted-answer-text'] ) ) {
				$question['acceptedAnswer'] = self::build_answer(
					$data['accepted-answer-text'],
					isset( $data['accepted-answer-author'] ) ? $data['accepted-answer-author'] : '',
					isset( $data['accepted-answer-upvote-count'] ) ? $data['accepted-answer-upvote-count'] : '',
					isset( $data['accepted-answer-url'] ) ? $data['accepted-answer-url'] : '',
					isset( $data['accepted-answer-date'] ) ? $data['accepted-answer-date'] : ''
				);
				$answer_count++;
			}

			// suggestedAnswer — built from the repeater rows; rows without text are skipped.
			if ( isset( $data['suggested-answers'] ) && is_array( $data['suggested-answers'] ) ) {
				foreach ( $data['suggested-answers'] as $value ) {
					if ( ! isset( $value['answer-text'] ) || empty( $value['answer-text'] ) ) {
						continue;
					}
					$question['suggestedAnswer'][] = self::build_answer(
						$value['answer-text'],
						isset( $value['answer-author'] ) ? $value['answer-author'] : '',
						isset( $value['answer-upvote-count'] ) ? $value['answer-upvote-count'] : '',
						isset( $value['answer-url'] ) ? $value['answer-url'] : ''
					);
					$answer_count++;
				}
			}

			$question['answerCount'] = $answer_count;

			$schema['mainEntity'] = $question;

			return apply_filters( 'wp_schema_pro_schema_qa_page', $schema, $data, $post );
		}

	}
}
