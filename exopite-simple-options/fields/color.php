<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Color
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_color' ) ) {

	/**
	 * Class Exopite_Simple_Options_Framework_Field_color.
	 */
	class Exopite_Simple_Options_Framework_Field_color extends Exopite_Simple_Options_Framework_Fields {

		/**
		 * Constructor.
		 */
		public function __construct( $field, $value = '', $unique = '', $config = array(), $multilang = false ) {

			parent::__construct( $field, $value, $unique, $config, $multilang );
		}

		/**
		 * Render the field output.
		 */
		public function output() {

			$classes  = ( isset( $this->field['class'] ) ) ? implode( ' ', explode( ' ', $this->field['class'] ) ) : '';
			$controls = array( 'hue', 'brightness', 'saturation', 'wheel' );
			$control  = ( isset( $this->field['control'] ) ) ? $this->field['control'] : 'saturation';
			$formats  = array( 'rgb', 'hex' );
			$format   = ( isset( $this->field['format'] ) ) ? $this->field['format'] : 'rgb';

			echo $this->element_before();
			echo '<input type="';
			if ( isset( $this->field['picker'] ) && 'html5' == $this->field['picker'] ) {
				echo 'color';
			} else {
				echo 'text';
			}
			echo '" ';
			if ( ! isset( $this->field['picker'] ) || 'html5' != $this->field['picker'] ) {
				echo 'class="minicolor ' . esc_attr( $classes ) . '" ';
			}
			if ( isset( $this->field['rgba'] ) && $this->field['rgba'] ) {
				echo 'data-opacity="1" ';
			}
			if ( in_array( $control, $controls ) ) {
				echo 'data-control="' . esc_attr( $control ) . '" '; // hue, brightness, saturation, wheel.
			}
			if ( in_array( $format, $formats ) ) {
				echo 'data-format="' . esc_attr( $format ) . '" '; // hue, brightness, saturation, wheel.
			}
			echo 'name="' . $this->element_name() . '" value="' . esc_attr( $this->element_value() ) . '"';
			if ( isset( $this->field['picker'] ) && 'html5' == $this->field['picker'] ) {
				echo $this->element_class();
			}
			echo $this->element_attributes() . '/>';
		}

		/**
		 * Enqueue the field assets.
		 */
		public static function enqueue( $args ) {

			$resources = array(
				array(
					'name'       => 'minicolors_css',
					'fn'         => 'jquery.minicolors.css',
					'type'       => 'style',
					'dependency' => array(),
					'version'    => '20181201',
					'attr'       => 'all',
				),
				array(
					'name'       => 'minicolors_js',
					'fn'         => 'jquery.minicolors.js',
					'type'       => 'script',
					'dependency' => array( 'jquery' ),
					'version'    => '20181201',
					'attr'       => true,
				),
				array(
					'name'       => 'minicolors-loader',
					'fn'         => 'loader-minicolors.js',
					'type'       => 'script',
					'dependency' => array( 'minicolors_js' ),
					'version'    => '20190407',
					'attr'       => true,
				),
			);

			parent::do_enqueue( $resources, $args );
		}
	}

}
