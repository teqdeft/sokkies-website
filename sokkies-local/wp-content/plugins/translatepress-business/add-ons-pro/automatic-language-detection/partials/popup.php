<?php
if ( !defined('ABSPATH' ) )
    exit();
?>
<div class="trp_model_container" id="trp_ald_modal_container" style="<?php echo esc_attr( 'display: none; ' . $this->get_popup_css_variables() ); ?>" data-no-dynamic-translation data-no-translation>
    <?php
    $trp                = TRP_Translate_Press::get_trp_instance();
    $trp_settings       = $trp->get_component( 'settings' );
    $settings           = $trp_settings->get_settings();
    $this->trp_languages = $trp->get_component('languages');
    $languages_to_display = $this->settings['publish-languages'];
    $published_languages = $this->trp_languages->get_language_names( $languages_to_display );
    $trp_language_switcher = $trp->get_component('language_switcher');
    $language_cookie_data = $this->get_language_cookie_data();
    $shortcode_settings = $this->get_ald_language_switcher_shortcode_settings( $language_cookie_data );
    ?>
    <div class="trp_ald_modal trp-ald-custom-popup <?php echo esc_attr( $this->get_popup_animation_class() ); ?>" id="trp_ald_modal_popup">
        <div id="trp_ald_popup_text">
            <?php echo wp_kses_post( $language_cookie_data['popup_textarea'] ); ?>
        </div>

        <div class="trp_ald_select_and_button">
            <div class="trp_ald_ls_container">
                <?php
                $current_language_preference = $trp_language_switcher->add_shortcode_preferences($shortcode_settings, $settings['default-language'], $published_languages[$settings['default-language']]);
                ?>
                <div class="trp-shortcode-switcher__wrapper" role="group" data-open-mode="hover">
                    <div class="trp-language-switcher trp-language-switcher-container trp-ls-dropdown trp-shortcode-switcher trp-shortcode-anchor trp-open-on-hover trp-ald-language-selector" aria-hidden="true" inert data-no-translation>
                        <div class="trp-current-language-item__wrapper">
                            <div class="trp-ls-shortcode-current-language trp-language-item trp-language-item__default trp-language-item__current">
                                <?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
                            </div>
                            <svg class="trp-shortcode-arrow" width="20" height="20" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                <path d="M5 8L10 13L15 8" stroke="var(--text)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>

                    <div class="trp-language-switcher trp-language-switcher-container trp-ls-dropdown trp-shortcode-switcher trp-shortcode-overlay trp-open-on-hover trp-ald-language-selector"  id="trp_ald_popup_select_container" role="navigation" aria-label="<?php echo esc_attr__( 'Website language selector', 'translatepress-multilingual' ); ?>" data-no-translation <?php echo ( isset( $_GET['trp-edit-translation'] ) && $_GET['trp-edit-translation'] == 'preview' ) ? 'data-trp-unpreviewable="trp-unpreviewable"' : '' ?>>

		                <div class="trp-current-language-item__wrapper">
			                    <div class="trp-ls-shortcode-current-language trp-language-item trp-language-item__default trp-language-item__current" role="button" tabindex="0" aria-expanded="false" aria-label="<?php echo esc_attr__( 'Change language', 'translatepress-multilingual' ); ?>" aria-controls="trp_ald_popup_language_list" special-selector="trp_ald_popup_current_language" data-trp-ald-selected-language= "<?php echo esc_attr($settings["default-language"]); ?>">
			                        <?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
	                    </div>
	                    <svg class="trp-shortcode-arrow" width="20" height="20" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
	                        <path d="M5 8L10 13L15 8" stroke="var(--text)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
		                    </svg>
		                </div>
		                <div class="trp-ls-shortcode-language trp-switcher-dropdown-list" id="trp_ald_popup_language_list" role="listbox" aria-label="<?php echo esc_attr__( 'Available languages', 'translatepress-multilingual' ); ?>" hidden inert>
		                    <div class="trp-ald-popup-select trp-language-item trp-dropdown-item" id="<?php echo esc_attr($settings["default-language"]); ?>" role="option" tabindex="0" aria-selected="true" data-trp-ald-selected-language="<?php echo esc_attr($settings['default-language']); ?>">
		                        <?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
		                    </div>
	                    <?php foreach ( $published_languages as $code => $name ){
	                        if ($code != $settings['default-language']){
	                            $language_preference = $trp_language_switcher->add_shortcode_preferences($shortcode_settings, $code, $name);
                            ?>
	                            <div class="trp-ald-popup-select trp-language-item trp-dropdown-item" id="<?php echo esc_attr($code); ?>" role="option" tabindex="0" aria-selected="false" data-trp-ald-selected-language = "<?php echo esc_attr($code); ?>">
	                                <?php echo $language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
	                            </div>
                        <?php } ?>
	                    <?php } ?>
	                </div>
	            </div>
                </div>
            </div>


            <div class="trp_ald_button">
                <a href="<?php echo esc_url( $language_cookie_data['abs_home'] ); ?>"
                   id="trp_ald_popup_change_language">
                    <?php echo esc_html( $language_cookie_data['popup_textarea_change_button'] ); ?>
                </a>
            </div>
         </div>
        <a id="trp_ald_x_button_and_textarea" href="#">
            <span id="trp_ald_x_button" title="<?php echo esc_attr( $language_cookie_data['popup_textarea_close_button'] ); ?>"></span>
            <span id="trp_ald_x_button_textarea" title="<?php echo esc_attr( $language_cookie_data['popup_textarea_close_button'] ); ?>">
                <?php echo esc_html( $language_cookie_data['popup_textarea_close_button'] ); ?>
            </span>
        </a>
    </div>
</div>
