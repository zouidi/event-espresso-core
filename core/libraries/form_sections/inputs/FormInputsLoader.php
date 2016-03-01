<?php
namespace EventEspresso\core\libraries\form_sections\inputs;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class FormInputsLoader
 *
 * This class is responsible for determining what EE_Form_Input classes are available
 * based solely on the presence of "EE_*_Input.input.php" files with the
 * /core/libraries/form_sections/inputs/ folder.
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class FormInputsLoader {

	/*
	 * @var EE_Form_Input_Base[] $_strategies
	 */
	protected static $_loaded = array();



	/**
	 * returns an array of available EE_Form_Input_Base class names
	 *
	 * @param array $exclude array of inputs to be removed
	 * @return array
	 * @throws \EE_Error
	 */
	public static function get( $exclude = array() ) {
		if ( empty( FormInputsLoader::$_loaded ) ) {
			FormInputsLoader::load();
		}
		$exclude = is_array( $exclude ) ? $exclude : array( $exclude );
		return array_diff_key( FormInputsLoader::$_loaded, array_flip( $exclude ) );
	}



	/**
	 * @throws \EE_Error
	 */
	protected static function load() {
		// get all the files in that folder matching the mask
		$filepaths = apply_filters(
			'FHEE__FormInputsLoader__load__form_inputs',
			glob( __DIR__ . DS . 'EE_*_Input.input.php' )
		);
		if ( empty( $filepaths ) ) {
			return;
		}
		foreach ( (array)$filepaths as $file_path ) {
			// extract filename from path
			$file_path = basename( $file_path );
			// now remove any file extensions
			$form_input_name = '\\' . substr( $file_path, 0, strpos( $file_path, '.' ) );
			// first check to see if the class name represents an actual form input class.
			if ( strpos( strtolower( $form_input_name ), '_input' ) === false ) {
				continue;
			}
			if ( ! class_exists( $form_input_name ) ) {
				throw new \EE_Error(
					sprintf(
						__(
							'The "%1$s" form input class can\'t be loaded from %2$s.  Likely there is a typo in the class name or the file name.',
							'event_espresso'
						),
						$form_input_name,
						$file_path
					)
				);
			}
			if ( ! is_subclass_of( $form_input_name, 'EE_Form_Input_Base' ) ) {
				throw new \EE_Error(
					sprintf(
						__(
							'The "%1$s" form input class is not a child of EE_Form_Input_Base.',
							'event_espresso'
						),
						$form_input_name
					)
				);
			}
			$slug = strtolower(
				str_replace( array( '\EE_', '_Input' ), array( '', '' ), $form_input_name )
			);
			FormInputsLoader::$_loaded[ $slug ] = $form_input_name;
		}
	}


}
// End of file FormInputsLoader.php
// Location: core/libraries/form_sections/inputs/FormInputsLoader.php