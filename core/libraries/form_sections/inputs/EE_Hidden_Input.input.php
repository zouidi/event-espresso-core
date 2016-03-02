<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * EE_Hidden_Input
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Mike Nelson
 */
class EE_Hidden_Input extends EE_Form_Input_Base{

	/**
	 * @param array $input_settings
	 */
	function __construct($input_settings = array()){
		//require_once('strategies/display_strategies/EE_Text_Input_Display_Strategy.strategy.php');
		$this->_set_display_strategy(new EE_Hidden_Display_Strategy());
		if (
			isset( $input_settings['normalization_strategy'] )
			&& $input_settings['normalization_strategy'] instanceof EE_Normalization_Strategy_Base
		) {
			$this->_set_normalization_strategy( $input_settings['normalization_strategy'] );
		} else {
			$this->_set_normalization_strategy( new EE_Text_Normalization() );
		}
		parent::__construct( $input_settings );
	}



	/**
	 * @return string
	 */
	public function get_html_for_label() {
		return '';
	}




	/**
	 * list of possible validation strategies that *could* be applied to this input
	 *
	 * @return array EE_Enum_Validation_Strategy
	 */
	public static function optional_validation_strategies() {
		return array(
			'credit_card' => 'EE_Credit_Card_Validation_Strategy',
			'email'       => 'EE_Email_Validation_Strategy',
			'enum'        => 'EE_Enum_Validation_Strategy',
			'float'       => 'EE_Float_Validation_Strategy',
			'int'         => 'EE_Int_Validation_Strategy',
			'full_html'   => 'EE_Full_HTML_Validation_Strategy',
			'many_valued' => 'EE_Many_Valued_Validation_Strategy',
			'max_length'  => 'EE_Max_Length_Validation_Strategy',
			'min_length'  => 'EE_Min_Length_Validation_Strategy',
			'plaintext'   => 'EE_Plaintext_Validation_Strategy',
			'required'    => 'EE_Required_Validation_Strategy',
			'simple_html' => 'EE_Simple_HTML_Validation_Strategy',
			'text'        => 'EE_Text_Validation_Strategy',
			'url'         => 'EE_URL_Validation_Strategy',
		);
	}



}
// End of file EE_Hidden_Input.input.php