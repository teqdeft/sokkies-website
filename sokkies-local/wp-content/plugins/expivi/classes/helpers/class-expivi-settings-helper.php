<?php
/**
 * Expivi Settings Helper
 *
 * @package Expivi/Helpers
 */

defined( 'ABSPATH' ) || exit;

trait Expivi_Settings_Helper {

	/**
	 * Get Expivi settings input field
	 *
	 * @param string $field Expivi setting.
	 * @param string $type Input field type.
	 * @param string $setting_group_name Setting group name.
	 * @param null   $setting_default Expivi setting default.
	 * @return string|void
	 */
	public function get_form_field( string $field, string $type, string $setting_group_name, $setting_default = null ) {
		switch ( $type ) {
			case 'text':
				return $this->get_form_field_text_input( $field, $setting_group_name, $setting_default );
			case 'textarea':
				return $this->get_form_field_text_area( $field, $setting_group_name, $setting_default );
			case 'checkbox':
				return $this->get_form_field_checkbox_input( $field, $setting_group_name, $setting_default );
			case 'email':
				return $this->get_form_field_email_input( $field, $setting_group_name, $setting_default );
			case 'multi_email':
				return $this->get_form_field_multi_email_input( $field, $setting_group_name, $setting_default );
		}
	}

	/**
	 * Copies the plugin template to the currently used theme template.
	 * Example for template name: 'viewer/viewer.phtml'
	 *
	 * @param string $template_name Relative path to the template.
	 */
	public function copy_plugin_template_to_theme( string $template_name ): bool {
		$source_dir      = XPV()->fs->combine( XPV()->plugin_path(), 'templates' );
		$destination_dir = XPV()->fs->combine( xpv_get_theme_dir(), XPV()->template_path() );

		return XPV()->fs->copy( $template_name, $source_dir, $destination_dir, false );
	}

	/**
	 * Removes the theme template in the currently theme.
	 * Example for template name: 'viewer/viewer.phtml'
	 *
	 * @param string $template_name Relative path to the template.
	 */
	public function remove_plugin_template_in_theme( string $template_name ): bool {
		$fullpath  = XPV()->fs->combine( xpv_get_theme_dir(), XPV()->template_path(), $template_name );
		$filename  = basename( $fullpath );
		$theme_dir = dirname( $fullpath );

		return XPV()->fs->delete( $filename, $theme_dir );
	}

	/**
	 * Get Expivi settings checkbox input field
	 *
	 * @param string $field Expivi setting.
	 * @param string $setting_group_name Setting group name.
	 * @param null   $setting_default Expivi setting default.
	 * @return string
	 */
	private function get_form_field_checkbox_input( string $field, string $setting_group_name, $setting_default = null ): string {
		$checked = ! ! $this->get_setting( $field, $setting_default, $setting_group_name );
		return '<input type="checkbox" name="' . $setting_group_name . '[' . $field . ']" value="' . esc_attr( $field ) . '" ' . ( $checked ? 'checked' : '' ) . '/>';
	}

	/**
	 * Get Expivi settings text input field
	 *
	 * @param string $field Expivi setting.
	 * @param string $setting_group_name Setting group name.
	 * @param null   $setting_default Expivi setting default.
	 * @return string
	 */
	private function get_form_field_text_input( string $field, string $setting_group_name, $setting_default = null ): string {
		$value = $this->get_setting( $field, $setting_default, $setting_group_name );
		return '<input type="text" style="width:600px;" name="' . $setting_group_name . '[' . $field . ']"  value="' . esc_attr( $value ) . '">';
	}

	/**
	 * Get Expivi settings text area field
	 *
	 * @param string $field Expivi setting.
	 * @param string $setting_group_name Setting group name.
	 * @param null   $setting_default Expivi setting default.
	 * @return string
	 */
	private function get_form_field_text_area( string $field, string $setting_group_name, $setting_default = null ): string {
		$value = $this->get_setting( $field, $setting_default, $setting_group_name );
		return '<textarea style="width:600px;" rows="8" name="' . $setting_group_name . '[' . $field . ']">' . esc_attr( $value ) . '</textarea>';
	}

	/**
	 * Get Expivi settings email input field
	 *
	 * @param string $field Expivi setting.
	 * @param string $setting_group_name Setting group name.
	 * @param null   $setting_default Expivi setting default.
	 * @return string
	 */
	private function get_form_field_email_input( string $field, string $setting_group_name, $setting_default = null ): string {
		$value = $this->get_setting( $field, $setting_default, $setting_group_name );
		return '<input type="email" style="width:600px;" name="' . $setting_group_name . '[' . $field . ']"  value="' . esc_attr( $value ) . '">';
	}

	/**
	 * Get Expivi settings email input field
	 *
	 * @param string $field Expivi setting.
	 * @param string $setting_group_name Setting group name.
	 * @param null   $setting_default Expivi setting default.
	 * @return string
	 */
	private function get_form_field_multi_email_input( string $field, string $setting_group_name, $setting_default = null ): string {
		$value = $this->get_setting( $field, $setting_default, $setting_group_name );
		return '<input type="email" multiple style="width:600px;" name="' . $setting_group_name . '[' . $field . ']"  value="' . esc_attr( $value ) . '">';
	}
}
