<?php
namespace EventEspresso\modules\gateway_data_router;

use EventEspresso\modules\ForwardedModuleResponse;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class GatewayResponse
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class GatewayResponse extends ForwardedModuleResponse {

	/**
	 * GatewayResponse constructor.
	 */
	public function __construct(
	) {
		// plz see the ForwardedModuleResponse::$dataMap description for details regarding this
		$this->setDataMap(
			array(
				'session_id'                 => array( 'sanitize_text_field' ),
				'transaction_id'             => array( 'absint' ),
				'transaction'                => array( 'instanceof' => 'EE_Transaction' ),
				'selected_method_of_payment' => array( 'sanitize_text_field' ),
			)
		);
		parent::__construct();
	}



	/**
	 * overrides the parent valid() method which merely checks that the data array is not empty,
	 * but we want to ensure that these critical parameters are set before forwarding this object
	 *
	 * @return bool
	 */
	public function valid() {
		return ! empty( $this->data['session_id'] )
		       && ! empty( $this->data[ 'transaction_id' ] )
		       && ! empty( $this->data[ 'selected_method_of_payment' ] )
			? true
			: false;
	}


}
// End of file GatewayResponse.php
// Location: /GatewayResponse.php