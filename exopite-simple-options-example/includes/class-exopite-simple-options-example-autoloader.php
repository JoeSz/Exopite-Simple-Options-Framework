<?php
/**
 * Class autoloader.
 *
 * @package Exopite_Simple_Options_Example
 */

namespace Exopite_Simple_Options_Example;

/**
 * Loads this plugin's classes from WordPress-style file names.
 *
 * Exopite_Simple_Options_Example\Foo_Bar      -> {includes,admin,public}/class-foo-bar.php
 * Exopite_Simple_Options_Example\Sub\Foo_Bar  -> {includes,admin,public}/sub/class-foo-bar.php
 *
 * Pattern: Autoloader. Why: classes load on first use, no require list to maintain.
 */
final class Exopite_Simple_Options_Example_Autoloader {

	/**
	 * Folders searched, relative to the plugin root.
	 */
	private const DIRS = array( 'includes', 'admin', 'public' );

	/**
	 * Registers the autoloader.
	 */
	public static function register(): void {
		spl_autoload_register( array( self::class, 'load' ) );
	}

	/**
	 * Loads a class of this plugin's namespace; ignores all others.
	 *
	 * @param string $class_name Fully qualified class name.
	 */
	public static function load( string $class_name ): void {
		$prefix = __NAMESPACE__ . '\\';
		if ( ! str_starts_with( $class_name, $prefix ) ) {
			return;
		}

		$parts = explode( '\\', substr( $class_name, strlen( $prefix ) ) );
		$class = array_pop( $parts );
		$sub   = '';
		foreach ( $parts as $part ) {
			$sub .= strtolower( str_replace( '_', '-', $part ) ) . '/';
		}
		$file = $sub . 'class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';

		$root = dirname( __DIR__ ) . '/';
		foreach ( self::DIRS as $dir ) {
			$path = $root . $dir . '/' . $file;
			if ( is_readable( $path ) ) {
				require_once $path;
				return;
			}
		}
	}
}
