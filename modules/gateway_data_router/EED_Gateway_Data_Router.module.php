<?php
if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class EED_Gateway_Data_Router
 *
 * Handles responses from payment gateways
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         4.8
 *
 */
class EED_Gateway_Data_Router extends EED_Module {

	/**
	 * @return EED_Gateway_Data_Router
	 */
	public static function instance() {
		return parent::get_instance( __CLASS__ );
	}



	/**
	 *    set_hooks - for hooking into EE Core, other modules, etc
	 *
	 * @access    public
	 * @return    void
	 */
	public static function set_hooks() {
		require_once( plugin_dir_path(__FILE__ ) . DS . 'EE_Gateway_Response.php' );
		// route requests to domain.com/?ee_gateway_route=receive_ipn  to  EED_Gateway_Data_Router::receive_ipn()
		EE_Config::register_route( 'receive_ipn', 'EED_Gateway_Data_Router', 'receive_ipn', 'ee_gateway_route' );
		// route requests to domain.com/?ee_gateway_route=gateway_response  to  EED_Gateway_Data_Router::process_gateway_response()
		EE_Config::register_route( 'gateway_response', 'EED_Gateway_Data_Router', 'process_gateway_response', 'ee_gateway_route' );
	}



	/**
	 *    set_hooks_admin - for hooking into EE Admin Core, other modules, etc
	 *
	 * @access    public
	 * @return    void
	 */
	public static function set_hooks_admin() {
	}



	/**
	 *    run - initial module setup
	 *
	 * @access    public
	 * @param    WP $WP
	 * @return    void
	 */
	public function run( $WP ) {
	}



	public function receive_ipn() {

	}



	public function process_gateway_response() {
		$gateway_response = new \EventEspresso\modules\gateway_data_router\EE_Gateway_Response();
		$request_param_map = array(
			'spco_txn' => 'transaction_id',
			'selected_method_of_payment' => 'selected_method_of_payment',
		);
		foreach ( $request_param_map as $request_param => $dto_property ) {
			foreach ( array( $_POST, $_GET ) as $data_source ) {
				if ( ! empty( $data_source ) ) {
					if ( ! empty( $data_source[ $request_param ] ) ) {
						$setter = 'set_' . $dto_property;
						if ( method_exists( $gateway_response, $setter ) ) {
							$gateway_response->{$setter}( $data_source[ $request_param ] );
							continue;
						}
					}
				}
			}
			// didn't find parameter in the request? let's look in the session then

		}
	}


}
// End of file EED_Gateway_Data_Router.module.php
// Location: /EED_Gateway_Data_Router.module.php