<?php
/**
 * Contains test class for /core/db_models/EEM_Registration.model.php
 *
 * @since  		4.6.x
 * @package 		Event Espresso
 * @subpackage 	tests
 */

/**
 * All tests for the EEM_Registration class.
 *
 * @since 		4.6.x
 * @package 		Event Espresso
 * @subpackage 	tests
 * @group core/db_models
 */
class EEM_Registration_Test extends EE_UnitTestCase {


	public function setUp() {
		//set timezone string.  NOTE, this is purposely a high positive timezone string because it works better for testing expiry times.
		update_option( 'timezone_string', 'Australia/Sydney' );
		parent::setUp();
	}


	public function tearDown() {
		//restore the timezone string to the default
		update_option( 'timezone_string', '' );
		parent::tearDown();
	}



	/**
	 * This sets up some registrations in the db for testing with.
	 * @since 4.6.0
	 */
	public function _setup_registrations() {
		//setup some dates we'll use for testing with.
		$timezone = new DateTimeZone( 'America/Toronto' );
		$past_start_date = new DateTime( "now -2months", $timezone );
		$future_end_date = new DateTime( "now +2months", $timezone );
		$current = new DateTime( "now", $timezone );
		$formats = array( 'Y-m-d',  'h:i a' );
		$full_format = implode( ' ', $formats );

		//let's setup the args for our payments in an array, then we can just loop through to grab
		//them and set things up.
		$registration_args = array(
			array( 'REG_date' => $past_start_date->format( $full_format )/* , 'timezone' => 'America/Toronto', 'formats' => $formats */),
			//array( 'REG_date' => $future_end_date->format( $full_format ) /*, 'timezone' => 'America/Toronto', 'formats' => $formats*/ ),
			//array( 'REG_date' => $current->sub( new DateInterval( "PT2H") )->format( $full_format ) /*, 'timezone' => 'America/Toronto', 'formats' => $formats*/ ),
			//array( 'REG_date' => $current->add( new DateInterval( "P1M" ) )->format( $full_format) /*, 'timezone' => 'America/Toronto', 'formats' => $formats*/ ),
			//array( 'REG_date' => $past_start_date->format( $full_format ) /*, 'timezone' => 'America/Toronto', 'formats' => $formats*/ ),
		);
		//need to create an event to add all these registrations to because of the capability checks
		//$events = $this->factory->event->create_many( 4,  array( 'EVT_wp_user' => get_current_user_id() ) );
		$registrations = array();
		//foreach ( $events as $event ) {
		for ( $x = 1; $x <= 1; $x++ ) {
			echo "\n\n\n >>>>>>>>>>>>>>>>>>>>>>>>> X  " . $x . " <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<";
			foreach( $registration_args as $att_nmbr => $registration_arg ) {
				echo "\n\n\n >>>>>>>>>>>>>>>>>>>>>>>>> ATT_NMBR  " . $att_nmbr . " <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<";
				$registration_arg = array_merge(
					array(
						'REG_count' 	  => 1,
						'REG_group_size'  => 1,
						'Event' => array(
							'EVT_ID' 	  => $x,
							'EVT_name' 	  => sprintf( 'Event %d', $x ),
							'EVT_wp_user' => get_current_user_id(),
						),
						'Status' => array(
							'STS_ID' => EEM_Registration::status_id_pending_payment,
						),
						'timezone' => 'America/Toronto',
						'formats'  => $formats
					),
					$registration_arg
				);
				echo "\n\n\n " . __LINE__ . ") " . __METHOD__ . "()";
				$this->factory->registration->set_properties_and_relations( $registration_arg );
				$registration = $this->factory->registration->create_object();
				echo "\n\n " . __LINE__ . ") " . __METHOD__ . "() ";
				echo "\n FINAL OBJECT CLASS: " . get_class( $registration );
				echo "\n SPL_OBJECT_HASH: " . spl_object_hash( $registration ) . "\n";
				echo "\n registration->ID(): " . $registration->ID();
				if ( $registration instanceof EE_Registration ) {
					//$registration->save();
					//$registrations[] = $registration;
					//if ( $x === 1 ) {
						echo "\n\n " . __LINE__ . ") " . __METHOD__ . "()";
						echo "\n registration->get('EVT_ID'): " . $registration->get('EVT_ID');
						echo "\n registration->event()->ID(): " . $registration->event()->ID();
						echo "\n registration->event()->name(): " . $registration->event()->name();
					//}
				}
				//set registrations to pending so we can test
				//$reg->set_status( EEM_Registration::status_id_pending_payment );
				//$reg->_add_relation_to( $event, 'Event' );
				//$reg->save();
			}
		}

		$this->assertEquals( 20, EEM_Registration::instance()->count() );
		//$this->assertEquals( 20, count( $registrations ) );
	}



	/**
	 * @since 4.6.0
	 */
	//public function test_get_registrations_per_day_report() {
	//	$this->_setup_registrations();
	//
	//	$regs_per_day = EEM_Registration::instance()->get_registrations_per_day_report();
	//
	//	//first assert count of results
	//	$this->assertEquals( 3, count( $regs_per_day ) );
	//
	//	//next there should be a total = 1 for each result
	//	foreach ( $regs_per_day as $registration ) {
	//		$this->assertEquals( 4, $registration->total );
	//	}
	//}




	public function test_get_registrations_per_event_report() {
		$this->_setup_registrations();
		$regs_per_event = EEM_Registration::instance()->get_registrations_per_event_report();
		echo "\n regs_per_event: \n";
		var_dump( $regs_per_event );
		//first assert total count of results
		$this->assertEquals( 4, count( $regs_per_event ) );

		//next there should be a total = 1 for each result
		foreach ( $regs_per_event as $registration ) {
			$this->assertEquals( 3, $registration->total );
		}
	}



	/**
	 * @group 7965
	 */
	//function test_delete_registrations_with_no_transaction(){
	//	$deletable_count = 5;
	//	$safe_count = 8;
	//	$this->factory->registration->create_many( $deletable_count, array( 'TXN_ID' =>  0 ) );
	//	//$this->factory->registration->set_properties_and_relations( array( 'Transaction' ) );
	//	//$this->factory->registration->create_many( $safe_count );
	//	for ( $i = 0; $i < $safe_count; $i++ ) {
	//		$this->new_model_obj_with_dependencies( 'Registration', array( 'Transaction' ) );
	//	}
	//	$deleted = EEM_Registration::instance()->delete_registrations_with_no_transaction();
	//	$this->assertEquals( $deletable_count, $deleted );
	//}



}
// End of file EEM_Registration_Test.php
// Location: tests/testcases/core/db_models/EEM_Registration_Test.php
