<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }



/**
 * EE Factory Class for registrations
 *
 * @since        4.3.0
 * @package        Event Espresso
 * @subpackage    tests
 *
 */
class EE_UnitTest_Factory_For_Registration extends EE_UnitTest_Factory_for_Model_Object {


	/**
	 * constructor
	 *
	 * @param EE_UnitTest_Factory $factory
	 * @param array | null        $properties_and_relations
	 *          pass null (or nothing) to just get the default properties with NO relations
	 *          or pass empty array for default properties AND relations
	 *          or non-empty array to override default properties and manually set related objects and their properties,
	 */
	public function __construct( $factory, $properties_and_relations = null ) {
		echo "\n\n ************ " . __LINE__ . ") " . __METHOD__ . "() " . $this->factory_type() . ' ' . spl_object_hash( $this ) . " ************ ";
		echo is_null( $properties_and_relations )
			? "\n not chained \n"
			: "\n CHAINED \n";
		$this->set_model_object_name( 'Registration' );
		parent::__construct( $factory, $properties_and_relations );
	}



	/**
	 * _set_default_properties_and_relations
	 *
	 * @access protected
	 * @param string $called_class in order to avoid recursive application of relations,
	 *                             we need to know which class is making this request
	 * @return void
	 */
	protected function _set_default_properties_and_relations( $called_class ) {
		// set some sensible defaults for this model object
		if ( empty( $this->_default_properties ) ) {
			echo "\n " . __LINE__ . ") " . __METHOD__ . "() " . $this->factory_type() . ' ' . spl_object_hash( $this );
			$this->_default_properties = array(
				'REG_date'        => 0, //time(),
				'REG_final_price' => 0,
				'REG_paid'        => 0,
				'REG_session'     => uniqid(),
				'REG_code'        => "1-1-" . EE_UnitTest_Factory::$counter . "-" . substr( uniqid(), 0, 4 ),
				'REG_url_link'    => EE_UnitTest_Factory::$counter . md5( 'ticket' . microtime() ),
				'REG_count'       => EE_UnitTest_Factory::$counter,
				'REG_group_size'  => EE_UnitTest_Factory::$counter,
			);
			EE_UnitTest_Factory::$counter++;
		}
		// and set some sensible default relations
		if ( empty( $this->_default_relations ) ) {
			$this->_default_relations = array(
				'Event'                => array(),
				'Attendee'             => array(),
				'Transaction'          => array(),
				'Ticket'               => array(),
				'Status'               => array(
					'STS_ID'   => EEM_Registration::status_id_pending_payment,
				),
				//'Answer'               => array(),
				//'Checkin'              => array(),
				//'Payment'              => array(),
				//'Registration_Payment' => array(),
			);
			$this->_resolve_default_relations( $called_class );
		}
	}




}
// End of file EE_UnitTest_Factory_For_Registration.class.php
// Location: /EE_UnitTest_Factory_For_Registration.class.php