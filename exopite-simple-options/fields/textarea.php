<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Textarea
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_textarea' ) ) {

	/**
	 * Class Exopite_Simple_Options_Framework_Field_textarea.
	 */
	class Exopite_Simple_Options_Framework_Field_textarea extends Exopite_Simple_Options_Framework_Fields {

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

			echo $this->element_before();
			echo '<textarea name="' . $this->element_name() . '"' . $this->element_class() . $this->element_attributes() . '>' . esc_textarea( (string) $this->element_value() ) . '</textarea>';
			echo $this->element_after();
		}
	}

}
