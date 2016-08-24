<?php
/**
 * Bootstrap for EE Unit Tests
 *
 * @since 		4.3.0
 * @package 		Event Espresso
 * @subpackage 	Tests
 */
// if you get fatal errors or memory overruns or other nasty fun stuff like that during the unit tests,
// then set EE_UNIT_TEST_DEBUG to true (bool) and you'll get a list of test case classes being run and other debug info
define( 'EE_UNIT_TEST_DEBUG', false );   // true    false
if ( EE_UNIT_TEST_DEBUG ) {
	echo "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n";
	echo "\n ************************************************************************************************************** ";
	echo "\n ******************************************** BOOTSTRAP UNIT TESTS ********************************************";
	echo "\n ************************************************************************************************************** \n\n\n";
	echo "\n " . __LINE__ . ") " . basename( __FILE__ ) . "()";
}

require( dirname( __FILE__ ) . '/includes/define-constants.php' );
if ( ! file_exists( WP_TESTS_DIR . '/includes/functions.php' ) ) {
	die( "The WordPress PHPUnit test suite could not be found.\n" );
}
require_once WP_TESTS_DIR . '/includes/functions.php';
function _install_and_load_event_espresso() {
	require EE_TESTS_DIR . 'includes/loader.php';
}
tests_add_filter( 'muplugins_loaded', '_install_and_load_event_espresso' );
require WP_TESTS_DIR . '/includes/bootstrap.php';
//Load the EE_specific testing tools
require EE_TESTS_DIR . 'includes/EE_UnitTestCase.class.php';

bootstrap_unit_tests();

function bootstrap_unit_tests() {
	if ( EE_UNIT_TEST_DEBUG ) {
		echo "\n----------------------------------------------------------------------------------------";
		echo "\n ---------------------------------- BOOTSTRAPPING EE ---------------------------------- ";
		echo "\n----------------------------------------------------------------------------------------\n";
	}
	add_filter(
		'FHEE__EE_Load_Espresso_Core___load_dependency_map__dependency_map',
		array( 'EE_UnitTestCase', 'load_dependency_map' )
	);
	add_filter(
		'FHEE__EE_Load_Espresso_Core___load_registry__registry',
		array( 'EE_UnitTestCase', 'load_registry' )
	);
	// prepare to run activation for first request
	delete_option( 'espresso_db_update' );
	// here we go
	\EE_UnitTestCase::set_EE_Bootstrap( new \EE_Bootstrap() );
	remove_action( 'AHEE__EE_System__load_espresso_addons', 'load_espresso_new_addon' );
	do_action( 'plugins_loaded' );
	do_action( 'init' );
	if ( EE_UNIT_TEST_DEBUG ) {
		echo "\n --------------------------------- BOOTSTRAP COMPLETE --------------------------------- \n\n";
	}
}

/**
 * redefining wp_mail function here as a mock for our tests.  Has to be done early
 * to override the existing wp_mail.  Tests can use the given filter to adjust the responses as necessary.
 */
function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
	return apply_filters( 'FHEE__wp_mail', true, $to, $subject, $message, $headers, $attachments );
}