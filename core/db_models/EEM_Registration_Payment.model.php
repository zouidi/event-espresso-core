<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 * EEM_Registration_Payment
 *
 * model for join relationship between registrations, line items and payments
 * Client code will probably never need to use this, as you can easily query  the HABTM relationships from the related models
 *
 * @package			Event Espresso
 * @subpackage 	includes/models/
 * @author				Brent Christensen
 */
class EEM_Registration_Payment extends EEM_Base {

  	// private instance
	protected static $_instance = NULL;


	protected function __construct( $timezone = NULL ) {

		$this->singular_item = __( 'Registration Payment', 'event_espresso' );
		$this->plural_item 	= __( 'Registration Payments', 'event_espresso' );

		$this->_tables = array(
			'Registration_Payment' => new EE_Primary_Table( 'esp_registration_payment', 'RPY_ID' )
		);

		$this->_fields = array(
			'Registration_Payment'=>array(
				'RPY_ID' 				=> new EE_Primary_Key_Int_Field( 'RPY_ID', __( 'Registration Payment ID', 'event_espresso' )),
				'REG_ID' 				=> new EE_Foreign_Key_Int_Field( 'REG_ID', __( 'Registration ID', 'event_espresso' ), false, 0, 'Registration' ),
				'PAY_ID' 				=> new EE_Foreign_Key_Int_Field( 'PAY_ID', __( 'Payment ID', 'event_espresso' ), true, null, 'Payment' ),
				'RPY_amount' 	=> new EE_Money_Field( 'RPY_amount', __( 'Amount attributed to the registration', 'event_espresso' ), false, 0 ),
			)
		);

		$this->_model_relations = array(
			'Registration' 	=> new EE_Belongs_To_Relation(),
			'Payment' 		=> new EE_Belongs_To_Relation(),
		);

		parent::__construct( $timezone );
	}




	/**
	 * Get the sum of the revenue per day for the period given.
	 * This also considers whether the current user should see revenue for registrations attached to events
	 * they did not author (`read_others_events` required to see revenue for all events)
	 * @param string $period
	 * @return stdClass[]
	 */
	public function get_revenue_per_day_report( $period = '-1 month' ) {
		$sql_date = EEM_Payment::instance()->convert_datetime_for_query( 'PAY_timestamp', date( 'Y-m-d H:i:s', strtotime( $period ) ), 'Y-m-d H:i:s', 'UTC' );

		EE_Registry::instance()->load_helper( 'DTT_Helper' );
		$query_interval = EEH_DTT_Helper::get_sql_query_interval_for_offset( $this->get_timezone(), 'Payment.PAY_timestamp' );

		$where = array(
			'Payment.PAY_timestamp' => array( '>=', $sql_date ),
			'Payment.STS_ID' => EEM_Payment::status_id_approved
		);

		if ( ! EE_Registry::instance()->CAP->current_user_can( 'ee_read_others_registrations', 'txn_per_day_report' ) ) {
			$where['Registration.Event.EVT_wp_user'] = get_current_user_id();
		}

		$results = $this->_get_all_wpdb_results(
			array(
				$where,
				'group_by' => 'txnDate',
				'order_by' => array( 'Payment.PAY_timestamp' => 'ASC' )
			),
			OBJECT,
			array(
				'txnDate' => array( 'DATE(' . $query_interval . ')', '%s' ),
				'revenue' => array( 'SUM(Registration_Payment.RPY_amount)', '%d' )
			)
		);
		return $results;
	}

}
// end of file: /core/db_models/EEM_Registration_Payment.model.php