<?php
namespace EventEspresso\core\libraries\form_sections\strategies\validation;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class ValidationStrategiesLoader
 *
 * This class is responsible for determining what form input validation strategies are available
 * based solely on the presence of "EE_*_Validation_Strategy.strategy.php" files with the
 * /core/libraries/form_sections/strategies/validation/ folder.
 *
 * the add and remove methods will simply add a requested validation class to a passed array,
 * but only if the requested validation class file is available
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         4.10.0
 *
 */
class ValidationStrategiesLoader {

	/*
	 * @var EE_Validation_Strategy_Base[] $_loaded
	 */
	protected static $_loaded = array();



	/**
	 * returns an array of available EE_Validation_Strategy_Base class names
	 *
	 * @param array $exclude array of inputs to be removed
	 * @param bool  $include if set to true, this will only include elements from the above list,
	 *                       instead of excluding them, IF they are available
	 * @return array
	 */
	public static function get( $exclude = array(), $include = false ) {
		if ( empty( ValidationStrategiesLoader::$_loaded ) ) {
			ValidationStrategiesLoader::load();
		}
		// make sure $exclude is an array
		$exclude = is_array( $exclude ) ? $exclude : array( $exclude );
		// and not numerically indexed
		$exclude = array_key_exists( 0, $exclude ) ? array_flip( $exclude ) : $exclude;
		return $include
			? array_intersect_key( ValidationStrategiesLoader::$_loaded, $exclude )
			: array_diff_key( ValidationStrategiesLoader::$_loaded, $exclude );
	}



	/**
	 * adds the requested validation class to the passed array,
	 * but only if the requested validation class file is available
	 *
	 * @param string $validation_strategy_slug
	 * @param array  $existing_validation_strategies
	 * @return array
	 * @throws \EE_Error
	 */
	public static function add( $validation_strategy_slug = '', array $existing_validation_strategies = array() ) {
		ValidationStrategiesLoader::validateSlug( $validation_strategy_slug );
		return array_merge(
			$existing_validation_strategies,
			array( $validation_strategy_slug => ValidationStrategiesLoader::$_loaded[ $validation_strategy_slug ] )
		);
	}



	/**
	 * adds the requested validation class to the passed array,
	 * but only if the requested validation class file is available
	 *
	 * @param string $validation_strategy_slug
	 * @param array  $existing_validation_strategies
	 * @return array
	 * @throws \EE_Error
	 */
	public static function remove( $validation_strategy_slug = '', array $existing_validation_strategies = array() ) {
		ValidationStrategiesLoader::validateSlug( $validation_strategy_slug, false );
		unset( $existing_validation_strategies[ $validation_strategy_slug ] );
		return $existing_validation_strategies;
	}



	/**
	 * adds the requested validation class to the passed array,
	 * but only if the requested validation class file is available
	 *
	 * @param array  $existing_validation_strategies
	 * @return array
	 * @throws \EE_Error
	 */
	public static function remove_missing_validation_strategies( array $existing_validation_strategies = array() ) {
		foreach ( $existing_validation_strategies as $validation_strategy_slug => $existing_validation_strategy ) {
			try {
				ValidationStrategiesLoader::validateSlug( $validation_strategy_slug );
			} catch ( \Exception $e ) {
				unset( $existing_validation_strategies[ $validation_strategy_slug ] );
			}
		}
		return $existing_validation_strategies;
	}



	/**
	 * @param string $validation_strategy_slug
	 * @param bool   $load_strategies
	 * @return bool
	 * @throws \EE_Error
	 */
	protected static function validateSlug( $validation_strategy_slug = '', $load_strategies = true ) {
		if ( empty( $validation_strategy_slug ) ) {
			throw new \EE_Error(
				__( 'You must supply a slug in order to add a validation strategy.', 'event_espresso' )
			);
		}
		if ( ! $load_strategies ) {
			return true;
		}
		if ( empty( ValidationStrategiesLoader::$_loaded ) ) {
			ValidationStrategiesLoader::load();
		}
		if ( ! isset( ValidationStrategiesLoader::$_loaded[ $validation_strategy_slug ] ) ) {
			throw new \EE_Error(
				sprintf(
					__( 'The "%1$s" validation strategy was not found or could not be loaded.', 'event_espresso' ),
					$validation_strategy_slug
				)
			);
		}
		return true;
	}



	/**
	 * @throws \EE_Error
	 */
	protected static function load() {
		// get all the files in that folder matching the mask
		$filepaths = apply_filters(
			'FHEE__ValidationStrategiesLoader__load__validation_strategies',
			glob( __DIR__ . DS . 'EE_*_Validation_Strategy.strategy.php' )
		);
		if ( empty( $filepaths ) ) {
			return;
		}
		foreach ( (array)$filepaths as $file_path ) {
			// extract filename from path
			$file_path = basename( $file_path );
			// now remove any file extensions
			$validation_strategy_name = '\\' . substr( $file_path, 0, strpos( $file_path, '.' ) );
			// first check to see if the class name represents an actual validation strategy class.
			if ( strpos( strtolower( $validation_strategy_name ), 'validation_strategy' ) === false ) {
				continue;
			}
			if ( ! class_exists( $validation_strategy_name ) ) {
				throw new \EE_Error(
					sprintf(
						__(
							'The "%1$s" validation strategy class can\'t be loaded from %2$s.  Likely there is a typo in the class name or the file name.',
							'event_espresso'
						),
						$validation_strategy_name,
						$file_path
					)
				);
			}
			if ( ! is_subclass_of( $validation_strategy_name, 'EE_Validation_Strategy_Base' ) ) {
				throw new \EE_Error(
					sprintf(
						__(
							'The "%1$s" validation strategy class is not a child of EE_Validation_Strategy_Base.',
							'event_espresso'
						),
						$validation_strategy_name
					)
				);
			}
			$slug = strtolower(
				str_replace( array( '\EE_', '_Validation_Strategy' ), array( '', '' ), $validation_strategy_name )
			);
			ValidationStrategiesLoader::$_loaded[ $slug ] = $validation_strategy_name;
		}
	}




}
// End of file ValidationStrategiesLoader.php
// Location: /ValidationStrategiesLoader.php