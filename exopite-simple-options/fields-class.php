<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Options Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Fields' ) ) {

	abstract class Exopite_Simple_Options_Framework_Fields {

		public $multilang;
		public $current_lang;
		public $lang_default;
		public $lang_current;
		public $languages;

		public function __construct( $field = array(), $value = null, $unique = '', $config = array()) {

			$this->field     = $field;
			$this->value     = $value;
			$this->org_value = $value;
			$this->unique    = $unique;
			$this->config    = $config;
			$this->where     = ( isset( $this->config['type'] ) ) ? $this->config['type'] : '';
			$this->multilang = ( isset( $this->config['multilang'] ) ) ? $this->config['multilang'] : false;

			$this->lang_default = ( $this->multilang && isset( $this->multilang['default'] ) ) ? $this->multilang['default'] : mb_substr( get_locale(), 0, 2 );
			$this->lang_current = ( $this->multilang && isset( $this->multilang['current'] ) ) ? $this->multilang['current'] : $this->lang_default;

			$this->languages = (
				$this->multilang &&
				isset( $this->multilang['languages'] ) &&
				is_array( $this->multilang['languages'] )
			) ? $this->multilang['current'] : array( $this->lang_default );

		}

		/*
		 * @return bool true if multilang is set to true
		*/
		public function is_multilang() {

			// We should have received multilang as well together with $this->config['multilang']
			return ( isset($this->config['multilang']) && $this->config['multilang'] && $this->multilang ) ? true : false;

		}

		abstract public function output();

		public function element_before() {

			return ( isset( $this->field['before'] ) ) ? '<div class="exopite-sof-before">' . $this->field['before'] . '</div>' : '';

		}

		public function element_after() {

			$out = ( isset( $this->field['info'] ) ) ? '<span class="exopite-sof-text-desc">' . $this->field['info'] . '</span>' : '';
			$out .= $this->element_help();
			$out .= ( isset( $this->field['after'] ) ) ? '<div class="exopite-sof-after">' . $this->field['after'] . '</div>' : '';

			// $out .= $this->element_get_error();

			return $out;

		}

		public function element_help() {
			return ( isset( $this->field['help'] ) ) ? '<span class="exopite-sof-help" title="' . $this->field['help'] . '" data-title="' . $this->field['help'] . '"><span class="fa fa-question-circle"></span></span>' : '';
		}

		public function element_type() {

			return $this->field['type'];

		}

		public function element_name( $extra_name = '' ) {



			$extra_multilang = ( $this->is_multilang() ) ? '[' . $this->lang_current . ']' : '';

//			TODO: Accout for 'simple' options style
//			if ( $this->config['type'] == 'metabox' && isset( $this->config['options'] ) && $this->config['options'] == 'simple' ) {
//				$name = $this->field['id'] . $extra_multilang . $extra_name;
//			} else {
//				$name = $this->unique . '[' . $this->field['id'] . ']' . $extra_multilang . $extra_name;
//			}

			$name = $this->unique . $extra_multilang . '[' . $this->field['id'] . ']' . $extra_name;

			return ( ! empty( $this->unique ) ) ? $name : '';

		}

		public function element_value( $value = null ) {

			$value = $this->value;

			if ( empty( $value ) && isset( $this->field['default'] ) && !empty($this->field['default'] ) ) {

				$default = $this->field['default'];

				if ( is_array( $default ) ) {

					if ( isset($default['function']) && is_callable( $default['function'] ) ) {
						$args = ( isset( $default['args'] ) ) ? $default['args'] : '';

						return call_user_func( $default['function'], $args );
					}

				}

				return $default;

			}

			return $value;

			/**
			 * Set default if not exist
			 */
//			if ( (
//				     // multilang activated and multilang set to value but not in the current language
//				     ( is_array( $this->multilang ) && isset( $this->value['multilang'] ) && ! isset( $value[ $this->multilang['current'] ] ) ) ||
//				     // multilang is activated but still "single language" value there and not current language (either current is set or next rule apply)
//				     ( is_array( $this->multilang ) && ! isset( $this->value['multilang'] ) && $this->multilang['current'] != $this->multilang['default'] ) ||
//				     // value is not set
//				     ! isset( $value )
//			     ) &&
//			     // and default value is set in options
//			     isset( $this->field['default'] ) && $this->field['default'] !== ''
//			) {
//
//				$default = $this->field['default'];
//
//				if ( is_array( $default ) ) {
//
//					if ( is_callable( $default['function'] ) ) {
//						$args = ( isset( $default['args'] ) ) ? $default['args'] : '';
//
//						return call_user_func( $default['function'], $args );
//					}
//
//				}
//
//				return $default;
//
//			}
//
//			if ( is_array( $this->multilang ) && isset( $this->value['multilang'] ) && is_array( $value ) ) {
//
//				$current = $this->multilang['current'];
//
//				if ( isset( $value[ $current ] ) ) {
//					$value = $value[ $current ];
//				} else if ( $this->multilang['current'] == $this->multilang['default'] && isset( $value[ $current ] ) ) {
//					$value = $this->value;
//				} else {
//					$value = '';
//				}
//
//			} else if ( is_array( $this->multilang ) && ! is_array( $value ) && ( $this->multilang['current'] != $this->multilang['default'] ) ) {
//				$value = '';
//			} else if ( ! is_array( $this->multilang ) && isset( $this->value['multilang'] ) && is_array( $this->value ) ) {
//
//				$value = array_values( $this->value );
//				$value = $value[0];
//
//			}
//
//			return $value;

		}

		public function element_attributes( $el_attributes = array() ) {

			$attributes = ( isset( $this->field['attributes'] ) ) ? $this->field['attributes'] : array();
			$element_id = ( isset( $this->field['id'] ) ) ? $this->field['id'] : '';

			if ( $el_attributes !== false ) {
				$sub_elemenet  = ( isset( $this->field['sub'] ) ) ? 'sub-' : '';
				$el_attributes = ( is_string( $el_attributes ) || is_numeric( $el_attributes ) ) ? array( 'data-' . $sub_elemenet . 'depend-id' => $element_id . '_' . $el_attributes ) : $el_attributes;
				$el_attributes = ( empty( $el_attributes ) && isset( $element_id ) ) ? array( 'data-' . $sub_elemenet . 'depend-id' => $element_id ) : $el_attributes;
			}

			$attributes = wp_parse_args( $attributes, $el_attributes );

			$atts = '';

			if ( ! empty( $attributes ) ) {
				foreach ( $attributes as $key => $value ) {
					if ( $value === 'only-key' ) {
						$atts .= ' ' . $key;
					} else {
						$atts .= ' ' . $key . '="' . $value . '"';
					}
				}
			}

			return $atts;

		}

		public function element_class( $el_class = '' ) {

			$field_class = '';
			$classes     = ( isset( $this->field['class'] ) ) ? array_merge( explode( ' ', $el_class ), explode( ' ', $this->field['class'] ) ) : explode( ' ', $el_class );
			$classes     = array_filter( $classes );
			$field_class = implode( ' ', $classes );

			return ( ! empty( $field_class ) ) ? ' class="' . $field_class . '"' : '';

		}

		public function checked( $helper = '', $current = '', $type = 'checked', $echo = false ) {

			if ( is_array( $helper ) && in_array( $current, $helper ) ) {
				$result = ' ' . $type . '="' . $type . '"';
			} else if ( $helper == $current ) {
				$result = ' ' . $type . '="' . $type . '"';
			} else {
				$result = '';
			}

			if ( $echo ) {
				echo $result;
			}

			return $result;

		}

	}

}