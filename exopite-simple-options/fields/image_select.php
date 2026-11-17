<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Image Select
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_image_select' ) ) {

	/**
	 * Class Exopite_Simple_Options_Framework_Field_image_select.
	 */
	class Exopite_Simple_Options_Framework_Field_image_select extends Exopite_Simple_Options_Framework_Fields {

		/**
		 * Constructor.
		 */
		public function __construct( $field, $value = '', $unique = '', $config = array() ) {
			parent::__construct( $field, $value, $unique, $config );
		}

		/**
		 * Render the field output.
		 */
		public function output() {

			$input_type = ( ! empty( $this->field['radio'] ) ) ? 'radio' : 'checkbox';
			$input_attr = ( 'checkbox' == $input_type ) ? '[]' : '';
			$layout     = ( isset( $this->field['layout'] ) && 'vertical' == $this->field['layout'] ) ? 'exopite-sof-field-image-selector-vertical' : 'exopite-sof-field-image-selector-horizontal';

			echo $this->element_before();
			echo '<div class="exopite-sof-field-image-selector ' . esc_attr( $layout ) . '">';

			if ( isset( $this->field['options'] ) ) {
				$options = $this->field['options'];
				foreach ( $options as $key => $value ) {
					echo '<label><input type="' . esc_attr( $input_type ) . '" name="' . $this->element_name( $input_attr ) . '" value="' . esc_attr( $key ) . '"' . $this->element_class() . $this->element_attributes( $key ) . $this->checked( $this->element_value(), $key ) . '/>';
					echo ( ! empty( $this->field['text_select'] ) ) ? '<span class="exopite-sof-' . esc_attr( sanitize_title( $value ) ) . '">' . wp_kses_post( $value ) . '</span>' : '<img src="' . esc_url( $value ) . '" alt="' . esc_attr( $key ) . '" />';
					echo '</label>';
				}
			}

			echo '</div>';
			echo $this->element_after();
		}
	}

}
