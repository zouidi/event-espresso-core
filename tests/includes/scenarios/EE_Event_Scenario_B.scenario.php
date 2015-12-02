<?php
if (!defined('EVENT_ESPRESSO_VERSION'))
	exit('No direct script access allowed');

/**
 * This scenario creates an event that has:
 * - Two Datetimes
 *      - D1 - reg limit 15		( TA, TB )		<< can only sell 15 max : Tickets A & B sold out after 15 sales
 *      - D2 - reg limit 17 	( TA, TC ) 	<< can only sell 17 max : Tickets A & C sold out after 17 sales
 * - Three Tickets
 *      - TA - qty 23 	( D1, D2 )    << can only sell 15 max due to D1 reg limit ( which sells out Tickets A & B )
 *      - TB - qty 5 	( D1 )    		<< can only sell 5 max due to TB qty
 *      - TC - qty 15 	( D2 )    		<< can only sell 15 max due to TC qty
 *
 *  MAX SELLOUT:
 *  	- 5 TB tickets for D1 ( TB sold out )
 * 		- 10 TA tickets for D1 ( D1 sold out = TA sold out )
 * 		- 7 TC tickets for D2 ( since 10 TA tickets have already been sold )
 *
 * @package    Event Espresso
 * @subpackage tests/scenarios
 * @author     Darren Ethier
 */
class EE_Event_Scenario_B extends EE_Test_Scenario {

	public function __construct( EE_UnitTestCase $eetest ) {
		$this->type = 'event';
		$this->name = 'Event Scenario B';
		parent::__construct( $eetest );
	}

	protected function _set_up_expected(){
		$this->_expected_values = array(
			'total_available_spaces' => 22,
			'total_remaining_spaces' => 22
		);
	}


	protected function _set_up_scenario(){
		$event = $this->generate_objects_for_scenario(
			array(
				'Event' => array(
					'EVT_name' => 'Test Scenario EVT B',
					'Datetime'   => array(
						'DTT_name'      => 'Datetime 1',
						'DTT_reg_limit' => 15,
						'Ticket'        => array(
							'TKT_name' => 'Ticket A',
							'TKT_qty'  => 23,
						),
						'Ticket*'       => array(
							'TKT_name' => 'Ticket B',
							'TKT_qty'  => 5,
						),
					),
					'Datetime*'  => array(
						'DTT_name'      => 'Datetime 2',
						'DTT_reg_limit' => 17,
						'Ticket'        => array(
							'TKT_name' => 'Ticket A',
							'TKT_qty'  => 23,
						),
						'Ticket*'       => array(
							'TKT_name' => 'Ticket C',
							'TKT_qty'  => 15,
						),
					),
				),
			)
		);

		//assign the event object as the scenario object
		$this->_scenario_object = reset( $event );
	}



	protected function _get_scenario_object(){
		return $this->_scenario_object;
	}
}