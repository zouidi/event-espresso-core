<?php
/**
 * Loader for EE Unit Tests initializes plugin and gets thing off to a start.
 *
 * @since 		4.3.0
 * @package 		Event Espresso
 * @subpackage 	tests
 */


/**
 * Filter testsbypass so that every time PHPUnit is ran, we setup EE properly as
 * if it were an activation.
 *
 * @since 4.3.0
 *
 */
tests_add_filter('FHEE__EE_System__detect_if_activation_or_upgrade__testsbypass', '__return_true');

//make sure EE_session does not load
tests_add_filter( 'FHEE_load_EE_Session', '__return_false' );
// and don't set cookies
tests_add_filter( 'FHEE__EE_Front_Controller____construct__set_test_cookie', '__return_false' );

tests_add_filter( 'FHEE__EE_Error__get_error__show_normal_exceptions', '__return_true');

// load dependency_map and registry mock
tests_add_filter( 'AHEE__EE_Load_Espresso_Core__handle_request__start', 'espresso_tests_load_espresso_core' );
tests_add_filter( 'FHEE__EE_Load_Espresso_Core___load_dependency_map', 'espresso_tests_load_dependency_map' );
tests_add_filter( 'FHEE__EE_Load_Espresso_Core___load_registry', 'espresso_tests_load_registry_mock' );
tests_add_filter( 'FHEE__EE_Registry__instance', 'espresso_tests_swap_registry' );

require __DIR__ . '/../../espresso.php';

/**
 * espresso_tests_load_espresso_core
 *
 * @param \EE_Load_Espresso_Core $espresso_core
 * @return \EE_Dependency_Map
 * @throws \EE_Error
 */
function espresso_tests_load_espresso_core( \EE_Load_Espresso_Core $espresso_core ) {
}

/**
 * espresso_tests_load_dependency_map
 *
 * @param \EE_Dependency_Map $dependency_map
 * @return \EE_Dependency_Map
 * @throws \EE_Error
 */
function espresso_tests_load_dependency_map( EE_Dependency_Map $dependency_map ) {
	EE_Dependency_Map::register_dependencies(
		'EE_Registry_Mock',
		array( 'EE_Dependency_Map' => EE_Dependency_Map::load_from_cache )
	);
	EE_Dependency_Map::register_class_loader( 'EE_Registry_Mock' );
	EE_Dependency_Map::register_dependencies(
		'EE_Session_Mock',
		array( 'EE_Encryption' => EE_Dependency_Map::load_from_cache )
	);
	EE_Dependency_Map::register_class_loader( 'EE_Session_Mock' );
	return $dependency_map;
}

/**
 * espresso_tests_load_registry_mock
 *
 * @return \EE_Registry_Mock
 * @throws \EE_Error
 */
function espresso_tests_load_registry_mock() {
	static $registry = null;
	if ( ! $registry instanceof EE_Registry ) {
		add_filter(
			'FHEE__EE_Registry____construct___class_abbreviations',
			function ( $class_abbreviations = array() ) {
				$class_abbreviations['EE_Session_Mock'] = 'SSN';
				return $class_abbreviations;
			}
		);
		add_filter(
			'FHEE__EE_Registry__load_core__core_paths',
			function ( $core_paths = array() ) {
				$core_paths[] = EE_TESTS_DIR . 'mocks' . DS . 'core' . DS;
				return $core_paths;
			}
		);
		require_once EE_TESTS_DIR . 'mocks/core/EE_Registry_Mock.core.php';
		$registry = EE_Registry_Mock::instance( EE_Dependency_Map::instance() );
		$registry->initialize();
		$registry->load_mock( 'Session_Mock' );
	}
	return $registry;
}

/**
 * @param \EE_Registry $Registry
 * @return \EE_Registry_Mock
 */
function espresso_tests_swap_registry( \EE_Registry $Registry ) {
	require_once EE_TESTS_DIR . 'mocks/core/EE_Registry_Mock.core.php';
	return $Registry instanceof EE_Registry_Mock ? $Registry : espresso_tests_load_registry_mock();
}