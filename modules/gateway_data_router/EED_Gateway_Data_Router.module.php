<?php
use EventEspresso\modules\ForwardedModuleResponse;
use EventEspresso\modules\gateway_data_router\GatewayResponse;

defined('EVENT_ESPRESSO_VERSION') || exit();



/**
 * Class EED_Gateway_Data_Router
 * Handles responses from payment gateways
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         4.8
 */
class EED_Gateway_Data_Router extends EED_Module
{

    /**
     * @return EED_Module|EED_Gateway_Data_Router
     */
    public static function instance()
    {
        return parent::get_instance(__CLASS__);
    }



    /**
     *    set_hooks - for hooking into EE Core, other modules, etc
     *
     * @access    public
     * @return    void
     */
    public static function set_hooks()
    {
        // route requests to domain.com/?ee_gateway_response=receive_ipn
        //                to  EED_Gateway_Data_Router::receive_ipn()
        EE_Config::register_route('receive_ipn', 'EED_Gateway_Data_Router', 'receive_ipn', 'ee_gateway_response');
        // route requests to domain.com/?ee_gateway_response=user_return
        //                to  EED_Gateway_Data_Router::receive_gateway_response()
        EE_Config::register_route(
            'user_return',                      // << route param "pretty" value
            'EED_Gateway_Data_Router',          // this class
            'process_user_return_from_gateway', // method to route request to
            'ee_gateway_response'               // << custom route key
        );
        EE_Config::register_forward(
            'user_return',                  // << FROM: route param "pretty" value - must match existing route
            ForwardedModuleResponse::PROCESS_FORWARD,    // << STATUS : If the "FROM: route param" listed immediately above this,
            // returns this value, then forward to the "TO: route" in the array below
            array(
                'spco',                     // << TO: custom route key - must match existing route
                'process_gateway_response'  // << TO: route param "pretty" value - must match existing route
            ),
            'ee_gateway_response'           // << FROM: custom route key - must match existing route
        );
    }



    /**
     *    set_hooks_admin - for hooking into EE Admin Core, other modules, etc
     *
     * @access    public
     * @return    void
     */
    public static function set_hooks_admin()
    {
    }



    /**
     *    run - initial module setup
     *
     * @access    public
     * @param    WP $WP
     * @return    void
     */
    public function run($WP)
    {
    }



    public function receive_ipn()
    {
        if (EE_Registry::instance()->REQ->is_set('e_reg_url_link')) {
            /** @type EEM_Transaction $EEM_Transaction */
            $EEM_Transaction = EE_Registry::instance()->load_model('Transaction');
            $current_txn = $EEM_Transaction->get_transaction_from_reg_url_link();
        } else {
            $current_txn = null;
        }
        $payment_method = null;
        $payment_method_slug = EE_Registry::instance()->REQ->get('ee_payment_method', null);
        if ($payment_method_slug) {
            $payment_method = EEM_Payment_Method::instance()->get_one_by_slug($payment_method_slug);
        }
        /** @type EE_Payment_Processor $payment_processor */
        $payment_processor = EE_Registry::instance()->load_core('Payment_Processor');
        $payment_processor->process_ipn($_REQUEST, $current_txn, $payment_method);
        //allow gateways to add a filter to stop rendering the page
        if (apply_filters('FHEE__EES_Espresso_Txn_Page__run__exit', false)) {
            exit;
        }
    }



    /**
     * @return GatewayResponse
     * @throws \EE_Error
     */
    public function process_user_return_from_gateway()
    {
        // create an instance of our DTO (Data Transfer Object)
        $gateway_response = new GatewayResponse();
        // array of parameters we are looking for where keys represent the $_REQUEST key
        // and values represent the keys we will use internally for setting and getting the data
        $request_param_map = array(
            'spco_txn'                   => 'transaction_id',
            'selected_method_of_payment' => 'selected_method_of_payment',
        );
        // cycle thru each of the above data parameters
        foreach ($request_param_map as $request_param => $dto_property) {
            // first look in the $_POST array, then if not found, look in the $_GET array
            foreach (array($_POST, $_GET) as $data_source) {
                // but only if the array isn't empty, and the parameter exists, the internal key matches one for our DTO
                if (
                    ! empty($data_source)
                    && ! empty($data_source[$request_param])
                    && $gateway_response->has($dto_property)
                ) {
                    // and set the value (validation will occur within the DTO)
                    $gateway_response->set($dto_property, $data_source[$request_param]);
                    continue;
                }
            }
            // didn't find parameter in the request? then let's look in the session...
            $session = EE_Session::instance();
            if ($session instanceof EE_Session) {
                if ( ! $gateway_response->get('session_id')) {
                    $gateway_response->set('session_id', $session->id());
                }
                if ($session->checkout() instanceof EE_Checkout) {
                    if ( ! $gateway_response->get('selected_method_of_payment')) {
                        $gateway_response->set(
                            'selected_method_of_payment',
                            $session->checkout()->selected_method_of_payment
                        );
                    }
                    if ( ! $gateway_response->get('transaction_id')) {
                        $gateway_response->set('transaction_id', $session->checkout()->transaction->ID());
                    }
                    if ( ! $gateway_response->get('transaction')) {
                        $gateway_response->set('transaction', $session->checkout()->transaction);
                    }
                }
            }
        }
        if ($gateway_response->valid()) {
            $gateway_response->setStatus(ForwardedModuleResponse::PROCESS_FORWARD);
        }
        return $gateway_response;
    }



    /**
     * Gets the URL for IPNs for this payment method (or a generic one)
     *
     * @param EE_Payment_Method $pm
     * @return string
     */
    public static function get_static_ipn_url_for_payment_method(EE_Payment_Method $pm = null)
    {
        $url = add_query_arg(
            array(
                'ee_gateway_response' => 'receive_ipn',
            ),
            site_url()
        );
        if ($pm instanceof EE_Payment_Method) {
            $url = add_query_arg(
                array(
                    'ee_payment_method' => $pm->slug(),
                ),
                $url
            );
        }
        return $url;
    }



    /**
     * Gets the return URL payment methods should use if they can't set it dynamically
     *
     * @param EE_Payment_Method $pm
     * @return string
     */
    public static function get_static_return_url(EE_Payment_Method $pm = null)
    {
        $url = add_query_arg(
            array(
                'ee_gateway_response' => 'user_return',
            ),
            site_url()
        );
        if ($pm instanceof EE_Payment_Method) {
            $url = add_query_arg(
                array(
                    'ee_payment_method' => $pm->slug(),
                ),
                $url
            );
        }
        return $url;
    }
}
// End of file EED_Gateway_Data_Router.module.php
// Location: /EED_Gateway_Data_Router.module.php