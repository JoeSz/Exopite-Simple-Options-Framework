<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Backup
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_backup' ) ) {
	/**
	 * Class Exopite_Simple_Options_Framework_Field_backup.
	 */
	class Exopite_Simple_Options_Framework_Field_backup extends Exopite_Simple_Options_Framework_Fields {

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

			if ( 'menu' == $this->config['type'] ) {

				$nonce   = wp_create_nonce( 'exopite_sof_backup_' . $this->unique );
				$options = get_option( $this->unique );
				$export  = esc_url(
					add_query_arg(
						array(
							'action'  => 'exopite-sof-export-options',
							'export'  => $this->unique,
							'wpnonce' => $nonce,
						),
						admin_url( 'admin-ajax.php' )
					)
				);

				$encoded_options = '';
				if ( $options ) {

					$encoded_options = $this->encode_string( $options );

				}

				echo '<textarea name="_nonce" class="exopite-sof__import"></textarea>';
				echo '<small class="exopite-sof-info--small">( ' . esc_attr__( 'copy-paste your backup string here', 'exopite-sof' ) . ' )</small>';
				echo '<a href="#" class="button button-primary exopite-sof-import-js" data-confirm="' . esc_attr__( 'Are you sure, you want to overwrite existing options?', 'exopite-sof' ) . '">' . esc_attr__( 'Import a Backup', 'exopite-sof' ) . '</a>';

				echo '<hr />';
				echo '<textarea name="_nonce" class="exopite-sof__export" readonly>' . esc_textarea( $encoded_options ) . '</textarea>';
				echo '<a href="' . $export . '" class="button button-primary" target="_blank">' . esc_attr__( 'Download Backup', 'exopite-sof' ) . '</a>';

				echo '<hr />';
				echo '<small class="exopite-sof-info--small exopite-sof-info--warning">' . esc_attr__( 'Please be sure for reset all of framework options.', 'exopite-sof' ) . '</small>';
				echo '<a href="#" class="button button-warning exopite-sof-reset-js" data-confirm="' . esc_attr__( 'Are you sure, you want to reset all options?', 'exopite-sof' ) . '">' . esc_attr__( 'Reset All Options', 'exopite-sof' ) . '</a>';

				echo '<div class="exopite-sof--data" data-admin="' . esc_url( admin_url( 'admin-ajax.php' ) ) . '" data-unique="' . esc_attr( $this->unique ) . '" data-wpnonce="' . esc_attr( $nonce ) . '"></div>';

			} else {

				echo 'This item only available in menu!<br>';

			}

			echo $this->element_after();
		}

		/**
		 * Encode string for backup options
		 */
		function encode_string( $option ) {
			return json_encode( $option );
		}

		/**
		 * Decode string for backup options
		 */
		function decode_string( $option ) {
			return json_decode( $option, true );
		}

		/**
		 * Enqueue the field assets.
		 */
		public static function enqueue( $args ) {

			/*
			 * https://sweetalert.js.org/guides/
			 */
			$resources = array(
				array(
					'name'       => 'sweetalert',
					'fn'         => 'sweetalert.min.js',
					'type'       => 'script',
					'dependency' => false,
					'version'    => '2.1.0',
					'attr'       => true,
				),
			);

			parent::do_enqueue( $resources, $args );
		}
	}

}
