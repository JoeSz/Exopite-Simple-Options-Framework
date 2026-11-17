<?php
/**
 * Plugin Name:       Exopite Simple Options Framework Example
 * Plugin URI:        https://github.com/JoeSz/Exopite-Simple-Options-Framework
 * Description:       Example how a plugin uses the Exopite Simple Options Framework plugin: an options page with every field type, a metabox and a "simple" metabox. Needs the "Exopite Simple Options Framework" plugin.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      8.2
 * Author:            Joe Szalai
 * Author URI:        https://joe.szalai.org
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       exopite-sof-example
 *
 * The plugin does not contain the framework. It only calls the classes of the
 * "Exopite Simple Options Framework" plugin, see includes/class-exopite-sof-example.php.
 *
 * @package Exopite_Sof_Example
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

require_once __DIR__ . '/includes/class-exopite-sof-example.php';

Exopite_Sof_Example::register_hooks();
