<?php
if ( ! defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}
/**
 * EE_Load_Textdomain
 *
 * @package		Event Espresso
 * @subpackage	/includes/core/EE_Load_Textdomain.core.php
 * @author		Darren Ethier
 */
class EE_Load_Textdomain extends EE_Base {

	const EE_LANG_FILES_URL = 'https://github.com/eventespresso/languages-ee4/blob/master/';

	/**
	 * holds the current lang in WP
	 * @var string
	 */
	private static $_lang;

	/**
	 * @var EEH_Sideloader $_sideloader
	 */
	private static $_sideloader;


	/**
	 * this takes care of retrieving a matching textdomain for event espresso for the current WPLANG from EE GitHub repo (if necessary) and then loading it for translations.
	 * should only be called in wp plugins_loaded callback
	 *
	 * @return void
	 * @throws \EE_Error
	 */
	public static function load_textdomain() {
		self::_maybe_get_language_file();
		//now load the textdomain
		if ( ! empty( self::$_lang ) ) {
			if ( is_readable( EE_LANGUAGES_SAFE_DIR . 'event_espresso-' . self::$_lang . '.mo' ) ) {
				load_plugin_textdomain('event_espresso', FALSE, EE_LANGUAGES_SAFE_LOC);
			} else if ( is_readable( EE_LANGUAGES_SAFE_DIR . 'event-espresso-4-' . self::$_lang . '.mo' ) ) {
				load_textdomain( 'event_espresso', EE_LANGUAGES_SAFE_DIR . 'event-espresso-4-' . self::$_lang . '.mo'  );
			}
		} else {
			load_plugin_textdomain( 'event_espresso', FALSE, dirname( EE_PLUGIN_BASENAME ) . '/languages/');
		}
	}



	/**
	 * The purpose of this method is to side load the lang file for the given WPLANG locale (if necessary).
	 *
	 * @access private
	 * @static
	 * @return void
	 * @throws \EE_Error
	 */
	private static function _maybe_get_language_file() {
		self::$_lang = get_locale();
		if (
			empty( self::$_lang )
			|| $has_check = get_option( 'ee_lang_check_' . self::$_lang . '_' . EVENT_ESPRESSO_VERSION )
		) {
			return;
		}
		//if lang is en_US or empty then lets just get out.  (Event Espresso core is en_US)
		if ( empty( self::$_lang ) || self::$_lang === 'en_US' ) {
			return;
		}
		$filename = 'event_espresso-' . self::$_lang . '.mo';
		//made it here so let's get the file from the github repo
		/** @var EEH_Sideloader $sideloader */
		$sideloader = EE_Load_Textdomain::_get_sideloader();
		$sideloader->init(
			array(
				'_upload_to'     => EE_PLUGIN_DIR_PATH . 'languages/',
				'_upload_from'   => EE_Load_Textdomain::EE_LANG_FILES_URL  . $filename . '?raw=true',
				'_new_file_name' => $filename
			)
		);
		$sideloader->sideload();
		update_option( 'ee_lang_check_' . self::$_lang . '_' . EVENT_ESPRESSO_VERSION, 1 );
	}



	/**
	 * @param \EEH_Sideloader $sideloader
	 * @return void
	 */
	public static function set_sideloader( EEH_Sideloader $sideloader ) {
		EE_Load_Textdomain::$_sideloader = $sideloader;
	}



	/**
	 * @return \EEH_Sideloader
	 * @throws \EE_Error
	 */
	private static function _get_sideloader() {
		if ( ! EE_Load_Textdomain::$_sideloader instanceof EEH_Sideloader ) {
			throw new \EE_Error(
				__(
					'A valid instance of the EEH_Sideloader class is required for EE_Load_Textdomain to function properly.',
					'event_espresso'
				)
			);
		}
		return EE_Load_Textdomain::$_sideloader;
	}


} //end EE_Load_Textdomain
