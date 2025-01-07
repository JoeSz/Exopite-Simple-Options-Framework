<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Field: email
 *
 */

if ( ! class_exists( 'Exopite_Simple_Options_Framework_Field_email' ) ) {
	require_once __DIR__ . '/text.php';
	class Exopite_Simple_Options_Framework_Field_email extends Exopite_Simple_Options_Framework_Field_text {}
}