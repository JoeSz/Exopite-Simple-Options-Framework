<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Textarea
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_meta' ) ) {

	/**
	 * Class Exopite_Simple_Options_Framework_Field_meta.
	 */
	class Exopite_Simple_Options_Framework_Field_meta extends Exopite_Simple_Options_Framework_Fields {

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

			if ( 'metabox' != $this->config['type'] ) {

				echo 'This item only available in metabox!<br>';

			} elseif ( ! empty( $this->field['meta-key'] ) ) {

					$value = get_post_meta( get_the_ID(), $this->field['meta-key'], true );

					echo $this->element_before();
					echo '<textarea readonly' . $this->element_class() . $this->element_attributes() . '>' . esc_textarea( var_export( $value, true ) ) . '</textarea>';
					echo $this->element_after();
			}
		}
	}

}
