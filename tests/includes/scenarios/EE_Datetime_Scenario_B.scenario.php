<?php
if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class EE_Datetime_Scenario_B
 *
 * This scenario is modeled after the issue reported in Codebase Ticket:
 * @link https://events.codebasehq.com/projects/event-espresso/tickets/8997
 * and creates 3 Datetimes and five Tickets, where each ticket is related to ALL datetimes
 * a total of 8 tickets have been sold, with sales spread across three of the tickets
 * Therefore there is a total of ONE space remaining for each datetime
 * and since each ticket will report that once space,
 * then there are a total of 5 tickets still "available"
 *
 * - Three Datetimes
 *      - D1 - reg limit 9, DTT_sold = 8
 *      - D2 - reg limit 9, DTT_sold = 8
 *      - D3 - reg limit 9, DTT_sold = 8
 * - Five Tickets
 *      - TA - qty 9 (D1, D2, D3), TKT_sold = 4
 *      - TB - qty 9 (D1, D2, D3), TKT_sold = 3
 *      - TC - qty 9 (D1, D2, D3), TKT_sold = 1
 *      - TD - qty 9 (D1, D2, D3), TKT_sold = 0
 *      - TE - qty 9 (D1, D2, D3), TKT_sold = 0
 *
 * @package               Event Espresso
 * @subpackage            core
 * @author                Brent Christensen
 * @since                 4.5.21
 *
 */
class EE_Datetime_Scenario_B extends EE_Test_Scenario {

	public function __construct( EE_UnitTestCase $eetest ) {
		$this->type = 'datetime';
		$this->name = 'Datetime Scenario B - Ticket 8997';
		parent::__construct( $eetest );
	}



	protected function _set_up_expected() {
		// array of Datetime IDs => available ticket quantities
		$this->_expected_values = array(
			'sum_tickets_currently_available_at_datetime' => 5,
			'datetime_id_to_tickets_map'                  => array(
				1 => 1,
				2 => 1,
				3 => 1,
			),
		);
	}



	protected function _set_up_scenario() {
		$datetimes = $this->generate_objects_for_scenario(
			array(
				'Datetime'   => array(
					'DTT_name'      => 'Datetime 1',
					'DTT_reg_limit' => 9,
					'DTT_sold'      => 8,
					'Ticket'       => array(
						'TKT_name' => 'Ticket A',
						'TKT_qty'  => 9,
						'TKT_sold' => 4,
					),
					'Ticket*'      => array(
						'TKT_name' => 'Ticket B',
						'TKT_qty'  => 9,
						'TKT_sold' => 3,
					),
					'Ticket**' => array(
						'TKT_name' => 'Ticket C',
						'TKT_qty'  => 9,
						'TKT_sold' => 1,
					),
					'Ticket***'  => array(
						'TKT_name' => 'Ticket D',
						'TKT_qty'  => 9,
						'TKT_sold' => 0,
					),
					'Ticket****' => array(
						'TKT_name' => 'Ticket E',
						'TKT_qty'  => 9,
						'TKT_sold' => 0,
					),
				),
				'Datetime*'  => array(
					'DTT_name'      => 'Datetime 2',
					'DTT_reg_limit' => 9,
					'DTT_sold'      => 8,
					'Ticket'       => array(
						'TKT_name' => 'Ticket A',
						'TKT_qty'  => 9,
						'TKT_sold' => 4,
					),
					'Ticket*'      => array(
						'TKT_name' => 'Ticket B',
						'TKT_qty'  => 9,
						'TKT_sold' => 3,
					),
					'Ticket**' => array(
						'TKT_name' => 'Ticket C',
						'TKT_qty'  => 9,
						'TKT_sold' => 1,
					),
					'Ticket***'  => array(
						'TKT_name' => 'Ticket D',
						'TKT_qty'  => 9,
						'TKT_sold' => 0,
					),
					'Ticket****' => array(
						'TKT_name' => 'Ticket E',
						'TKT_qty'  => 9,
						'TKT_sold' => 0,
					),
				),
				'Datetime**' => array(
					'DTT_name'      => 'Datetime 3',
					'DTT_reg_limit' => 9,
					'DTT_sold'      => 8,
					'Ticket'       => array(
						'TKT_name' => 'Ticket A',
						'TKT_qty'  => 9,
						'TKT_sold' => 4,
					),
					'Ticket*'       => array(
						'TKT_name' => 'Ticket B',
						'TKT_qty'  => 9,
						'TKT_sold' => 3,
					),
					'Ticket**' => array(
						'TKT_name' => 'Ticket C',
						'TKT_qty'  => 9,
						'TKT_sold' => 1,
					),
					'Ticket***'  => array(
						'TKT_name' => 'Ticket D',
						'TKT_qty'  => 9,
						'TKT_sold' => 0,
					),
					'Ticket****' => array(
						'TKT_name' => 'Ticket E',
						'TKT_qty'  => 9,
						'TKT_sold' => 0,
					),
				),
			)
		);
		//assign the first datetime object as the scenario object (it's the one that will be used for tests.
		$this->_scenario_object = reset( $datetimes );
	}



	protected function _get_scenario_object() {
		return $this->_scenario_object;
	}



}
// End of file EE_Datetime_Scenario_B.scenario.php
// Location: /tests/includes/scenarios/EE_Datetime_Scenario_B.scenario.php