<?php
/**
 * Loader for EE Unit Tests initializes plugin and gets thing off to a start.
 *
 * @since 		4.3.0
 * @package 		Event Espresso
 * @subpackage 	tests
 */
echo EE_UNIT_TEST_DEBUG ? "\n " . __LINE__ . ") " . basename( __FILE__ ) . "()\n\n" : '';
/**
 * Filter tests bypass so that every time PHPUnit is ran, we setup EE properly as
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
// don't bootstrap EE immediately upon loading espresso.php
tests_add_filter( 'FHEE__espresso__bootstrap', '__return_false');
// prepare to run activation for first request
delete_option( 'espresso_db_update' );
// Start loading EE
require dirname( __FILE__ ) . '/../../espresso.php';
// load PSR4 autoloader and other files needed for bootstrapping
require_once( EE_PLUGIN_DIR . 'core/Psr4Autoloader.php' );
require_once( EE_CORE . 'EE_Bootstrap.core.php' );
require_once( EE_CORE . 'EE_Dependency_Map.core.php' );
require_once( EE_CORE . 'EE_Registry.core.php' );
require_once( EE_CORE . 'request_stack' . DS . 'EE_Request.core.php' );
require_once( EE_CORE . 'request_stack' . DS . 'EE_Response.core.php' );
require_once( EE_TESTS_DIR . 'mocks/addons/eea-new-addon/eea-new-addon.php' );





