<?php
/**
 * Example usage of the Exopite Simple Options Framework.
 *
 * @package Exopite_Sof_Example
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Registers an options page with every field type, a metabox and a "simple"
 * metabox. Copy the parts you need into your own plugin or theme.
 *
 * How it works:
 * - Everything is created on "init" (admin only). All plugins are loaded
 *   before "init", so the framework class exists, whatever the load order is.
 * - If the framework plugin is not active, an admin notice is shown instead.
 */
class Exopite_Sof_Example {

	/**
	 * Option name of the options page (get_option( self::OPTION_ID )).
	 */
	const OPTION_ID = 'exopite_sof_example';

	/**
	 * Post meta key of the metabox (all fields in one array).
	 */
	const META_ID = 'exopite_sof_example_meta';

	/**
	 * Id of the "simple" metabox (one post meta key per field).
	 */
	const SIMPLE_META_ID = 'exopite_sof_example_simple';

	/**
	 * Register the WordPress hooks.
	 */
	public static function register_hooks() {
		add_action( 'init', array( __CLASS__, 'init' ) );
	}

	/**
	 * Create the options page and the metaboxes.
	 */
	public static function init() {

		// The framework only works in the admin area.
		if ( ! is_admin() ) {
			return;
		}

		// The framework plugin is not active.
		if ( ! class_exists( 'Exopite_Simple_Options_Framework' ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'framework_missing_notice' ) );
			return;
		}

		new Exopite_Simple_Options_Framework( self::options_page_config(), self::options_page_fields() );
		new Exopite_Simple_Options_Framework( self::metabox_config(), self::metabox_fields() );
		new Exopite_Simple_Options_Framework( self::simple_metabox_config(), self::simple_metabox_fields() );
	}

	/**
	 * Admin notice if the framework plugin is missing.
	 */
	public static function framework_missing_notice() {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'Exopite Simple Options Framework Example: please install and activate the "Exopite Simple Options Framework" plugin.', 'exopite-sof-example' ) . '</p></div>';
	}

	/*
	 * ------------------------------------------------------------------
	 * Options page
	 * ------------------------------------------------------------------
	 */

	/**
	 * Config of the options page (type "menu").
	 *
	 * @return array
	 */
	private static function options_page_config(): array {
		return array(
			'type'            => 'menu',               // Required: "menu" or "metabox".
			'id'              => self::OPTION_ID,      // Required: option name, get_option( id ).
			'menu_title'      => __( 'SOF Example', 'exopite-sof-example' ),        // Required for menus.
			'plugin_basename' => plugin_basename( dirname( __DIR__ ) . '/exopite-simple-options-example.php' ), // Required: adds "Settings" to the plugin list.
			'submenu'         => false,                // true: submenu of "parent".
			'parent'          => 'options-general.php',
			'title'           => __( 'Exopite Simple Options Framework Example', 'exopite-sof-example' ),
			'option_title'    => __( 'Exopite Simple Options Framework Example: every field type', 'exopite-sof-example' ),
			'capability'      => 'manage_options',
			'settings_link'   => true,
			'tabbed'          => true,
			'search_box'      => true,
			'icon'            => 'dashicons-admin-settings',
			'position'        => 99,
			'multilang'       => false,
		);
	}

	/**
	 * Sections and fields of the options page.
	 *
	 * @return array
	 */
	private static function options_page_fields(): array {

		$fields   = array();
		$fields[] = self::section_basic();
		$fields[] = self::section_choices();
		$fields[] = self::section_media();
		$fields[] = self::section_editors();
		$fields[] = self::section_layout();
		$fields[] = self::section_content();
		$fields[] = self::section_nested_menu();
		$fields[] = self::section_read_values();
		$fields[] = self::section_backup();

		return $fields;
	}

	/**
	 * Basic input fields.
	 *
	 * @return array
	 */
	private static function section_basic(): array {
		return array(
			'name'   => 'basic',
			'title'  => __( 'Basic', 'exopite-sof-example' ),
			'icon'   => 'dashicons-admin-generic',
			'fields' => array_merge(
				self::basic_fields( '' ),
				array(
					array(
						'id'    => 'password',
						'type'  => 'password',
						'title' => __( 'Password', 'exopite-sof-example' ),
					),
					array(
						'id'      => 'hidden',
						'type'    => 'hidden',
						'default' => 'hidden value',
					),
					array(
						'id'      => 'number',
						'type'    => 'number',
						'title'   => __( 'Number (0 to 100)', 'exopite-sof-example' ),
						'min'     => 0,
						'max'     => 100,
						'step'    => 1,
						'unit'    => 'px',
						'default' => 10,
					),
					array(
						'id'      => 'range',
						'type'    => 'range',
						'title'   => __( 'Range', 'exopite-sof-example' ),
						'min'     => 0,
						'max'     => 50,
						'step'    => 5,
						'unit'    => '%',
						'default' => 20,
					),
					array(
						'id'      => 'text_prepend',
						'type'    => 'text',
						'title'   => __( 'Text with prepend, append and help', 'exopite-sof-example' ),
						'prepend' => 'fa-font',
						'append'  => 'dashicons-admin-links',
						'help'    => __( 'Help text on mouse over', 'exopite-sof-example' ),
						'before'  => __( 'Text before the field', 'exopite-sof-example' ),
						'after'   => __( 'Text after the field', 'exopite-sof-example' ),
					),
					array(
						'id'       => 'text_sanitize',
						'type'     => 'text',
						'title'    => __( 'Own sanitize function', 'exopite-sof-example' ),
						'info'     => __( 'Saved with sanitize_email().', 'exopite-sof-example' ),
						'sanitize' => 'sanitize_email',
					),
				)
			),
		);
	}

	/**
	 * Fields used on the options page and in the metabox.
	 *
	 * @param string $prefix Field id prefix.
	 * @return array
	 */
	private static function basic_fields( string $prefix ): array {
		return array(
			array(
				'id'          => $prefix . 'text',
				'type'        => 'text',
				'title'       => __( 'Text', 'exopite-sof-example' ),
				'description' => __( 'Field description', 'exopite-sof-example' ),
				'default'     => 'Default text',
				'attributes'  => array( 'placeholder' => 'Placeholder' ),
			),
			array(
				'id'      => $prefix . 'textarea',
				'type'    => 'textarea',
				'title'   => __( 'Textarea', 'exopite-sof-example' ),
				'default' => "Line 1\nLine 2",
			),
			array(
				'id'    => $prefix . 'checkbox',
				'type'  => 'checkbox',
				'title' => __( 'Checkbox', 'exopite-sof-example' ),
				'label' => __( 'Check me', 'exopite-sof-example' ),
			),
			array(
				'id'    => $prefix . 'checkbox_fancy',
				'type'  => 'checkbox',
				'title' => __( 'Checkbox (fancy)', 'exopite-sof-example' ),
				'style' => 'fancy',
				'label' => __( 'Check me', 'exopite-sof-example' ),
			),
			array(
				'id'      => $prefix . 'select',
				'type'    => 'select',
				'title'   => __( 'Select', 'exopite-sof-example' ),
				'options' => array(
					'bmw'      => 'BMW',
					'mercedes' => 'Mercedes',
					'vw'       => 'Volkswagen',
				),
				'default' => 'mercedes',
			),
			array(
				'id'         => $prefix . 'ace',
				'type'       => 'ace_editor',
				'title'      => __( 'ACE code editor', 'exopite-sof-example' ),
				'options'    => array(
					'theme' => 'ace/theme/chrome',
					'mode'  => 'ace/mode/javascript',
				),
				'attributes' => array( 'style' => 'height: 200px; max-width: 700px;' ),
			),
		);
	}

	/**
	 * Choice fields and a field dependency.
	 *
	 * @return array
	 */
	private static function section_choices(): array {
		return array(
			'name'   => 'choices',
			'title'  => __( 'Choices', 'exopite-sof-example' ),
			'icon'   => 'fa fa-check-square-o',
			'fields' => array(
				array(
					'id'    => 'switcher',
					'type'  => 'switcher',
					'title' => __( 'Switcher (controls the next field)', 'exopite-sof-example' ),
					'label' => __( 'Show the dependent field', 'exopite-sof-example' ),
				),
				array(
					'id'         => 'dependent',
					'type'       => 'text',
					'title'      => __( 'Dependent text (visible if the switcher is on)', 'exopite-sof-example' ),
					'dependency' => array( 'switcher', '==', 'true' ),
				),
				array(
					'id'      => 'radio',
					'type'    => 'radio',
					'title'   => __( 'Radio', 'exopite-sof-example' ),
					'options' => array(
						'one' => 'One',
						'two' => 'Two',
					),
					'default' => 'one',
				),
				array(
					'id'      => 'radio_fancy',
					'type'    => 'radio',
					'title'   => __( 'Radio (fancy)', 'exopite-sof-example' ),
					'style'   => 'fancy',
					'options' => array(
						'a' => 'A',
						'b' => 'B',
					),
				),
				array(
					'id'      => 'button_bar',
					'type'    => 'button_bar',
					'title'   => __( 'Button bar', 'exopite-sof-example' ),
					'options' => array(
						'left'   => 'Left',
						'center' => 'Center',
						'right'  => 'Right',
					),
					'default' => 'center',
				),
				array(
					'id'      => 'tap_list',
					'type'    => 'tap_list',
					'title'   => __( 'Tap list (checkboxes)', 'exopite-sof-example' ),
					'options' => array(
						'red'   => 'Red',
						'green' => 'Green',
						'blue'  => 'Blue',
					),
				),
				array(
					'id'      => 'tap_list_radio',
					'type'    => 'tap_list',
					'title'   => __( 'Tap list (radio)', 'exopite-sof-example' ),
					'radio'   => true,
					'options' => array(
						'small' => 'Small',
						'large' => 'Large',
					),
				),
				array(
					'id'          => 'image_select',
					'type'        => 'image_select',
					'title'       => __( 'Image select (text)', 'exopite-sof-example' ),
					'text_select' => true,
					'radio'       => true,
					'options'     => array(
						'small'  => 'Small',
						'medium' => 'Medium',
						'large'  => 'Large',
					),
				),
				array(
					'id'         => 'select_multi',
					'type'       => 'select',
					'title'      => __( 'Select multiple (chosen)', 'exopite-sof-example' ),
					'class'      => 'chosen',
					'attributes' => array(
						'multiple' => 'multiple',
						'style'    => 'width: 300px;',
					),
					'options'    => array(
						'a' => 'Apple',
						'b' => 'Banana',
						'c' => 'Cherry',
					),
				),
				array(
					'id'             => 'select_pages',
					'type'           => 'select',
					'title'          => __( 'Select from pages (query)', 'exopite-sof-example' ),
					'query'          => array(
						'type' => 'pages',
						'args' => array(
							'orderby' => 'title',
							'order'   => 'ASC',
						),
					),
					'default_option' => __( '- select a page -', 'exopite-sof-example' ),
				),
			),
		);
	}

	/**
	 * Media and color fields.
	 *
	 * @return array
	 */
	private static function section_media(): array {
		return array(
			'name'   => 'media',
			'title'  => __( 'Media and colors', 'exopite-sof-example' ),
			'icon'   => 'dashicons-format-image',
			'fields' => array(
				array(
					'id'    => 'color',
					'type'  => 'color',
					'title' => __( 'Color (minicolors)', 'exopite-sof-example' ),
					'rgba'  => true,
				),
				array(
					'id'    => 'color_wp',
					'type'  => 'color_wp',
					'title' => __( 'Color (WordPress color picker, rgba)', 'exopite-sof-example' ),
					'rgba'  => true,
				),
				array(
					'id'     => 'date',
					'type'   => 'date',
					'title'  => __( 'Date', 'exopite-sof-example' ),
					'format' => 'yy-mm-dd',
					'picker' => 'datepicker',
				),
				array(
					'id'        => 'image',
					'type'      => 'image',
					'title'     => __( 'Image', 'exopite-sof-example' ),
					'add_title' => __( 'Choose image', 'exopite-sof-example' ),
				),
				array(
					'id'    => 'gallery',
					'type'  => 'gallery',
					'title' => __( 'Gallery', 'exopite-sof-example' ),
				),
				array(
					'id'      => 'video',
					'type'    => 'video',
					'title'   => __( 'Video (oEmbed)', 'exopite-sof-example' ),
					'default' => 'https://vimeo.com/76979871',
					'options' => array(
						'input'  => true,
						'oembed' => true,
					),
				),
				array(
					'id'      => 'upload',
					'type'    => 'upload',
					'title'   => __( 'Upload (Fine Uploader)', 'exopite-sof-example' ),
					'options' => array(
						'attach'                   => false,
						'filecount'                => 3,
						'delete-enabled'           => true,
						'delete-force-confirm'     => true,
						'retry-enable-auto'        => false,
						'retry-max-auto-attempts'  => 3,
						'retry-auto-attempt-delay' => 2,
						'auto-upload'              => false,
					),
				),
			),
		);
	}

	/**
	 * Editors and typography.
	 *
	 * @return array
	 */
	private static function section_editors(): array {
		return array(
			'name'   => 'editors',
			'title'  => __( 'Editors and typography', 'exopite-sof-example' ),
			'icon'   => 'dashicons-edit',
			'fields' => array(
				array(
					'id'     => 'editor_tinymce',
					'type'   => 'editor',
					'title'  => __( 'Editor (TinyMCE)', 'exopite-sof-example' ),
					'editor' => 'tinymce',
				),
				array(
					'id'     => 'editor_trumbowyg',
					'type'   => 'editor',
					'title'  => __( 'Editor (Trumbowyg)', 'exopite-sof-example' ),
					'editor' => 'trumbowyg',
				),
				array(
					'id'           => 'typography',
					'type'         => 'typography',
					'title'        => __( 'Typography', 'exopite-sof-example' ),
					'preview'      => true,
					'preview_text' => __( 'The quick brown fox jumps over the lazy dog', 'exopite-sof-example' ),
					'default'      => array(
						'family'  => 'Arial',
						'variant' => 'regular',
						'size'    => 16,
						'height'  => 20,
						'color'   => '#333333',
					),
				),
			),
		);
	}

	/**
	 * Group, tab, fieldset and accordion, also nested.
	 *
	 * @return array
	 */
	private static function section_layout(): array {
		return array(
			'name'   => 'layout',
			'title'  => __( 'Group, tab, fieldset, accordion', 'exopite-sof-example' ),
			'icon'   => 'dashicons-screenoptions',
			'fields' => array(
				array(
					'id'      => 'group_repeater',
					'type'    => 'group',
					'title'   => __( 'Group (repeater, sortable)', 'exopite-sof-example' ),
					'options' => array(
						'repeater'     => true,
						'accordion'    => true,
						'button_title' => __( 'Add new item', 'exopite-sof-example' ),
						'group_title'  => __( 'Item', 'exopite-sof-example' ),
						'limit'        => 5,
						'sortable'     => true,
					),
					'fields'  => array(
						array(
							'id'    => 'g_title',
							'type'  => 'text',
							'title' => __( 'Title', 'exopite-sof-example' ),
						),
						array(
							'id'    => 'g_text',
							'type'  => 'textarea',
							'title' => __( 'Text', 'exopite-sof-example' ),
						),
						array(
							'id'    => 'g_switch',
							'type'  => 'switcher',
							'title' => __( 'Active', 'exopite-sof-example' ),
						),
						array(
							'id'    => 'g_color',
							'type'  => 'color_wp',
							'title' => __( 'Color', 'exopite-sof-example' ),
						),
					),
				),
				array(
					'id'      => 'group_single',
					'type'    => 'group',
					'title'   => __( 'Group (not a repeater)', 'exopite-sof-example' ),
					'options' => array(
						'repeater'    => false,
						'group_title' => __( 'Single group', 'exopite-sof-example' ),
					),
					'fields'  => array(
						array(
							'id'    => 'gs_text',
							'type'  => 'text',
							'title' => __( 'Text', 'exopite-sof-example' ),
						),
						array(
							'id'    => 'gs_number',
							'type'  => 'number',
							'title' => __( 'Number', 'exopite-sof-example' ),
						),
					),
				),
				array(
					'id'      => 'fieldset',
					'type'    => 'fieldset',
					'title'   => __( 'Fieldset (2 columns)', 'exopite-sof-example' ),
					'options' => array( 'cols' => 2 ),
					'fields'  => array(
						array(
							'id'    => 'fs_first',
							'type'  => 'text',
							'title' => __( 'First name', 'exopite-sof-example' ),
						),
						array(
							'id'    => 'fs_last',
							'type'  => 'text',
							'title' => __( 'Last name', 'exopite-sof-example' ),
						),
					),
				),
				array(
					'id'      => 'tab',
					'type'    => 'tab',
					'title'   => __( 'Tab', 'exopite-sof-example' ),
					'options' => array( 'equal_width' => true ),
					'tabs'    => array(
						array(
							'title'  => __( 'Tab 1', 'exopite-sof-example' ),
							'fields' => array(
								array(
									'id'    => 'tab1_text',
									'type'  => 'text',
									'title' => __( 'Tab 1 text', 'exopite-sof-example' ),
								),
							),
						),
						array(
							'title'  => __( 'Tab 2', 'exopite-sof-example' ),
							'fields' => array(
								array(
									'id'      => 'tab2_select',
									'type'    => 'select',
									'title'   => __( 'Tab 2 select', 'exopite-sof-example' ),
									'options' => array(
										'x' => 'X',
										'y' => 'Y',
									),
								),
							),
						),
					),
				),
				array(
					'id'       => 'accordion',
					'type'     => 'accordion',
					'title'    => __( 'Accordion', 'exopite-sof-example' ),
					'options'  => array( 'allow_all_open' => false ),
					'sections' => array(
						array(
							'options' => array(
								'title'  => __( 'Section 1', 'exopite-sof-example' ),
								'closed' => false,
							),
							'fields'  => array(
								array(
									'id'    => 'acc1_text',
									'type'  => 'text',
									'title' => __( 'Section 1 text', 'exopite-sof-example' ),
								),
							),
						),
						array(
							'options' => array( 'title' => __( 'Section 2', 'exopite-sof-example' ) ),
							'fields'  => array(
								array(
									'id'    => 'acc2_textarea',
									'type'  => 'textarea',
									'title' => __( 'Section 2 textarea', 'exopite-sof-example' ),
								),
							),
						),
					),
				),
				array(
					'id'      => 'group_nested',
					'type'    => 'group',
					'title'   => __( 'Nested group (repeater in repeater)', 'exopite-sof-example' ),
					'options' => array(
						'repeater'     => true,
						'accordion'    => true,
						'button_title' => __( 'Add outer item', 'exopite-sof-example' ),
						'group_title'  => __( 'Outer item', 'exopite-sof-example' ),
						'limit'        => 5,
					),
					'fields'  => array(
						array(
							'id'    => 'gn_title',
							'type'  => 'text',
							'title' => __( 'Outer title', 'exopite-sof-example' ),
						),
						array(
							'id'      => 'gn_inner',
							'type'    => 'group',
							'title'   => __( 'Inner group', 'exopite-sof-example' ),
							'options' => array(
								'repeater'     => true,
								'accordion'    => true,
								'button_title' => __( 'Add inner item', 'exopite-sof-example' ),
								'group_title'  => __( 'Inner item', 'exopite-sof-example' ),
								'limit'        => 5,
							),
							'fields'  => array(
								array(
									'id'    => 'gn_inner_text',
									'type'  => 'text',
									'title' => __( 'Inner text', 'exopite-sof-example' ),
								),
								array(
									'id'    => 'gn_inner_check',
									'type'  => 'checkbox',
									'title' => __( 'Inner check', 'exopite-sof-example' ),
									'label' => __( 'Yes', 'exopite-sof-example' ),
								),
							),
						),
					),
				),
				array(
					'id'       => 'accordion_nested',
					'type'     => 'accordion',
					'title'    => __( 'Nested accordion (accordion, tab and group inside)', 'exopite-sof-example' ),
					'options'  => array( 'allow_all_open' => true ),
					'sections' => array(
						array(
							'options' => array(
								'title'  => __( 'Accordion inside', 'exopite-sof-example' ),
								'closed' => false,
							),
							'fields'  => array(
								array(
									'id'    => 'an_text',
									'type'  => 'text',
									'title' => __( 'Outer text', 'exopite-sof-example' ),
								),
								array(
									'id'       => 'an_inner_accordion',
									'type'     => 'accordion',
									'title'    => __( 'Inner accordion', 'exopite-sof-example' ),
									'sections' => array(
										array(
											'options' => array(
												'title'  => __( 'Inner A', 'exopite-sof-example' ),
												'closed' => false,
											),
											'fields'  => array(
												array(
													'id'   => 'an_inner_a_text',
													'type' => 'text',
													'title' => __( 'Inner A text', 'exopite-sof-example' ),
												),
											),
										),
										array(
											'options' => array( 'title' => __( 'Inner B', 'exopite-sof-example' ) ),
											'fields'  => array(
												array(
													'id'   => 'an_inner_b_switch',
													'type' => 'switcher',
													'title' => __( 'Inner B switcher', 'exopite-sof-example' ),
												),
											),
										),
									),
								),
							),
						),
						array(
							'options' => array( 'title' => __( 'Tab inside', 'exopite-sof-example' ) ),
							'fields'  => array(
								array(
									'id'      => 'an_tab',
									'type'    => 'tab',
									'title'   => __( 'Tab in accordion', 'exopite-sof-example' ),
									'options' => array( 'equal_width' => true ),
									'tabs'    => array(
										array(
											'title'  => __( 'Tab A', 'exopite-sof-example' ),
											'fields' => array(
												array(
													'id'   => 'an_tab_a_text',
													'type' => 'text',
													'title' => __( 'Tab A text', 'exopite-sof-example' ),
												),
											),
										),
										array(
											'title'  => __( 'Tab B', 'exopite-sof-example' ),
											'fields' => array(
												array(
													'id'   => 'an_tab_b_textarea',
													'type' => 'textarea',
													'title' => __( 'Tab B textarea', 'exopite-sof-example' ),
												),
											),
										),
									),
								),
							),
						),
						array(
							'options' => array( 'title' => __( 'Group inside', 'exopite-sof-example' ) ),
							'fields'  => array(
								array(
									'id'      => 'an_group',
									'type'    => 'group',
									'title'   => __( 'Group in accordion', 'exopite-sof-example' ),
									'options' => array(
										'repeater'     => true,
										'button_title' => __( 'Add row', 'exopite-sof-example' ),
										'group_title'  => __( 'Row', 'exopite-sof-example' ),
									),
									'fields'  => array(
										array(
											'id'    => 'an_group_text',
											'type'  => 'text',
											'title' => __( 'Row text', 'exopite-sof-example' ),
										),
									),
								),
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Static content: notice, content, card, button.
	 *
	 * @return array
	 */
	private static function section_content(): array {
		return array(
			'name'   => 'content',
			'title'  => __( 'Content, notice, card, button', 'exopite-sof-example' ),
			'icon'   => 'dashicons-info',
			'fields' => array(
				array(
					'type'    => 'notice',
					'class'   => 'info',
					'content' => __( 'This is an info notice.', 'exopite-sof-example' ),
				),
				array(
					'type'    => 'notice',
					'class'   => 'danger',
					'content' => __( 'This is a danger notice.', 'exopite-sof-example' ),
				),
				array(
					'type'    => 'content',
					'title'   => __( 'Content', 'exopite-sof-example' ),
					'content' => '<p>' . sprintf( /* translators: %s: the word HTML in bold. */ esc_html__( 'Static %s content.', 'exopite-sof-example' ), '<strong>HTML</strong>' ) . '</p>',
				),
				array(
					'type'    => 'card',
					'title'   => __( 'Card', 'exopite-sof-example' ),
					'header'  => __( 'Card header', 'exopite-sof-example' ),
					'content' => __( 'Card content', 'exopite-sof-example' ),
					'footer'  => __( 'Card footer', 'exopite-sof-example' ),
				),
				array(
					'id'      => 'button',
					'type'    => 'button',
					'title'   => __( 'Button', 'exopite-sof-example' ),
					'options' => array(
						'href'   => 'https://wordpress.org',
						'target' => '_blank',
						'value'  => __( 'Open wordpress.org', 'exopite-sof-example' ),
					),
				),
			),
		);
	}

	/**
	 * A menu item with sub sections.
	 *
	 * @return array
	 */
	private static function section_nested_menu(): array {
		return array(
			'title'    => __( 'Nested menu', 'exopite-sof-example' ),
			'icon'     => 'dashicons-menu',
			'sections' => array(
				array(
					'name'   => 'nested1',
					'title'  => __( 'Sub section 1', 'exopite-sof-example' ),
					'fields' => array(
						array(
							'id'    => 'nested1_text',
							'type'  => 'text',
							'title' => __( 'Sub section 1 text', 'exopite-sof-example' ),
						),
					),
				),
				array(
					'name'   => 'nested2',
					'title'  => __( 'Sub section 2', 'exopite-sof-example' ),
					'fields' => array(
						array(
							'id'    => 'nested2_text',
							'type'  => 'text',
							'title' => __( 'Sub section 2 text', 'exopite-sof-example' ),
						),
					),
				),
			),
		);
	}

	/**
	 * Shows how to read the saved values.
	 *
	 * @return array
	 */
	private static function section_read_values(): array {
		return array(
			'name'   => 'read',
			'title'  => __( 'Read the values', 'exopite-sof-example' ),
			'icon'   => 'dashicons-editor-code',
			'fields' => array(
				array(
					'type'     => 'content',
					'title'    => __( 'Read the values in your code', 'exopite-sof-example' ),
					'callback' => array(
						'function' => array( __CLASS__, 'render_read_values' ),
					),
				),
			),
		);
	}

	/**
	 * Content of the "Read the values" section.
	 *
	 * @return string
	 */
	public static function render_read_values() {

		$options = get_exopite_sof_option( self::OPTION_ID );
		$text    = ( is_array( $options ) && isset( $options['text'] ) ) ? $options['text'] : '';

		$code = "\$options = get_exopite_sof_option( '" . self::OPTION_ID . "' );\n"
			. "\$text    = \$options['text'];\n\n"
			. "// Metabox, all fields in one array:\n"
			. "\$meta = get_post_meta( \$post_id, '" . self::META_ID . "', true );\n\n"
			. "// \"Simple\" metabox, one meta key per field:\n"
			. "\$value = get_post_meta( \$post_id, 'exopite_sof_example_simple_text', true );";

		return '<pre><code>' . esc_html( $code ) . '</code></pre>'
			. '<p>' . esc_html__( 'Saved value of the "Text" field:', 'exopite-sof-example' ) . ' <code>' . esc_html( $text ) . '</code></p>';
	}

	/**
	 * Import, export and reset of the options.
	 *
	 * @return array
	 */
	private static function section_backup(): array {
		return array(
			'name'   => 'backup',
			'title'  => __( 'Backup', 'exopite-sof-example' ),
			'icon'   => 'dashicons-backup',
			'fields' => array(
				array(
					'id'    => 'backup',
					'type'  => 'backup',
					'title' => __( 'Import, export and reset', 'exopite-sof-example' ),
				),
			),
		);
	}

	/*
	 * ------------------------------------------------------------------
	 * Metaboxes
	 * ------------------------------------------------------------------
	 */

	/**
	 * Config of the metabox (all fields saved in one post meta array).
	 *
	 * @return array
	 */
	private static function metabox_config(): array {
		return array(
			'type'       => 'metabox',
			'id'         => self::META_ID,             // Post meta key.
			'post_types' => array( 'post', 'page' ),
			'title'      => __( 'SOF Example: metabox (one array)', 'exopite-sof-example' ),
			'context'    => 'advanced',               // One of: normal, advanced, side.
			'priority'   => 'default',
			'capability' => 'edit_posts',
			'tabbed'     => true,
			'multilang'  => false,
		);
	}

	/**
	 * Fields of the metabox.
	 *
	 * @return array
	 */
	private static function metabox_fields(): array {
		return array(
			array(
				'name'   => 'meta_basic',
				'title'  => __( 'Basic', 'exopite-sof-example' ),
				'icon'   => 'dashicons-admin-generic',
				'fields' => self::basic_fields( 'meta_' ),
			),
			array(
				'name'   => 'meta_media',
				'title'  => __( 'Media', 'exopite-sof-example' ),
				'icon'   => 'dashicons-admin-media',
				'fields' => array(
					array(
						'id'    => 'meta_image',
						'type'  => 'image',
						'title' => __( 'Image', 'exopite-sof-example' ),
					),
					array(
						'id'    => 'meta_gallery',
						'type'  => 'gallery',
						'title' => __( 'Gallery', 'exopite-sof-example' ),
					),
					array(
						'id'      => 'meta_upload',
						'type'    => 'upload',
						'title'   => __( 'Upload (attached to this post)', 'exopite-sof-example' ),
						'options' => array(
							'attach'                   => true,
							'filecount'                => 3,
							'delete-enabled'           => true,
							'delete-force-confirm'     => true,
							'retry-enable-auto'        => false,
							'retry-max-auto-attempts'  => 3,
							'retry-auto-attempt-delay' => 2,
							'auto-upload'              => true,
						),
					),
					array(
						'id'      => 'meta_attached',
						'type'    => 'attached',
						'title'   => __( 'Attached images (select and delete)', 'exopite-sof-example' ),
						'options' => array( 'type' => 'image' ),
					),
				),
			),
			array(
				'name'   => 'meta_more',
				'title'  => __( 'More', 'exopite-sof-example' ),
				'icon'   => 'dashicons-list-view',
				'fields' => array(
					array(
						'id'      => 'meta_group',
						'type'    => 'group',
						'title'   => __( 'Group (repeater)', 'exopite-sof-example' ),
						'options' => array(
							'repeater'     => true,
							'button_title' => __( 'Add row', 'exopite-sof-example' ),
							'group_title'  => __( 'Row', 'exopite-sof-example' ),
						),
						'fields'  => array(
							array(
								'id'    => 'mg_text',
								'type'  => 'text',
								'title' => __( 'Text', 'exopite-sof-example' ),
							),
							array(
								'id'    => 'mg_check',
								'type'  => 'checkbox',
								'title' => __( 'Check', 'exopite-sof-example' ),
								'label' => __( 'Yes', 'exopite-sof-example' ),
							),
						),
					),
					array(
						'id'       => 'meta_view',
						'type'     => 'meta',
						'title'    => __( 'Show a post meta value (read only)', 'exopite-sof-example' ),
						'meta-key' => '_edit_last',
					),
				),
			),
		);
	}

	/**
	 * Config of the "simple" metabox (one post meta key per field).
	 *
	 * @return array
	 */
	private static function simple_metabox_config(): array {
		return array(
			'type'       => 'metabox',
			'id'         => self::SIMPLE_META_ID,
			'post_types' => array( 'post', 'page' ),
			'title'      => __( 'SOF Example: simple metabox', 'exopite-sof-example' ),
			'context'    => 'side',
			'priority'   => 'default',
			'capability' => 'edit_posts',
			'options'    => 'simple',                  // Every field is its own post meta key.
			'multilang'  => false,
		);
	}

	/**
	 * Fields of the "simple" metabox. The field ids are the post meta keys.
	 *
	 * @return array
	 */
	private static function simple_metabox_fields(): array {
		return array(
			array(
				'name'   => 'simple',
				'title'  => __( 'Simple', 'exopite-sof-example' ),
				'fields' => array(
					array(
						'id'    => 'exopite_sof_example_simple_text',
						'type'  => 'text',
						'title' => __( 'Text', 'exopite-sof-example' ),
					),
					array(
						'id'    => 'exopite_sof_example_simple_switch',
						'type'  => 'switcher',
						'title' => __( 'Switcher', 'exopite-sof-example' ),
					),
					array(
						'id'      => 'exopite_sof_example_simple_select',
						'type'    => 'select',
						'title'   => __( 'Select', 'exopite-sof-example' ),
						'options' => array(
							'a' => 'A',
							'b' => 'B',
						),
					),
				),
			),
		);
	}
}
