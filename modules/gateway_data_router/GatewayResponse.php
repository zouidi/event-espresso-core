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