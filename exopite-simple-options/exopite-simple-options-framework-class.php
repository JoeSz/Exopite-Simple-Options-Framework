<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 * INFOS AND TODOS:
 * - INFOS:
 *   - menu working without multilang??
 *   - menu working with multilang (WPML, Polyland and qTranslate-X)
 *   - meta working with multilang IF not simple (disabled as default)
 *     (WPML, Polyland do not need this)
 *     (required for qTranlate-X only, may check if activated)
 *   - meta simple is working
 *   - enqueue of fields are local, except:
 *     - ACE (for some reason not working if local. Wierd)
 *     - FontAwesome <- need font files - done
 *   - default value is correct, doesn't show up on empty (should only show up is not exist/null!)
 *   - ACE Field can not be sanitized with wp_kses (need to store JS/CSS/PHP code, maybe convert chars?)
 *     -> changed to esc_textarea
 * -TODOS
 *   - drag and drop reorder is not working on group, check JS reorder
 *
 * - DONE
 *  - If disable multilang, does not load default language options
 *   - something wrong with trumbowyg style - done
 *
 * - IDEAS
 *   - import options from file
 */
/**
 * Available fields:
 * - ACE field
 * - attached
 * - backup
 * - button
 * - botton_bar
 * - card
 * - checkbox
 * - color
 * - content
 * - date
 * - editor
 * - group
 * - hidden
 * - image
 * - image_select
 * - meta
 * - notice
 * - number
 * - password
 * - radio
 * - range
 * - select
 * - switcher
 * - tap_list
 * - text
 * - textarea
 * - upload
 * - video mp4/oembed
 */

/**
 * Standard args for all field:
 * - type
 * - id
 * - title
 *   - description
 * - class
 * - attributes
 * - before
 * - after
 */

/**
 * ToDo:
 * - remove all CDN
 * - possibility to override indluded files from path
 * - complatibility with WPML/qTranslate and Polylang
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework' ) ) :

	class Exopite_Simple_Options_Framework {

		/**
		 *
		 * dirname
		 * @access public
		 * @var string
		 *
		 */
		public $dirname = '';

		/**
		 *
		 * unique
		 * @access public
		 * @var string
		 *
		 */
		public $unique = '';

		/**
		 *
		 * notice
		 * @access public
		 * @var boolean
		 *
		 */
		public $notice = false;

		/**
		 *
		 * settings
		 * @access public
		 * @var array
		 *
		 */
		public $config = array();

		/**
		 *
		 * options
		 * @access public
		 * @var array
		 *
		 */
		public $fields = array();

		public $multilang = false;
		public $lang_default;
		public $lang_current;

		public $languages = array();

		public $version = '1.0';

		public $debug = false;

		/**
		 *
		 * options store
		 * @access public
		 * @var array
		 *
		 */
		public $db_options = array();


		/*
		 * Sets the type to  metabox|menu
		 * @var string
		 */
		private $type;

		/*
		 * @var object WP_Error
		 */
		protected $errors;

		/*
		 * @var array required fields for $type = menu
		 */
//		protected $required_keys_all_types = array( 'type' );
		protected $required_keys_all_types = array();


		/*
		 * @var array required fields for $type = menu
		 */
		protected $required_keys_menu = array( 'id', 'menu_title', 'plugin_basename' );

		/*
		 * @var array required fields for $type = metabox
		 */
		protected $required_keys_metabox = array( 'id', 'post_types', 'title', 'capability' );


		public function __construct( $config, $fields ) {

			// If we are not in admin area exit.
			if ( ! is_admin() ) {
				return;
			}

			$this->version = '20190910';

			// TODO: Do sanitize $config['id']
			$this->unique = $config['id'];
			// Filter for override every exopite $config and $fields
			$this->config = apply_filters( 'exopite_sof_config', $config );
			$this->fields = apply_filters( 'exopite_sof_options', $fields );

			// Filter for override $config and $fields with respect to $config and $fields
			$this->config = apply_filters( 'exopite_sof_config_' . $this->unique, $config );
			$this->fields = apply_filters( 'exopite_sof_options_' . $this->unique, $fields );

			// now is_menu() and is_metabox() available

			$this->check_required_configuration_keys();

			$this->set_properties();

			$this->load_classes();

			$this->setup_multilang();

			$this->define_shared_hooks();

			$this->define_menu_hooks();

			$this->define_metabox_hooks();


		}

		protected function setup_multilang() {

			/**
			 * Set multilang default true.
			 *
			 * In metabox there is no "multilang", because there are no need for that,
			 * WPML and others will handle mulitlang on their own.
			 * Meta will be saved for each language "copy" separately.
			 *
			 * OK, that was not true. My bad! :( - Joe
			 * WPML and Polylang save meta (custom fields) induvidually in post language version
			 * but it is required for qTranslate-X (only).
			 * So I modified the class, now "multilang" = true is working on meta too.
			 * Should leave it false as default and write in readme for qTranslate-X
			 * it is required to set it true.
			 */

//			// Check simple
//			if ( $this->is_multilang() ) {
//				// if ( $this->is_menu() && $this->is_multilang() ) {
//
//				$multilang_defaults = Exopite_Simple_Options_Framework_Helper::get_language_defaults();
//
//				if ( is_array( $multilang_defaults ) ) {
//
//					$this->config['multilang'] = $this->multilang = $multilang_defaults;
//
//					$this->lang_current = $this->multilang['current'];
//					$this->lang_default = $this->multilang['default'];
//					$this->languages    = $this->multilang['languages'];
//
//				}
//
//			}

			// Srt Defaults for all cases
			$multilang_defaults = Exopite_Simple_Options_Framework_Helper::get_language_defaults();

			if ( is_array( $multilang_defaults ) ) {

				$this->config['multilang'] = $this->multilang = $multilang_defaults;

				$this->lang_current = $this->multilang['current'];
				$this->lang_default = $this->multilang['default'];
				$this->languages    = $this->multilang['languages'];

			}


		}

		/*
		 * Checks for required keys in configuration array
		 * and throw admin error if a required key is missing
		 */
		protected function check_required_configuration_keys() {
			// instantiate the Wp_Error for $this->errors
			$this->errors = new WP_Error();

			$required_key_array = $this->required_keys_all_types;

			if ( $this->is_menu() ) {
				$required_key_array = $this->required_keys_menu;
			}

			if ( $this->is_metabox() ) {
				$required_key_array = $this->required_keys_metabox;
			}

			// Loop through all required keys array to check if every required key is set.
			if ( ! empty( $required_key_array ) && ! empty( $this->config ) ) {

				foreach ( $required_key_array as $key ) :

					if ( ! array_key_exists( $key, $this->config ) ) {
						// Add error message to the WP_Error object
						$this->errors->add( "missing_config_key_{$key}", sprintf( __( "%s is missing in the configuration array", 'exopite-simple-options' ), $key ) );
					}

				endforeach;

				// if the errors are logged, add the admin display hook
				if ( ! empty( $this->errors->get_error_messages() ) ) {
					add_action( 'admin_notices', array( $this, 'display_admin_error' ) );
				}

			} // ! empty( $required_key_array )

		} //check_required_keys()

		/*
		 * Set Properties of the class
		 */
		protected function set_properties() {

			if ( isset( $this->config['type'] ) ) {
				$this->set_type( $this->config['type'] );
			}

			// Parse the configuration against default values for Menu
			if ( $this->is_menu() ) {
				$default_menu_config = $this->get_config_default_menu();
				$this->config        = wp_parse_args( $this->config, $default_menu_config );
			}

			// Parse the configuration against default values for Metabox
			if ( $this->is_metabox() ) {
				$default_metabox_config = $this->get_config_default_metabox();
				$this->config           = wp_parse_args( $this->config, $default_metabox_config );

			}

			$this->config['is_options_simple'] = ( $this->is_options_simple() ) ? true : false;

			$this->dirname = wp_normalize_path( dirname( __FILE__ ) );


			// Rao's Test

//			$main_array = array(
//				'literature' => array(
//					'books'      => array(),
//					'audiobooks' => array(
//						'cd'  => array(),
//						'mp3' => array()
//					)// array 'audiobooks'
//				),// array 'literature'
//
//				'electronics' => array(
//					'tv-video'  => array(),
//					'computers' => array(
//						'desktops' => array(
//							'windows' => 'rao pc',
//							'apple'   => 'JOE PC'
//						),
//						'laptops'  => array()
//					)    // array 'computers'
//				)// array 'electronics'
//			);
//			$keys_array = array(
//				'electronics',
//				'computers',
//				'desktops',
//				'windows',
//			);
//
//			$dirty_value = $this->get_array_nested_value($main_array, $keys_array, '88');
//
//			echo "<pre>";
//			var_dump( $dirty_value );
//			echo "</pre>";
//			die();


		}


		public function get_array_nested_value( array $main_array, array $keys_array, $default_value = null ) {

			$length = count( $keys_array );

			for ( $i = 0; $i < $length; $i ++ ) {

				$is_set = ( isset( $main_array[ $keys_array[ $i ] ] ) ) ? true : false;

				if ( ! $is_set ) {
					// if the array key is not set, we break out of loop and return $$default_value
					break;
				} else {
					// Reset the $main_array to the sub array that we know exit
					$main_array = $main_array[ $keys_array[ $i ] ];

					if ( $i === $length - 1 ) { // We are at the last item of array
						// $main_array is now the required value / array
						return $main_array;
					}

				}

			} // end for loop

			return $default_value;

		}


		public function display_error() {
			add_action( 'admin_notices', array( $this, 'display_admin_error' ) );
		}

		public function display_admin_error() {

			$class        = 'notice notice-error';
			$message      = '';
			$errors_array = $this->errors->get_error_messages();


			if ( ! empty( $errors_array ) ) {
				// Get the error messages from the array
				$message .= esc_html( implode( ', ', $errors_array ) );
			} else {
				// if no message is set, throw generic error message
				$message .= __( 'Irks! An un-known error has occurred.', 'exopite-simple-options' );
			}

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
		}

		/*
		 * Register all of the hooks shared by all $type  metabox | menu
		 */
		protected function define_shared_hooks() {

			// Upload hooks are only required for both,
			Exopite_Simple_Options_Framework_Upload::add_hooks();

			//scripts and styles
			add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts_styles' ) );

			/**
			 * Add "code" plugin for TinyMCE
			 * @link https://www.tinymce.com/docs/plugins/code/
			 */
			add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );

		}//define_shared_hooks()

		/**
		 * Register all of the hooks related to 'menu' functionality
		 *
		 * @access   protected
		 */
		protected function define_menu_hooks() {

			if ( $this->is_menu() ) {
				/**
				 * Load options only if menu
				 * on metabox, page id is not yet available
				 */
				$this->db_options = apply_filters( 'exopite_sof_menu_get_options', get_option( $this->unique ), $this->unique );


				add_action( 'admin_init', array( $this, 'register_setting' ) );
				add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
				add_action( 'wp_ajax_exopite-sof-export-options', array( $this, 'export_options' ) );
				add_action( 'wp_ajax_exopite-sof-import-options', array( $this, 'import_options' ) );
				add_action( 'wp_ajax_exopite-sof-reset-options', array( $this, 'reset_options' ) );

				if ( isset( $this->config['plugin_basename'] ) && ! empty( $this->config['plugin_basename'] ) ) {
					add_filter( 'plugin_action_links_' . $this->config['plugin_basename'], array(
						$this,
						'plugin_action_links'
					) );
				}
			}
		}

		/**
		 * Register all of the hooks related to 'metabox' functionality
		 *
		 * @access   protected
		 */
		protected function define_metabox_hooks() {

			if ( $this->is_metabox() ) {

				/**
				 * Add metabox and register custom fields
				 *
				 * @link https://code.tutsplus.com/articles/rock-solid-wordpress-30-themes-using-custom-post-types--net-12093
				 */
				add_action( 'admin_init', array( $this, 'add_meta_box' ) );
				add_action( 'save_post', array( $this, 'save' ) );

			}

		}

		/*
		 * Sets the $type property
		 *
		 * @param string  $config_type
		 */
		protected function set_type( $config_type ) {

			$config_type = sanitize_key( $config_type );

			switch ( $config_type ) {
				case ( 'menu' ):
					$this->type = 'menu';
					break;

				case ( 'metabox' ):
					$this->type = 'metabox';
					break;

				default:
					$this->type = '';
			}

		}

		/*
		 * @return bool true if its a metabox type
		 */
		protected function is_metabox() {

			return ( $this->type === 'metabox' ) ? true : false;
		}

		/*
		 * @return bool true if its a metabox type
		 */
		protected function is_menu() {

			return ( $this->type === 'menu' ) ? true : false;
		}

		/*
		 * @return bool true if its a 'options'   => 'simple' in $config
		 */
		protected function is_options_simple() {

			// 'options'    => 'simple' in $config only required for metabox type
			return ( ! $this->is_menu() && isset( $this->config['options'] ) && $this->config['options'] === 'simple' ) ? true : false;
		}

		/*
		 * @return bool true if multilang is set to true
		*/
		protected function is_multilang() {

			/**
			 * Make sure if metabox and simple options are activated,
			 * then multilang is disabled.
			 * Enabled multilang is only required for qTranslate-X.
			 */
			if ( $this->is_metabox() && $this->is_options_simple() ) {
				return false;
			}

			return isset( $this->config['multilang'] ) ? $this->config['multilang'] : false;
		}

		/*
		 * @return bool true if its menu options
		 */
		protected function is_menu_page_loaded() {


			$current_screen = get_current_screen();

			return substr( $current_screen->id, - strlen( $this->unique ) ) === $this->unique;

		}

		/*
		 * check if the admin screen is of the post_type defined in config
		 * @return bool true if its menu options
		 */
		protected function is_metabox_enabled_post_type() {

			//
			if ( ! isset( $this->config['post_types'] ) ) {
				return false;
			}

			$current_screen = get_current_screen();

			$post_type_loaded = $current_screen->id;

			return ( in_array( $post_type_loaded, $this->config['post_types'] ) ) ? true : false;

		}


		// for TinyMCE Code Plugin
		public function mce_external_plugins( $plugins ) {
			$url             = $this->get_url( $this->dirname );
			$base            = trailingslashit( join( '/', array( $url, 'assets' ) ) );
			$plugins['code'] = $base . 'plugin.code.min.js';

			return $plugins;
		}

		public function import_options() {

			$retval = 'error';

			if ( isset( $_POST['unique'] ) && ! empty( $_POST['value'] ) && isset( $_POST['wpnonce'] ) && wp_verify_nonce( $_POST['wpnonce'], 'exopite_sof_backup' ) ) {

				$option_key = sanitize_key( $_POST['unique'] );

				// Using base_64_decode
				$value = unserialize( gzuncompress( stripslashes( call_user_func( 'base' . '64' . '_decode', rtrim( strtr( $_POST['value'], '-_', '+/' ), '=' ) ) ) ) );


				if ( is_array( $value ) ) {

					update_option( $option_key, $value );
					$retval = 'success';

				}


				//Using json_decode
//				$value = json_decode( $_POST['value']);
//
//				if ( is_array( $value ) ) {
//
//					update_option( $_POST['unique'], $value );
//					$retval = 'success';
//
//				}

			}

			die( $retval );

		}

		public function export_options() {

			if ( isset( $_GET['export'] ) && isset( $_GET['wpnonce'] ) && wp_verify_nonce( $_GET['wpnonce'], 'exopite_sof_backup' ) ) {

				$option_key = sanitize_key( $_GET['export'] );

				header( 'Content-Type: plain/text' );
				header( 'Content-disposition: attachment; filename=exopite-sof-options-' . gmdate( 'd-m-Y' ) . '.txt' );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );

				// Using base64_encode
				echo rtrim( strtr( call_user_func( 'base' . '64' . '_encode', addslashes( gzcompress( serialize( get_option( $option_key ) ), 9 ) ) ), '+/', '-_' ), '=' );
				// Why we are using base64_encode to hide the options? We should use the standard json to transfer/save settings between . It is suspicious always.


				// Using json_encode()
				//echo json_encode( $options_array );

			}

			die();
		}

		public function reset_options() {

			$retval = 'error';

			if ( isset( $_POST['unique'] ) && isset( $_POST['wpnonce'] ) && wp_verify_nonce( $_POST['wpnonce'], 'exopite_sof_backup' ) ) {

				delete_option( sanitize_key( $_POST['unique'] ) );

				$retval = 'success';

			}

			die( $retval );
		}

		/**
		 * Load classes
		 */
		public function load_classes() {

			require_once 'helper-class.php';
			require_once 'fields-class.php';
			require_once 'upload-class.php';

		}

		/**
		 * Get url from path
		 * works only for local urls
		 *
		 * @param  string $path the path
		 *
		 * @return string   the generated url
		 */
		public function get_url( $path = '' ) {

			$url = str_replace(
				wp_normalize_path( untrailingslashit( ABSPATH ) ),
				site_url(),
				$path
			);

			return $url;
		}

		public function locate_template( $type ) {

			/**
			 * Ideas:
			 * - May extend this with override.
			 */
			// This should be the name of directory in theme/ child theme
			$override_dir_name = 'exopite';
			$fields_dir_name   = 'fields';

			$template = join( DIRECTORY_SEPARATOR, array( $this->dirname, $fields_dir_name, $type . '.php' ) );


			/* TODO: check why its not overriding

			$locations[] = join( DIRECTORY_SEPARATOR, array(
				$override_dir_name,
				$fields_dir_name,
				$type . '.php'
			) );


			/// Filter the locations to search for a template file

			$locations = apply_filters( 'exopite_template_paths', $locations );

			$template = locate_template( $locations, false );

			// If we cannot find template path in theme, then load from our framework
			if ( empty( $template ) ) {

				$template = join( DIRECTORY_SEPARATOR, array( $this->dirname, $fields_dir_name, $type . '.php' ) );

			}

			*/

			return $template;

		}

		/**
		 * Register "settings" for plugin option page in plugins list
		 *
		 * @param array $links plugin links
		 *
		 * @return array possibly modified $links
		 */
		public function plugin_action_links( $links ) {
			/**
			 *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
			 */

			// BOOL of settings is given true | false
			if ( is_bool( $this->config['settings_link'] ) ) {

				// FALSE: If it is false, no need to go further
				if ( ! $this->config['settings_link'] ) {
					return $links;
				}

				// TRUE: if Settings link is not defined, lets create one
				if ( $this->config['settings_link'] ) {

					$options_base_file_name = sanitize_file_name( $this->config['parent'] );

					$options_page_id = $this->unique;

					$settings_link = "{$options_base_file_name}?page={$options_page_id}";

					$settings_link_array = array(
						'<a href="' . admin_url( $settings_link ) . '">' . __( 'Settings', '' ) . '</a>',
					);

					return array_merge( $settings_link_array, $links );
				}
			} // if ( is_bool( $this->config['settings_link'] ) )

			// URL of settings is given
			if ( ! is_bool( $this->config['settings_link'] ) && ! is_array( $this->config['settings_link'] ) ) {
				$settings_link = esc_url( $this->config['settings_link'] );

				return array_merge( $settings_link, $links );
			}

			// Array of settings_link is given
			if ( is_array( $this->config['settings_link'] ) ) {


				$settings_links_config_array = $this->config['settings_link'];
				$settings_link_array         = array();

				foreach ( $settings_links_config_array as $link ) {

					$link_text         = isset( $link['text'] ) ? sanitize_text_field( $link['text'] ) : __( 'Settings', '' );
					$link_url_un_clean = isset( $link['url'] ) ? $link['url'] : '#';

					$link_type = isset( $link['type'] ) ? sanitize_key( $link['type'] ) : 'default';

					switch ( $link_type ) {
						case ( 'external' ):
							$link_url = esc_url_raw( $link_url_un_clean );
							break;

						case ( 'file' ):
							$link_url = admin_url( sanitize_file_name( $link_url_un_clean ) );
							break;

						default:

							if ( $this->config['submenu'] ) {

								$options_base_file_name = sanitize_file_name( $this->config['parent'] );

								$options_base_file_name_extension = pathinfo( parse_url( $options_base_file_name )['path'], PATHINFO_EXTENSION );

//								var_dump( $options_base_file_name_extension); die();

								if ( $options_base_file_name_extension === 'php' ) {
									$options_base = $options_base_file_name;
								} else {
									$options_base = 'admin.php';
								}
								$options_page_id = $this->unique;

								$settings_link = "{$options_base}?page={$options_page_id}";

								$link_url = admin_url( $settings_link );

							} else {
								$settings_link = "?page={$this->unique}";
								$link_url      = admin_url( $settings_link );
							}


					}

					$settings_link_array[] = '<a href="' . $link_url . '">' . $link_text . '</a>';

				}

				return array_merge( $settings_link_array, $links );


			} // if ( is_array( $this->config['settings_link'] ) )

			// if nothing is returned so far, return original $links
			return $links;

		}

		/*
		 * Get default config for metabox
		 * @return array $default
		 */
		public function get_config_default_metabox() {

			$default = array(

				'title'      => '',
				'post_types' => array( 'post' ),
				'context'    => 'advanced',
				'priority'   => 'default',
				'capability' => 'edit_posts',
				'tabbed'     => true,
				'options'    => false,
				'multilang'  => false

			);

			return apply_filters( 'exopite_sof_filter_config_default_metabox_array', $default );
		}


		/*
		 * Get default config for group type field
		 * @return array $default
		 */
		public function get_config_default_group_options() {

			$default = array(
				//
				'repeater'        => true,
				'accordion'       => true,
				'button_title'    => __( 'Add New', 'exopite-options-framework' ),
				'accordion_title' => __( 'Accordion Title', 'exopite-options-framework' ),
				'limit'           => 50,
				'sortable'        => true,
			);

			return apply_filters( 'exopite_sof_filter_config_default_group_array', $default );
		}

		/*
		 * Get default config for menu
		 * @return array $default
		 */
		public function get_config_default_menu() {

			$default = array(
				//
				'parent'        => 'options-general.php',
				'menu'          => 'plugins.php', // For backward compatibility
				'menu_title'    => __( 'Plugin Options', 'exopite-options-framework' ),
				// Required for submenu
				'submenu'       => false,
				//The name of this page
				'title'         => __( 'Plugin Options', 'exopite-options-framework' ),
//				'menu_title'    => __( 'Plugin Options', 'exopite-options-framework' ),
				// The capability needed to view the page
				'capability'    => 'manage_options',
				'settings_link' => true,
				'tabbed'        => true,
				'position'      => 100,
				'icon'          => '',
				'multilang'     => true,
				'options'       => false
			);

			return apply_filters( 'exopite_sof_filter_config_default_menu_array', $default );
		}

		/* Create a meta box for our custom fields */
		public function add_meta_box() {

			add_meta_box(
				$this->unique,
				$this->config['title'],
				array( $this, 'display_page' ),
				$this->config['post_types'],
				$this->config['context'],
				$this->config['priority']
			);

		}

		/**
		 * Register settings for plugin option page with a callback to save
		 */
		public function register_setting() {

			register_setting( $this->unique, $this->unique, array( $this, 'save' ) );

		}

		/**
		 * Register plugin option page
		 */
		public function add_admin_menu() {

//			$default = array(
//				'title'      => 'Options',
//				'capability' => 'manage_options',
//				'tabbed'     => true,
//
//			);


			// Is it a main menu or sub_menu
			if ( ! (bool) $this->config['submenu'] ) {

//				$default['icon']     = '';
//				$default['position'] = 100;
//				$default['menu']     = 'Plugin menu';

//				$this->config = wp_parse_args( $this->config, $default );

				$menu = add_menu_page(
					$this->config['title'],
					$this->config['menu_title'],
					$this->config['capability'],
					$this->unique, //slug
					array( $this, 'display_page' ),
					$this->config['icon'],
					$this->config['position']
				);

			} else {

//				$this->config = wp_parse_args( $this->config, $default );

				$submenu = add_submenu_page(
					$this->config['parent'],
					$this->config['title'],
					$this->config['title'],
					$this->config['capability'],
					$this->unique, // slug
					array( $this, 'display_page' )
				);

			}

		}

		/**
		 * Load scripts and styles
		 *
		 * @hooked  admin_enqueue_scripts
		 *
		 * @param string hook name
		 */
		public function load_scripts_styles( $hook ) {

			/**
			 * Ideas:
			 * - split JS to (metabox and menu) and menu -> not all scripts are required for metabox
			 * - proper versioning based on file timestamp?
			 */

			// return if not admin
			if ( ! is_admin() ) {
				return;
			}

			/*
			 * Load Scripts for only Menu page
			 */
			if ( $this->is_menu_page_loaded() ):
				// TODO: Shift Scripts from all $type to this section

			endif; //$this->is_menu_page_loaded()


			/*
			 * Load Scripts for metabox that have enabled metabox using Exopite framework
			 */
			if ( $this->is_metabox_enabled_post_type() ):
				// TODO: Shift Scripts from all $type to this section

			endif; // $this->is_metabox_enabled_post_type()


			/*
			 * Load Scripts shared by all $type
			 */
			if ( $this->is_menu_page_loaded() || $this->is_metabox_enabled_post_type() ) :

				$url  = $this->get_url( $this->dirname );
				$base = trailingslashit( join( '/', array( $url, 'assets' ) ) );

				if ( ! wp_style_is( 'font-awesome' ) || ! wp_style_is( 'font-awesome-470' ) || ! wp_style_is( 'FontAwesome' ) ) {

					/* Get font awsome */
					//TODO: Remove CDN for fontawsome
					// For this need font files too
					// wp_register_style( 'font-awesome-470', "//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css", false, '470' );
					// wp_enqueue_style( 'font-awesome-470' );
					wp_enqueue_style( 'font-awesome-470', $base . 'font-awesome-4.7.0/font-awesome.min.css', array(), $this->version, 'all' );

				}

				//TODO: Remove CDN for jQuery
				wp_enqueue_style( 'jquery-ui', $base . 'jquery-ui.css', array(), '1.8.24', 'all' );
				wp_enqueue_style( 'exopite-simple-options-framework', $base . 'styles.css', array(), $this->version, 'all' );

				// Add jQuery form scripts for menu options AJAX save
				wp_enqueue_script( 'jquery-form' );

				// Add the date picker script
				wp_enqueue_script( 'jquery-ui-datepicker' );

				wp_enqueue_script( 'jquery-ui-sortable' );

				$scripts_styles = array(
					array(
						'name' => 'jquery-interdependencies',
						'fn'   => 'jquery.interdependencies.min.js',
						'dep'  => array(
							'jquery',
							'jquery-ui-datepicker',
							'wp-color-picker'
						),
					),
					array(
						'name' => 'exopite-simple-options-framework-js',
						'fn'   => 'scripts.min.js',
						'dep'  => array(
							'jquery',
							'jquery-ui-datepicker',
							'wp-color-picker',
							'jquery-interdependencies'
						),
					),
				);

				foreach ( $scripts_styles as $item ) {
					wp_enqueue_script( $item['name'], $base . $item['fn'], $item['dep'], $this->version, true );
				}

				// wp_enqueue_script( 'jquery-interdependencies', $base . 'jquery.interdependencies.min.js', array(
				// 	'jquery',
				// 	'jquery-ui-datepicker',
				// 	'wp-color-picker'
				// ), $this->version, true );

				/**
				 * Load classes and enqueue class scripts
				 * with this, only enqueue scripts if class/field is used
				 */
				$this->include_enqueue_field_classes();

				// wp_enqueue_script( 'exopite-simple-options-framework-js', $base . 'scripts.min.js', array(
				// 	'jquery',
				// 	'jquery-ui-datepicker',
				// 	'wp-color-picker',
				// 	'jquery-interdependencies'
				// ), $this->version, true );

			endif; //$this->is_menu_page_loaded() || $this->is_metabox_enabled_post_type()


		}

		/*
		 * Return the array of languages except current language
		 *
		 * @return array $languages_except_current
		 *
		 */
		public function languages_except_current_language() {

			$all_languages = $this->languages;

			if ( empty( $all_languages ) ) {
				return $all_languages;
			}

			$languages_except_current = array_diff( $all_languages, array( $this->lang_current ) );

			unset( $all_languages );

			return $languages_except_current;

		}

		/*
		 * Save options or metabox to meta
		 *
		 * @return mixed
		 *
		 * @hooked  'save_post' for metabox
		 * @hooked  register_setting() for menu
		 *
		 */
		public function save( $posted_data ) {

			// $this->write_log( 'posted_data', var_export( $_POST, true ) . PHP_EOL . PHP_EOL );
			// $this->write_log( 'posted_data', var_export( $this->unique, true ) . PHP_EOL . PHP_EOL );

			// Is user has ability to save?
			if ( ! current_user_can( $this->config['capability'] ) ) {
				return null;
			}

			//TODO Verify nonce

			$valid   = array();
			$post_id = null;

			/*
			 * @var $section_fields_with_values it will hold the sanitized fields => value
			 */
			$section_fields_with_values = array();

			// Specific to Menu Options
			if ( $this->is_menu() ) {

				/**
				 * Import options should not be checked.
				 */
				if ( isset( $_POST['action'] ) && sanitize_key( $_POST['action'] ) === 'exopite-sof-import-options' ) {
					return apply_filters( 'exopite_sof_import_options', $posted_data, $this->unique );
				}

				// Preserve values start with "_".
				$options = get_option( $this->unique );
				foreach ( $options as $key => $value ) {

					if ( substr( $key, 0, 1 ) === '_' ) {

						$valid[ $key ] = $value;

					}

				}

			}

			// Specific to Metabox
			if ( $this->is_metabox() ) {

//				$this->write_log( 'post_id', var_export( $posted_data, true ) . PHP_EOL . PHP_EOL );

				// if this is metabox, $posted_data is post_id we are saving
				$post_id = $posted_data;

				// Stop WP from clearing custom fields on autosave
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
					return null;
				}

				// Prevent quick edit from clearing custom fields
				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
					return null;
				}

				// If the post type is not in our post_type array, do nothing
				if ( ! in_array( get_post_type( $post_id ), $this->config['post_types'] ) ) {
					return null;
				}
				// Global $post is not required and should not be used as we already have the $post id in $posted_data
//				global $post;


			}

			/**
			 * This must be running on post meta too because of the qTranslate-X.
			 * Maybe check first if qTranslate-X is installed and active?
			 */
			// Preserve other language setting
			// Get current field value, make sure we are not override other language values
			if ( $this->is_multilang() ) {
				$other_languages = $this->languages_except_current_language();

				// TODO: Take care of group type as well, its hard to preserve their value
				foreach ( $other_languages as $language ) {
					if ( $this->is_metabox() ) {
						// required only for qTranslate-X
						$post_meta = get_post_meta( $post_id );
						if ( ! empty( $post_meta ) ) {
							$options_array_from_post_meta = maybe_unserialize( $post_meta[ $this->unique ][0] );
							if ( is_array( $options_array_from_post_meta ) ) {
								$section_fields_with_values[ $language ] = $options_array_from_post_meta[ $language ];
							}
						}
					}
					if ( $this->is_menu() ) {
						$section_fields_with_values[ $language ] = $this->db_options[ $language ];
					}
				}
			}

			/**
			 * Loop all fields (from $config fields array ) and update values from $_POST
			 *
			 * for both menu and meta
			 */
			$section_fields_current_lang = array();

			foreach ( $this->fields as $section ) {

				// Make sure we have $fields array and get sanitized Values
				if ( isset( $section['fields'] ) && is_array( $section['fields'] ) ) {

					$section_fields_current_lang = array_merge(
						$section_fields_current_lang,  // Value we currently have in the array
						$this->get_sanitized_fields_values( $section['fields'], $posted_data ) // sanitized array we are getting
					);

				}

			}

			// update $section_fields_with_values with $section_fields_current_lang
			if ( $this->is_multilang() ) {
				$section_fields_with_values[ $this->lang_current ] = $section_fields_current_lang;
			} else {
				$section_fields_with_values = $section_fields_current_lang;
			}

			// TODO: remove one set from the following set of filters + actions
			/**
			 * The idea here is that, this hook run on both.
			 */
			do_action( 'exopite_sof_do_save_options', $section_fields_with_values, $this->unique, $post_id );
			$valid = apply_filters( 'exopite_sof_save_options', $section_fields_with_values, $this->unique, $post_id );

			if ( $this->is_menu() ) {

				// update $section_fields_with_values with $section_fields_current_lang
				// if ( $this->is_multilang() ) {
				// 	$section_fields_with_values[ $this->lang_current ] = $section_fields_current_lang;
				// } else {
				// 	$section_fields_with_values = $section_fields_current_lang;
				// }
				// These actions and filters only for options menu

				$valid = apply_filters( 'exopite_sof_save_menu_options', $section_fields_with_values, $this->unique );
				do_action( 'exopite_sof_do_save_menu_options', $section_fields_with_values, $this->unique );

				return $valid;
			}

			if ( $this->is_metabox() ) {

				// When we click on "New Post" (CPT), then $post is not available, so we need to check if it is set
				/**
				 * NEED TESTING!
				 * I'm not sure about this, need check. I think post ID IS available because saving custom field
				 * should be after post is inserted.
				 */
				if ( isset( $post_id ) ) {

					$valid = apply_filters( 'exopite_sof_save_meta_options', $section_fields_with_values, $this->unique, $post_id );
					do_action( 'exopite_sof_do_save_meta_options', $section_fields_with_values, $this->unique, $post_id );
					// $valid = apply_filters( 'exopite_sof_save_meta_options', $section_fields_current_lang, $this->unique, $post_id );
					// do_action( 'exopite_sof_do_save_meta_options', $section_fields_current_lang, $this->unique, $post_id );
//
//
//					$this->write_log( 'posted_data', var_export( $section_fields_current_lang, true ) . PHP_EOL );

//                  TODO: Account for options = 'simple'
					if ( $this->is_options_simple() ) {

						foreach ( $valid as $key => $value ) {
//							$meta_key = $this->unique . '_' . $key;

							update_post_meta( $post_id, $key, $value );

						}
					} else {
						update_post_meta( $post_id, $this->unique, $valid );
					}

//					update_post_meta( $post_id, $this->unique, $valid );

				}

			}

			unset( $post_id, $valid, $posted_data, $section_fields_with_values, $section_fields_current_lang );

		}

		//DEGUB
		public function write_log( $type, $log_line ) {

			$hash        = '';
			$fn          = plugin_dir_path( __FILE__ ) . '/' . $type . '-' . $hash . '.log';
			$log_in_file = file_put_contents( $fn, date( 'Y-m-d H:i:s' ) . ' - ' . $log_line . PHP_EOL, FILE_APPEND );

		}


		public function get_sanitized_fields_values( $fields, $posted_data ) {
			$sanitized_fields_data = array();
			foreach ( $fields as $index => $field ) :


				$field_type = ( isset( $field['type'] ) ) ? $field['type'] : false;
				$field_id   = ( isset( $field['id'] ) ) ? $field['id'] : false;

				// if do not have $field_id or $field_type, we continue to next field
				if ( ! $field_id || ! $field_type ) {
					continue;
				}


				// if field is not a group

				if ( $field_type !== 'group' ) {

					$sanitized_fields_data[ $field['id'] ] = $this->get_sanitized_field_value_from_global_post( $field );

				} // ( $field_type !== 'group' )

				// If the field is group
				if ( $field_type === 'group' ) {

					$group = $field;

					$group_id = ( isset( $field['id'] ) ) ? $field['id'] : false;

					$group_fields = isset( $field['fields'] ) && is_array( $field['fields'] ) ? $field['fields'] : false;

					// We are not processing if group_id is not there
					if ( $group_id && $group_fields ):


						// Normalise the group options (so we dont need to check for isset()
						$default_group_options = $this->get_config_default_group_options();
						$group_options         = ( isset( $group['options'] ) ) ? $group['options'] : $default_group_options;
						$group['options']      = $group_options = wp_parse_args( $group_options, $default_group_options );

						$is_repeater = ( isset( $group['options']['repeater'] ) ) ? (bool) $group['options']['repeater'] : false;


						// If the group is NOT a repeater
						if ( ! $is_repeater ) :


							foreach ( $group_fields as $sub_field ) :

								$sub_field_id = ( isset( $sub_field['id'] ) ) ? $sub_field['id'] : false;


								$sanitized_fields_data[ $group_id ][ $sub_field_id ] = $this->get_sanitized_field_value_from_global_post( $sub_field, $group_id );;

//								$this->write_log( 'posted_data', var_export( $sanitized_fields_data, true ) . PHP_EOL . PHP_EOL );
							endforeach;


						endif; // ( ! $is_repeater )  // If the group is a repeater

						// If the group is a repeater
						if ( $is_repeater ):

							$repeater_count = 0;

							if ( $this->is_multilang() ) {
								// How many times $_POST has this

								if ( $this->is_menu() ) {
									$repeater_count = ( isset( $posted_data[ $this->lang_current ][ $group_id ] ) && is_array( $posted_data[ $this->lang_current ][ $group_id ] ) ) ? count( $posted_data[ $this->lang_current ][ $group_id ] ) : 0;
								}
								if ( $this->is_metabox() ) {
									$repeater_count = ( isset( $_POST[ $this->unique ][ $this->lang_current ][ $group_id ] ) && is_array( $_POST[ $this->unique ][ $this->lang_current ][ $group_id ] ) ) ? count( $_POST[ $this->unique ][ $this->lang_current ][ $group_id ] ) : 0;
								}
								/**
								 * Here you are assume multilang IS menu
								 * not multilang IS meta.
								 * This may incorrect.
								 */
								/**
								 * This must be running on post meta too because of the qTranslate-X.
								 * Maybe check first if qTranslate-X is installed and active?
								 */
								// $repeater_count = ( isset( $posted_data[ $this->lang_current ][ $group_id ] ) && is_array( $posted_data[ $this->lang_current ][ $group_id ] ) ) ? count( $posted_data[ $this->lang_current ][ $group_id ] ) : 0;

								// // This code is duplicated
								// for ( $i = 0; $i < $repeater_count; $i ++ ) {

								// 	foreach ( $group_fields as $sub_field ) :

								// 		$sub_field_id = ( isset( $sub_field['id'] ) ) ? $sub_field['id'] : false;

								// 		// sub field id is required
								// 		if ( ! $sub_field_id ) {
								// 			continue;
								// 		}

								// 		$sanitized_fields_data[ $group_id ][ $i ][ $sub_field_id ] = $this->get_sanitized_field_value_from_global_post( $sub_field, $group_id, $i );

								// 	endforeach; // $group_fields
								// }
							} // $this->is_multilang()

							/**
							 * ToDos:
							 * - On simple options NEED to disable multilang! (if meta)
							 */
							if ( ! $this->is_multilang() ) {


								if ( $this->is_options_simple() ) {

									$repeater_count = ( isset( $_POST[ $group_id ] ) && is_array( $_POST[ $group_id ] ) ) ? count( $_POST[ $group_id ] ) : 0;

								} else {

									if ( $this->is_menu() ) {
										$repeater_count = ( isset( $posted_data[ $group_id ] ) && is_array( $posted_data[ $group_id ] ) ) ? count( $posted_data[ $group_id ] ) : 0;
									}
									if ( $this->is_metabox() ) {
										$repeater_count = ( isset( $_POST[ $this->unique ][ $group_id ] ) && is_array( $_POST[ $this->unique ][ $group_id ] ) ) ? count( $_POST[ $this->unique ][ $group_id ] ) : 0;
									}
									/**
									 * Here you are assume multilang IS menu
									 * not multilang IS meta.
									 * This may incorrect.
									 */
									/**
									 * This must be running on post meta too because of the qTranslate-X.
									 * Maybe check first if qTranslate-X is installed and active?
									 */
									// $repeater_count = ( isset( $_POST[ $this->unique ][ $group_id ] ) && is_array( $_POST[ $this->unique ][ $group_id ] ) ) ? count( $_POST[ $this->unique ][ $group_id ] ) : 0;

								}

								// // This code is duplicated
								// for ( $i = 0; $i < $repeater_count; $i ++ ) {

								// 	foreach ( $group_fields as $sub_field ) :

								// 		$sub_field_id = ( isset( $sub_field['id'] ) ) ? $sub_field['id'] : false;

								// 		// sub field id is required
								// 		if ( ! $sub_field_id ) {
								// 			continue;
								// 		}

								// 		$sanitized_fields_data[ $group_id ][ $i ][ $sub_field_id ] = $this->get_sanitized_field_value_from_global_post( $sub_field, $group_id, $i );

								// 	endforeach; // $group_fields
								// }
							}


							// This code is run anyway. No need to duplicate.
							for ( $i = 0; $i < $repeater_count; $i ++ ) {

								foreach ( $group_fields as $sub_field ) :

									$sub_field_id = ( isset( $sub_field['id'] ) ) ? $sub_field['id'] : false;

									// sub field id is required
									if ( ! $sub_field_id ) {
										continue;
									}

									$sanitized_fields_data[ $group_id ][ $i ][ $sub_field_id ] = $this->get_sanitized_field_value_from_global_post( $sub_field, $group_id, $i );

								endforeach; // $group_fields
							}

						endif; // ( $is_repeater )

					endif; // ( ! $group_id )

				} // ( $field_type === 'group' )

			endforeach; // $fields array

			return $sanitized_fields_data;

		} //


		/*
		 * Get the clean value from single field
		 *
		 * @param array  $field
		 * @return mixed $clean_value
		 */
		public function get_sanitized_field_value_from_global_post( $field, $group_id = null, $group_field_index = null ) {


			if ( ! isset( $field['id'] ) || ! isset( $field['type'] ) ) {
				return '';
			} else {
				$field_id   = $field['id'];
				$field_type = $field['type'];
			}

			// Initialize array
			$keys_array = array();

			// Adding elements to $keys_array
			// order matters!!!
			if ( ! $this->is_options_simple() ) {
				$keys_array[] = $this->unique;
			}

			if ( $this->is_multilang() ) {
				$keys_array[] = $this->lang_current;
			}

			if ( $group_id !== null ) {
				$keys_array[] = $group_id;
			}

			if ( $group_field_index !== null ) {
				$keys_array[] = $group_field_index;
			}

			$keys_array[] = $field_id;


			// Get $dirty_value from global $_POST
			$dirty_value = $this->get_array_nested_value( $_POST, $keys_array, '' );

			// TODO: account for $options type 'simple'
//			if ( $this->config['options'] === 'simple' ) {
//							$value = ( isset( $_POST[ $field['id'] ] ) ) ? $_POST[ $field['id'] ] : '';
//						} else {
//							$value = ( isset( $_POST[ $this->unique ][ $field['id'] ] ) ) ? $_POST[ $this->unique ][ $field['id'] ] : '';
//						}
//			}


			$clean_value = $this->sanitize( $field, $dirty_value );

			return $clean_value;

		}

		/*
		 * Pass the field and value to run sanitization by type of field
		 *
		 * @param array $field
		 * @param mixed $value
		 *
		 * $return mixed $value after sanitization
		 */
		public function get_sanitized_field_value_by_type( $field, $value ) {

			$field_type = ( isset( $field['type'] ) ) ? $field['type'] : '';

			switch ( $field_type ) {

				case 'panel':
					// no break
				case 'notice':
					// no break
				case 'image_select':
					// no break
				case 'select':
					// no break
				case 'tap_list':
					// no break
				case 'editor':
					// no break
				case 'textarea':
					// HTML and array are allowed
					$value = esc_textarea( $value );
					// $value = wp_kses_post( $value );
					break;

				case 'ace_editor':
					// TODO:
					// This is not a good idea, here we can save code
					// like JS or CSS or even PHP
					// depense of the use of the field
					// this can be like "paste your google analytics code here"
					// wp_kses_post will remove this.
					$value = esc_textarea( $value );
					// $value = wp_kses_post( $value );
					break;

				case 'switcher':
					// no break
				case 'checkbox':
					$value = ( $value === 'yes' ) ? 'yes' : 'no';
					break;

				case 'range':
					// no break
				case 'number':
					if ( isset( $field['min'] ) && $value < $field['min'] ) {
						$value = $field['min'];
					}
					if ( isset( $field['max'] ) && $value > $field['max'] ) {
						$value = $field['max'];
					}
					$value = ( is_numeric( $value ) ) ? $value : 0;

					break;

				default:
					$value = ( ! empty( $value ) ) ? sanitize_text_field( $value ) : '';

			}

			return $value;

		}


		/**
		 * Validate and sanitize values
		 *
		 * @param $field
		 * @param $value
		 *
		 * @return mixed
		 */
		public function sanitize( $field, $dirty_value ) {


			$dirty_value = isset( $dirty_value ) ? $dirty_value : '';


			// if $config array has sanitize function, then call it
			if ( isset( $field['sanitize'] ) && ! empty( $field['sanitize'] ) && function_exists( $field['sanitize'] ) ) {


				// TODO: in future, we can allow for sanitize functions array as well
				$sanitize_func_name = $field['sanitize'];

				$clean_value = call_user_func( $sanitize_func_name, $dirty_value );

			} else {

				// if $config array doe not have sanitize function, do sanitize on field type basis
				$clean_value = $this->get_sanitized_field_value_by_type( $field, $dirty_value );

			}

			return apply_filters( 'exopite_sof_sanitize_value', $clean_value, $this->config );

		}

		/**
		 * Loop fileds based on field from user
		 *
		 * @param $callbacks
		 */
		public function loop_fields( $callbacks ) {

			foreach ( $this->fields as $section ) {

				// before
				if ( $callbacks['before'] ) {
					call_user_func( array( $this, $callbacks['before'] ), $section );
				}

				if ( ! isset( $section['fields'] ) || ! is_array( $section['fields'] ) ) {
					continue;
				}

				foreach ( $section['fields'] as $field ) {

					// If has subfields
					if ( $callbacks['main'] == 'include_enqueue_field_class' && isset( $field['fields'] ) ) {

						foreach ( $field['fields'] as $subfield ) {

							if ( $callbacks['main'] ) {
								call_user_func( array( $this, $callbacks['main'] ), $subfield );
							}

						}

					}

					if ( $callbacks['main'] ) {
						call_user_func( array( $this, $callbacks['main'] ), $field );
					}

					// main


				}

				// after
				if ( $callbacks['after'] ) {
					call_user_func( array( $this, $callbacks['after'] ) );
				}
			}

		}

		/**
		 * Loop and add callback to include and enqueue
		 */
		public function include_enqueue_field_classes() {

			$callbacks = array(
				'before' => false,
				'main'   => 'include_enqueue_field_class',
				'after'  => false
			);

			$this->loop_fields( $callbacks );

		}

		/**
		 * Include field classes
		 * and enqueue they scripts
		 */
		public function include_enqueue_field_class( $field ) {

			$class = 'Exopite_Simple_Options_Framework_Field_' . $field['type'];

			if ( ! class_exists( $class ) ) {

				$field_filename = $this->locate_template( $field['type'] );

				if ( file_exists( $field_filename ) ) {

					require_once join( DIRECTORY_SEPARATOR, array(
						$this->dirname,
						'fields',
						$field['type'] . '.php'
					) );

				}

			}

			if ( class_exists( $class ) ) {


				if ( class_exists( $class ) && method_exists( $class, 'enqueue' ) ) {

					$args = array(
						'plugin_sof_url'  => plugin_dir_url( __FILE__ ),
						'plugin_sof_path' => plugin_dir_path( __FILE__ ),
						'field'           => $field,
					);

					$class::enqueue( $args );

				}

			}

		}

		/**
		 * Generate files
		 *
		 * @param  array $field field args
		 *
		 * @echo string   generated HTML for the field
		 */
		public function add_field( $field, $value = null ) {


			do_action( 'exopite_sof_before_generate_field', $field, $this->config );
			do_action( 'exopite_sof_before_add_field', $field, $this->config );

			$output     = '';
			$class      = 'Exopite_Simple_Options_Framework_Field_' . $field['type'];
			$depend     = '';
			$wrap_class = ( ! empty( $field['wrap_class'] ) ) ? ' ' . $field['wrap_class'] : '';
			$hidden     = ( $field['type'] == 'hidden' ) ? ' hidden' : '';
			$sub        = ( ! empty( $field['sub'] ) ) ? 'sub-' : '';

			if ( ! empty( $field['dependency'] ) ) {
				$hidden = ' hidden';
				$depend .= ' data-' . $sub . 'controller="' . $field['dependency'][0] . '"';
				$depend .= ' data-' . $sub . 'condition="' . $field['dependency'][1] . '"';
				$depend .= ' data-' . $sub . 'value="' . $field['dependency'][2] . '"';
			}

			$output .= '<div class="exopite-sof-field exopite-sof-field-' . $field['type'] . $wrap_class . $hidden . '"' . $depend . '>';

			if ( ! empty( $field['title'] ) ) {

				$output .= '<h4 class="exopite-sof-title">';

				$output .= $field['title'];

				if ( ! empty( $field['description'] ) ) {
					$output .= '<p class="exopite-sof-description">' . $field['description'] . '</p>';
				}

				$output .= '</h4>'; // exopite-sof-title
				$output .= '<div class="exopite-sof-fieldset">';
			}

			if ( class_exists( $class ) ) {

				if ( empty( $value ) ) {


					if ( ( isset( $field['sub'] ) && ! empty( $field['sub'] ) ) ) {

						$value = ( isset( $field['id'] ) && isset( $this->db_options[ $this->lang_current ][ $field['id'] ] ) ) ? $this->db_options[ $this->lang_current ][ $field['id'] ] : null;

					}


					if ( $this->is_menu() ) {

						if ( $this->is_multilang() ) {
							$value = ( isset( $field['id'] ) && isset( $this->db_options[ $this->lang_current ][ $field['id'] ] ) ) ? $this->db_options[ $this->lang_current ][ $field['id'] ] : null;
						} else {
							$value = ( isset( $field['id'] ) && isset( $this->db_options[ $field['id'] ] ) ) ? $this->db_options[ $field['id'] ] : null;
						}

					}

					if ( $this->is_metabox() ) {

//						TODO: Account for options= 'simple'

						if ( $this->is_options_simple() ) {


							$meta = get_post_meta( get_the_ID() );

							/*
							 * get_post_meta return empty on non existing meta
							 * we need to check if meta key is exist to return null,
							 * because default value can only display if value is null
							 * on empty vlaue - may saved by the user - should display empty.
							 */
							if ( isset( $field['id'] ) && isset( $meta[ $field['id'] ] ) ) {

								$value = array_shift( $meta[ $field['id'] ] );

							} else {
								$value = null;
							}

						} else {
							// This is working without options simple
							if ( $this->is_multilang() ) {
								$meta  = get_post_meta( get_the_ID(), $this->unique, true );
								$value = ( isset( $field['id'] ) && isset( $meta[ $this->lang_current ][ $field['id'] ] ) ) ? $meta[ $this->lang_current ][ $field['id'] ] : null;
							} else {
								$meta  = get_post_meta( get_the_ID(), $this->unique, true );
								$value = ( isset( $field['id'] ) && isset( $meta[ $field['id'] ] ) ) ? $meta[ $field['id'] ] : null;
							}
							// $meta  = get_post_meta( get_the_ID(), $this->unique, true );
							// $value = ( isset( $field['id'] ) && isset( $meta[ $field['id'] ] ) ) ? $meta[ $field['id'] ] : null;

						}


//						if ( $this->config['type'] == 'metabox' && isset( $this->config['options'] ) && $this->config['options'] == 'simple' ) {
//							$meta = get_post_meta( get_the_ID() );
//
//							/*
//							 * get_post_meta return empty on non existing meta
//							 * we need to check if meta key is exist to return null,
//							 * because default value can only display if value is null
//							 * on empty vlaue - may saved by the user - should display empty.
//							 */
//							if ( isset( $field['id'] ) && isset( $meta[ $field['id'] ] ) ) {
//								$value = get_post_meta( get_the_ID(), $field['id'], true );
//							} else {
//								$value = null;
//							}
//						} else {
//							$meta = get_post_meta( get_the_ID() , $this->unique, true );
//							$value = ( isset( $field['id'] ) && isset( $meta[ $field['id'] ] ) ) ? $meta[ $field['id'] ] : null;
//						}


						// $meta = get_post_meta( get_the_ID(), $field['id'], true );
						// $value = ( isset( $field['id'] ) ) ? $meta : null;
						// $value = ( isset( $field['id'] ) && isset( $this->db_options[$field['id']] ) ) ? $this->db_options[$field['id']] : null;
					}


				}


				ob_start();
				$element = new $class( $field, $value, $this->unique, $this->config, $this->multilang );
				$element->output();
				$output .= ob_get_clean();

			} else {

				$output .= '<div class="danger unknown">';
				$output .= esc_attr__( 'ERROR:', 'exopite-simple-options' ) . ' ';
				$output .= esc_attr__( 'This field class is not available!', 'exopite-simple-options' );
				$output .= ' <i>(' . $field['type'] . ')</i>';
				$output .= '</div>';

			}

			if ( ! empty( $field['title'] ) ) {
				$output .= '</div>';
			} // exopite-sof-fieldset

			$output .= '<div class="clearfix"></div>';

			$output .= '</div>'; // exopite-sof-field

			do_action( 'exopite_sof_after_generate_field', $field, $this->config );

			echo apply_filters( 'exopite_sof_add_field', $output, $field, $this->config );

			do_action( 'exopite_sof_after_add_field', $field, $this->config );

		}

		/**
		 * Display form and header for options page
		 * for metabox no need to do this.
		 */
		public function display_options_page_header() {

			echo '<form method="post" action="options.php" enctype="multipart/form-data" name="' . $this->unique . '" class="exopite-sof-form-js ' . $this->unique . '-form" data-save="' . esc_attr__( 'Saving...', 'exopite-simple-options' ) . '" data-saved="' . esc_attr__( 'Saved Successfully.', 'exopite-simple-options' ) . '">';

			settings_fields( $this->unique );
			do_settings_sections( $this->unique );
			$current_language_title = '';
			if ( $this->is_multilang() ) {
				$current_language_title = apply_filters( 'exopite_sof_title_language_notice', $this->lang_current );
				$current_language_title = ' [ ' . $current_language_title . ' ]';
			}


			echo '<header class="exopite-sof-header exopite-sof-header-js">';
			echo '<h1>' . $this->config['title'] . $current_language_title . '</h1>';

			echo '<fieldset><span class="exopite-sof-ajax-message"></span>';
			submit_button( esc_attr__( 'Save Settings', 'exopite-simple-options' ), 'primary ' . 'exopite-sof-submit-button-js', $this->unique . '-save', false, array() );
			echo '</fieldset>';
			echo '</header>';

		}

		/**
		 * Display form and footer for options page
		 * for metabox no need to do this.
		 */
		public function display_options_page_footer() {

			echo '<footer class="exopite-sof-footer-js exopite-sof-footer">';

			echo '<fieldset><span class="exopite-sof-ajax-message"></span>';
			submit_button( esc_attr__( 'Save Settings', 'exopite-simple-options' ), 'primary ' . 'exopite-sof-submit-button-js', '', false, array() );
			echo '</fieldset>';

			echo '</footer>';

			echo '</form>';

		}

		/**
		 * Display section header, only first is visible on start
		 */
		public function display_options_section_header( $section ) {

			$visibility = ' hide';
			if ( $section === reset( $this->fields ) ) {
				$visibility = '';
			}

			echo '<div class="exopite-sof-section exopite-sof-section-' . $section['name'] . $visibility . '">';

			if ( isset( $section['title'] ) && ! empty( $section['title'] ) ) {

				echo '<h2 class="exopite-sof-section-header"><span class="dashicons-before ' . $section['icon'] . '"></span>' . $section['title'] . '</h2>';


			}

		}

		/**
		 * Display section footer
		 */
		public function display_options_section_footer() {

			echo '</div>'; // exopite-sof-section

		}

		/**
		 * Display form form either options page or metabox
		 */
		public function display_page() {

			do_action( 'exopite_simple_options_framework_form_' . $this->config['type'] . '_before' );

			settings_errors();

			echo '<div class="exopite-sof-wrapper exopite-sof-wrapper-' . $this->config['type'] . ' ' . $this->unique . '-options">';

			switch ( $this->config['type'] ) {
				case 'menu':
					add_action( 'exopite_sof_display_page_header', array(
						$this,
						'display_options_page_header'
					), 10, 1 );
					do_action( 'exopite_sof_display_page_header', $this->config );
					break;

				case 'metabox':
					/**
					 * Get options
					 * Can not get options in __consturct, because there, the_ID is not yet available.
					 *
					 * Only if options is not simple, if yes, then value determinated when field are displayed.
					 * Simple options is stored az induvidual meta key, value pair, otherwise it is stored in an array.
					 *
					 * I implemented this option because it is possible to search in serialized (array) post meta:
					 * @link https://wordpress.stackexchange.com/questions/16709/meta-query-with-meta-values-as-serialize-arrays
					 * @link https://stackoverflow.com/questions/15056407/wordpress-search-serialized-meta-data-with-custom-query
					 * @link https://www.simonbattersby.com/blog/2013/03/querying-wordpress-serialized-custom-post-data/
					 *
					 * but there is no way to sort them with wp_query or SQL.
					 * @link https://wordpress.stackexchange.com/questions/87265/order-by-meta-value-serialized-array/87268#87268
					 * "Not in any reliable way. You can certainly ORDER BY that value but the sorting will use the whole serialized string,
					 * which will give * you technically accurate results but not the results you want. You can't extract part of the string
					 * for sorting within the query itself. Even if you wrote raw SQL, which would give you access to database functions like
					 * SUBSTRING, I can't think of a dependable way to do it. You'd need a MySQL function that would unserialize the value--
					 * you'd have to write it yourself.
					 * Basically, if you need to sort on a meta_value you can't store it serialized. Sorry."
					 *
					 * It is possible to get all required posts and store them in an array and then sort them as an array,
					 * but what if you want multiple keys/value pair to be sorted?
					 *
					 * UPDATE
					 * it is maybe possible:
					 * @link http://www.russellengland.com/2012/07/how-to-unserialize-data-using-mysql.html
					 * but it is waaay more complicated and less documented as meta query sort and search.
					 * It should be not an excuse to use it, but it is not as reliable as it should be.
					 *
					 * @link https://wpquestions.com/Order_by_meta_key_where_value_is_serialized/7908
					 * "...meta info serialized is not a good idea. But you really are going to lose the ability to query your
					 * data in any efficient manner when serializing entries into the WP database.
					 *
					 * The overall performance saving and gain you think you are achieving by serialization is not going to be noticeable to
					 * any major extent. You might obtain a slightly smaller database size but the cost of SQL transactions is going to be
					 * heavy if you ever query those fields and try to compare them in any useful, meaningful manner.
					 *
					 * Instead, save serialization for data that you do not intend to query in that nature, but instead would only access in
					 * a passive fashion by the direct WP API call get_post_meta() - from that function you can unpack a serialized entry
					 * to access its array properties too."
					 */
					if ( $this->config['type'] == 'metabox' && ( ! isset( $this->config['options'] ) || $this->config['options'] != 'simple' ) ) {
						$meta_options     = get_post_meta( get_the_ID(), $this->unique, true );
						$this->db_options = apply_filters( 'exopite-simple-options-framework-meta-get-options', $meta_options, $this->unique, get_the_ID() );
					}

					if ( $this->debug ) {
						echo '<pre>POST_META<br>';

						$meta_options = get_post_meta( get_the_ID() );
//						var_export( get_post_meta( get_the_ID() ) );
						var_export( $meta_options );
						echo '</pre>';
					}

					break;
			}

			if ( isset( $this->config['multilang'] ) && is_array( $this->config['multilang'] ) ) {

				switch ( $this->config['type'] ) {
					case 'menu':
						$current_language = $this->config['multilang']['current'];
						break;

					case 'metabox':
						// Pages/Posts can not have "All languages" displayed, then default will be displayed
						$current_language = ( $this->config['multilang']['current'] == 'all' ) ? $this->config['multilang']['default'] : $this->config['multilang']['current'];
						break;
				}

				$current_language = 'en';
				/**
				 * Current language need to pass to save function, if "All languages" seleted, WPML report default
				 * on save hook.
				 */
				echo '<input type="hidden" name="_language" value="' . $current_language . '">';
			}

			$sections = 0;

			foreach ( $this->fields as $section ) {
				$sections ++;
			}

			$tabbed = ( $sections > 1 && $this->config['tabbed'] ) ? ' exopite-sof-content-nav exopite-sof-content-js' : '';


			// echo '<pre>';
			// var_export( $this->config );
			// echo '</pre>';

			if ( $this->debug ) {

				echo '<pre>MULTILANG<br>';
				var_export( $this->config['multilang'] );
				echo '</pre>';

				echo '<pre>DB_OPTIONS<br>';
				var_export( $this->db_options );
				echo '</pre>';

				echo '<pre>SIMPLE:<br>';
				var_export( $this->is_options_simple );
				echo '</pre>';
			}
			/**
			 * Generate fields
			 */
			// Generate tab navigation
			echo '<div class="exopite-sof-content' . $tabbed . '">';

			if ( ! empty( $tabbed ) ) {

				echo '<div class="exopite-sof-nav"><ul class="exopite-sof-nav-list">';
				foreach ( $this->fields as $section ) {
					$active = '';
					if ( $section === reset( $this->fields ) ) {
						$active = ' active';
					}
//
//					echo "<pre>";
//					var_dump( $section);
//					echo "</pre>";

					$depend = '';
					$hidden = '';

					// Dependency for tabs too
					if ( ! empty( $section['dependency'] ) ) {
						$hidden = ' hidden';
						$depend = ' data-controller="' . $section['dependency'][0] . '"';
						$depend .= ' data-condition="' . $section['dependency'][1] . '"';
						$depend .= ' data-value="' . $section['dependency'][2] . '"';
					}

					echo '<li  class="exopite-sof-nav-list-item' . $active . $hidden . '"' . $depend . ' data-section="' . $section['name'] . '">';
					if ( strpos( $section['icon'], 'dashicon' ) !== false ) {
						echo '<span class="dashicons-before ' . $section['icon'] . '"></span>';
					} elseif ( strpos( $section['icon'], 'fa' ) !== false ) {
						echo '<span class="fa-before ' . $section['icon'] . '" aria-hidden="true"></span>';
					}
					echo $section['title'];
					echo '</li>';

				}


				echo '</ul></div>';

			}

			echo '<div class="exopite-sof-sections">';

			// Generate fields
			$callbacks = array(
				'before' => 'display_options_section_header',
				'main'   => 'add_field',
				'after'  => 'display_options_section_footer'
			);

			$this->loop_fields( $callbacks );

			echo '</div>'; // sections
			echo '</div>'; // content
			if ( $this->config['type'] == 'menu' ) {

				add_action( 'exopite_sof_display_page_footer', array(
					$this,
					'display_options_page_footer'
				), 10, 1 );
				do_action( 'exopite_sof_display_page_footer', $this->config );

			}

			echo '</div>';

			do_action( 'exopite_sof_form_' . $this->config['type'] . '_after' );

		} // display_page()


	} //class

endif;
