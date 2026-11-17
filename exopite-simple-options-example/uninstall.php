<?php
/**
 * Fired when the plugin is uninstalled: removes the example's own data.
 *
 * @package Exopite_Simple_Options_Example
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Options page.
delete_option( 'exopite_sof_example' );

// Metabox (one array) and "simple" metabox (one key per field).
foreach ( array( 'exopite_sof_example_meta', 'exopite_sof_example_simple_text', 'exopite_sof_example_simple_switch', 'exopite_sof_example_simple_select' ) as $exopite_sof_example_meta_key ) {
	delete_post_meta_by_key( $exopite_sof_example_meta_key );
}
