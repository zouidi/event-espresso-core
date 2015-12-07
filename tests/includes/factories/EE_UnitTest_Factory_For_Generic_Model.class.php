<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }



/**
 * EE_UnitTest_Factory_For_Generic_Model
 *
 * @since        4.3.0
 * @package        Event Espresso
 * @subpackage    tests
 *
 */
class EE_UnitTest_Factory_For_Generic_Model extends EE_UnitTest_Factory_for_Model_Object {


	/**
	 * constructor
	 *
	 * @param string $model_name
	 * @param EE_UnitTest_Factory  $factory
	 * @param array | null         $properties_and_relations
	 *          pass null (or nothing) to just get the default properties with NO relations
	 *          or pass empty array for default properties AND relations
	 *          or non-empty array to override default properties and manually set related objects and their properties,
	 */
	public function __construct( $model_name, $factory, $properties_and_relations = null ) {
		$this->set_model_object_name( $model_name );
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
			$field_settings = $this->_model->field_settings( true );
			foreach ( $field_settings as $field_setting ) {
				if ( ! $field_setting instanceof EE_Primary_Key_Field_Base ) {
					$this->_default_properties[ $field_setting->get_name() ] = $field_setting->get_default_value();
				}
			}
		}
		// and set some sensible default relations
		if ( empty( $this->_default_relations ) ) {
			//$relation_settings = $this->_model->relation_settings();
			//foreach ( $relation_settings as $relation_name => $relation_setting ) {
			//	$this->_default_relations[ $relation_name ] = array();
			//}
			$this->_default_relations = array();
			$this->_resolve_default_relations( $called_class );
		}
	}




}
// End of file EE_UnitTest_Factory_For_Generic_Model.class.php
// Location: /EE_UnitTest_Factory_For_Generic_Model.class.php