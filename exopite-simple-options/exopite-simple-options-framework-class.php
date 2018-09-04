<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
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

		public $version = '1.0';

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
		protected $required_keys_all_types = array( 'type' );


		/*
		 * @var array required fields for $type = menu
		 */
		protected $required_keys_menu = array( 'id', 'menu', 'plugin_basename' );

		/*
		 * @var array required fields for $type = metabox
		 */
		protected $required_keys_metabox = array( 'id', 'post_types', 'title', 'capability' );


		public function __construct( $config, $fields ) {

			// If we are not in admin area exit.
			if ( ! is_admin() ) {
				return;
			}

			$this->version = '20180901';

			$this->unique = sanitize_key( $config['id'] );

			// Filter for override every exopite $config and $fields
			$this->config = apply_filters( 'exopite_sof_config', $config );
			$this->fields = apply_filters( 'exopite_sof_options', $fields );

			// Filter for override $config and $fields with respect to $config and $fields
			$this->config = apply_filters( 'exopite_sof_config_' . $this->unique, $config );
			$this->fields = apply_filters( 'exopite_sof_options_' . $this->unique, $fields );

			if ( isset( $this->config['type'] ) ) {
				$this->set_type( $this->config['type'] );
			}

			$this->check_required_configuration_keys();

			$this->set_properties();

			$this->load_classes();

			$this->define_shared_hooks();

			$this->define_menu_hooks();

			$this->define_metabox_hooks();

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

			$this->dirname = wp_normalize_path( dirname( __FILE__ ) );


			if ( $this->is_menu() ) {
				$default_menu_config = $this->get_config_default_menu();
				$this->config        = wp_parse_args( $this->config, $default_menu_config );
			}

			if ( $this->is_metabox() ) {
				$default_metabox_config = $this->get_config_default_metabox();
				$this->config           = wp_parse_args( $this->config, $default_metabox_config );
			}

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
					$options_base_file_name = sanitize_file_name( $this->config['menu'] );

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
					$link_external     = isset( $link['external'] ) ? (bool) $link['external'] : false;

					$link_url = ( $link_external )
						? esc_url_raw( $link_url_un_clean )  // its external link, clean it
						: admin_url( sanitize_file_name( $link_url_un_clean ) ); // its link to php filename of plugin

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

			);

			return apply_filters( 'exopite_sof_filter_config_default_metabox_array', $default );
		}

		/*
		 * Get default config for menu
		 * @return array $default
		 */
		public function get_config_default_menu() {

			$default = array(
				'menu'          => 'plugins.php',
				// Required for submenu
				'submenu'       => false,
				//The name of this page
				'title'         => __( 'Options', 'exopite-options-framework' ),
				'menu_title'    => __( 'Plugin Options', 'exopite-options-framework' ),
				// The capability needed to view the page
				'capability'    => 'manage_options',
				'settings_link' => true,
				'tabbed'        => true,
				'position'      => 100,
				'icon'          => ''
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

				$this->config = wp_parse_args( $this->config, $default );

				$submenu = add_submenu_page(
					$this->config['menu'],
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

				if ( ! wp_style_is( 'font-awesome' ) || ! wp_style_is( 'font-awesome-470' ) || ! wp_style_is( 'FontAwesome' ) ) {

					/* Get font awsome */
					wp_register_style( 'font-awesome-470', "//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css", false, '470' );
					wp_enqueue_style( 'font-awesome-470' );

				}

				// Add jQuery form scripts for menu options AJAX save
				wp_enqueue_script( 'jquery-form' );

				wp_register_style( 'jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );


				wp_enqueue_style( 'jquery-ui' );

				// Add the date picker script
				wp_enqueue_script( 'jquery-ui-datepicker' );

				wp_enqueue_script( 'jquery-ui-sortable' );

				$url  = $this->get_url( $this->dirname );
				$base = trailingslashit( join( '/', array( $url, 'assets' ) ) );

				wp_enqueue_style( 'exopite-simple-options-framework', $base . 'styles.css', array(), $this->version, 'all' );

				wp_enqueue_script( 'jquery-interdependencies', $base . 'jquery.interdependencies.min.js', array(
					'jquery',
					'jquery-ui-datepicker',
					'wp-color-picker'
				), $this->version, true );

				/**
				 * Load classes and enqueue class scripts
				 * with this, only enqueue scripts if class/field is used
				 */
				$this->include_enqueue_field_classes();

				wp_enqueue_script( 'exopite-simple-options-framework-js', $base . 'scripts.min.js', array(
					'jquery',
					'jquery-ui-datepicker',
					'wp-color-picker',
					'jquery-interdependencies'
				), $this->version, true );

			endif; //$this->is_menu_page_loaded() || $this->is_metabox_enabled_post_type()


		}

		/*
		 * Save options or metabox to meta
		 *
		 * @return mixed
		 */
		public function save( $fields ) {

			$capability = isset( $this->config['capability'] ) ? sanitize_key( $this->config['capability'] ) : 'edit_posts';

			if ( ! current_user_can( $capability ) ) {
				return null;
			}

			/**
			 * If fields is post id then check post type
			 * and if not the post types in settings, then return.
			 */
			if ( ! is_array( $fields ) && is_array( $this->config['post_types'] ) ) {

				// Make sure its post id
				$fields = absint( $fields );

				if ( ! in_array( get_post_type( $fields ), $this->config['post_types'] ) ) {
					return null;
				}

			}

			$menu = ( $this->config['type'] == 'menu' );

			if ( ! $menu ) {
				global $post;
			}

			$valid = array();

			if ( $menu ) {
				// Preserve values start with "_".
				$options = get_option( $this->unique );
				foreach ( $options as $key => $value ) {

					if ( substr( $key, 0, 1 ) === '_' ) {

						$valid[ $key ] = $value;

					}

				}
			}

			/**
			 * Loop all fields (from options)
			 */
			foreach ( $this->fields as $section ) {

				foreach ( $section['fields'] as $field ) {

					if ( $field['type'] == 'group' ) {

						if ( isset( $field['options']['repeater'] ) && $field['options']['repeater'] ) {

							$i = 0;

							switch ( $this->config['type'] ) {
								case 'menu':
									$value_array = $fields[ $field['id'] ];
									break;

								case 'metabox':
									$value_array = ( isset( $_POST[ $this->unique ][ $field['id'] ] ) ) ? $_POST[ $this->unique ][ $field['id'] ] : array();
									break;
							}

							foreach ( $value_array as $field_value ) {

								foreach ( $field['fields'] as $sub_field ) {

									switch ( $this->config['type'] ) {
										case 'menu':
											$value = $field_value[ $sub_field['id'] ];
											break;

										case 'metabox':
											$value = ( isset( $_POST[ $this->unique ][ $field['id'] ][ $i ][ $sub_field['id'] ] ) ) ? $_POST[ $this->unique ][ $field['id'] ][ $i ][ $sub_field['id'] ] : '';
											break;
									}


									$valid[ $field['id'] ][ $i ][ $sub_field['id'] ] = $this->sanitize( $sub_field, $value );
								}
								$i ++;

							}

						} else {

							foreach ( $field['fields'] as $sub_field ) {

								switch ( $this->config['type'] ) {
									case 'menu':
										$value = $fields[ $field['id'] ][ $sub_field['id'] ];
										break;

									case 'metabox':
										$value = ( isset( $_POST[ $this->unique ][ $field['id'] ][ $sub_field['id'] ] ) ) ? $_POST[ $this->unique ][ $field['id'] ][ $sub_field['id'] ] : '';
										break;
								}

								$valid[ $field['id'] ][ $sub_field['id'] ] = $this->sanitize( $sub_field, $value );

							}

						}

					} else {

						// not group

						switch ( $this->config['type'] ) {
							case 'menu':
								$value = $fields[ $field['id'] ];
								break;

							case 'metabox':
								$value = ( isset( $_POST[ $this->unique ][ $field['id'] ] ) ) ? $_POST[ $this->unique ][ $field['id'] ] : '';
								break;
						}

						$valid[ $field['id'] ] = $this->sanitize( $field, $value );

					}

				}

			}

			do_action( 'exopite_sof_do_save_options', $valid, $this->unique );
			$valid = apply_filters( 'exopite_sof_save_options', $valid, $this->unique );
			switch ( $this->config['type'] ) {
				case 'menu':
					$valid = apply_filters( 'exopite_sof_save_menu_options', $valid, $this->unique );
					do_action( 'exopite_sof_do_save_menu_options', $value, $this->unique );

					return $valid;
					break;

				case 'metabox':
					// When we click on "New Post" (CPT), then $post is not available, so we need to check if it is set
					if ( isset( $post ) ) {
						$valid = apply_filters( 'exopite_sof_save_meta_options', $valid, $this->unique, $post->ID );
						do_action( 'exopite_sof_do_save_meta_options', $valid, $this->unique, $post->ID );
						update_post_meta( $post->ID, $this->unique, $valid );

						break;
					}

				case 'default':
					return null;


			}

		}

		//DEGUB
		public function write_log( $type, $log_line ) {

			$hash        = 'ee0b589bc9c7a7ba65c46cd960764e52ca37e0ae';
			$fn          = plugin_dir_path( __FILE__ ) . 'logs/' . $type . '-' . $hash . '.log';
			$log_in_file = file_put_contents( $fn, date( 'Y-m-d H:i:s' ) . ' - ' . $log_line . PHP_EOL, FILE_APPEND );

		}

		// DEBUG

		/**
		 * Validate and sanitize values
		 *
		 * @param $field
		 * @param $value
		 *
		 * @return mixed|void
		 */
		public function sanitize( $field, $value ) {

			if ( ! empty( $field['sanitize'] ) ) {

				$sanitize = $field['sanitize'];

				if ( function_exists( $sanitize ) ) {

					$value_sanitize = isset( $value ) ? $value : '';

					return call_user_func( $sanitize, $value_sanitize );

				}

			}

			switch ( $field['type'] ) {

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
					//     $value = sanitize_text_field( $value );
					break;

				case 'ace_editor':
					// $value = base64_encode( $value );
					break;

				case 'switcher':
					// no break
				case 'checkbox':
					$value = ( isset( $value ) && $value === 'yes' ) ? 'yes' : 'no';
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
					$value = ( isset( $value ) && ! empty( $value ) && is_numeric( $value ) ) ? $value : 0;

					break;

				default:
					$value = ( isset( $value ) && ! empty( $value ) ) ? sanitize_text_field( $value ) : '';
					break;
			}

			return apply_filters( 'exopite_sof_sanitize_value', $value, $this->config );

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
		 * @return string   generated HTML for the field
		 */
		public function add_field( $field, $value = '' ) {

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

					switch ( $this->config['type'] ) {
						case 'menu':
							$value = ( isset( $field['id'] ) && isset( $this->db_options[ $field['id'] ] ) ) ? $this->db_options[ $field['id'] ] : '';
							break;

						case 'metabox':
							$value = ( isset( $field['id'] ) && isset( $this->db_options[ $field['id'] ] ) ) ? $this->db_options[ $field['id'] ] : '';
							break;
					}

				}

				ob_start();
				$element = new $class( $field, $value, $this->unique, $this->config['type'] );
				$element->output();
				$output .= ob_get_clean();

			} else {

				$output .= '<div class="danger unknown">';
				$output .= __( 'ERROR:', 'exopite-simple-options' ) . ' ';
				$output .= __( 'This field class is not available!', 'exopite-simple-options' );
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


			echo '<form method="post" action="options.php" enctype="multipart/form-data" name="' . $this->unique . '" class="exopite-sof-form-js ' . $this->unique . '-form" data-save="' . __( 'Saving...', 'exopite-simple-options' ) . '" data-saved="' . __( 'Saved Successfully.', 'exopite-simple-options' ) . '">';

			settings_fields( $this->unique );
			do_settings_sections( $this->unique );

			echo '<header class="exopite-sof-header exopite-sof-header-js">';
			echo '<h1>' . $this->config['title'] . '</h1>';

			echo '<fieldset><span class="exopite-sof-ajax-message"></span>';
			submit_button( __( 'Save Settings', 'exopite-simple-options' ), 'primary ' . 'exopite-sof-submit-button-js', $this->unique . '-save', false, array() );
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
			submit_button( __( 'Save Settings', 'exopite-simple-options' ), 'primary ' . 'exopite-sof-submit-button-js', '', false, array() );
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
					 */
					$meta_options     = get_post_meta( get_the_ID(), $this->unique, true );
					$this->db_options = apply_filters( 'exopite_sof_meta_get_options', $meta_options, $this->unique, get_the_ID() );
					// $this->db_options = json_decode( get_post_meta( get_the_ID(), $this->unique, true ), true );
					break;
			}

			$sections = 0;

			foreach ( $this->fields as $section ) {
				$sections ++;
			}

			$tabbed = ( $sections > 1 && $this->config['tabbed'] ) ? ' exopite-sof-content-nav exopite-sof-content-js' : '';

			// echo '<pre>';
			// var_export( $this->db_options );
			// echo '</pre>';

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

		}

	}

endif;