<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Options Class
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Fields' ) ) {

	/**
	 * Class Exopite_Simple_Options_Framework_Fields.
	 */
	abstract class Exopite_Simple_Options_Framework_Fields {

		// Declared for PHP 8.2+ (no dynamic properties).
		public $google_fonts;

		public $field;
		public $value;
		public $org_value;
		public $unique;
		public $config;
		public $where;
		public $multilang;
		public $lang_default;
		public $lang_current;
		public $languages;
		public $is_multilang;

		/**
		 * Constructor.
		 */
		public function __construct( $field = array(), $value = null, $unique = '', $config = array() ) {

			$this->field        = $field;
			$this->value        = $value;
			$this->org_value    = $value;
			$this->unique       = $unique;
			$this->config       = $config;
			$this->where        = ( isset( $this->config['type'] ) ) ? $this->config['type'] : '';
			$this->multilang    = ( isset( $this->config['multilang'] ) ) ? $this->config['multilang'] : false;
			$this->is_multilang = ( isset( $this->config['is_multilang'] ) ) ? (bool) $this->config['is_multilang'] : false;
			$this->google_fonts = '';

			$this->lang_default = ( $this->multilang && isset( $this->multilang['default'] ) ) ? $this->multilang['default'] : mb_substr( get_locale(), 0, 2 );
			$this->lang_current = ( $this->multilang && isset( $this->multilang['current'] ) ) ? $this->multilang['current'] : $this->lang_default;

			$this->languages = (
				$this->multilang &&
				isset( $this->multilang['languages'] ) &&
				is_array( $this->multilang['languages'] )
			) ? $this->multilang['languages'] : array( $this->lang_default );
		}

		/*
		 * @return bool true if multilang is set to true
		*/
		public function is_multilang() {

			return $this->is_multilang;
		}

		/**
		 * Render the field output.
		 */
		abstract public function output();

		/**
		 * Element before.
		 */
		public function element_before() {

			$element = 'div';
			if ( isset( $this->field['pseudo'] ) && $this->field['pseudo'] ) {
				$element = 'span';
			}
			return ( isset( $this->field['before'] ) ) ? '<' . $element . ' class="exopite-sof-before">' . $this->field['before'] . '</' . $element . '>' : '';
		}

		/**
		 * Element after.
		 */
		public function element_after() {

			$out  = ( isset( $this->field['info'] ) ) ? '<span class="exopite-sof-text-desc">' . $this->field['info'] . '</span>' : '';
			$out .= $this->element_help();
			$out .= ( isset( $this->field['after'] ) ) ? '<div class="exopite-sof-after">' . $this->field['after'] . '</div>' : '';

			return $out;
		}

		/**
		 * Element prepend.
		 */
		public function element_prepend() {

			$out = '';

			if ( isset( $this->field['prepend'] ) || isset( $this->field['append'] ) ) {
				$out .= '<span class="exopite-sof-form-field exopite-sof-form-field-input">';
			}

			if ( isset( $this->field['prepend'] ) ) {

				$out .= '<span class="input-prepend">';

				if ( strpos( $this->field['prepend'], 'fa-' ) !== false ) {
					$out .= '<i class="fa ' . esc_attr( $this->field['prepend'] ) . '" aria-hidden="true"></i>';
				} elseif ( strpos( $this->field['prepend'], 'dashicons' ) !== false ) {
					$out .= '<span class="dashicons ' . esc_attr( $this->field['prepend'] ) . '"></span>';
				} else {
					$out .= $this->field['prepend'];
				}

				$out .= '</span>';
			}

			return $out;
		}

		/**
		 * Element append.
		 */
		public function element_append() {

			$out = '';

			if ( isset( $this->field['append'] ) ) {
				$out .= '<span class="input-append">';

				if ( strpos( $this->field['append'], 'fa-' ) !== false ) {
					$out .= '<i class="fa ' . esc_attr( $this->field['append'] ) . '" aria-hidden="true"></i>';
				} elseif ( strpos( $this->field['append'], 'dashicons' ) !== false ) {
					$out .= '<span class="dashicons ' . esc_attr( $this->field['append'] ) . '"></span>';
				} else {
					$out .= $this->field['append'];
				}

				$out .= '</span>';
			}

			if ( isset( $this->field['prepend'] ) || isset( $this->field['append'] ) ) {
				$out .= '</span>';
			}

			return $out;
		}

		/**
		 * Element help.
		 */
		public function element_help() {
			return ( isset( $this->field['help'] ) ) ? '<span class="exopite-sof-help" title="' . esc_attr( $this->field['help'] ) . '" data-title="' . esc_attr( $this->field['help'] ) . '"><span class="fa fa-question"></span></span>' : '';
		}

		/**
		 * Element type.
		 */
		public function element_type() {

			return $this->field['type'];
		}

		/**
		 * Element name.
		 */
		public function element_name( $extra_name = '' ) {

			$extra_multilang = ( isset( $this->config['is_multilang'] ) && ( true === $this->config['is_multilang'] ) ) ? '[' . $this->lang_current . ']' : '';
			// for some reason this not work, maybe because abstract class
			// $extra_multilang = ( $this->is_multilang() ) ? '[' . $this->lang_current . ']' : '';.

			// Because we changed to unique, this will determinate if it is a "sub" field. Sub field is inside group.
			if ( isset( $this->field['sub'] ) ) {

				$name = $this->unique . '[' . $this->field['id'] . ']' . $extra_name;

			} elseif ( $this->config['is_options_simple'] ) {

					$name = $this->field['id'] . $extra_name;

			} else {
				// This is the actual.
				$name = $this->unique . $extra_multilang . '[' . $this->field['id'] . ']' . $extra_name;
			}

			return ( ! empty( $this->unique ) ) ? esc_attr( $name ) : '';
		}

		/**
		 * Element value.
		 */
		public function element_value( $value = null ) {

			$value = $this->value;

			if ( ! isset( $value ) && isset( $this->field['default'] ) && ! empty( $this->field['default'] ) ) {

				$default = $this->field['default'];

				if ( is_array( $default ) ) {

					if ( isset( $default['function'] ) && is_callable( $default['function'] ) ) {
						$args = ( isset( $default['args'] ) ) ? $default['args'] : '';

						return call_user_func( $default['function'], $args );
					}
				}

				return $default;

			}

			return $value;
		}

		/**
		 * Element attributes.
		 */
		public function element_attributes( $el_attributes = array() ) {

			$attributes = ( isset( $this->field['attributes'] ) ) ? $this->field['attributes'] : array();
			$element_id = ( isset( $this->field['id'] ) ) ? $this->field['id'] : '';

			if ( false !== $el_attributes ) {
				$sub_element   = ( isset( $this->field['sub'] ) ) ? 'sub-' : '';
				$el_attributes = ( is_string( $el_attributes ) || is_numeric( $el_attributes ) ) ? array( 'data-' . $sub_element . 'depend-id' => $element_id . '_' . $el_attributes ) : $el_attributes;
				$el_attributes = ( empty( $el_attributes ) && isset( $element_id ) ) ? array( 'data-' . $sub_element . 'depend-id' => $element_id ) : $el_attributes;
			}

			$attributes = wp_parse_args( $attributes, $el_attributes );

			$atts = '';

			if ( ! empty( $attributes ) ) {
				foreach ( $attributes as $key => $value ) {
					if ( 'only-key' === $value ) {
						$atts .= ' ' . esc_attr( $key );
					} else {
						$atts .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
					}
				}
			}

			return $atts;
		}

		/**
		 * Element class.
		 */
		public function element_class( $el_class = '' ) {

			$classes     = ( isset( $this->field['class'] ) ) ? array_merge( explode( ' ', $el_class ), explode( ' ', $this->field['class'] ) ) : explode( ' ', $el_class );
			$classes     = array_filter( $classes );
			$field_class = implode( ' ', $classes );

			return ( ! empty( $field_class ) ) ? ' class="' . esc_attr( $field_class ) . '"' : '';
		}

		/**
		 * Checked.
		 */
		public function checked( $value = '', $current = '', $type = 'checked', $echo = false ) {

			$value = maybe_unserialize( $value );
			if ( is_array( $value ) && in_array( $current, $value ) ) {
				$result = ' ' . $type . '="' . $type . '"';
			} elseif ( $value == $current ) {
				$result = ' ' . $type . '="' . $type . '"';
			} else {
				$result = '';
			}

			if ( $echo ) {
				echo $result;
			}

			return $result;
		}

		/**
		 * Get google fonts json.
		 */
		public function get_google_fonts_json() {

			if ( empty( $this->google_fonts ) ) {

				$google_fonts_json_fn = implode( DIRECTORY_SEPARATOR, array( __DIR__, 'assets', 'google-fonts.json' ) );

				if ( file_exists( $google_fonts_json_fn ) ) {
					$google_fonts_json  = file_get_contents( $google_fonts_json_fn );
					$this->google_fonts = json_decode( $google_fonts_json );
				}
			}

			return $this->google_fonts;
		}

		/**
		 * Do enqueue.
		 */
		public static function do_enqueue( $styles_scripts, $args ) {

			foreach ( $styles_scripts as $resource ) {

				$resource_file = join(
					DIRECTORY_SEPARATOR,
					array(
						$args['plugin_sof_path'] . 'assets',
						$resource['fn'],
					)
				);
				$resource_url  = join(
					'/',
					array(
						untrailingslashit( $args['plugin_sof_url'] ),
						'assets',
						$resource['fn'],
					)
				);

				if ( ! file_exists( $resource_file ) ) {
					continue;
				}

				if ( ! empty( $resource['version'] ) ) {
					$version = $resource['version'];
				} else {
					$version = filemtime( $resource_file );
				}

				switch ( $resource['type'] ) {
					case 'script':
						$function = 'wp_enqueue_script';
						break;
					case 'style':
						$function = 'wp_enqueue_style';
						break;
					default:
						continue 2;

				}

				$function( $resource['name'], $resource_url, $resource['dependency'], $version, $resource['attr'] );

			}
		}
	}

}
