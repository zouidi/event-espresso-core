<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * EE_Year_Input
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Mike Nelson
 *
 * This input has a default validation strategy of plaintext (which can be removed after construction)
 */
class EE_Text_Input extends EE_Form_Input_Base{


	/**
	 * @param array $options
	 */
	function __construct($options = array()){
		$this->_set_display_strategy(new EE_Text_Input_Display_Strategy());
		$this->_set_normalization_strategy(new EE_Text_Normalization());
		//by default we use the plaintext validation. If you want something else,
		//just remove it after the input is constructed :P using EE_Form_Input_Base::remove_validation_strategy()
		$this->_add_validation_strategy( new EE_Plaintext_Validation_Strategy() );
		parent::__construct($options);
	}



	/**
	 * list of possible validation strategies that *could* be applied to this input
	 *
	 * @return array
	 */
	public static function optional_validation_strategies() {
		return array(
			//'credit_card' => 'EE_Credit_Card_Validation_Strategy',
			//'email'       => 'EE_Email_Validation_Strategy',
			//'enum'        => 'EE_Enum_Validation_Strategy',
			//'float'       => 'EE_Float_Validation_Strategy',
			'int'         => 'EE_Int_Validation_Strategy',
			//'full_html'   => 'EE_Full_HTML_Validation_Strategy',
			//'many_valued' => 'EE_Many_Valued_Validation_Strategy',
			'max_length'  => 'EE_Max_Length_Validation_Strategy',
			'min_length'  => 'EE_Min_Length_Validation_Strategy',
			'plaintext'   => 'EE_Plaintext_Validation_Strategy',
			'required'    => 'EE_Required_Validation_Strategy',
			'simple_html' => 'EE_Simple_HTML_Validation_Strategy',
			'text'        => 'EE_Text_Validation_Strategy',
			//'url'         => 'EE_URL_Validation_Strategy',
		);
	}

}