<?php
/**
 * Admin area: creates the options page and the metaboxes with the framework.
 *
 * @package Exopite_Simple_Options_Example
 */

namespace Exopite_Simple_Options_Example;

/**
 * Registers an options page with every field type, a metabox and a "simple"
 * metabox through the Exopite Simple Options Framework plugin.
 *
 * The framework is created on "init" (hooked in define_admin_hooks()). All
 * plugins are loaded before "init", so the framework class exists, whatever
 * the plugin load order is. If the framework plugin is not active, an admin
 * notice is shown instead.
 */
final class Exopite_Simple_Options_Example_Admin {

	/**
	 * Option name of the options page: get_option( self::OPTION_ID ).
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
	 * Sets up the admin part.
	 *
	 * @param string $plugin_name Plugin slug.
	 * @param string $version     Plugin version.
	 */
	public function __construct(
		private string $plugin_name,
		private string $version
	) {}

	/**
	 * Create the options page and the metaboxes.
	 *
	 * Hooked to "init".
	 */
	public function register_options(): void {

		// The framework only works in the admin area.
		if ( ! is_admin() ) {
			return;
		}

		// The framework plugin is not active.
		if ( ! class_exists( '\Exopite_Simple_Options_Framework' ) ) {
			add_action( 'admin_notices', array( $this, 'framework_missing_notice' ) );
			return;
		}

		new \Exopite_Simple_Options_Framework( $this->options_page_config(), $this->options_page_fields() );
		new \Exopite_Simple_Options_Framework( $this->metabox_config(), $this->metabox_fields() );
		new \Exopite_Simple_Options_Framework( $this->simple_metabox_config(), $this->simple_metabox_fields() );
	}

	/**
	 * Admin notice if the framework plugin is missing.
	 */
	public function framework_missing_notice(): void {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'Exopite Simple Options Framework Example: please install and activate the "Exopite Simple Options Framework" plugin.', 'exopite-simple-options-example' ) . '</p></div>';
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
	private function options_page_config(): array {
		return array(
			'type'            => 'menu',               // Required: "menu" or "metabox".
			'id'              => self::OPTION_ID,      // Required: option name, get_option( id ).
			'menu_title'      => __( 'SOF Example', 'exopite-simple-options-example' ),        // Required for menus.
			'plugin_basename' => plugin_basename( EXOPITE_SIMPLE_OPTIONS_EXAMPLE_FILE ), // Required: adds "Settings" to the plugin list.
			'submenu'         => false,                // true: submenu of "parent".
			'parent'          => 'options-general.php',
			'title'           => __( 'Exopite Simple Options Framework Example', 'exopite-simple-options-example' ),
			'option_title'    => __( 'Exopite Simple Options Framework Example: every field type', 'exopite-simple-options-example' ),
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
	private function options_page_fields(): array {

		$fields   = array();
		$fields[] = $this->section_basic();
		$fields[] = $this->section_choices();
		$fields[] = $this->section_media();
		$fields[] = $this->section_editors();
		$fields[] = $this->section_layout();
		$fields[] = $this->section_content();
		$fields[] = $this->section_nested_menu();
		$fields[] = $this->section_read_values();
		$fields[] = $this->section_backup();

		return $fields;
	}

	/**
	 * Basic input fields.
	 *
	 * @return array
	 */
	private function section_basic(): array {
		return array(
			'name'   => 'basic',
			'title'  => __( 'Basic', 'exopite-simple-options-example' ),
			'icon'   => 'dashicons-admin-generic',
			'fields' => array_merge(
				$this->basic_fields( '' ),
				array(
					array(
						'id'    => 'password',
						'type'  => 'password',
						'title' => __( 'Password', 'exopite-simple-options-example' ),
					),
					array(
						'id'      => 'hidden',
						'type'    => 'hidden',
						'default' => 'hidden value',
					),
					array(
						'id'      => 'number',
						'type'    => 'number',
						'title'   => __( 'Number (0 to 100)', 'exopite-simple-options-example' ),
						'min'     => 0,
						'max'     => 100,
						'step'    => 1,
						'unit'    => 'px',
						'default' => 10,
					),
					array(
						'id'      => 'range',
						'type'    => 'range',
						'title'   => __( 'Range', 'exopite-simple-options-example' ),
						'min'     => 0,
						'max'     => 50,
						'step'    => 5,
						'unit'    => '%',
						'default' => 20,
					),
					array(
						'id'      => 'text_prepend',
						'type'    => 'text',
						'title'   => __( 'Text with prepend, append and help', 'exopite-simple-options-example' ),
						'prepend' => 'fa-font',
						'append'  => 'dashicons-admin-links',
						'help'    => __( 'Help text on mouse over', 'exopite-simple-options-example' ),
						'before'  => __( 'Text before the field', 'exopite-simple-options-example' ),
						'after'   => __( 'Text after the field', 'exopite-simple-options-example' ),
					),
					array(
						'id'       => 'text_sanitize',
						'type'     => 'text',
						'title'    => __( 'Own sanitize function', 'exopite-simple-options-example' ),
						'info'     => __( 'Saved with sanitize_email().', 'exopite-simple-options-example' ),
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
	private function basic_fields( string $prefix ): array {
		return array(
			array(
				'id'          => $prefix . 'text',
				'type'        => 'text',
				'title'       => __( 'Text', 'exopite-simple-options-example' ),
				'description' => __( 'Field description', 'exopite-simple-options-example' ),
				'default'     => 'Default text',
				'attributes'  => array( 'placeholder' => 'Placeholder' ),
			),
			array(
				'id'      => $prefix . 'textarea',
				'type'    => 'textarea',
				'title'   => __( 'Textarea', 'exopite-simple-options-example' ),
				'default' => "Line 1\nLine 2",
			),
			array(
				'id'    => $prefix . 'checkbox',
				'type'  => 'checkbox',
				'title' => __( 'Checkbox', 'exopite-simple-options-example' ),
				'label' => __( 'Check me', 'exopite-simple-options-example' ),
			),
			array(
				'id'    => $prefix . 'checkbox_fancy',
				'type'  => 'checkbox',
				'title' => __( 'Checkbox (fancy)', 'exopite-simple-options-example' ),
				'style' => 'fancy',
				'label' => __( 'Check me', 'exopite-simple-options-example' ),
			),
			array(
				'id'      => $prefix . 'select',
				'type'    => 'select',
				'title'   => __( 'Select', 'exopite-simple-options-example' ),
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
				'title'      => __( 'ACE code editor', 'exopite-simple-options-example' ),
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
	private function section_choices(): array {
		return array(
			'name'   => 'choices',
			'title'  => __( 'Choices', 'exopite-simple-options-example' ),
			'icon'   => 'fa fa-check-square-o',
			'fields' => array(
				array(
					'id'    => 'switcher',
					'type'  => 'switcher',
					'title' => __( 'Switcher (controls the next field)', 'exopite-simple-options-example' ),
					'label' => __( 'Show the dependent field', 'exopite-simple-options-example' ),
				),
				array(
					'id'         => 'dependent',
					'type'       => 'text',
					'title'      => __( 'Dependent text (visible if the switcher is on)', 'exopite-simple-options-example' ),
					'dependency' => array( 'switcher', '==', 'true' ),
				),
				array(
					'id'      => 'radio',
					'type'    => 'radio',
					'title'   => __( 'Radio', 'exopite-simple-options-example' ),
					'options' => array(
						'one' => 'One',
						'two' => 'Two',
					),
					'default' => 'one',
				),
				array(
					'id'      => 'radio_fancy',
					'type'    => 'radio',
					'title'   => __( 'Radio (fancy)', 'exopite-simple-options-example' ),
					'style'   => 'fancy',
					'options' => array(
						'a' => 'A',
						'b' => 'B',
					),
				),
				array(
					'id'      => 'button_bar',
					'type'    => 'button_bar',
					'title'   => __( 'Button bar', 'exopite-simple-options-example' ),
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
					'title'   => __( 'Tap list (checkboxes)', 'exopite-simple-options-example' ),
					'options' => array(
						'red'   => 'Red',
						'green' => 'Green',
						'blue'  => 'Blue',
					),
				),
				array(
					'id'      => 'tap_list_radio',
					'type'    => 'tap_list',
					'title'   => __( 'Tap list (radio)', 'exopite-simple-options-example' ),
					'radio'   => true,
					'options' => array(
						'small' => 'Small',
						'large' => 'Large',
					),
				),
				array(
					'id'          => 'image_select',
					'type'        => 'image_select',
					'title'       => __( 'Image select (text)', 'exopite-simple-options-example' ),
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
					'title'      => __( 'Select multiple (chosen)', 'exopite-simple-options-example' ),
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
					'title'          => __( 'Select from pages (query)', 'exopite-simple-options-example' ),
					'query'          => array(
						'type' => 'pages',
						'args' => array(
							'orderby' => 'title',
							'order'   => 'ASC',
						),
					),
					'default_option' => __( '- select a page -', 'exopite-simple-options-example' ),
				),
			),
		);
	}

	/**
	 * Media and color fields.
	 *
	 * @return array
	 */
	private function section_media(): array {
		return array(
			'name'   => 'media',
			'title'  => __( 'Media and colors', 'exopite-simple-options-example' ),
			'icon'   => 'dashicons-format-image',
			'fields' => array(
				array(
					'id'    => 'color',
					'type'  => 'color',
					'title' => __( 'Color (minicolors)', 'exopite-simple-options-example' ),
					'rgba'  => true,
				),
				array(
					'id'    => 'color_wp',
					'type'  => 'color_wp',
					'title' => __( 'Color (WordPress color picker, rgba)', 'exopite-simple-options-example' ),
					'rgba'  => true,
				),
				array(
					'id'     => 'date',
					'type'   => 'date',
					'title'  => __( 'Date', 'exopite-simple-options-example' ),
					'format' => 'yy-mm-dd',
					'picker' => 'datepicker',
				),
				array(
					'id'        => 'image',
					'type'      => 'image',
					'title'     => __( 'Image', 'exopite-simple-options-example' ),
					'add_title' => __( 'Choose image', 'exopite-simple-options-example' ),
				),
				array(
					'id'    => 'gallery',
					'type'  => 'gallery',
					'title' => __( 'Gallery', 'exopite-simple-options-example' ),
				),
				array(
					'id'      => 'video',
					'type'    => 'video',
					'title'   => __( 'Video (oEmbed)', 'exopite-simple-options-example' ),
					'default' => 'https://vimeo.com/76979871',
					'options' => array(
						'input'  => true,
						'oembed' => true,
					),
				),
				array(
					'id'      => 'upload',
					'type'    => 'upload',
					'title'   => __( 'Upload (Fine Uploader)', 'exopite-simple-options-example' ),
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
	private function section_editors(): array {
		return array(
			'name'   => 'editors',
			'title'  => __( 'Editors and typography', 'exopite-simple-options-example' ),
			'icon'   => 'dashicons-edit',
			'fields' => array(
				array(
					'id'     => 'editor_tinymce',
					'type'   => 'editor',
					'title'  => __( 'Editor (TinyMCE)', 'exopite-simple-options-example' ),
					'editor' => 'tinymce',
				),
				array(
					'id'     => 'editor_trumbowyg',
					'type'   => 'editor',
					'title'  => __( 'Editor (Trumbowyg)', 'exopite-simple-options-example' ),
					'editor' => 'trumbowyg',
				),
				array(
					'id'           => 'typography',
					'type'         => 'typography',
					'title'        => __( 'Typography', 'exopite-simple-options-example' ),
					'preview'      => true,
					'preview_text' => __( 'The quick brown fox jumps over the lazy dog', 'exopite-simple-options-example' ),
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
	private function section_layout(): array {
		return array(
			'name'   => 'layout',
			'title'  => __( 'Group, tab, fieldset, accordion', 'exopite-simple-options-example' ),
			'icon'   => 'dashicons-screenoptions',
			'fields' => array(
				array(
					'id'      => 'group_repeater',
					'type'    => 'group',
					'title'   => __( 'Group (repeater, sortable)', 'exopite-simple-options-example' ),
					'options' => array(
						'repeater'     => true,
						'accordion'    => true,
						'button_title' => __( 'Add new item', 'exopite-simple-options-example' ),
						'group_title'  => __( 'Item', 'exopite-simple-options-example' ),
						'limit'        => 5,
						'sortable'     => true,
					),
					'fields'  => array(
						array(
							'id'    => 'g_title',
							'type'  => 'text',
							'title' => __( 'Title', 'exopite-simple-options-example' ),
						),
						array(
							'id'    => 'g_text',
							'type'  => 'textarea',
							'title' => __( 'Text', 'exopite-simple-options-example' ),
						),
						array(
							'id'    => 'g_switch',
							'type'  => 'switcher',
							'title' => __( 'Active', 'exopite-simple-options-example' ),
						),
						array(
							'id'    => 'g_color',
							'type'  => 'color_wp',
							'title' => __( 'Color', 'exopite-simple-options-example' ),
						),
					),
				),
				array(
					'id'      => 'group_single',
					'type'    => 'group',
					'title'   => __( 'Group (not a repeater)', 'exopite-simple-options-example' ),
					'options' => array(
						'repeater'    => false,
						'group_title' => __( 'Single group', 'exopite-simple-options-example' ),
					),
					'fields'  => array(
						array(
							'id'    => 'gs_text',
							'type'  => 'text',
							'title' => __( 'Text', 'exopite-simple-options-example' ),
						),
						array(
							'id'    => 'gs_number',
							'type'  => 'number',
							'title' => __( 'Number', 'exopite-simple-options-example' ),
						),
					),
				),
				array(
					'id'      => 'fieldset',
					'type'    => 'fieldset',
					'title'   => __( 'Fieldset (2 columns)', 'exopite-simple-options-example' ),
					'options' => array( 'cols' => 2 ),
					'fields'  => array(
						array(
							'id'    => 'fs_first',
							'type'  => 'text',
							'title' => __( 'First name', 'exopite-simple-options-example' ),
						),
						array(
							'id'    => 'fs_last',
							'type'  => 'text',
							'title' => __( 'Last name', 'exopite-simple-options-example' ),
						),
					),
				),
				array(
					'id'      => 'tab',
					'type'    => 'tab',
					'title'   => __( 'Tab', 'exopite-simple-options-example' ),
					'options' => array( 'equal_width' => true ),
					'tabs'    => array(
						array(
							'title'  => __( 'Tab 1', 'exopite-simple-options-example' ),
							'fields' => array(
								array(
									'id'    => 'tab1_text',
									'type'  => 'text',
									'title' => __( 'Tab 1 text', 'exopite-simple-options-example' ),
								),
							),
						),
						array(
							'title'  => __( 'Tab 2', 'exopite-simple-options-example' ),
							'fields' => array(
								array(
									'id'      => 'tab2_select',
									'type'    => 'select',
									'title'   => __( 'Tab 2 select', 'exopite-simple-options-example' ),
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
					'title'    => __( 'Accordion', 'exopite-simple-options-example' ),
					'options'  => array( 'allow_all_open' => false ),
					'sections' => array(
						array(
							'options' => array(
								'title'  => __( 'Section 1', 'exopite-simple-options-example' ),
								'closed' => false,
							),
							'fields'  => array(
								array(
									'id'    => 'acc1_text',
									'type'  => 'text',
									'title' => __( 'Section 1 text', 'exopite-simple-options-example' ),
								),
							),
						),
						array(
							'options' => array( 'title' => __( 'Section 2', 'exopite-simple-options-example' ) ),
							'fields'  => array(
								array(
									'id'    => 'acc2_textarea',
									'type'  => 'textarea',
									'title' => __( 'Section 2 textarea', 'exopite-simple-options-example' ),
								),
							),
						),
					),
				),
				array(
					'id'      => 'group_nested',
					'type'    => 'group',
					'title'   => __( 'Nested group (repeater in repeater)', 'exopite-simple-options-example' ),
					'options' => array(
						'repeater'     => true,
						'accordion'    => true,
						'button_title' => __( 'Add outer item', 'exopite-simple-options-example' ),
						'group_title'  => __( 'Outer item', 'exopite-simple-options-example' ),
						'limit'        => 5,
					),
					'fields'  => array(
						array(
							'id'    => 'gn_title',
							'type'  => 'text',
							'title' => __( 'Outer title', 'exopite-simple-options-example' ),
						),
						array(
							'id'      => 'gn_inner',
							'type'    => 'group',
							'title'   => __( 'Inner group', 'exopite-simple-options-example' ),
							'options' => array(
								'repeater'     => true,
								'accordion'    => true,
								'button_title' => __( 'Add inner item', 'exopite-simple-options-example' ),
								'group_title'  => __( 'Inner item', 'exopite-simple-options-example' ),
								'limit'        => 5,
							),
							'fields'  => array(
								array(
									'id'    => 'gn_inner_text',
									'type'  => 'text',
									'title' => __( 'Inner text', 'exopite-simple-options-example' ),
								),
								array(
									'id'    => 'gn_inner_check',
									'type'  => 'checkbox',
									'title' => __( 'Inner check', 'exopite-simple-options-example' ),
									'label' => __( 'Yes', 'exopite-simple-options-example' ),
								),
							),
						),
					),
				),
				array(
					'id'       => 'accordion_nested',
					'type'     => 'accordion',
					'title'    => __( 'Nested accordion (accordion, tab and group inside)', 'exopite-simple-options-example' ),
					'options'  => array( 'allow_all_open' => true ),
					'sections' => array(
						array(
							'options' => array(
								'title'  => __( 'Accordion inside', 'exopite-simple-options-example' ),
								'closed' => false,
							),
							'fields'  => array(
								array(
									'id'    => 'an_text',
									'type'  => 'text',
									'title' => __( 'Outer text', 'exopite-simple-options-example' ),
								),
								array(
									'id'       => 'an_inner_accordion',
									'type'     => 'accordion',
									'title'    => __( 'Inner accordion', 'exopite-simple-options-example' ),
									'sections' => array(
										array(
											'options' => array(
												'title'  => __( 'Inner A', 'exopite-simple-options-example' ),
												'closed' => false,
											),
											'fields'  => array(
												array(
													'id'   => 'an_inner_a_text',
													'type' => 'text',
													'title' => __( 'Inner A text', 'exopite-simple-options-example' ),
												),
											),
										),
										array(
											'options' => array( 'title' => __( 'Inner B', 'exopite-simple-options-example' ) ),
											'fields'  => array(
												array(
													'id'   => 'an_inner_b_switch',
													'type' => 'switcher',
													'title' => __( 'Inner B switcher', 'exopite-simple-options-example' ),
												),
											),
										),
									),
								),
							),
						),
						array(
							'options' => array( 'title' => __( 'Tab inside', 'exopite-simple-options-example' ) ),
							'fields'  => array(
								array(
									'id'      => 'an_tab',
									'type'    => 'tab',
									'title'   => __( 'Tab in accordion', 'exopite-simple-options-example' ),
									'options' => array( 'equal_width' => true ),
									'tabs'    => array(
										array(
											'title'  => __( 'Tab A', 'exopite-simple-options-example' ),
											'fields' => array(
												array(
													'id'   => 'an_tab_a_text',
													'type' => 'text',
													'title' => __( 'Tab A text', 'exopite-simple-options-example' ),
												),
											),
										),
										array(
											'title'  => __( 'Tab B', 'exopite-simple-options-example' ),
											'fields' => array(
												array(
													'id'   => 'an_tab_b_textarea',
													'type' => 'textarea',
													'title' => __( 'Tab B textarea', 'exopite-simple-options-example' ),
												),
											),
										),
									),
								),
							),
						),
						array(
							'options' => array( 'title' => __( 'Group inside', 'exopite-simple-options-example' ) ),
							'fields'  => array(
								array(
									'id'      => 'an_group',
									'type'    => 'group',
									'title'   => __( 'Group in accordion', 'exopite-simple-options-example' ),
									'options' => array(
										'repeater'     => true,
										'button_title' => __( 'Add row', 'exopite-simple-options-example' ),
										'group_title'  => __( 'Row', 'exopite-simple-options-example' ),
									),
									'fields'  => array(
										array(
											'id'    => 'an_group_text',
											'type'  => 'text',
											'title' => __( 'Row text', 'exopite-simple-options-example' ),
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
	private function section_content(): array {
		return array(
			'name'   => 'content',
			'title'  => __( 'Content, notice, card, button', 'exopite-simple-options-example' ),
			'icon'   => 'dashicons-info',
			'fields' => array(
				array(
					'type'    => 'notice',
					'class'   => 'info',
					'content' => __( 'This is an info notice.', 'exopite-simple-options-example' ),
				),
				array(
					'type'    => 'notice',
					'class'   => 'danger',
					'content' => __( 'This is a danger notice.', 'exopite-simple-options-example' ),
				),
				array(
					'type'    => 'content',
					'title'   => __( 'Content', 'exopite-simple-options-example' ),
					'content' => '<p>' . sprintf( /* translators: %s: the word HTML in bold. */ esc_html__( 'Static %s content.', 'exopite-simple-options-example' ), '<strong>HTML</strong>' ) . '</p>',
				),
				array(
					'type'    => 'card',
					'title'   => __( 'Card', 'exopite-simple-options-example' ),
					'header'  => __( 'Card header', 'exopite-simple-options-example' ),
					'content' => __( 'Card content', 'exopite-simple-options-example' ),
					'footer'  => __( 'Card footer', 'exopite-simple-options-example' ),
				),
				array(
					'id'      => 'button',
					'type'    => 'button',
					'title'   => __( 'Button', 'exopite-simple-options-example' ),
					'options' => array(
						'href'   => 'https://wordpress.org',
						'target' => '_blank',
						'value'  => __( 'Open wordpress.org', 'exopite-simple-options-example' ),
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
	private function section_nested_menu(): array {
		return array(
			'title'    => __( 'Nested menu', 'exopite-simple-options-example' ),
			'icon'     => 'dashicons-menu',
			'sections' => array(
				array(
					'name'   => 'nested1',
					'title'  => __( 'Sub section 1', 'exopite-simple-options-example' ),
					'fields' => array(
						array(
							'id'    => 'nested1_text',
							'type'  => 'text',
							'title' => __( 'Sub section 1 text', 'exopite-simple-options-example' ),
						),
					),
				),
				array(
					'name'   => 'nested2',
					'title'  => __( 'Sub section 2', 'exopite-simple-options-example' ),
					'fields' => array(
						array(
							'id'    => 'nested2_text',
							'type'  => 'text',
							'title' => __( 'Sub section 2 text', 'exopite-simple-options-example' ),
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
	private function section_read_values(): array {
		return array(
			'name'   => 'read',
			'title'  => __( 'Read the values', 'exopite-simple-options-example' ),
			'icon'   => 'dashicons-editor-code',
			'fields' => array(
				array(
					'type'     => 'content',
					'title'    => __( 'Read the values in your code', 'exopite-simple-options-example' ),
					'callback' => array(
						'function' => array( $this, 'render_read_values' ),
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
	public function render_read_values() {

		$options = \get_exopite_sof_option( self::OPTION_ID );
		$text    = ( is_array( $options ) && isset( $options['text'] ) ) ? $options['text'] : '';

		$code = "\$options = get_exopite_sof_option( '" . self::OPTION_ID . "' );\n"
			. "\$text    = \$options['text'];\n\n"
			. "// Metabox, all fields in one array:\n"
			. "\$meta = get_post_meta( \$post_id, '" . self::META_ID . "', true );\n\n"
			. "// \"Simple\" metabox, one meta key per field:\n"
			. "\$value = get_post_meta( \$post_id, 'exopite_sof_example_simple_text', true );";

		return '<pre><code>' . esc_html( $code ) . '</code></pre>'
			. '<p>' . esc_html__( 'Saved value of the "Text" field:', 'exopite-simple-options-example' ) . ' <code>' . esc_html( $text ) . '</code></p>';
	}

	/**
	 * Import, export and reset of the options.
	 *
	 * @return array
	 */
	private function section_backup(): array {
		return array(
			'name'   => 'backup',
			'title'  => __( 'Backup', 'exopite-simple-options-example' ),
			'icon'   => 'dashicons-backup',
			'fields' => array(
				array(
					'id'    => 'backup',
					'type'  => 'backup',
					'title' => __( 'Import, export and reset', 'exopite-simple-options-example' ),
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
	private function metabox_config(): array {
		return array(
			'type'       => 'metabox',
			'id'         => self::META_ID,             // Post meta key.
			'post_types' => array( 'post', 'page' ),
			'title'      => __( 'SOF Example: metabox (one array)', 'exopite-simple-options-example' ),
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
	private function metabox_fields(): array {
		return array(
			array(
				'name'   => 'meta_basic',
				'title'  => __( 'Basic', 'exopite-simple-options-example' ),
				'icon'   => 'dashicons-admin-generic',
				'fields' => $this->basic_fields( 'meta_' ),
			),
			array(
				'name'   => 'meta_media',
				'title'  => __( 'Media', 'exopite-simple-options-example' ),
				'icon'   => 'dashicons-admin-media',
				'fields' => array(
					array(
						'id'    => 'meta_image',
						'type'  => 'image',
						'title' => __( 'Image', 'exopite-simple-options-example' ),
					),
					array(
						'id'    => 'meta_gallery',
						'type'  => 'gallery',
						'title' => __( 'Gallery', 'exopite-simple-options-example' ),
					),
					array(
						'id'      => 'meta_upload',
						'type'    => 'upload',
						'title'   => __( 'Upload (attached to this post)', 'exopite-simple-options-example' ),
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
						'title'   => __( 'Attached images (select and delete)', 'exopite-simple-options-example' ),
						'options' => array( 'type' => 'image' ),
					),
				),
			),
			array(
				'name'   => 'meta_more',
				'title'  => __( 'More', 'exopite-simple-options-example' ),
				'icon'   => 'dashicons-list-view',
				'fields' => array(
					array(
						'id'      => 'meta_group',
						'type'    => 'group',
						'title'   => __( 'Group (repeater)', 'exopite-simple-options-example' ),
						'options' => array(
							'repeater'     => true,
							'button_title' => __( 'Add row', 'exopite-simple-options-example' ),
							'group_title'  => __( 'Row', 'exopite-simple-options-example' ),
						),
						'fields'  => array(
							array(
								'id'    => 'mg_text',
								'type'  => 'text',
								'title' => __( 'Text', 'exopite-simple-options-example' ),
							),
							array(
								'id'    => 'mg_check',
								'type'  => 'checkbox',
								'title' => __( 'Check', 'exopite-simple-options-example' ),
								'label' => __( 'Yes', 'exopite-simple-options-example' ),
							),
						),
					),
					array(
						'id'       => 'meta_view',
						'type'     => 'meta',
						'title'    => __( 'Show a post meta value (read only)', 'exopite-simple-options-example' ),
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
	private function simple_metabox_config(): array {
		return array(
			'type'       => 'metabox',
			'id'         => self::SIMPLE_META_ID,
			'post_types' => array( 'post', 'page' ),
			'title'      => __( 'SOF Example: simple metabox', 'exopite-simple-options-example' ),
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
	private function simple_metabox_fields(): array {
		return array(
			array(
				'name'   => 'simple',
				'title'  => __( 'Simple', 'exopite-simple-options-example' ),
				'fields' => array(
					array(
						'id'    => 'exopite_sof_example_simple_text',
						'type'  => 'text',
						'title' => __( 'Text', 'exopite-simple-options-example' ),
					),
					array(
						'id'    => 'exopite_sof_example_simple_switch',
						'type'  => 'switcher',
						'title' => __( 'Switcher', 'exopite-simple-options-example' ),
					),
					array(
						'id'      => 'exopite_sof_example_simple_select',
						'type'    => 'select',
						'title'   => __( 'Select', 'exopite-simple-options-example' ),
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
