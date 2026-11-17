<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Attached
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_attached' ) ) {

	/**
	 * Class Exopite_Simple_Options_Framework_Field_attached.
	 */
	class Exopite_Simple_Options_Framework_Field_attached extends Exopite_Simple_Options_Framework_Fields {

		/**
		 * Constructor.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '' ) {

			parent::__construct( $field, $value, $unique, $where );

			$defaults = array(
				'type' => 'image',
			);

			$options = ( ! empty( $this->field['options'] ) ) ? $this->field['options'] : array();

			$this->field['options'] = wp_parse_args( $options, $defaults );
		}

		/**
		 * Render the field output.
		 */
		public function output() {

			echo $this->element_before();

			if ( 'metabox' != $this->where ) {

				echo 'This item only available in metabox!<br>';

			} else {

				$attachment_type = ( isset( $this->field['options'] ) && isset( $this->field['options']['type'] ) ) ? $this->field['options']['type'] : '';

				$images = get_attached_media( $attachment_type, get_the_ID() );

				$post_type = get_post_type_object( get_post_type( get_the_ID() ) );

				if ( count( $images ) == 0 ) {

					printf( esc_html__( 'There is no attachment with type %1$s for this %2$s.', 'exopite-sof' ), esc_html( $this->field['options']['type'] ), esc_html( $post_type->labels->singular_name ) );

				} else {

					echo '<div class="exopite-sof-attachment-container" data-ajaxurl="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '" data-nonce="' . esc_attr( wp_create_nonce( Exopite_Simple_Options_Framework_Upload::NONCE_ACTION ) ) . '">';

					foreach ( $images as $image ) { ?>
						<span class="exopite-sof-attachment-media exopite-sof-attachment-media-js"
								data-media-id="<?php echo absint( $image->ID ); ?>"><span
									class="exopite-sof-attachment-media-delete-overlay"></span><span
									class="exopite-sof-attachment-media-delete exopite-sof-attachment-media-delete-js"><i
										class="fa fa-times" aria-hidden="true"></i></span><img
									src="<?php echo esc_url( wp_get_attachment_image_src( $image->ID, 'thumbnail' )[0] ); ?>"/></span>
						<?php
					}

					echo '</div>';

				}
			}

			echo $this->element_after();
		}

		/**
		 * Enqueue the field assets.
		 */
		public static function enqueue( $args ) {

			$resources = array(
				array(
					'name'       => 'jquery-finderselect',
					'fn'         => 'jquery.finderSelect.min.js',
					'type'       => 'script',
					'dependency' => array( 'jquery' ),
					'version'    => '0.7.0',
					'attr'       => true,
				),
				array(
					'name'       => 'exopite-sof-jquery-finderselect-loader',
					'fn'         => 'loader-jquery-finderselect.min.js',
					'type'       => 'script',
					'dependency' => array( 'jquery-finderselect' ),
					'version'    => '',
					'attr'       => true,
				),
			);

			parent::do_enqueue( $resources, $args );
		}
	}

}
