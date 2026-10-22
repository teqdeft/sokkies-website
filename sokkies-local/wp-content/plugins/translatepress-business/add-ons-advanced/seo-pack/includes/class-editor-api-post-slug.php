<?php


// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();

class TRP_IN_Editor_Api_Post_Slug {

	/* @var TRP_Query */
	protected $trp_query;
	/* @var TRP_IN_SP_Slug_Manager */
	protected $slug_manager;
	/* @var TRP_Translation_Manager */
	protected $translation_manager;
	/* @var TRP_Url_Converter */
	protected $url_converter;
    protected $settings;

    /* @var TRP_Slug_Query */
    protected $slug_query;

    /* @var TRP_IN_SP_Editor_Actions */
    protected $editor_actions;

	/**
	 * TRP_Translation_Manager constructor.
	 *
	 * @param array $settings Settings option.
	 */
	public function __construct( $settings, $slug_manager ) {
		$this->settings = $settings;
		$this->slug_manager = $slug_manager;
        $this->slug_query = new TRP_Slug_Query();
        $this->editor_actions = new TRP_IN_SP_Editor_Actions( $this->slug_query, $settings );
	}

	/**
	 * Returns translations of slugs
	 *
	 * Hooked to wp_ajax_trp_get_translations_postslug
	 */
	public function postslug_get_translations() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			check_ajax_referer( 'postslug_get_translations', 'security' );
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'trp_get_translations_postslug' && ! empty( $_POST['language'] ) && in_array( $_POST['language'], $this->settings['translation-languages'] ) ) {
				$ids = (empty($_POST['string_ids']) )? array() : json_decode(stripslashes($_POST['string_ids']));/* phpcs:ignore */ /* sanitized downstream */
				if ( is_array( $ids )){
					$trp = TRP_Translate_Press::get_trp_instance();
					if (!$this->translation_manager) {
						$this->translation_manager = $trp->get_component('translation_manager');
					}
					$localized_text = $this->translation_manager->string_groups();
					$id_array = array();
					$dictionaries = array();

					foreach ( $ids as $id ) {
						if ( isset( $id ) && is_numeric( $id ) ) {
							$id_array[] = (int) $id;
						}
					}

                    /*
                     * Automatic slug translation on the front-end only covers the language the page is
                     * currently rendered in, so opening the editor would leave every other language
                     * untranslated. Fill in the missing ones before reading the translations below.
                     */
                    $original_slugs = array();
                    foreach ( $id_array as $post_id ) {
                        $post_slug = get_post_field( 'post_name', $post_id );
                        if ( !empty( $post_slug ) ) {
                            $original_slugs[] = $post_slug;
                        }
                    }

                    $all_languages = isset( $_POST['all_languages'] ) && $_POST['all_languages'] === 'true'; /* phpcs:ignore */ /* nonce checked above */
                    $languages     = $all_languages ? $this->settings['translation-languages'] : array( sanitize_text_field( $_POST['language'] ) ); /* phpcs:ignore */ /* checked against existing languages above */

                    $this->machine_translate_missing_slugs( $original_slugs, $languages );

					foreach( $id_array as $post_id ) {
                        $original = get_post_field( 'post_name', $post_id );
                        $original_id_array = $this->slug_query->get_ids_from_original( (array) $original );

                        $original_id = $original_id_array[$original];

                        $translations = $this->slug_query->get_translated_slugs_from_original( (array) $original );

                        $entry = array(
                            'dbID'              => $post_id,
							'original_id'       => $original_id,
							'translationsArray' => array(),
							'type'              => 'postslug',
							'group'             => $localized_text['slugs'],
							'original'          => urldecode( $original )
						);

						foreach ( $this->settings['translation-languages'] as $language ) {
							if ( $language != $this->settings['default-language'] ) {
                                if ( isset( $translations[ $original_id ][ $language ] ) ){
                                    $translated = $translations[ $original_id ][ $language ]['translated'];
                                    $translation_id = $translations[$original_id][$language]['id'];
                                    $status = $translations[$original_id][$language]['status'];
                                }else {
                                    $translated = '';
                                    $translation_id = '';
                                    $status = 0;
                                }
								$entry['translationsArray'][$language] = array(
									'translation_id'    => $translation_id,
                                    'original_id'       => $original_id,
                                    'status'            => $status,
									'translated'        => urldecode( $translated ),
									'editedTranslation' => urldecode( $translated ),
								);
							}
						}
						$dictionaries[] = $entry;
					}
					echo trp_safe_json_encode( $dictionaries );//phpcs:ignore
				}
			}
		}
		wp_die();
	}

    /**
     * Machine translate post slugs for the languages that don't have a translation yet.
     *
     * On the front-end, slugs are only sent to automatic translation for the language the page is
     * being rendered in ( see TRP_IN_SP_Slug_Manager::include_slug_for_machine_translation() ).
     * The Translation Editor asks for all the languages at once, so without this the slug would
     * only be automatically translated for the language currently loaded in the preview.
     *
     * Mirrors what TRP_Editor_Api_Regular_Strings does for regular strings.
     *
     * @param array $original_slugs Original ( default language ) post slugs.
     * @param array $languages      Language codes to translate into.
     * @return void
     */
    protected function machine_translate_missing_slugs( $original_slugs, $languages ) {
        if ( empty( $original_slugs ) || empty( $languages ) || !apply_filters( 'trp_machine_translate_slug', false ) ) {
            return;
        }

        $trp                = TRP_Translate_Press::get_trp_instance();
        $translation_render = $trp->get_component( 'translation_render' );
        $trp_query          = $trp->get_component( 'query' );

        if ( !$translation_render || !$trp_query ) {
            return;
        }

        $original_slugs = array_values( array_unique( $original_slugs ) );

        foreach ( $languages as $language ) {
            if ( $language === $this->settings['default-language'] || !in_array( $language, $this->settings['translation-languages'] ) ) {
                continue;
            }

            $existing_translations = $this->slug_query->get_translated_slugs_from_original( $original_slugs, $language );
            if ( !is_array( $existing_translations ) ) {
                $existing_translations = array();
            }

            // keep only the slugs that don't have a translation in this language yet
            $missing_slugs = array();
            $strings       = array();
            foreach ( $original_slugs as $original_slug ) {
                // get_translated_slugs_from_original() normalizes the slugs it looks for, so do the same here
                $normalized_slug = strtolower( urlencode( urldecode( $original_slug ) ) );
                if ( !empty( $existing_translations[ $normalized_slug ] ) ) {
                    continue;
                }

                $trimmed_slug = trp_full_trim( $original_slug, array( 'numerals' => 'no' ) );
                if ( empty( $trimmed_slug ) ) {
                    continue;
                }

                // the automatic translation is done on the human readable form of the slug
                $missing_slugs[] = $original_slug;
                $strings[]       = urldecode( str_replace( '-', ' ', $trimmed_slug ) );
            }

            if ( empty( $strings ) ) {
                continue;
            }

            $translated_strings = $translation_render->process_strings( $strings, $language );
            if ( !is_array( $translated_strings ) ) {
                continue;
            }

            $slugs_for_insertion = array();
            foreach ( $missing_slugs as $key => $original_slug ) {
                if ( empty( $translated_strings[ $key ] ) ) {
                    continue;
                }

                $sanitized_slug = sanitize_title( $translated_strings[ $key ] );
                if ( empty( $sanitized_slug ) ) {
                    continue;
                }

                $sanitized_slug = str_replace( ' ', '-', $sanitized_slug );
                $sanitized_slug = apply_filters( 'trp_machine_translated_slug', $sanitized_slug, $original_slug, $language, $translated_strings[ $key ], 'post' );

                $slugs_for_insertion[ $original_slug ] = array(
                    'original'   => $original_slug,
                    'translated' => $sanitized_slug,
                    'status'     => $trp_query->get_constant_machine_translated(),
                    'type'       => 'post',
                );
            }

            if ( !empty( $slugs_for_insertion ) ) {
                $this->slug_query->insert_slugs( $slugs_for_insertion, $language );
            }
        }
    }

	/**
	 * Save translations of slugs
	 *
	 * Hooked to wp_ajax_trp_save_translations_postslug
	 */
	public function postslug_save_translations() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
			check_ajax_referer( 'postslug_save_translations', 'security' );
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'trp_save_translations_postslug' && !empty( $_POST['strings'] ) ) {
				$slugs = json_decode(stripslashes($_POST['strings'])); /* phpcs:ignore */ /* sanitized downstream */
				$update_slugs = array();

                if ( !empty( $slugs ) ){
                    $update_slugs = $this->editor_actions->save_slugs( $slugs, 'post' );
                }

			}
		}

		echo trp_safe_json_encode( $update_slugs );//phpcs:ignore
		wp_die();
	}

}