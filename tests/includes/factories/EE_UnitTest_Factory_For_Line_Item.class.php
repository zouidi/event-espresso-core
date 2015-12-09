<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }



/**
 * EE Factory Class for Line_Item.
 *
 * @since        4.3.0
 * @package        Event Espresso
 * @subpackage    tests
 *
 */
class EE_UnitTest_Factory_For_Line_Item extends EE_UnitTest_Factory_for_Model_Object {


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
		$this->set_model_object_name( 'Line_Item' );
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
			$this->_default_properties = array(
				'LIN_code' 	=> 'total',
				'LIN_name' 	=> 'Grand Total',
				'LIN_type' 	=> EEM_Line_Item::type_total,
				'LIN_timestamp' => time() - DAY_IN_SECONDS,
			);
		}
		// and set some sensible default relations
		if ( empty( $this->_default_relations ) ) {
			$this->_default_relations = array(
				'Transaction' => array(),
				//'Ticket'      => array(),
				//'Price'       => array(),
				//'Event'       => array(),
			);
			$this->_resolve_default_relations( $called_class );
		}
	}




}
// End of file EE_UnitTest_Factory_For_Line_Item.class.php
// Location: /EE_UnitTest_Factory_For_Line_Item.class.php