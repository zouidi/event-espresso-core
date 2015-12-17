<?php
if (!defined('EVENT_ESPRESSO_VERSION'))
	exit('No direct script access allowed');

/**
 * This scenario creates an event that has:
 * - Three Datetimes
 *      - D1 - reg limit 5
 *      - D2 - reg limit 20
 *      - D3 - reg limit EE_INF
 * - Four Tickets
 *      - TA - qty 5 (D1, D2, D3)
 *      - TB - qty 15 (D2,D3)
 *      - TC - qty 5 (D1, D3)
 *      - TD - qty 5 (D1)
 *
 * @package    Event Espresso
 * @subpackage tests/scenarios
 * @author     Darren Ethier
 */
class EE_Event_Scenario_F extends EE_Test_Scenario {

	public function __construct( EE_UnitTestCase $eetest ) {
		$this->type = 'event';
		$this->name = 'Event Scenario F';
		parent::__construct( $eetest );
	}

	protected function _set_up_expected(){
		$this->_expected_values = array(
			'total_available_spaces' => 25,
			'total_remaining_spaces' => 25
		);
	}


	protected function _set_up_scenario(){
		$event = $this->generate_objects_for_scenario(
			array(
				'Event' => array(
					'EVT_name' => 'Test Scenario EVT F',
					'Datetime'    => array(
						'DTT_name'      => 'Datetime 1',
						'DTT_reg_limit' => 5,
						'Ticket'        => array(
							'TKT_ID' => '*TA',
							'TKT_name' => 'Ticket A',
							'TKT_qty' => 5
						),
						'Ticket*'       => array(
							'TKT_ID' => '*TC',
							'TKT_name' => 'Ticket C',
							'TKT_qty' => 5,
						),
					),
					'Datetime*'   => array(
						'DTT_name'      => 'Datetime 2',
						'DTT_reg_limit' => 20,
						'Ticket'        => array(
							'TKT_ID' => '*TA',
						),
						'Ticket*'       => array(
							'TKT_ID' => '*TB',
							'TKT_name' => 'Ticket B',
							'TKT_qty' => 15
						),
						'Ticket**'  => array(
							'TKT_name' => 'Ticket D',
							'TKT_qty' => 5
						),
					),
					'Datetime**'  => array(
						'DTT_name'      => 'Datetime 3',
						'Ticket'        => array(
							'TKT_ID' => '*TA',
						),
						'Ticket*'       => array(
							'TKT_ID' => '*TB',
						),
						'Ticket**'  => array(
							'TKT_ID' => '*TC',
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