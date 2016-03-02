<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * Class EE_Required_Validation_Strategy
 *
 * @package 			Event Espresso
 * @subpackage 	core
 * @author 				Mike Nelson
 * @since 				4.6
 *
 */
class EE_Required_Validation_Strategy extends EE_Validation_Strategy_Base{


	/*
	 * indicates whether or not this validation strategy is general enough that it can be applied to any/most input
	 * a validation strategy that only applies to one,or very few, input type(s) would set this value to false
	 *
	 *  @var boolean $_generally_applicable
	 */
	protected static $_generally_applicable = true;



	/**
	 * @param null $validation_error_message
	 */
	public function __construct( $validation_error_message = NULL ) {
		if( ! $validation_error_message ){
			$validation_error_message = __("This field is required.", "event_espresso");
		}
		parent::__construct( $validation_error_message );
	}



	/**
	 * just checks the field isn't blank
	 *
	 * @param $normalized_value
	 * @return bool
	 * @throws \EE_Validation_Error
	 */
	function validate($normalized_value) {
		if( $normalized_value === '' || $normalized_value === NULL || $normalized_value === array()){
			throw new EE_Validation_Error( $this->get_validation_error_message(), 'required');
		}else{
			return true;
		}
	}



	/**
	 * @return array
	 */
	function get_jquery_validation_rule_array(){
		return array( 'required'=>true, 'messages' => array( 'required' => $this->get_validation_error_message() ) );
	}

}
