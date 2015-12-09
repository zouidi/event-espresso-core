<?php
/**
 * Contains test class for /core/db_models/EEM_Event.model.php
 *
 * @since  		4.6.x
 * @package 		Event Espresso
 * @subpackage 	tests
 */

/**
 * All tests for the EEM_Event class.
 *
 * @since 		4.6.x
 * @package 		Event Espresso
 * @subpackage 	tests
 * @group core/db_models
 */
class EEM_Event_Test extends EE_UnitTestCase {


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
	 * This just sets up some events in the db for running certain tests that query getting events back.
	 * @since 4.6.x
	 */
	protected function _setup_events() {
		//setup some dates we'll use for testing with.
		$timezone = new DateTimeZone( 'America/Toronto' );
		$upcoming_start_date = new DateTime( "now +2hours", $timezone );
		$past_start_date = new DateTime( "now -2days", $timezone );
		$current_end_date = new DateTime( "now +2days", $timezone );
		$current = new DateTime( "now", $timezone );
		$formats = array( 'Y-d-m',  'h:i a' );
		$full_format = implode( ' ', $formats );

		//setup some datetimes to attach to events.
		$datetime_args = array(
			1 => array(
				'DTT_name' 		=> 'expired_datetime',
				'DTT_EVT_start' => $past_start_date->format( $full_format ),
				'DTT_EVT_end' 	=> $past_start_date->format( $full_format)
			),
			2 => array(
				'DTT_name' 		=> 'upcoming_datetime',
				'DTT_EVT_start' => $upcoming_start_date->format( $full_format ),
				'DTT_EVT_end' 	=> $upcoming_start_date->format( $full_format)
			),
			3 	=> array(
				'DTT_name' 		=> 'active_datetime',
				'DTT_EVT_start' => $current->sub( new DateInterval( "PT2H") )->format( $full_format ),
				'DTT_EVT_end'   => $current_end_date->add( new DateInterval( "PT2H" ) )->format( $full_format)
			),
			4 => array(
				'DTT_name' 		=> 'sold_out_datetime',
				'DTT_EVT_start' => $upcoming_start_date->format( $full_format ),
				'DTT_EVT_end'   => $upcoming_start_date->format( $full_format),
				'DTT_reg_limit' => 10,
				'DTT_sold' 		=> 10
			),
			5 => array(
				'DTT_name' 		=> 'inactive_datetime',
				'DTT_EVT_start' => $current->sub( new DateInterval( "PT2H") )->format( $full_format ),
				'DTT_EVT_end'   => $current_end_date->add( new DateInterval( "PT2H" ) )->format( $full_format)
			)
		);
		// now create 1 event for each datetime (the last of which will NOT be published
		for ( $x = 1; $x <= count( $datetime_args ); $x++ ) {
			$args = array(
				'EVT_name'    => sprintf( 'Event %d', $x ),
				'EVT_wp_user' => get_current_user_id(),
				'status'      => $x != count( $datetime_args ) ? 'publish' : 'draft',
				'Datetime'    => array_merge(
					array(
						'timezone' => 'America/Toronto',
						'formats'  => $formats,
					),
					$datetime_args[ $x ]
				)
			);
			$this->factory->event->set_properties_and_relations( $args );
			$this->factory->event->create_object();
		}


	}



	/**
	 * This tests getting active events.
	 * @since 4.6.x
	 */
	public function test_get_active_events() {
		$this->_setup_events();
		$this->assertEquals( 1, EEM_Event::instance()->get_active_events( array(), true ) );
	}


	public function test_get_upcoming_events() {
		$this->_setup_events();
		//now do our tests
		$this->assertEquals( 2, EEM_Event::instance()->get_upcoming_events( array(), true ) );
	}


	public function test_get_expired_events() {
		$this->_setup_events();
		//now do our tests
		$this->assertEquals( 1, EEM_Event::instance()->get_expired_events( array(), true ) );
	}

	public function test_get_inactive_events() {
		$this->_setup_events();
		//now do our tests
		$this->assertEquals( 1, EEM_Event::instance()->get_inactive_events( array(), true ) );
	}


	/**
	 * @see https://events.codebasehq.com/projects/event-espresso/tickets/8799
	 * @group 8799
	 * @since 4.8.8.rc.019
	 */
	public function test_default_reg_status() {
		//first verify the default reg status on config is pending payment
		$this->assertEquals( EEM_Registration::status_id_pending_payment, EE_Registry::instance()->CFG->registration->default_STS_ID );

		//verify creating default event from the model has that default reg status
		/** @type EE_Event $event */
		$event = EEM_Event::instance()->create_default_object();
		$this->assertEquals( EEM_Registration::status_id_pending_payment, $event->default_registration_status() );

		//let's update config in the db to have default reg status of approved
		EE_Registry::instance()->CFG->registration->default_STS_ID = EEM_Registration::status_id_approved;
		EE_Registry::instance()->CFG->update_espresso_config();

		//let's reset for new test
		EEM_Event::reset();
		EE_Registry::reset();

		//k NOW the default reg status in config should be approved
		$this->assertEquals( EEM_Registration::status_id_approved, EE_Registry::instance()->CFG->registration->default_STS_ID );

		//new default event should have approved as the default reg status
		$event = EEM_Event::instance()->create_default_object();
		$this->assertEquals( EEM_Registration::status_id_approved, $event->default_registration_status() );
	}
}

// location: tests/testcases/core/db_models/EEM_Event_Test.php
