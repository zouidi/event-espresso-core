<?php
if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class EE_URL_Input
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class EE_URL_Input extends EE_Form_Input_Base {

	/**
	 * @param array $input_settings
	 */
	function __construct( $input_settings = array() ) {
		$this->_set_display_strategy( new EE_Text_Input_Display_Strategy() );
		$this->_set_normalization_strategy( new EE_Text_Normalization() );
		$this->_add_validation_strategy(
			new EE_URL_Validation_Strategy(
				isset( $input_settings[ 'validation_error_message' ] )
					? $input_settings[ 'validation_error_message' ]
					: null
			)
		);
		parent::__construct( $input_settings );
		$this->set_html_class( $this->html_class() . ' url' );
	}


}
// End of file EE_URL_Input.php
// Location: /EE_URL_Input.php