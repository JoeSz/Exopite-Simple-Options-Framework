<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
/**
 *
 * Helper Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'Exopite_Simple_Options_Framework_Helper' ) ) {

	class Exopite_Simple_Options_Framework_Helper {

		/**
		 * Get language defaults
		 *
		 * ToDos:
		 * - add options to disable multilang
		 * - automatically save in value[current] also if no multilang plugin installed
		 *   this case without multilang plugin installed, return 'all'
		 *   because then developer will see the options and recognise the lang param,
		 *   then may think about to "turn off" the function or handle different languages
		 */
		public static function get_language_defaults( $enabled = true ) {

			if ( ! $enabled ) {
				return false;
			}
			$multilang = array();

			if ( class_exists( 'SitePress' ) || class_exists( 'Polylang' ) || function_exists( 'qtrans_getSortedLanguages' ) ) {

				if ( class_exists( 'SitePress' ) ) {

					global $sitepress;
					$multilang['default']   = $sitepress->get_default_language();
					$multilang['current']   = $sitepress->get_current_language();
					$multilang['languages'] = $sitepress->get_active_languages();

				} else if ( class_exists( 'Polylang' ) ) {

					global $polylang;
					$current    = pll_current_language();
					$default    = pll_default_language();
					$current    = ( empty( $current ) ) ? $default : $current;
					$poly_langs = $polylang->model->get_languages_list();
					$languages  = array();

					foreach ( $poly_langs as $p_lang ) {
						$languages[ $p_lang->slug ] = $p_lang->slug;
					}

					$multilang['default']   = $default;
					$multilang['current']   = $current;
					$multilang['languages'] = $languages;

				} else if ( function_exists( 'qtrans_getSortedLanguages' ) ) {

					global $q_config;
					$multilang['default']   = $q_config['default_language'];
					$multilang['current']   = $q_config['language'];
					$multilang['languages'] = array_flip( qtrans_getSortedLanguages() );

				}

			} else {

				$default                = mb_substr( get_locale(), 0, 2 );
				$multilang['default']   = $default;
				$multilang['current']   = $default;
				$multilang['languages'] = array( $default );

			}

			$multilang = apply_filters( 'exopite-simple-options-framework-language-defaults', $multilang );

			return ( ! empty( $multilang ) ) ? $multilang : false;

		}

	}

}
