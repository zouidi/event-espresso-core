<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }



/**
 * EE Factory Class for EE_Message_Template_Group
 *
 * @since        4.3.0
 * @package        Event Espresso
 * @subpackage    tests
 *
 */
class EE_UnitTest_Factory_For_Message_Template_Group extends EE_UnitTest_Factory_for_Model_Object {


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
		$this->set_model_object_name( 'Message_Template_Group' );
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
				'MTP_name' 			=> sprintf( 'Message Template Group %d', EE_UnitTest_Factory::$counter ),
				'MTP_description'  	=> sprintf( 'Message Template Group %d description', EE_UnitTest_Factory::$counter ),
				'MTP_user_id'      	=> get_current_user_id(),
				'MTP_messenger'    	=> sprintf( 'MTP_messenger_%d', EE_UnitTest_Factory::$counter ),
				'MTP_message_type' 	=> sprintf( 'MTP_message_type_%d', EE_UnitTest_Factory::$counter ),
			);
			EE_UnitTest_Factory::$counter++;
		}
		// and set some sensible default relations
		if ( empty( $this->_default_relations ) ) {
			$this->_default_relations = array(
				'Message_Template' => array(),
				//'Event'            => array(),
				//'WP_User'          => array(),
			);
			$this->_resolve_default_relations( $called_class );
		}
	}



}
// End of file EE_UnitTest_Factory_For_Message_Template_Group.class.php
// Location: /EE_UnitTest_Factory_For_Message_Template_Group.class.php