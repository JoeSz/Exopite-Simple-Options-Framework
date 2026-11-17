<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Group
 *
 * @since 1.0.0
 * @version 1.0.0
 */
/**
 *
 * - check defults
 * - design
 * - add/remove group item
 * - accrodion (open/close) items (after clone, new item open)
 *   https://codepen.io/brenden/pen/Kwbpyj
 *   https://www.jquery-az.com/detailed-guide-use-jquery-accordion-plugin-examples/
 *   http://inspirationalpixels.com/tutorials/creating-an-accordion-with-html-css-jquery
 *   https://www.w3schools.com/howto/howto_js_accordion.asp
 *   http://uniondesign.ca/simple-accordion-without-jquery-ui/
 * - clean up
 *
 * - remove name if empty unique from all fields
 *   so this->name() should include name="" and
 *   fields are not
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_group' ) ) {
	/**
	 * Class Exopite_Simple_Options_Framework_Field_group.
	 */
	class Exopite_Simple_Options_Framework_Field_group extends Exopite_Simple_Options_Framework_Fields {

		// Declared for PHP 8.2+ (no dynamic properties).
		public $group_title;
		public $is_repeater;
		public $is_accordion;
		public $is_accordion_closed;
		public $limit;
		public $is_cloneable;
		public $is_sortable;

		/**
		 * Constructor.
		 */
		public function __construct( $field, $value = '', $unique = '', $config = array() ) {
			parent::__construct( $field, $value, $unique, $config );

			$defaults = array(
				'group_title'  => esc_attr( 'Group Title', 'exopite-sof' ),
				'repeater'     => false,
				'cloneable'    => true,
				'sortable'     => true,
				'accordion'    => true,
				'closed'       => true,
				'limit'        => 0,
				'button_title' => esc_attr( 'Add new', 'exopite-sof' ),
			);

			$options = ( ! empty( $this->field['options'] ) ) ? $this->field['options'] : array();

			$this->field['options']    = wp_parse_args( $options, $defaults );
			$this->group_title         = ( isset( $this->field['options']['group_title'] ) ) ? $this->field['options']['group_title'] : $defaults['group_title'];
			$this->is_repeater         = ( isset( $this->field['options']['repeater'] ) ) ? (bool) $this->field['options']['repeater'] : $defaults['repeater'];
			$this->is_accordion        = ( isset( $this->field['options']['accordion'] ) ) ? (bool) $this->field['options']['accordion'] : $defaults['accordion'];
			$this->is_accordion_closed = ( isset( $this->field['options']['closed'] ) ) ? (bool) $this->field['options']['closed'] : $defaults['closed'];
			$this->limit               = ( isset( $this->field['options']['limit'] ) ) ? (int) $this->field['options']['limit'] : $defaults['limit'];
			$this->is_multilang        = ( isset( $this->config['is_multilang'] ) ) ? (bool) $this->config['is_multilang'] : false;

			if ( ! $this->is_repeater ) {
				$this->is_cloneable = false;
				$this->is_sortable  = false;
			} else {
				$this->is_cloneable = ( isset( $this->field['options']['cloneable'] ) ) ? (bool) $this->field['options']['cloneable'] : $defaults['cloneable'];
				$this->is_sortable  = ( isset( $this->field['options']['sortable'] ) ) ? (bool) $this->field['options']['sortable'] : $defaults['sortable'];
			}
		}

		/**
		 * Render the field output.
		 */
		public function output() {

			echo $this->element_before();

			$unallows = array();
			// $unallows  = array( 'group', 'tab' );
			$fields    = array_values( $this->field['fields'] );
			$unique_id = ( ! empty( $this->unique ) ) ? $this->unique : $this->field['id'];

			$multilang_array_index = ( $this->is_multilang ) ? "[{$this->config['multilang']['current']}]" : '';

			if ( $this->config['is_options_simple'] ) {
				$parent_array = $this->field['id'];
			} else {
				$parent_array = $this->unique . '[' . $this->field['id'] . ']';
			}

			if ( $this->is_repeater ) {

				if ( $this->config['is_options_simple'] ) {

					$base_id = array(
						'id'                => "{$this->field['id']}[REPLACEME]",
						'is_options_simple' => true,
					);

				} else {

					$base_id = array(
						'id' => $this->unique . $multilang_array_index . '[' . $this->field['id'] . ']' . '[REPLACEME]',
					);

				}
			} elseif ( $this->config['is_options_simple'] ) {

					$base_id = array(
						'id'                => "{$this->field['id']}",
						'is_options_simple' => true,
					);

			} else {

				$base_id = array(
					'id' => $this->unique . $multilang_array_index . '[' . $this->field['id'] . ']',
				);
			}

			$muster_classes = array();
			if ( $this->is_repeater ) {
				$muster_classes = array( 'exopite-sof-cloneable__muster', 'exopite-sof-cloneable__muster--hidden' );
				if ( $this->is_accordion ) {
					$muster_classes[] = 'exopite-sof-accordion--hidden';
					$muster_classes[] = 'exopite-sof-accordion__item';
				}
			} elseif ( $this->is_accordion ) {
					$muster_classes[] = 'exopite-sof-accordion__item';
				if ( $this->is_accordion_closed ) {
					$muster_classes[] = 'exopite-sof-accordion--hidden';
				}
			}

			$limit    = $this->limit;
			$sortable = $this->is_sortable;
			$classes  = array( 'exopite-sof-group' );
			if ( $this->is_accordion ) {
				$classes[] = 'exopite-sof-accordion';
			}

			echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-limit="' . esc_attr( $limit ) . '">';

			$wrapper_classes = array( 'exopite-sof-accordion__wrapper' );

			if ( $this->is_accordion && ! $this->is_repeater ) {
				echo '<div class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . '">';
			}

			echo '<div class="exopite-sof-cloneable__item ' . esc_attr( implode( ' ', $muster_classes ) ) . '">';

			if ( $this->is_repeater || ! empty( $this->group_title ) ) {

				echo '<h4 class="exopite-sof-cloneable__title exopite-sof-accordion__title"><span class="exopite-sof-cloneable__text">' . wp_kses_post( $this->group_title ) . '</span>';
				if ( $this->is_repeater ) {
					echo '<span class="exopite-sof-cloneable--helper">';
					if ( $sortable ) {
						echo '<i class="fa fa-arrows-v"></i>';
					}
					if ( $this->is_cloneable ) {
						echo '<i class="exopite-sof-cloneable--clone fa fa-clone disabled"></i>';
					}
					echo '<i class="exopite-sof-cloneable--remove fa fa-times disabled"></i>';
					echo '</span>';
				}
				echo '</h4>';

			}

			echo '<div class="exopite-sof-cloneable__content ';
			if ( ! $this->is_repeater ) {
				echo 'exopite-sof-sub-dependencies ';
			}
			echo 'exopite-sof-accordion__content">';

			$self                      = new Exopite_Simple_Options_Framework( $base_id, null );
			$self->config['multilang'] = $this->config['multilang'];

			$num = 0;

			foreach ( $fields as $field ) {

				if ( in_array( $field['type'], $unallows ) ) {
					$field['_notice'] = true;
					continue;
				}

				$class = 'Exopite_Simple_Options_Framework_Field_' . $field['type'];

				if ( $this->config['is_options_simple'] ) {
					$field['is_options_simple'] = true;
				}

				$field['sub'] = true;

				$field_default = ( isset( $field['default'] ) ) ? $field['default'] : '';

				// Set repeater default field fields as disabled,
				// to prevent save them.
				// If repeater, template field has no values.
				if ( $this->is_repeater ) {

					$field_value = null;

					$field_attributes = array(
						'disabled' => 'only-key',
					);

					if ( isset( $field['attributes'] ) && is_array( $field['attributes'] ) ) {
						$field['attributes'] += $field_attributes;
					} else {
						$field['attributes'] = $field_attributes;
					}
				} else {

					if ( is_serialized( $this->value ) ) {
						$this->value = self::unserialize_value( $this->value );
					}

					$field_value = ( isset( $field['id'] ) && isset( $this->value[ $field['id'] ] ) ) ? $this->value[ $field['id'] ] : '';

					$field_value = ( $this->is_repeater ) ? null : $field_value;

				}

				$self->add_field( $field, $field_value );

				++$num;

			}

			echo '</div>'; // exopite-sof-cloneable-content.

			echo '</div>'; // exopite-sof-cloneable__item.

			if ( $this->is_accordion && ! $this->is_repeater ) {
				echo '</div>';  // exopite-sof-accordion__wrapper.
			}

			// IF REPEATER.

			if ( $this->field['options']['repeater'] ) {

				$classes = array( 'exopite-sof-cloneable__wrapper', 'exopite-sof-accordion__wrapper' );

				if ( isset( $this->field['options']['mode'] ) && 'compact' == $this->field['options']['mode'] ) {
					$classes[] = 'exopite-sof-group-compact';
				}

				if ( isset( $this->config['type'] ) && 'metabox' == $this->config['type'] && isset( $this->config['options'] ) && 'simple' == $this->config['options'] ) {

					echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-is-sortable="' . esc_attr( $sortable ) . '" data-name="' . $this->element_name() . '">';

				} else {

					$data_multilang = ( $this->config['multilang'] ) ? true : false;

					echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" data-multilang="' . esc_attr( $data_multilang ) . '" data-is-sortable="' . esc_attr( $sortable ) . '" data-name="' . esc_attr( $base_id['id'] ) . '">';

				}

				if ( $this->value ) {

					if ( $this->config['is_options_simple'] ) {

						if ( is_serialized( $this->value ) ) {
							$this->value = self::unserialize_value( $this->value );
						}
					}

					$num = 0;

					foreach ( $this->value as $key => $value ) {

						/**
						 * If multilang, then
						 * - check if first element is current language is exist
						 * - is a string (if changed from single language) but not current language
						 * then skip.
						 * (without this check group will display from other languages elements as empty)
						 */

						echo '<div class="exopite-sof-cloneable__item';
						if ( $this->is_accordion && $this->is_accordion_closed ) {
							echo ' exopite-sof-accordion__item';
						}
						if ( $this->is_accordion && $this->is_accordion_closed ) {
							echo ' exopite-sof-accordion--hidden';
						}
						echo '">';

						echo '<h4 class="exopite-sof-cloneable__title exopite-sof-accordion__title"><span class="exopite-sof-cloneable__text">' . wp_kses_post( $this->field['options']['group_title'] ) . '</span>';
						echo '<span class="exopite-sof-cloneable--helper">';
						if ( $sortable ) {
							echo '<i class="fa fa-arrows-v"></i>';
						}
						if ( $this->is_cloneable ) {
							echo '<i class="exopite-sof-cloneable--clone fa fa-clone"></i>';
						}
						echo '<i class="exopite-sof-cloneable--remove fa fa-times"></i>';
						echo '</span>';
						echo '</h4>';
						echo '<div class="exopite-sof-cloneable__content exopite-sof-sub-dependencies exopite-sof-accordion__content">';

						if ( $this->config['is_options_simple'] ) {

							$self->unique = $this->field['id'] . '[' . $num . ']';

						} else {

							$self->unique = $this->unique . $multilang_array_index . '[' . $this->field['id'] . '][' . $num . ']';

						}

						foreach ( $fields as $field ) {

							$field['sub'] = true;

							if ( $this->config['is_options_simple'] ) {
								$field['is_options_simple'] = true;
							}

							if ( in_array( $field['type'], $unallows ) ) {
								continue;
							}

							$value = ( isset( $this->value[ $num ][ $field['id'] ] ) ) ? $this->value[ $num ][ $field['id'] ] : '';

							$self->add_field( $field, $value );

						}

						echo '</div>'; // exopite-sof-cloneable__content.
						echo '</div>'; // exopite-sof-cloneable__item.

						++$num;

					}
				}

				echo '</div>'; // exopite-sof-cloneable__wrapper.

				echo '<div class="exopite-sof-cloneable-data" data-unique-id="' . esc_attr( $unique_id ) . '" data-limit="' . esc_attr( $this->field['options']['limit'] ) . '">' . esc_attr__( 'Max items:', 'exopite-sof' ) . ' ' . $this->field['options']['limit'] . '</div>';

				echo '<a href="#" class="button button-primary exopite-sof-cloneable--add">' . wp_kses_post( $this->field['options']['button_title'] ) . '</a>';

			}

			echo '</div>'; // exopite-sof-group.

			echo $this->element_after();
		}
	}

}
