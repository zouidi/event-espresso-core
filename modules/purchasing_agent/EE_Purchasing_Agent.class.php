<?php
if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}
/**
 * Class EE_Purchasing_Agent
 *
 * Description
 *
 * @package               Event Espresso
 * @subpackage            core
 * @author                Brent Christensen
 * @since                 $VID:$
 *
 */
class EE_Purchasing_Agent {


	/**
	 * the currently logged in user submitting the reg form
	 * and handling payment for the transaction
	 *
	 * @type EE_Attendee $_purchasing_agent
	 */
	protected $_purchasing_agent = null;



	/**
	 * EE_Purchasing_Agent constructor.
	 * @param \EE_Attendee $attendee
	 */
	public function __construct( EE_Attendee $attendee = null ) {
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
	 * @param EE_Form_Section_Proper $attendee_info_reg_form
	 * @param EE_Checkout            $checkout
	 * @return string content
	 */
	public function generate_purchasing_agent_form( EE_Form_Section_Proper $attendee_info_reg_form, EE_Checkout $checkout  ) {
		static $sync_info_displayed = false;
		if ( $sync_info_displayed || $checkout->revisit || $checkout->admin_request ) {
			return $attendee_info_reg_form;
		}
		$sync_info_displayed = true;
		EE_Registry::instance()->load_helper( 'HTML' );
		$attendee_info_reg_form->add_subsections(
			array(
				'purchasing_agent' => new EE_Form_Section_Proper(
					array(
						'name'            => 'purchaser',
						'html_id'         => 'purchaser',
						'layout_strategy' => new EE_Div_Per_Section_Layout(),
						'subsections'     => array(
							'purchasing_agent_h4'       => new EE_Form_Section_HTML(
								EEH_HTML::h4( __( 'Purchasing Details.', 'event_espresso' ), '', 'ee-reg-form-qstn-grp-title section-title' )
							),
							'select_input'              => $this->_purchasing_agent_input(),
							'attendee_sync_info_notice' => $this->_attendee_sync_info_notice(),
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
	 * @return EE_Select_Input
	 */
	protected function _purchasing_agent_input() {
		return new EE_Select_Input(
			array(
				'attendee' => __( 'Myself and maybe others: All information entered in the Attendee 1 box below will be mine.', 'event_espresso' ),
				'separate' => __( 'People other than myself: All information entered in the Attendee boxes below is for others.', 'event_espresso' ),
			),
			array(
				'html_name'        => 'ee_reg_qstn[purchasing_agent]',
				'html_id'          => 'ee_reg_qstn-purchasing_agent',
				'html_class'       => 'ee-reg-qstn',
				'required'         => true,
				'html_label_id'    => 'ee_reg_qstn-purchasing_agent-lbl',
				'html_label_class' => 'ee-reg-qstn',
				'html_label_text'  => __( 'I am purchasing tickets for...', 'event_espresso' ),
				'default'          => 'attendee'
			)
		);
	}



	/**
	 * _attendee_sync_info_notice
	 *
	 * displays a notice regarding information being synced with the WP user profile
	 * if the purchasing agent is also an attendee
	 * @return EE_Form_Section_HTML
	 */
	protected function _attendee_sync_info_notice() {
		return new EE_Form_Section_HTML(
			EEH_HTML::div(
				sprintf(
					__( '%1$sNote%2$s: If purchasing tickets for yourself, then any changes made to the %1$sPersonal Information%2$s details for Attendee 1 will be synced with your user profile for this site.', 'event_espresso' ),
					'<strong>', '</strong>'
				),
				'ee-attendee-sync-info-notice-dv',
				'highlight-bg important-notice'
			)
		);
	}



	/**
	 * _purchasing_agent_billing_form
	 *
	 * displays an EE_Billing_Attendee_Info_Form for a non-attendee purchaser to fill out
	 * @return \EE_Billing_Attendee_Info_Form
	 */
	public function _purchasing_agent_billing_form() {
		$billing_form = new EE_Billing_Attendee_Info_Form();
		if ( $this->_purchasing_agent instanceof EE_Attendee ) {
			$billing_form->populate_from_attendee( $this->_purchasing_agent );
		}
		return new EE_Form_Section_Proper(
			array(
				'layout_strategy' => new EE_Div_Per_Section_Layout(),
				'subsections'     => array(
					'frame_open'   => new EE_Form_Section_HTML(
						EEH_HTML::div( '', '', 'ee-reg-form-qstn-grp-dv' )
						. EEH_HTML::p(
							__( 'Please provide and/or confirm the following required information if you are purchasing tickets for others. This information will be synced with your user profile for this site', 'event_espresso' ),
							'',
							'highlight-bg important-notice'
						)
					),
					'billing_form' => $billing_form,
					'frame_close'  => new EE_Form_Section_HTML(
						EEH_HTML::divx()
					),
				)
			)
		);
	}



	/**
	 * callback for FHEE__registration_page_attendee_information__before_attendee_panels
	 * @param array $defined_vars
	 * @return string
	 */
	public function display_purchasing_agent_form( $defined_vars ) {
		$html = '';
		if ( isset( $defined_vars[ 'purchasing_agent' ] ) ) {
			EE_Registry::instance()->load_helper( 'HTML' );
			$html .= '<fieldset id="ee-purchaser" class="ee-reg-form-attendee-dv" >';
			$html .= '<legend class="spco-attendee-lgnd smaller-text lt-grey-text">' . __( 'Purchasing Agent', 'event_espresso' ) . '</legend>';
			$html .= $defined_vars[ 'purchasing_agent' ];
			$html .= '</fieldset>';
		}
		return $html;
	}



	/**
	 * callback for FHEE__EE_SPCO_ReFHEE__Single_Page_Checkout___check_form_submission__valid_data
	 * @param array        $valid_data
	 * @param \EE_Checkout $checkout
	 */
	public function process_attendee_information( $valid_data, EE_Checkout $checkout ) {
		// to get info / do stuff, we may need/want to hook into other SPCO actions/filters like:
		// FHEE__EE_SPCO_Reg_Step_Attendee_Information___save_registration_form_input
		// FHEE__EE_SPCO_Reg_Step_Attendee_Information___associate_attendee_with_registration__attendee  (new filter)
		// for now, let's save the purchasing_agent attendee object as TXN meta data (ATT_ID is enough)
		// it will also be cached at EE_Checkout->purchasing_agent during SPCO visit
	}



}
// End of file EE_Purchasing_Agent.class.php
// Location: /EE_Purchasing_Agent.class.php