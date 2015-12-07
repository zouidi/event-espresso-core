<?php
/**
 * This is a factory for more quickly setting up objects/items needed for EE Unit Tests.
 *
 * Examples of things we might setup using the factory are events, registrations, tickets etc.
 *
 * @since 		4.3.0
 * @package 	Event Espresso
 * @subpackage 	tests
 */
class EE_UnitTest_Factory extends WP_UnitTest_Factory {


	/**
	 * @type int
	 */
	public static $counter = 0;

	/**
	 * EE_Test_Factories extend the EE_UnitTest_Factory_for_Model_Object class,
	 * which extends the WP_UnitTest_Factory_for_Thing abstract class
	 *
	 * @see wp tests/includes/EE_UnitTest_Factory.class.php
	 * @var EE_UnitTest_Factory_for_Model_Object[]
	 */
	public $repo = array();

	/**
	 * @type EE_UnitTest_Factory_For_Attendee $attendee
	 */
	public $attendee = null;

	/**
	 * @type EE_UnitTest_Factory_For_Attendee $attendee_chained
	 */
	public $attendee_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Country $country
	 */
	public $country = null;

	/**
	 * @type EE_UnitTest_Factory_For_Country $country_chained
	 */
	public $country_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Datetime $datetime
	 */
	public $datetime = null;

	/**
	 * @type EE_UnitTest_Factory_For_Datetime $datetime_chained
	 */
	public $datetime_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Event $event
	 */
	public $event = null;

	/**
	 * @type EE_UnitTest_Factory_For_Event $event_chained
	 */
	public $event_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Line_item $line_item
	 */
	public $line_item = null;

	/**
	 * @type EE_UnitTest_Factory_For_Line_item $line_item_chained
	 */
	public $line_item_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Payment $payment
	 */
	public $payment = null;

	/**
	 * @type EE_UnitTest_Factory_For_Payment $payment_chained
	 */
	public $payment_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Price $price
	 */
	public $price = null;

	/**
	 * @type EE_UnitTest_Factory_For_Price $price_chained
	 */
	public $price_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Price_Type $price_type
	 */
	public $price_type = null;

	/**
	 * @type EE_UnitTest_Factory_For_Price_Type $price_type_chained
	 */
	public $price_type_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Registration $registration
	 */
	public $registration = null;

	/**
	 * @type EE_UnitTest_Factory_For_Registration $registration_chained
	 */
	public $registration_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_State $state
	 */
	public $state = null;

	/**
	 * @type EE_UnitTest_Factory_For_State $state_chained
	 */
	public $state_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Status $status
	 */
	public $status = null;

	/**
	 * @type EE_UnitTest_Factory_For_Status $status_chained
	 */
	public $status_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Term $term
	 */
	public $term = null;

	/**
	 * @type EE_UnitTest_Factory_For_Term $term_chained
	 */
	public $term_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Ticket $ticket
	 */
	public $ticket = null;

	/**
	 * @type EE_UnitTest_Factory_For_Ticket $ticket_chained
	 */
	public $ticket_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Transaction $transaction
	 */
	public $transaction = null;

	/**
	 * @type EE_UnitTest_Factory_For_Transaction $transaction_chained
	 */
	public $transaction_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Venue $venue
	 */
	public $venue = null;

	/**
	 * @type EE_UnitTest_Factory_For_Venue $venue_chained
	 */
	public $venue_chained = null;

	/**
	 * @type EE_UnitTest_Factory_For_Wp_User $wp_user
	 */
	public $wp_user = null;

	/**
	 * @type EE_UnitTest_Factory_For_Wp_User $wp_user_chained
	 */
	public $wp_user_chained = null;



	public function __construct() {
		//echo "\n\n ************ " . __LINE__ . ") " . __METHOD__ . "() " . spl_object_hash( $this ) . " ************ \n\n ";
		parent::__construct();

		$factories = get_class_vars( 'EE_UnitTest_Factory' );
		foreach ( $factories as $factory => $value ) {
			$class = 'EE_UnitTest_Factory_For_' . str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $factory ) ) );
			if (
				class_exists( $class )
				&& property_exists( $this, $factory )
				&& ! $this->$factory instanceof EE_UnitTest_Factory_for_Model_Object
			) {
				$this->$factory = new $class( $this );
				$factory .= '_chained';
				$this->$factory = new $class( $this, array() );
			}
		}

	}



	/**
	 * get_factory_for_model
	 *
	 * @param string $model_name
	 * @return \EE_UnitTest_Factory_for_Model_Object
	 */
	public function get_factory_for_model( $model_name ) {
		$model_name = strtolower( rtrim( $model_name, '*' ) );
		//echo "\n\n " . __LINE__ . ") " . __METHOD__ . "()";
		//echo "\n MODEL_NAME: " . $model_name . "\n";
		if ( property_exists( $this, $model_name ) ) {
			//echo "  property exists: \n";
			return $this->$model_name;
		} else if ( isset( $this->repo[ $model_name ] ) ) {
			//echo "  repo exists: \n";
			return $this->repo[ $model_name ];
		} else {
			return $this->construct_generic_factory_for_model( $model_name );
		}
		//return null;
	}




	/**
	 * construct_generic_factory_for_model
	 *
	 * @param string $model_name
	 * @return \EE_UnitTest_Factory_for_Model_Object
	 */
	public function construct_generic_factory_for_model( $model_name ) {
		//echo "\n\n " . __LINE__ . ") " . __METHOD__ . "()";
		$model_name = strtolower( rtrim( $model_name, '*' ) );
		//echo "\n MODEL_NAME: " . $model_name . "\n";
		$this->repo[ $model_name ] = new EE_UnitTest_Factory_For_Generic_Model( $model_name, $this );
		$this->repo[ $model_name . '_chained' ] = new EE_UnitTest_Factory_For_Generic_Model( $model_name, $this, array() );
		return $this->get_factory_for_model( $model_name );
	}



	/**
	 * @param string $factory
	 * @return \EE_UnitTest_Factory_for_Model_Object
	 */
	public function __get( $factory ) {
		if ( isset( $this->repo[ $factory ] ) ) {
			return $this->repo[ $factory ];
		}
		return null;
	}



}


