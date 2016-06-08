<?php
if ( !defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 *
 * EE_Paypal_Standard_Form
 * Override's normal EE_Payment_Method_Form to force shipping details to be set to require info
 * whenever the admin selects paypal to calculate shipping or taxes
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Mike Nelson
 *
 */
class EE_Paypal_Standard_Form extends EE_Payment_Method_Form {



	/**
	 * @param EE_PMT_Paypal_Standard $payment_method_type
	 */
	public function __construct( $payment_method_type ){
		parent::__construct(
			array(
				'payment_method_type'          => $payment_method_type,
				'extra_meta_inputs'            => array(
					'paypal_id'        => new EE_Text_Input( array(
						'html_label_text' => sprintf( __( "Paypal Email %s", 'event_espresso' ), $payment_method_type->get_help_tab_link() ),
						'html_help_text'  => __( "Typically payment@example-domain.com", 'event_espresso' ),
						'required'        => true
					) ),
					'image_url'        => new EE_Admin_File_Uploader_Input( array(
						'html_help_text'  => __( "Used for your business/personal logo on the PayPal page", 'event_espresso' ),
						'html_label_text' => __( 'Image URL', 'event_espresso' )
					) ),
					'paypal_taxes'     => new EE_Yes_No_Input( array(
						'html_label_text' => sprintf( __( 'Paypal Calculates Taxes %s', 'event_espresso' ), $payment_method_type->get_help_tab_link() ),
						'html_help_text'  => __( 'Whether Paypal should add taxes to the order', 'event_espresso' ),
						'default'         => false
					) ),
					'paypal_shipping'  => new EE_Yes_No_Input( array(
						'html_label_text' => sprintf( __( 'Paypal Calculates Shipping %s', 'event_espresso' ), $payment_method_type->get_help_tab_link() ),
						'html_help_text'  => __( 'Whether Paypal should add shipping surcharges', 'event_espresso' ),
						'default'         => false
					) ),
					'shipping_details' => new EE_Select_Input( array(
						EE_PMT_Paypal_Standard::shipping_info_none     => __( "Do not prompt for an address", 'event_espresso' ),
						EE_PMT_Paypal_Standard::shipping_info_optional => __( "Prompt for an address, but do not require it", 'event_espresso' ),
						EE_PMT_Paypal_Standard::shipping_info_required => __( "Prompt for an address, and require it", 'event_espresso' )
					) ),
				),
				'subsections' => array(
					'tls_check' => $this->_meets_new_tls_requirements()
				),
				'before_form_content_template' => $payment_method_type->file_folder() . DS . 'templates' . DS . 'paypal_standard_settings_before_form.template.php',
			)
		);
	}

	/**
	 * Pings the tls test server for paypal
	 * @return array|WP_Remote_Requests_Response|WP_Error
	 */
	protected function _meets_new_tls_requirements() {
		//not httpS
//		$result = wp_remote_get( 'http://tlstest.paypal.com', array( 'httpversion' => '1.1' );
		//using http 1.0
//		$result = wp_remote_get( 'https://tlstest.paypal.com', array( 'httpversion' => '1.0') );
		//tls problem
//		add_action( 
//			'http_api_curl', 
//			function( $handle ) {
//				curl_setopt( $handle, CURLOPT_SSLVERSION, 3 );
//			},
//			10,
//			1 
//		);
//		$result = wp_remote_get( 'https://tlstest.paypal.com', array( 'httpversion' => '1.1' ) );
		//ok
		$result = wp_remote_get( 'https://tlstest.paypal.com' );
		if( is_wp_error( $result ) ) {
			$success = false;
			$message = sprintf(
				__( 'Problem communicating with PayPal: %1$s', 'event_espresso' ),
				$result->get_error_message()
			);
		} else {
			
			$response_body = wp_remote_retrieve_body( $result );
			if( strpos(  $response_body,
					'ERROR' ) !== false ) {
				$success = false;
				$message = sprintf(
					__( 'Problem communicating with PayPal: %1$s', 'event_espresso' ),
					$response_body
				);
			} else {
				$success = true;
				$message = sprintf(
					__( 'Successful communication with Paypal: %1$s', 'event_espresso' ),
					$response_body
				);
			}
		}
		if( ! $success ) {
			$message .= '<br><a href="https://devblog.paypal.com/upcoming-security-changes-notice/">' . __( 'Please Review PayPal\'s Documentation', 'event_espresso' ) . '</a>';
		}
		return new EE_Form_Section_HTML(
			EEH_HTML::tr(
				EEH_HTML::td( __( 'Communicateion Test', 'event_espresso' ) )
				. EEH_HTML::td( $message, 'paypal-communication-test', $success ? 'success' : 'attention' )
			)
		);
	}


	/**
	 * @param array $req_data
	 */
	protected function _normalize( $req_data ) {
		parent::_normalize( $req_data );
		$paypal_calculates_shipping = $this->get_input_value( 'paypal_shipping' );
		$paypal_calculates_taxes = $this->get_input_value( 'paypal_taxes' );
		$paypal_requests_address_info = $this->get_input_value( 'shipping_details' );
		if (
			( $paypal_calculates_shipping || $paypal_calculates_taxes ) &&
			$paypal_requests_address_info == EE_PMT_Paypal_Standard::shipping_info_none
		) {
			//they want paypal to calculate taxes or shipping. They need to ask for
			//address info, otherwise paypal can't calculate taxes or shipping
			/** @type EE_Select_Input $shipping_details_input */
			$shipping_details_input = $this->get_input( 'shipping_details' );
			$shipping_details_input->set_default( EE_PMT_Paypal_Standard::shipping_info_optional );
			$shipping_details_input_options = $shipping_details_input->options();
			EE_Error::add_attention(
				sprintf(
					__( 'Automatically set "%s" to "%s" because Paypal requires address info in order to calculate shipping or taxes.', 'event_espresso' ),
					strip_tags( $shipping_details_input->html_label_text() ),
					isset( $shipping_details_input_options[ EE_PMT_Paypal_Standard::shipping_info_optional ] )
						? $shipping_details_input_options[ EE_PMT_Paypal_Standard::shipping_info_optional ]
						: __( 'Unknown', 'event_espresso' )
				),
				__FILE__, __FUNCTION__, __LINE__
			);
		}
	}



}
// End of file EE_Paypal_Standard_Form.php