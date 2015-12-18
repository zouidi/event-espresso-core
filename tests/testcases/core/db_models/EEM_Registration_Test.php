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

		// let's setup the args for our registration dates in an array,
		// then we can just use them in the loop below to set things up.
		$registration_dates = array(
			$past_start_date->format( $full_format ),
			$future_end_date->format( $full_format ),
			$current->sub( new DateInterval( "PT2H") )->format( $full_format ),
			$current->add( new DateInterval( "P1M" ) )->format( $full_format),
			$past_start_date->format( $full_format ),
		);
		// need to create 4 events to add all these registrations to because of the capability checks
		for ( $x = 1; $x <= 4; $x++ ) {
			$event = $this->factory->event->create_object(
				array(
					'EVT_name'    => sprintf( 'Event %d', $x ),
					'EVT_wp_user' => get_current_user_id(),
				)
			);
			// create 5 events for each event
			for ( $y = 0; $y < 5; $y++ ) {
				$registration_arg = array(
					'REG_date' 		=> $registration_dates[ $y ],
					'REG_count' 	  => $y + 1,
					'REG_group_size'  => 5,
					'Event' => array(
						'EVT_ID' 	  => $event->ID(),
					),
					'Status' => array(
						'STS_ID' => EEM_Registration::status_id_pending_payment,
					),
					'timezone' => 'America/Toronto',
					'formats'  => $formats
				);
				$this->factory->registration->set_properties_and_relations( $registration_arg );
				$this->factory->registration->create_object();
			}
		}

		$this->assertEquals( 20, EEM_Registration::instance()->count() );
	}



	/**
	 * @since 4.6.0
	 */
	public function test_get_registrations_per_day_report() {
		$this->_setup_registrations();

		$regs_per_day = EEM_Registration::instance()->get_registrations_per_day_report();

		//first assert count of results
		$this->assertEquals( 3, count( $regs_per_day ) );

		//next there should be a total = 1 for each result
		foreach ( $regs_per_day as $registration ) {
			$this->assertEquals( 4, $registration->total );
		}
	}




	public function test_get_registrations_per_event_report() {
		$this->_setup_registrations();
		$regs_per_event = EEM_Registration::instance()->get_registrations_per_event_report();
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
	function test_delete_registrations_with_no_transaction(){
		$deletable_count = 5;
		$safe_count = 8;
		for ( $i = 0; $i < $deletable_count; $i++ ) {
			// first reset any defaults that the factories set automagically
			$this->factory->registration->set_properties_and_relations( null );
			// create a default object with NO relations
			$this->factory->registration->create_object();
		}
		for ( $i = 0; $i < $safe_count; $i++ ) {
			// reset default properties and relations again
			$this->factory->registration->set_properties_and_relations(
				array(
					'REG_date' => time(),
					// include TXN relation this time but leave array empty to use all defaults
					'Transaction' => null
				)
			);
			$this->factory->registration->create_object();
		}
		$deleted = EEM_Registration::instance()->delete_registrations_with_no_transaction();
		$this->assertEquals( $deletable_count, $deleted );
	}



}
// End of file EEM_Registration_Test.php
// Location: tests/testcases/core/db_models/EEM_Registration_Test.php
