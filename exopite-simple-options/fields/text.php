<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Text
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_text' ) ) {

	class Exopite_Simple_Options_Framework_Field_text extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '', $multilang ) {
			parent::__construct( $field, $value, $unique, $where, $multilang );

		}

		public function output() {

			echo $this->element_before();
			echo '<input type="' . $this->element_type() . '" name="' . $this->element_name() . '" value="' . $this->element_value() . '"' . $this->element_class() . $this->element_attributes() . '/>';
			echo $this->element_after();

		}

	}

}
