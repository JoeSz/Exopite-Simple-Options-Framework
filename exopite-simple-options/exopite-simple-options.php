<?php
/**
 * Plugin Name:       Exopite Simple Options Framework
 * Plugin URI:        https://github.com/JoeSz/Exopite-Simple-Options-Framework
 * Description:       Options page and metabox framework for other plugins and themes. Install it once, every plugin and theme on the site can use it. It can still be bundled into a plugin or theme instead.
 * Version:           20261117.4
 * Requires at least: 5.0
 * Requires PHP:      8.2
 * Author:            Joe Szalai
 * Author URI:        https://joe.szalai.org
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       exopite-sof
 * Domain Path:       /lang
 *
 * Install this plugin once per site. Other plugins and themes do not contain
 * a copy of the framework, they only call it.
 *
 * ---------------------------------------------------------------------------
 * HOW TO USE IT FROM ANOTHER PLUGIN OR A THEME
 * ---------------------------------------------------------------------------
 *
 * Create the options page or metabox on the "init" hook, never directly in
 * the main plugin file. All plugin files are loaded before "init", so the
 * framework class exists no matter in which order WordPress loaded the
 * plugins. Themes are loaded after the plugins, so this works in
 * functions.php too. The framework only runs in the admin area.
 *
 *     add_action( 'init', 'prefix_register_options' );
 *
 *     function prefix_register_options() {
 *
 *         if ( ! is_admin() ) {
 *             return;
 *         }
 *
 *         // The framework plugin is not active.
 *         if ( ! class_exists( 'Exopite_Simple_Options_Framework' ) ) {
 *             add_action( 'admin_notices', 'prefix_framework_missing_notice' );
 *             return;
 *         }
 *
 *         $config = array(
 *             'type'       => 'menu',               // "menu" or "metabox".
 *             'id'         => 'prefix_options',     // Option name in wp_options.
 *             'parent'     => 'options-general.php',
 *             'submenu'    => true,
 *             'title'      => 'My Plugin',
 *             'capability' => 'manage_options',
 *         );
 *
 *         $fields = array(
 *             array(
 *                 'name'   => 'general',
 *                 'title'  => 'General',
 *                 'fields' => array(
 *                     array(
 *                         'id'    => 'my_text',
 *                         'type'  => 'text',
 *                         'title' => 'My text',
 *                     ),
 *                 ),
 *             ),
 *         );
 *
 *         new Exopite_Simple_Options_Framework( $config, $fields );
 *     }
 *
 * Read the values (front end and admin):
 *
 *     $options = get_exopite_sof_option( 'prefix_options' );    // Menu options, multilang aware.
 *     $options = get_option( 'prefix_options' );                // Menu options, plain array.
 *     $meta    = get_post_meta( $post_id, 'metabox_id', true ); // Metabox, saved as one array.
 *     $value   = get_post_meta( $post_id, 'field_id', true );   // Metabox with 'options' => 'simple'.
 *
 * On the front end check function_exists( 'get_exopite_sof_option' ) if the
 * framework plugin may be inactive.
 *
 * For dependent code:
 * - Exopite_Simple_Options_Framework::VERSION is the framework version.
 * - The action "exopite_sof_loaded" fires on plugins_loaded (priority 1).
 *
 * A complete example with every field type: the "exopite-simple-options-example"
 * plugin in the repository (https://github.com/JoeSz/Exopite-Simple-Options-Framework).
 *
 * Old plugins that still bundle their own copy of the framework keep working.
 * If such a copy is loaded before this plugin, that copy is used for every
 * plugin on the site and administrators see a notice with its path.
 *
 * @package Exopite_Simple_Options_Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

define( 'EXOPITE_SOF_PLUGIN_FILE', __FILE__ );

/*
 * Load the framework right away (not on a hook), so it is in place before any
 * plugin or theme creates an options page on "init".
 * The class_exists guards inside keep an earlier loaded copy working.
 */
require_once __DIR__ . '/exopite-simple-options-framework-class.php';
require_once __DIR__ . '/class-exopite-sof-plugin.php';

Exopite_Sof_Plugin::register_hooks();
