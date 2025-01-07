<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Checkbox
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_checkbox' ) ) {

	class Exopite_Simple_Options_Framework_Field_checkbox extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $config = array() ) {

			parent::__construct( $field, $value, $unique, $config );

		}

		public function output() {
			echo $this->element_before();
			$label = ( isset( $this->field['label'] ) ) ? $this->field['label'] : '';
			$style = ( isset( $this->field['style'] ) ) ? $this->field['style'] : '';
            $checked_value = ( isset( $this->field['checked_value'] ) ) ? $this->field['checked_value'] : 'yes';

			switch ( $style ) {
				case 'fancy':
					echo '<label class="checkbox">';
					echo '<input type="checkbox" class="checkbox__input" name="' . $this->element_name() . '" value="'.$checked_value.'"' . $this->element_attributes() . checked( $this->element_value(), $checked_value, false ) . '>';
					echo '<div class="checkbox__checkmark"></div>';
					echo $label;
					echo '</label>';
					break;

				default:
					echo '<label><input type="checkbox" name="' . $this->element_name() . '" value="'.$checked_value.'"' . $this->element_class() . $this->element_attributes() . checked( $this->element_value(), $checked_value, false ) . '/> ' . $label . '</label>';
					break;
			}


			echo $this->element_after();

		}

	}

}
