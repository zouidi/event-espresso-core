<?php
if ( !defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 *
 * EEM_CTP_Base_Test
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Mike Nelson
 *
 */
class EEM_CPT_Base_Test extends EE_UnitTestCase{
	function test__get_default_where_conditions(){
		//use reflection to access this protected method
		$method = new ReflectionMethod('EEM_Event', '_get_default_where_conditions');
		$method->setAccessible(true);

		//try for normal
		$this->assertEquals( array(
			'status' => array('NOT IN',array('auto-draft','trash') ),
			'post_type' => 'espresso_events',
		), $method->invoke( EEM_Event::instance() ) );

		//minimum
		$method = new ReflectionMethod('EEM_Event', '_get_minimum_where_conditions');
		$method->setAccessible(true);
		$this->assertEquals( array(
			'post_type' => 'espresso_events',
		), $method->invoke( EEM_Event::instance() ) );
	}
}

// End of file EEM_CTP_Base_Test.php