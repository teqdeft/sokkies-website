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
if ( ! class_exists( 'BSF_AIOSRS_Pro_Schema_FAQ' ) ) {

	/**
	 * AIOSRS Schemas Initialization
	 *
	 * @since 1.0.0
	 */
	class BSF_AIOSRS_Pro_Schema_FAQ {

		/**
		 * Render Schema.
		 *
		 * @param  array $data Meta Data.
		 * @param  array $post Current Post Array.
		 * @return array
		 */
		public static function render( array $data, array $post ) {
			global $post; // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.VariableRedeclaration
			$schema = array();
			if ( isset( $data['question-answer'][0]['question'] ) && ! empty( $data['question-answer'][0]['question'] ) ) {

				$schema['@context']  = 'https://schema.org';
				$schema['@type']     = 'FAQPage';
				$schema['mainEntity'] = array();
				foreach ( $data['question-answer'] as $value ) {
					if ( ! isset( $value['question'] ) || empty( $value['question'] ) ) {
						continue;
					}
					$item = array(
						'@type' => 'Question',
						'name'  => $value['question'],
					);
					if ( isset( $value['answer'] ) && ! empty( $value['answer'] ) ) {
						$item['acceptedAnswer'] = array(
							'@type' => 'Answer',
							'text'  => $value['answer'],
						);
					}
					$schema['mainEntity'][] = $item;
				}
			}
			return apply_filters( 'wp_schema_pro_schema_faq', $schema, $data, $post );
		}

	}
}
