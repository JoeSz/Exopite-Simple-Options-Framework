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
 * This file is only the plugin bootstrap. When the folder is bundled inside
 * another plugin or theme, this file is not loaded and nothing changes.
 *
 * ---------------------------------------------------------------------------
 * HOW TO USE IT FROM ANOTHER PLUGIN OR A THEME
 * ---------------------------------------------------------------------------
 *
 * 1. Install and activate this plugin once per site.
 *
 * 2. In your plugin or theme, create the options page or metabox on a hook,
 *    never directly in the main plugin file. All plugin files are loaded
 *    before "plugins_loaded", so on "init" the framework class exists no
 *    matter in which order WordPress loaded the plugins. Themes are loaded
 *    after the plugins, so this works in functions.php too.
 *
 *    The framework only runs in the admin area. Create it on "init" (or on
 *    "admin_menu" with priority 0), so its admin_menu, admin_init and
 *    save_post hooks are still registered in time.
 *
 *        add_action( 'init', 'prefix_register_options' );
 *
 *        function prefix_register_options() {
 *
 *            if ( ! is_admin() ) {
 *                return;
 *            }
 *
 *            if ( ! class_exists( 'Exopite_Simple_Options_Framework' ) ) {
 *
 *                // Optional: fall back to a copy bundled with your plugin.
 *                $bundled = __DIR__ . '/exopite-simple-options/exopite-simple-options-framework-class.php';
 *
 *                if ( ! file_exists( $bundled ) ) {
 *                    add_action( 'admin_notices', 'prefix_framework_missing_notice' );
 *                    return;
 *                }
 *
 *                require_once $bundled;
 *            }
 *
 *            $config = array(
 *                'type'       => 'menu',               // "menu" or "metabox".
 *                'id'         => 'prefix_options',     // Option name in wp_options.
 *                'parent'     => 'options-general.php',
 *                'submenu'    => true,
 *                'title'      => 'My Plugin',
 *                'capability' => 'manage_options',
 *            );
 *
 *            $fields = array(
 *                array(
 *                    'name'   => 'general',
 *                    'title'  => 'General',
 *                    'fields' => array(
 *                        array(
 *                            'id'    => 'my_text',
 *                            'type'  => 'text',
 *                            'title' => 'My text',
 *                        ),
 *                    ),
 *                ),
 *            );
 *
 *            new Exopite_Simple_Options_Framework( $config, $fields );
 *        }
 *
 *    Because the bundled copy above is only loaded on "init" and only if the
 *    class does not exist yet, this plugin always wins over bundled copies,
 *    whatever the load order is. A bundled copy that is required directly in
 *    a main plugin file (not on a hook) can still be loaded first; then an
 *    admin notice shows its path and version.
 *
 * 3. Read the values (front end and admin):
 *
 *        $options = get_exopite_sof_option( 'prefix_options' ); // Menu options, multilang aware.
 *        $options = get_option( 'prefix_options' );             // Plain options array.
 *        $meta    = get_post_meta( $post_id, 'metabox_id', true ); // Metabox, saved as one array.
 *        $value   = get_post_meta( $post_id, 'field_id', true );   // Metabox with 'options' => 'simple'.
 *
 *    get_exopite_sof_option() is defined by the framework class file, check
 *    it with function_exists() on the front end if the plugin may be inactive.
 *
 * Useful for dependent code:
 * - Action "exopite_sof_loaded" (plugins_loaded, priority 1): fires only when
 *   this plugin is active. Do not rely on it alone, it does not fire for
 *   bundled copies.
 * - Exopite_Simple_Options_Framework::VERSION: the version that is in use.
 *
 * All field types and config keys: see README.md
 * (https://github.com/JoeSz/Exopite-Simple-Options-Framework).
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
