<?php
if (!defined('EVENT_ESPRESSO_VERSION'))
	exit('No direct script access allowed');

/**
 * This scenario creates an event that has:
 * - Three Datetimes
 *      - D1 - reg limit 3 		( T1, T3, T4 ) 	<< can only sell 3 max : Tickets 1, 3 & 4 sold out after 3 sales
 *      - D2 - reg limit 2 		( T2, T3 )    	<< can only sell 2 max : Tickets 2 & 3 sold out after 2 sales
 *      - D3 - reg limit 10 	( T1, T4 ) 		<< can only sell 3 max : Tickets 1 & 4 sold out after 3 sales
 * - Four Tickets
 *      - T1 - qty 2 ( D1, D3 ) 	<< can only sell 2 max due to TKT qty ( which sells out Ticket 1 )
 *      - T2 - qty 2 ( D2 )        	<< can only sell 2 max due to TKT qty && DT reg limit ( which sells out T2 && D2 )
 *      - T3 - qty 2 ( D1, D2 ) 	<< can only sell 2 max due to TKT qty ( which sells out Ticket 3 )
 *      - T4 - qty 2 ( D1, D3 ) 	<< can only sell 2 max due to TKT qty ( which sells out Ticket 4 )
 *
 *  FASTEST SELLOUT:
 * 		- 2 T2 (or T3) tickets for D2 ( T2 & T3 sold out + D2 sold out )
 * 		- 1 T1 (or T4) ticket for D1 ( T1 & T4 sold out + D1 & D3 sold out )
 *
 *  MAX SELLOUT:
 * 		- 3 T1 (or T4) tickets for D1 (or D3) ( T1, T3 & T4 sold out + D1 & D3 sold out )
 * 		- 2 T2 tickets for D2 ( T2 sold out + D2 sold out )
 *
*@package    Event Espresso
 * @subpackage tests/scenarios
 * @author     Darren Ethier / Brent Christensen
 */
class EE_Event_Scenario_G extends EE_Test_Scenario {

	public function __construct( EE_UnitTestCase $eetest ) {
		$this->type = 'event';
		$this->name = 'Event Scenario G';
		parent::__construct( $eetest );
	}

	protected function _set_up_expected(){
		$this->_expected_values = array(
			'total_available_spaces' => 5,
			'total_remaining_spaces' => 3
		);
	}


	protected function _set_up_scenario(){
		$event = $this->generate_objects_for_scenario(
			array(
				'Event' => array(
					'EVT_name'    => 'Test Scenario EVT G',
					'Datetime'    => array(
						'DTT_name'      => 'Datetime 1',
						'DTT_reg_limit' => 3,
						'Ticket'    => array(
							'TKT_name' => 'Ticket 1',
							'TKT_qty'  => 2,
						),
						'Ticket*'   => array(
							'TKT_name' => 'Ticket 3',
							'TKT_qty'  => 2,
						),
						'Ticket**'  => array(
							'TKT_name' => 'Ticket 4',
							'TKT_qty'  => 2,
						),
					),
					'Datetime*'   => array(
						'DTT_name'      => 'Datetime 2',
						'DTT_reg_limit' => 2,
						'Ticket'    => array(
							'TKT_name' => 'Ticket 2',
							'TKT_qty'  => 2,
						),
						'Ticket*'   => array(
							'TKT_name' => 'Ticket 3',
							'TKT_qty'  => 2,
						),
					),
					'Datetime**'  => array(
						'DTT_name'      => 'Datetime 3',
						'DTT_reg_limit' => 10,
						'Ticket'    => array(
							'TKT_name' => 'Ticket 1',
							'TKT_qty'  => 2,
						),
						'Ticket*'   => array(
							'TKT_name' => 'Ticket 4',
							'TKT_qty'  => 2,
						),
					),
				),
			)
		);
		// simulate two sales for ticket 3, which will also increase sold qty for D1 & D2
		if ( $event instanceof EE_Event ) {
			$ticket = $event->tickets( array( 'TKT_name' => 'Ticket 3') );
			if ( $ticket instanceof EE_Ticket ) {
				$ticket->increase_sold( 2 );
			}

		}
		//assign the event object as the scenario object
		$this->_scenario_object = reset( $event );
	}



	protected function _get_scenario_object(){
		return $this->_scenario_object;
	}

}

// Location:/tests/includes/scenarios/EE_Event_Scenario_G.scenario.php