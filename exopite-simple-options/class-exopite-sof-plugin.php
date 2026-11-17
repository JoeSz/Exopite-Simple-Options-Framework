<?php
/**
 * Plugin mode of the framework.
 *
 * @package Exopite_Simple_Options_Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Exopite_Sof_Plugin' ) ) {

	/**
	 * Runs only when the framework is installed as a plugin: announces that the
	 * framework is ready and warns if another (bundled) copy was loaded first.
	 */
	class Exopite_Sof_Plugin {

		/**
		 * Register the WordPress hooks.
		 */
		public static function register_hooks() {
			add_action( 'plugins_loaded', array( __CLASS__, 'announce' ), 1 );
			add_action( 'admin_notices', array( __CLASS__, 'notice_other_copy' ) );
		}

		/**
		 * Fire "exopite_sof_loaded", so plugins and themes can wait for the framework.
		 */
		public static function announce() {
			/**
			 * Fires when the framework plugin is loaded.
			 *
			 * @param string $file Path of the framework class file that is in use.
			 */
			do_action( 'exopite_sof_loaded', self::loaded_file() );
		}

		/**
		 * Path of the framework class file PHP actually uses.
		 *
		 * @return string
		 */
		public static function loaded_file() {
			if ( ! class_exists( 'Exopite_Simple_Options_Framework' ) ) {
				return '';
			}
			$reflection = new ReflectionClass( 'Exopite_Simple_Options_Framework' );
			return wp_normalize_path( (string) $reflection->getFileName() );
		}

		/**
		 * Warn administrators if a bundled copy from another plugin or theme was
		 * loaded before this plugin, because then that copy is used everywhere.
		 */
		public static function notice_other_copy() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			$loaded = self::loaded_file();
			$own    = wp_normalize_path( __DIR__ . '/exopite-simple-options-framework-class.php' );

			if ( '' === $loaded || $loaded === $own ) {
				return;
			}

			$version = defined( 'Exopite_Simple_Options_Framework::VERSION' ) ? Exopite_Simple_Options_Framework::VERSION : __( 'unknown (older)', 'exopite-sof' );

			printf(
				'<div class="notice notice-warning"><p>%s</p></div>',
				esc_html(
					sprintf(
						/* translators: 1: file path of the other copy, 2: its version, 3: version of this plugin. */
						__( 'Exopite Simple Options Framework: another copy was loaded first and is used instead of this plugin: %1$s (version %2$s, this plugin: %3$s). Update or remove the bundled copy.', 'exopite-sof' ),
						$loaded,
						$version,
						self::own_version()
					)
				)
			);
		}

		/**
		 * Version from this plugin's header.
		 *
		 * @return string
		 */
		private static function own_version(): string {
			$data = get_file_data( EXOPITE_SOF_PLUGIN_FILE, array( 'Version' => 'Version' ) );
			return (string) $data['Version'];
		}
	}
}
