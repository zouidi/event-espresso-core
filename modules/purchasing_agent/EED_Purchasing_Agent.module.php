<?php

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class EED_Purchasing_Agent
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class EED_Purchasing_Agent extends EED_Module {


	/**
	 * @type EE_Purchasing_Agent $_purchasing_agent
	 */
	protected static $_purchasing_agent = null;



	/**
	 * All frontend hooks.
	 */
	public static function set_hooks() {
		//hook into spco for styles and scripts.
		add_action(
			'AHEE__EED_Single_Page_Checkout__enqueue_styles_and_scripts__attendee_information',
			array( 'EED_Purchasing_Agent', 'enqueue_scripts_styles' )
		);
		add_filter(
			'FHEE__EE_SPCO_Reg_Step_Attendee_Information__generate_reg_form__reg_form',
			array( 'EED_Purchasing_Agent', 'generate_purchasing_agent_form' ),
			10,
			2
		);
		add_action(
			'FHEE__registration_page_attendee_information__before_attendee_panels',
			array( 'EED_Purchasing_Agent', 'display_purchasing_agent_form' ),
			10,
			1
		);
	}



	/**
	 * All admin hooks (and ajax)
	 */
	public static function set_hooks_admin() {
		if ( EE_FRONT_AJAX ) {
			add_filter(
				'FHEE__EE_SPCO_Reg_Step_Attendee_Information__generate_reg_form__reg_form',
				array( 'EED_Purchasing_Agent', 'generate_purchasing_agent_form' ),
				10,
				2
			);
			add_action(
				'FHEE__registration_page_attendee_information__before_attendee_panels',
				array( 'EED_Purchasing_Agent', 'display_purchasing_agent_form' ),
				10,
				1
			);
		}
	}



	/**
	 * @param WP $WP
	 */
	public function run( $WP ) {
	}



	/**
	 * Callback for AHEE__EED_Single_Page_Checkout__enqueue_styles_and_scripts__attendee_information
	 * used to register and enqueue scripts for purchasing agent integration with spco.
	 */
	public static function enqueue_scripts_styles() {
		wp_register_script(
			'ee_purchasing_agent',
			plugin_dir_url( __FILE__ ) . 'ee-purchasing-agent.js',
			array( 'single_page_checkout' ),
			EVENT_ESPRESSO_VERSION,
			true
		);
		wp_enqueue_script( 'ee_purchasing_agent' );
	}



	/**
	 * @return EE_Purchasing_Agent
	 */
	protected static function _get_purchasing_agent() {
		if ( ! EED_Purchasing_Agent::$_purchasing_agent instanceof EE_Purchasing_Agent ) {
			require( plugin_dir_path( __FILE__ ) . 'EE_Purchasing_Agent.class.php' );
			$attendee = EED_Purchasing_Agent::get_attendee_for_user( get_current_user_id() );
			EED_Purchasing_Agent::$_purchasing_agent = new EE_Purchasing_Agent( $attendee );
		}
		return EED_Purchasing_Agent::$_purchasing_agent;
	}



	/**
	 * Returns the EE_Attendee object attached to the given wp user.
	 *
	 * @param $user_or_id int|\WP_User $mixed $user_or_id can be WP_User or the user_id.
	 * @return \EE_Attendee
	 */
	public static function get_attendee_for_user( $user_or_id ) {
		$user_id = $user_or_id instanceof WP_User ? $user_or_id->ID : (int)$user_or_id;
		$attID = get_user_option( 'EE_Attendee_ID', $user_id );
		$attendee = null;
		if ( $attID ) {
			$attendee = EEM_Attendee::instance()->get_one_by_ID( $attID );
			$attendee = $attendee instanceof EE_Attendee ? $attendee : null;
		}
		return $attendee;
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
	 * @return string                                content
	 */
	public static function generate_purchasing_agent_form(
		EE_Form_Section_Proper $attendee_info_reg_form,
		EE_Checkout $checkout
	) {
		return EED_Purchasing_Agent::_get_purchasing_agent()->generate_purchasing_agent_form(
			$attendee_info_reg_form,
			$checkout
		);
	}



	/**
	 * callback for FHEE__registration_page_attendee_information__before_attendee_panels
	 *
	 * @param array $defined_vars
	 */
	public static function display_purchasing_agent_form( $defined_vars ) {
		if ( ! is_user_logged_in() ) {
			// todo: prompt for login ?
		}
		echo EED_Purchasing_Agent::_get_purchasing_agent()->display_purchasing_agent_form( $defined_vars );
	}



	/**
	 * callback for FHEE__EE_SPCO_ReFHEE__Single_Page_Checkout___check_form_submission__valid_data
	 *
	 * @param array $valid_data
	 * @param \EE_Checkout $checkout
	 */
	public static function process_attendee_information( $valid_data, EE_Checkout $checkout ) {
		// collect any other required data from methods in this class,
		// and pass along to EE_Purchasing_Agent::process_attendee_information() for processing
		EED_Purchasing_Agent::_get_purchasing_agent()->process_attendee_information( $valid_data, $checkout );
	}



}
// End of file EED_Purchasing_Agent.module.php
// Location: /EED_Purchasing_Agent.module.php