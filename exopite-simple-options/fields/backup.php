<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: Backup
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_backup' ) ) {
	class Exopite_Simple_Options_Framework_Field_backup extends Exopite_Simple_Options_Framework_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '' ) {
			parent::__construct( $field, $value, $unique, $where );
		}

		public function output() {

			echo $this->element_before();

			if ( $this->where == 'metabox' ) {

				echo 'This item only available in menu!<br>';

			} else {

				$nonce   = wp_create_nonce( 'exopite_sof_backup' );
				$options = get_option( $this->unique );
				$export  = esc_url( add_query_arg( array(
					'action'  => 'exopite-sof-export-options',
					'export'  => $this->unique,
					'wpnonce' => $nonce
				), admin_url( 'admin-ajax.php' ) ) );

				$encoded_options = '';
				if ( $options ) {

					$encoded_options = $this->encode_string( $options );

				}

				echo '<textarea name="_nonce" class="exopite-sof__import"></textarea>';
				echo '<small class="exopite-sof-info--small">( ' . __( 'copy-paste your backup string here', 'exopite-sof' ) . ' )</small>';
				echo '<a href="#" class="button button-primary exopite-sof-import-js" data-confirm="' . __( 'Are you sure, you want to overwrite existing options?', 'exopite-sof' ) . '">' . __( 'Import a Backup', 'exopite-sof' ) . '</a>';

				echo '<hr />';
				echo '<textarea name="_nonce" class="exopite-sof__export" readonly>' . $encoded_options . '</textarea>';
				echo '<a href="' . $export . '" class="button button-primary" target="_blank">' . __( 'Download Backup', 'exopite-sof' ) . '</a>';

				echo '<hr />';
				echo '<small class="exopite-sof-info--small exopite-sof-info--warning">' . __( 'Please be sure for reset all of framework options.', 'exopite-sof' ) . '</small>';
				echo '<a href="#" class="button button-warning exopite-sof-reset-js" data-confirm="' . __( 'Are you sure, you want to reset all options?', 'exopite-sof' ) . '">' . __( 'Reset All Options', 'exopite-sof' ) . '</a>';

				echo '<div class="exopite-sof--data" data-admin="' . admin_url( 'admin-ajax.php' ) . '" data-unique="' . $this->unique . '" data-wpnonce="' . $nonce . '"></div>';

			}

			echo $this->element_after();

		}

		/**
		 * Encode string for backup options
		 */
		function encode_string( $string ) {
			return rtrim( strtr( call_user_func( 'base' . '64' . '_encode', addslashes( gzcompress( serialize( $string ), 9 ) ) ), '+/', '-_' ), '=' );
		}

		/**
		 * Decode string for backup options
		 */
		function decode_string( $string ) {
			return unserialize( gzuncompress( stripslashes( call_user_func( 'base' . '64' . '_decode', rtrim( strtr( $string, '-_', '+/' ), '=' ) ) ) ) );
		}

		public static function enqueue( $args ) {

			/*
			 * https://sweetalert.js.org/guides/
			 */
			// wp_enqueue_script( 'sweetalert', '//unpkg.com/sweetalert/dist/sweetalert.min.js', false, '2.1.0', true );
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
