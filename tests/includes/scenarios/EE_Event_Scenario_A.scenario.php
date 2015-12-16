<?php
if (!defined('EVENT_ESPRESSO_VERSION'))
	exit('No direct script access allowed');

/**
 * This scenario creates an event that has:
 * - Three Datetimes
 *      - D1 - reg limit 5 		( TA, TB, TC ) 	<< can only sell 5 max : Tickets A, B, C sold out after 5 sales
 *      - D2 - reg limit 20 	( TA, TB, TD )	<< can only sell 15 max : 10 TD + 5 (TA or TB)
 *      - D3 - reg limit 12    	( TA, TD )    		<< can only sell 12 max : 10 TD + 2 TA ( or 5 TA + 7 TD )
 * - Four Tickets
 *      - TA - qty 30 	( D1, D2, D3 ) 		<< can only sell 5 max due to D1 reg limit ( which sells out Tickets A, B, C )
 *      - TB - qty 5 	( D1, D2 )         	<< can only sell 5 max due to D1 reg limit ( which sells out Tickets A, B, C )
 *      - TC - qty 15 	( D1 )					<< can only sell 5 max due to D1 reg limit ( which sells out Tickets A, B, C )
 *      - TD - qty 10 	( D2, D3 )				<< can only sell 10 max due to TD qty
 *
 *  MAX SELLOUT:
 * 		- 5 TB tickets for D1 ( TB sold out + D1 sold out = TA & TC sold out as well )
 * 		- 10 TD tickets for D3 ( TD sold out + D3 sold out  = D2 sold out )
 *  	- ( D2 sold out due to TA, TB, & TD sell outs )
 *
 * @package    Event Espresso
 * @subpackage tests/scenarios
 * @author     Darren Ethier
 */
class EE_Event_Scenario_A extends EE_Test_Scenario {

	public function __construct( EE_UnitTestCase $eetest ) {
		$this->type = 'event';
		$this->name = 'Event Scenario A';
		parent::__construct( $eetest );
	}

	protected function _set_up_expected(){
		$this->_expected_values = array(
			'total_available_spaces' => 15,
			'total_remaining_spaces' => 15,
		);
	}


	protected function _set_up_scenario(){
		$TKT_A = $this->_eeTest->factory->ticket->create_object( array( 'TKT_name' => 'Ticket A', 'TKT_qty'  => 30 ) );
		$TKT_B = $this->_eeTest->factory->ticket->create_object( array( 'TKT_name' => 'Ticket B', 'TKT_qty'  => 5 ) );
		$TKT_C = $this->_eeTest->factory->ticket->create_object( array( 'TKT_name' => 'Ticket C', 'TKT_qty'  => 15 ) );
		$TKT_D = $this->_eeTest->factory->ticket->create_object( array( 'TKT_name' => 'Ticket D', 'TKT_qty'  => 10 ) );
		$event = $this->generate_objects_for_scenario(
			array(
				'Event' => array(
					'EVT_name' => 'Test Scenario EVT A',
					'Datetime'   => array(
						'DTT_name'      => 'Datetime 1',
						'DTT_reg_limit' => 5,
						'Ticket'        => array(
							'TKT_ID' => $TKT_A->ID()
						),
						'Ticket*'       => array(
							'TKT_ID' => $TKT_B->ID()
						),
						'Ticket**'      => array(
							'TKT_ID' => $TKT_C->ID()
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
						'Ticket**'       => array(
							'TKT_ID' => $TKT_D->ID()
						),
					),
					'Datetime**' => array(
						'DTT_name'      => 'Datetime 3',
						'DTT_reg_limit' => 12,
						'Ticket'        => array(
							'TKT_ID' => $TKT_A->ID()
						),
						'Ticket*' => array(
							'TKT_ID' => $TKT_D->ID()
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