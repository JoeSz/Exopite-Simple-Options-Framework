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
 * @package Exopite_Simple_Options_Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

define( 'EXOPITE_SOF_PLUGIN_FILE', __FILE__ );

/*
 * Load the framework right away (not on a hook), so plugins and themes loaded
 * after this one use this copy instead of a bundled one.
 * The class_exists guards inside keep an earlier loaded copy working.
 */
require_once __DIR__ . '/exopite-simple-options-framework-class.php';
require_once __DIR__ . '/class-exopite-sof-plugin.php';

Exopite_Sof_Plugin::register_hooks();
