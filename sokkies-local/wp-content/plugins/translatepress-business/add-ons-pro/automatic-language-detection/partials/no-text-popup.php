<?php
if ( !defined('ABSPATH' ) )
    exit();
?>
<template id="trp_ald_no_text_popup_template">
    <div id="trp_no_text_popup_wrap" class="<?php echo esc_attr( $this->get_popup_animation_class() ); ?>" style="<?php echo esc_attr( $this->get_popup_css_variables() ); ?>">
        <div id="trp_no_text_popup" class="trp_ald_no_text_popup trp-ald-custom-popup" data-no-dynamic-translation data-no-translation>
            <?php
            $trp                   = TRP_Translate_Press::get_trp_instance();
            $trp_settings          = $trp->get_component( 'settings' );
            $settings              = $trp_settings->get_settings();
            $this->trp_languages   = $trp->get_component( 'languages' );
            $languages_to_display  = $this->settings['publish-languages'];
            $published_languages   = $this->trp_languages->get_language_names( $languages_to_display );
            $trp_language_switcher = $trp->get_component( 'language_switcher' );
            $language_cookie_data  = $this->get_language_cookie_data();
            $shortcode_settings    = $this->get_ald_language_switcher_shortcode_settings( $language_cookie_data );
            ?>

            <div id="trp_ald_not_text_popup_ls_and_button">
                <div id="trp_ald_no_text_popup_div">
                    <span id="trp_ald_no_text_popup_text">
                        <?php echo wp_kses_post( $language_cookie_data['popup_textarea'] ); ?>
                    </span>
                </div>
	                <div class="trp_ald_ls_container">
                    <?php
                    $current_language_preference = $trp_language_switcher->add_shortcode_preferences( $shortcode_settings, $settings['default-language'], $published_languages[ $settings['default-language'] ] );
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

                        <div class="trp-language-switcher trp-language-switcher-container trp-ls-dropdown trp-shortcode-switcher trp-shortcode-overlay trp-open-on-hover trp-ald-language-selector" id="trp_ald_no_text_select" role="navigation" aria-label="<?php echo esc_attr__( 'Website language selector', 'translatepress-multilingual' ); ?>"
                             data-no-translation <?php echo ( isset( $_GET['trp-edit-translation'] ) && $_GET['trp-edit-translation'] == 'preview' ) ? 'data-trp-unpreviewable="trp-unpreviewable"' : '' ?>>

	                        <div class="trp-current-language-item__wrapper">
		                            <div class="trp-ls-shortcode-current-language trp-language-item trp-language-item__default trp-language-item__current" role="button" tabindex="0" aria-expanded="false" aria-label="<?php echo esc_attr__( 'Change language', 'translatepress-multilingual' ); ?>" aria-controls="trp_ald_no_text_popup_select_container"
	                                 special-selector="trp_ald_popup_current_language" data-trp-ald-selected-language="<?php echo esc_attr( $settings["default-language"] ); ?>">
	                                <?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
	                            </div>
	                            <svg class="trp-shortcode-arrow" width="20" height="20" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
	                                <path d="M5 8L10 13L15 8" stroke="var(--text)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
	                            </svg>
		                        </div>
		                        <div class="trp-ls-shortcode-language trp-switcher-dropdown-list" id="trp_ald_no_text_popup_select_container" role="listbox" aria-label="<?php echo esc_attr__( 'Available languages', 'translatepress-multilingual' ); ?>" hidden inert>
		                            <div class="trp-ald-popup-select trp-language-item trp-dropdown-item" id="<?php echo esc_attr( $settings['default-language'] ) ?>"
		                                 role="option" tabindex="0" aria-selected="true" data-trp-ald-selected-language="<?php echo esc_attr( $settings['default-language'] ); ?>">
		                                <?php echo $current_language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>
		                            </div>
	                            <?php foreach ( $published_languages as $code => $name ) {
	                                if ($code != $settings['default-language']){
	                                    $language_preference = $trp_language_switcher->add_shortcode_preferences( $shortcode_settings, $code, $name );
                                    ?>
	                                    <div class="trp-ald-popup-select trp-language-item trp-dropdown-item" id="<?php echo esc_attr( $code ); ?>"
	                                         role="option" tabindex="0" aria-selected="false" data-trp-ald-selected-language="<?php echo esc_attr( $code ); ?>">
	                                        <?php
	                                        echo $language_preference; /* phpcs:ignore */ /* escaped inside the function that generates the output */ ?>

                                    </div>
                                <?php } ?>
	                            <?php } ?>
	                        </div>
	                    </div>
                    </div>
	                </div>
                <div class="trp_ald_change_language_div">
                    <a href="<?php echo esc_url( $language_cookie_data['abs_home'] ); ?>" id="trp_ald_no_text_popup_change_language">
                        <?php echo esc_html( $language_cookie_data['popup_textarea_change_button'] ); ?>
                    </a>
                </div>
                <button type="button" id="trp_ald_no_text_popup_x_button_and_textarea">
                    <span id="trp_ald_no_text_popup_x_button" aria-hidden="true"></span>
                    <span id="trp_ald_no_text_popup_x_button_textarea">
                        <?php echo esc_html( $language_cookie_data['popup_textarea_close_button'] ); ?>
                    </span>
                </button>
            </div>
            <div id="trp_ald_no_text_popup_x">
                <button id="trp_close"></button>
            </div>
        </div>
    </div>
</template>
