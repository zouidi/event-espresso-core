<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * EE Factory Class for Question_Group
 *
 * @since        4.3.0
 * @package        Event Espresso
 * @subpackage    tests
 *
 */
class EE_UnitTest_Factory_For_Question_Group extends EE_UnitTest_Factory_for_Model_Object {


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
		$this->set_model_object_name( 'Question_Group' );
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
		//static $counter = 3;
		// set some sensible defaults for this model object
		if ( empty( $this->_default_properties ) ) {
			$this->_default_properties = array(
				'QSG_name' 			  => new WP_UnitTest_Generator_Sequence( 'Question Group %s', 3 ),
				'QSG_identifier'      => new WP_UnitTest_Generator_Sequence( 'question-group-%s', 3 ),
				'QSG_desc'            => new WP_UnitTest_Generator_Sequence( 'Description of Question Group %s', 3 ),
				'QSG_order'           => new WP_UnitTest_Generator_Sequence( '%s', 3 ),
				'QSG_show_group_name' => true,
				'QSG_show_group_desc' => false,
			);
		}
		// and set some sensible default relations
		if ( empty( $this->_default_relations ) ) {
			$this->_default_relations = array(
				'Question' 				=> array(),
				//'Event' 				=> array(),
				//'Event_Question_Group' 	=> array(),
				//'WP_User' 				=> array(),
			);
			$this->_resolve_default_relations( $called_class );
		}
	}



}
// End of file EE_UnitTest_Factory_For_Question_Group.class.php
// Location: /EE_UnitTest_Factory_For_Question_Group.class.php