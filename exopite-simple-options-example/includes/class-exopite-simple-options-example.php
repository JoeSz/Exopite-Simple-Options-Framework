<?php
/**
 * Core plugin class.
 *
 * @package Exopite_Simple_Options_Example
 */

namespace Exopite_Simple_Options_Example;

/**
 * Wires the plugin's parts together.
 *
 * Pattern: Facade. Why: one entry point that builds the parts and hands their
 * hooks to the loader.
 */
final class Exopite_Simple_Options_Example {

	/**
	 * Registers all hooks.
	 *
	 * @var Exopite_Simple_Options_Example_Loader
	 */
	private Exopite_Simple_Options_Example_Loader $loader;

	/**
	 * Builds the plugin and defines its hooks.
	 */
	public function __construct() {
		$this->loader = new Exopite_Simple_Options_Example_Loader();
		$this->define_admin_hooks();
	}

	/**
	 * Hooks of the admin area.
	 *
	 * The options page and metaboxes are created on "init": all plugins are
	 * loaded by then, so the framework plugin is available whatever the load order is.
	 */
	private function define_admin_hooks(): void {
		$admin = new Exopite_Simple_Options_Example_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $admin, 'register_options' );
	}

	/**
	 * Registers all hooks with WordPress.
	 */
	public function run(): void {
		$this->loader->run();
	}

	/**
	 * Plugin slug, used as text domain.
	 *
	 * @return string
	 */
	public function get_plugin_name(): string {
		return 'exopite-simple-options-example';
	}

	/**
	 * Plugin version.
	 *
	 * @return string
	 */
	public function get_version(): string {
		return EXOPITE_SIMPLE_OPTIONS_EXAMPLE_VERSION;
	}
}
