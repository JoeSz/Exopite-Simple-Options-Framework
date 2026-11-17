<?php
/**
 * Exopite Simple Options Framework Example
 *
 * Example how a plugin uses the Exopite Simple Options Framework plugin.
 * The plugin does not contain the framework, it only calls it. The options
 * page and the metaboxes are in admin/class-exopite-simple-options-example-admin.php.
 *
 * @package           Exopite_Simple_Options_Example
 *
 * @wordpress-plugin
 * Plugin Name:       Exopite Simple Options Framework Example
 * Plugin URI:        https://github.com/JoeSz/Exopite-Simple-Options-Framework
 * Description:       Example how a plugin uses the Exopite Simple Options Framework plugin: an options page with every field type, a metabox and a "simple" metabox. Needs the "Exopite Simple Options Framework" plugin.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.2
 * Author:            Joe Szalai
 * Author URI:        https://joe.szalai.org
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       exopite-simple-options-example
 */

namespace Exopite_Simple_Options_Example;

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'EXOPITE_SIMPLE_OPTIONS_EXAMPLE_VERSION', '1.0.0' );
define( 'EXOPITE_SIMPLE_OPTIONS_EXAMPLE_FILE', __FILE__ );
define( 'EXOPITE_SIMPLE_OPTIONS_EXAMPLE_PATH', plugin_dir_path( __FILE__ ) );

require_once EXOPITE_SIMPLE_OPTIONS_EXAMPLE_PATH . 'includes/class-exopite-simple-options-example-autoloader.php';
Exopite_Simple_Options_Example_Autoloader::register();

/**
 * Starts the plugin.
 */
function run_exopite_simple_options_example(): void {
	$plugin = new Exopite_Simple_Options_Example();
	$plugin->run();
}

run_exopite_simple_options_example();
