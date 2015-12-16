<?php
if (!defined('EVENT_ESPRESSO_VERSION'))
	exit('No direct script access allowed');

/**
 * This scenario creates an event that has:
 * - Four Datetimes
 *      - D1 - reg limit 55    ( TA, TB, TC, TD )
 *      - D2 - reg limit 20    ( TA, TB )
 *      - D3 - reg limit 12    ( TA, TD )
 *      - D4 - reg limit 30    ( TB, TC, TD, TE )
 * - Five Tickets
 *      - TA - qty 12 	( D1, D2, D3 )
 *      - TB - qty 20 	( D1, D2, D4 )
 *      - TC - qty 30 	( D1, D4 )
 *      - TD - qty 12 	( D1, D3, D4 )
 *      - TE - qty 30 	( D4 )
 *
 *  MAX SELLOUT:
 *        12 TD tickets for D3 ( D3 sold out + TA & TD sold out )
 *        18 TB tickets for D2 ( D4 sold out cuz 12 TD + 18 TB = 30 reg limit, so TB, TC sold out )
 *
*@package    Event Espresso
 * @subpackage tests/scenarios
 * @author     Darren Ethier
 */
class EE_Event_Scenario_E extends EE_Test_Scenario {

	public function __construct( EE_UnitTestCase $eetest ) {
		$this->type = 'event';
		$this->name = 'Event Scenario E';
		parent::__construct( $eetest );
	}

	protected function _set_up_expected(){
		$this->_expected_values = array(
			'total_available_spaces' => 30,
			'total_remaining_spaces' => 30
		);
	}


	protected function _set_up_scenario(){
		$TKT_A = $this->_eeTest->factory->ticket->create_object( array( 'TKT_name' => 'Ticket A', 'TKT_qty' => 12 ) );
		$TKT_B = $this->_eeTest->factory->ticket->create_object( array( 'TKT_name' => 'Ticket B', 'TKT_qty' => 20 ) );
		$TKT_C = $this->_eeTest->factory->ticket->create_object( array( 'TKT_name' => 'Ticket C', 'TKT_qty' => 30 ) );
		$TKT_D = $this->_eeTest->factory->ticket->create_object( array( 'TKT_name' => 'Ticket D', 'TKT_qty' => 12 ) );
		$TKT_E = $this->_eeTest->factory->ticket->create_object( array( 'TKT_name' => 'Ticket D', 'TKT_qty' => 30 ) );
		$event = $this->generate_objects_for_scenario(
			array(
				'Event' => array(
					'EVT_name'   => 'Test Scenario EVT E',
					'Datetime'   => array(
						'DTT_name'      => 'Datetime 1',
						'DTT_reg_limit' => 55,
						'Ticket'        => array(
							'TKT_ID' => $TKT_A->ID()
						),
						'Ticket*'       => array(
							'TKT_ID' => $TKT_B->ID()
						),
						'Ticket**'      => array(
							'TKT_ID' => $TKT_C->ID()
						),
						'Ticket***'      => array(
							'TKT_ID' => $TKT_D->ID()
						),
					),
					'Datetime*'  => array(
						'DTT_name'      => 'Datetime 2',
						'DTT_reg_limit' => 20,
						'Ticket'        => array(
							'TKT_ID' => $TKT_A->ID()
						),
						'Ticket*'       => array(
							'TKT_ID' => $TKT_B->ID()
						),
					),
					'Datetime**' => array(
						'DTT_name'      => 'Datetime 3',
						'DTT_reg_limit' => 12,
						'Ticket'        => array(
							'TKT_ID' => $TKT_A->ID()
						),
						'Ticket*'       => array(
							'TKT_ID' => $TKT_D->ID()
						),
					),
					'Datetime***' => array(
						'DTT_name'      => 'Datetime 4',
						'DTT_reg_limit' => 30,
						'Ticket'       => array(
							'TKT_ID' => $TKT_B->ID()
						),
						'Ticket*'      => array(
							'TKT_ID' => $TKT_C->ID()
						),
						'Ticket**'      => array(
							'TKT_ID' => $TKT_D->ID()
						),
						'Ticket***' => array(
							'TKT_ID' => $TKT_E->ID()
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