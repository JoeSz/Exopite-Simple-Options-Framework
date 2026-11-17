<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Notice
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_notice' ) ) {

	/**
	 * Class Exopite_Simple_Options_Framework_Field_notice.
	 */
	class Exopite_Simple_Options_Framework_Field_notice extends Exopite_Simple_Options_Framework_Fields {

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

			$classes = ( isset( $this->field['class'] ) ) ? implode( ' ', explode( ' ', $this->field['class'] ) ) : '';

			$content = ( isset( $this->field['content'] ) ) ? $this->field['content'] : '';

			if ( isset( $this->field['callback'] ) ) {

				$callback = $this->field['callback'];
				if ( is_callable( $callback['function'] ) ) {

					$args    = ( isset( $callback['args'] ) ) ? $callback['args'] : '';
					$content = call_user_func( $callback['function'], $args );

				}
			}

			echo $this->element_before();
			echo '<div class="exopite-sof-notice ' . esc_attr( $classes ) . '">' . $content . '</div>';
			echo $this->element_after();
		}
	}

}
