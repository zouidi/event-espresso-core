<?php
namespace EventEspresso\modules\purchasing_agent;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class PurchasingAgentForm
 *
 * generates a form for determining if the purchasing agent is also a registrant,
 * and if NOT, then captures the required billing information so that it is available
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         4.8
 *
 */
class PurchasingAgentForm {



	/**
	 * the currently logged in user submitting the reg form
	 * and handling payment for the transaction
	 *
	 * @type \EE_Attendee $_purchasing_agent
	 */
	protected $_purchasing_agent = null;



	/**
	 * EE_Purchasing_Agent constructor.
	 *
	 * @param \EE_Attendee $attendee
	 */
	public function __construct( \EE_Attendee $attendee = null ) {
		$this->_purchasing_agent = $attendee;
	}



	/**
	 * generate_purchasing_agent_form
	 *
	 * callback for FHEE__EE_SPCO_Reg_Step_Attendee_Information__generate_reg_form__reg_form
	 * with the purpose of adding a form subsection to the beginning of the SPCO reg form
	 * that determines whether the user filling out the form and purchasing the tickets
	 * will ALSO be an attendee, whose information will be entered into the Attendee 1 form
	 *
	 * @param \EE_Form_Section_Proper $attendee_info_reg_form
	 * @param \EE_Checkout            $checkout
	 * @return string content
	 */
	public function generate_purchasing_agent_form(
		\EE_Form_Section_Proper $attendee_info_reg_form,
		\EE_Checkout $checkout
	) {
		static $sync_info_displayed = false;
		if ( $sync_info_displayed || $checkout->revisit || $checkout->admin_request ) {
			return $attendee_info_reg_form;
		}
		//\EEH_Debug_Tools::printr( $attendee_info_reg_form->name(), '$attendee_info_reg_form->name()', __FILE__, __LINE__ );
		$sync_info_displayed = true;
		\EE_Registry::instance()->load_helper( 'HTML' );
		$attendee_info_reg_form->add_subsections(
			array(
				'purchasing_agent_form' => new \EE_Form_Section_Proper(
					array(
						'name'            => 'purchaser',
						'html_id'         => 'purchaser',
						'layout_strategy' => new \EE_Div_Per_Section_Layout(),
						'subsections'     => array(
							'purchasing_agent_h4'       => new \EE_Form_Section_HTML(
								\EEH_HTML::h4(
									__( 'Purchasing Details.', 'event_espresso' ),
									'',
									'ee-reg-form-qstn-grp-title section-title'
								)
							),
							'purchasing_agent_input'    => $this->_purchasing_agent_input(),
							'additional_info_notice'    => $this->_additional_info_notice(),
							'billing_form'              => $this->_purchasing_agent_billing_form(),
						),
					)
				)
			)
		);
		return $attendee_info_reg_form;
	}



	/**
	 * _purchasing_agent_input
	 *
	 * displays a dropdown input for selecting the purchasing agent for the TXN
	 *
	 * @return \EE_Select_Input
	 */
	protected function _purchasing_agent_input() {
		return new \EE_Radio_Button_Input(
			array(
				'attendee' => __( 'Myself', 'event_espresso' ),
				'separate' =>  __( 'Others', 'event_espresso' ),
			),
			array(
				'html_name'        => 'ee_purchasing_agent',
				'html_id'          => 'ee_purchasing_agent',
				'html_class'       => 'switch-field ee-reg-qstn',
				'required'         => false,
				'html_label_id'    => 'ee_reg_qstn-purchasing_agent-lbl',
				'html_label_class' => 'ee-reg-qstn',
				'html_label_text'  =>  __( 'I am registering for...', 'event_espresso' ),
				'default'          => 'attendee',
				'label_position'   => \EE_Form_Input_With_Options_Base::label_after_input
			)
		);
	}



	/**
	 * _attendee_sync_info_notice
	 *
	 * displays a notice regarding information being synced with the WP user profile
	 * if the purchasing agent is also an attendee
	 *
	 * @return \EE_Form_Section_HTML
	 */
	protected function _additional_info_notice() {
		return new \EE_Form_Section_HTML(
			\EEH_HTML::div(
				__(
					'If you are registering for yourself, then ALL information entered in the "Attendee 1" box below will be yours. If there are additional people in your registration group, then any additional attendee questions will be for them.',
					'event_espresso'
				),
				'ee-additional-attendee-info-notice-dv',
				'ee-additional-info'
			) .
			\EEH_HTML::div(
				__(
					'If you are NOT registering for yourself and will not be attending, then ALL information entered in the Attendee boxes further below is for others. But we still require some billing related information from you first.',
					'event_espresso'
				),
				'ee-additional-separate-info-notice-dv',
				'ee-additional-info',
				'display: none;'
			)
		);
	}



	/**
	 * _purchasing_agent_billing_form
	 *
	 * displays an EE_Billing_Attendee_Info_Form for a non-attendee purchaser to fill out
	 *
	 * @return \EE_Billing_Attendee_Info_Form
	 */
	public function _purchasing_agent_billing_form() {
		$billing_form = new \EE_Billing_Attendee_Info_Form();
		if ( $this->_purchasing_agent instanceof \EE_Attendee ) {
			$billing_form->populate_from_attendee( $this->_purchasing_agent );
		}
		return new \EE_Form_Section_Proper(
			array(
				'layout_strategy' => new \EE_Div_Per_Section_Layout(),
				'subsections'     => array(
					'frame_open'   => new \EE_Form_Section_HTML(
						\EEH_HTML::div( '', '', 'ee-reg-form-qstn-grp-dv' )
						. \EEH_HTML::p(
							__(
								'Please provide the following required information if you are purchasing tickets for others.',
								'event_espresso'
							),
							'',
							'important-notice'
						)
					),
					'billing_form' => $billing_form,
					'frame_close'  => new \EE_Form_Section_HTML(
						\EEH_HTML::divx()
					),
				)
			)
		);
	}



	/**
	 * callback for FHEE__registration_page_attendee_information__before_attendee_panels
	 *
	 * @param array $defined_vars
	 * @return string
	 */
	public function display_purchasing_agent_form( $defined_vars ) {
		$html = '';
		if ( isset( $defined_vars[ 'purchasing_agent_form' ] ) ) {
			\EE_Registry::instance()->load_helper( 'HTML' );
			$html .= '<fieldset id="ee-purchaser" class="ee-reg-form-attendee-dv" >';
			$html .= '<legend class="spco-attendee-lgnd smaller-text lt-grey-text">';
			$html .= __( 'Purchasing Agent', 'event_espresso' ) . '</legend>';
			$html .= $defined_vars[ 'purchasing_agent_form' ];
			$html .= '</fieldset>';
		}
		return $html;
	}



	/**
	 * callback for FHEE__EE_SPCO_ReFHEE__Single_Page_Checkout___check_form_submission__valid_data
	 *
	 * @param array        $valid_data
	 * @param \EE_Checkout $checkout
	 */
	public function process_attendee_information( $valid_data, \EE_Checkout $checkout ) {
		//\EEH_Debug_Tools::printr( __FUNCTION__, __CLASS__, __FILE__, __LINE__ );
		// to get info / do stuff, we may need/want to hook into other SPCO actions/filters like:
		// FHEE__EE_SPCO_Reg_Step_Attendee_Information___save_registration_form_input
		// FHEE__EE_SPCO_Reg_Step_Attendee_Information___associate_attendee_with_registration__attendee  (new filter)
		// for now, let's save the purchasing_agent attendee object as TXN meta data (ATT_ID is enough)
		// it will also be cached at EE_Checkout->purchasing_agent during SPCO visit
		//\EEH_Debug_Tools::printr( $valid_data, '$valid_data', __FILE__, __LINE__ );
		//\EEH_Debug_Tools::printr( $checkout->current_step->reg_form->name(), 'current_step->reg_form->name()', __FILE__, __LINE__ );
		/** @var \EE_Form_Section_Proper $purchasing_agent_form */
		//$purchasing_agent_form = $checkout->current_step->reg_form->get_subsection( 'purchasing_agent_form' );
		/** @var \EE_Radio_Button_Input $purchasing_agent_input */
		$purchasing_agent_input = $checkout->current_step->reg_form->find_subsection( 'purchasing_agent_input' );
		//\EEH_Debug_Tools::printr( $purchasing_agent_input, '$purchasing_agent_input', __FILE__, __LINE__ );
		$agent = $purchasing_agent_input instanceof \EE_Form_Input_Base
			? $purchasing_agent_input->raw_value()
			: '';
		//\EEH_Debug_Tools::printr( $agent, '$agent', __FILE__, __LINE__ );
		if ( $agent == 'attendee' ) {
			$billing_form = $checkout->current_step->reg_form->find_subsection( 'billing_form' );
			if ( $billing_form instanceof \EE_Form_Section_Proper ) {
				$billing_form->parent_section()->exclude( array( 'billing_form' ) );
				//\EEH_Debug_Tools::printr( $billing_form, '$billing_form', __FILE__, __LINE__ );
			}
		}
		//if ( isset( $reg_form[] ))

		//die();
	}



}
// End of file PurchasingAgentForm.php
// Location: /PurchasingAgentForm.php