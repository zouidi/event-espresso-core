<?php
if ( !defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 *
 * EEM_Post_Meta_Test
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Mike Nelson
 *
 */
class EEM_Post_Meta_Test extends EE_UnitTestCase{
	function test_get_all(){
		$postmeta = $this->new_model_obj_with_dependencies( 'Post_Meta', array( 'meta_key' => 'foo', 'meta_value' => 'bar' ) );
		$postmetas = EEM_Post_Meta::reset()->get_all( array( array( 'meta_key' => 'foo', 'meta_value' => 'bar', 'Attendee.ATT_ID' => $postmeta->get('post_id') ) ) );
		$this->assertEEModelObjectsEquals( $postmeta, reset( $postmetas ) );
		$this->assertEquals( 1, count( $postmetas ) );
	}
}

// End of file EEM_Post_Meta_Test.php