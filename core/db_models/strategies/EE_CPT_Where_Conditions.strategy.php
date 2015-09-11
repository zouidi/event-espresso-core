<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

/*
 * EE_CPT_Where_Conditions
 *
 * Strategy specifically for adding where conditions specific to CPT models.
 *
 * @package			Event Espresso
 * @subpackage		core/db_models/strategies/
 * @author				Michael Nelson
 */
class EE_CPT_Where_Conditions extends EE_Default_Where_Conditions{

	protected $_post_type;
	protected $_meta_field;



	/**
	 * @param array $post_type
	 * @param string $meta_field_to_chk
	 * @param array $custom_where_conditions
	 */
	function __construct( $post_type, $meta_field_to_chk, $custom_where_conditions = array() ){
		$this->_post_type = $post_type;
		$this->_meta_field = $meta_field_to_chk;
		parent::__construct( $custom_where_conditions );
	}



	/**
	 * Gets the field with the specified column. Note, this function might not work
	 * properly if two fields refer to columns with the same name on different tables
	 * @param string $column column name
	 * @return EE_Model_Field_Base
	 */
	protected function _get_field_on_column($column){
		$all_fields = $this->_model->field_settings(true);
		foreach($all_fields as $field_obj){
			if($column == $field_obj->get_table_column()){
				return $field_obj;
			}
		}
                throw new EE_Error( sprintf( __( 'Model EE_CPT_Where_Conditions misconfigured. Looking for a field with column %1$s on model %2$s but none found.', 'event_espresso'), $column, $this->_model->get_this_model_name() ));
	}



	/**
	 * At a minimum, we pretty well ALWAYS want to include the post type where querying
	 * CPT models, otherwise we could get rows which aren't of this post type
	 * @return array
	 */
	function _get_minimum_where_conditions(){
		return array(
			$this->_get_field_on_column('post_type')->get_name() => $this->_post_type
		);
	}



	/**
	 * @return array
	 * @throws \EE_Error
	 */
	protected function _get_default_where_conditions() {
		//find post_type field
		$status_field = $this->_get_field_on_column('post_status');
		return  array_replace_recursive(
			$this->_get_minimum_where_conditions(),
			array( $status_field->get_name() => array('NOT IN',array('auto-draft','trash')))
		);
	}
}
