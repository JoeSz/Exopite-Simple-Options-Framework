<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: url
 *
 */

if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_url' ) ) {
	require_once __DIR__ . '/text.php';
	class Exopite_Simple_Options_Framework_Field_url extends Exopite_Simple_Options_Framework_Field_text {}
}
