'use strict';

/**
 * Minimal build script: minifies our own JS and CSS from
 * exopite-simple-options/assets/dev into exopite-simple-options/assets.
 *
 * Vendored libraries are not touched. Usage: npm run build
 */

const fs = require( 'fs' );
const path = require( 'path' );
const { minify } = require( 'terser' );
const CleanCSS = require( 'clean-css' );

const dev = path.join( __dirname, '..', 'exopite-simple-options', 'assets', 'dev' );
const out = path.join( __dirname, '..', 'exopite-simple-options', 'assets' );

// Source file in dev => output file in assets (names are enqueued by the PHP).
const jsFiles = {
	'scripts.js': 'scripts.min.js',
	'ace-loader.js': 'ace-loader.min.js',
	'loader-color-picker.js': 'loader-color-picker.min.js',
	'loader-datepicker.js': 'loader-datepicker.min.js',
	'loader-fine-uploader.js': 'loader-fine-uploader.min.js',
	'loader-jquery-chosen.js': 'loader-jquery-chosen.min.js',
	'loader-jquery-finderselect.js': 'loader-jquery-finderselect.min.js',
	'loader-jquery-trumbowyg.js': 'loader-jquery-trumbowyg.min.js',
	// Enqueued without ".min" in the name, but it is the minified dev source.
	'loader-minicolors.js': 'loader-minicolors.js',
};

const cssFiles = {
	'styles.css': 'styles.css',
};

async function build() {
	for ( const [ src, dest ] of Object.entries( jsFiles ) ) {
		const code = fs.readFileSync( path.join( dev, src ), 'utf8' );
		// Keep top-level names: updateRangeInput() etc. are public globals.
		const result = await minify( code, { compress: true, mangle: { toplevel: false } } );
		if ( ! result.code ) {
			throw new Error( 'Empty output for ' + src );
		}
		fs.writeFileSync( path.join( out, dest ), result.code );
		console.log( src + ' -> ' + dest + ' (' + result.code.length + ' bytes)' );
	}

	for ( const [ src, dest ] of Object.entries( cssFiles ) ) {
		const code = fs.readFileSync( path.join( dev, src ), 'utf8' );
		// Level 0: whitespace/comments only, never merges or reorders rules.
		const result = new CleanCSS( { level: 0 } ).minify( code );
		if ( result.errors.length ) {
			throw new Error( result.errors.join( '\n' ) );
		}
		fs.writeFileSync( path.join( out, dest ), result.styles );
		console.log( src + ' -> ' + dest + ' (' + result.styles.length + ' bytes)' );
	}
}

build().catch( ( error ) => {
	console.error( error );
	process.exit( 1 );
} );
