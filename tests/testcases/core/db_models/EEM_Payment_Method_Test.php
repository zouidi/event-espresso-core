<?php
if ( !defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 *
 * EEM_Payment_Method_Test
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Mike Nelson
 *
 */
class EEM_Payment_Method_Test extends EE_UnitTestCase{
	/**
	 * @group 7201
	 */
	function test_ensure_is_obj(){
		// we don't need any other relations for this test so override with null to only receive the one object
		$this->factory->payment_method->set_properties_and_relations( null );
		$pm = $this->factory->payment_method->create_object();
		$this->assertNotEmpty( $pm->ID() );
		$this->assertEquals( $pm, EEM_Payment_Method::instance()->ensure_is_obj( $pm ) );
		$this->assertEquals( $pm, EEM_Payment_Method::instance()->ensure_is_obj( $pm->ID() ) );
		$this->assertEquals( $pm, EEM_Payment_Method::instance()->ensure_is_obj( $pm->slug() ) );
	}

	/**
	 * @group 7201
	 */
	function test_get_one_by_slug(){
		// we don't need any other relations for this test so override with null to only receive the one object
		$this->factory->payment_method->set_properties_and_relations( null );
		$pm = $this->factory->payment_method->create_object();
		$this->assertNotEmpty( $pm->ID() );
		$this->assertEquals( $pm, EEM_Payment_Method::instance()->get_one_by_ID( $pm->ID() ) );
		$this->assertEquals( $pm, EEM_Payment_Method::instance()->get_one_by_slug( $pm->slug()  ) );
	}
}

// End of file EEM_Payment_Method_Test.php
// Location: testcases\core\db_models\EEM_Payment_Method_Test.php