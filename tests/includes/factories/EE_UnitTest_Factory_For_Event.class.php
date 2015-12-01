<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * EE Factory Class for Events
 *
 * @since        4.3.0
 * @package        Event Espresso
 * @subpackage    tests
 *
 */
class EE_UnitTest_Factory_For_Event extends EE_UnitTest_Factory_for_Model_Object {


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
		$this->set_model_object_name( 'Event' );
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
		static $counter = 1;
		// set some sensible defaults for this model object
		if ( empty( $this->_default_properties ) ) {
			$this->_default_properties = array(
				'EVT_name'       => "Event $counter",
				'EVT_desc'       => "Event Content $counter",
				'EVT_short_desc' => "Event Excerpt $counter",
			);
			$counter++;
		}
		// and set some sensible default relations
		if ( empty( $this->_default_relations ) ) {
			$this->_default_relations = array(
				'Datetime' 	=> array(),
				'Venue' 	=> array(),
				//'Registration' 			 => array(),
				//'Question_Group'         => array(),
				//'Term_Taxonomy'          => array(),
				//'Message_Template_Group' => array(),
				//'Attendee'               => array(),
				//'WP_User'                => array(),
			);
			$counter++;
			$this->_resolve_default_relations( $called_class );
		}
	}



}
// End of file EE_UnitTest_Factory_For_Event.class.php
// Location: /EE_UnitTest_Factory_For_Event.class.php