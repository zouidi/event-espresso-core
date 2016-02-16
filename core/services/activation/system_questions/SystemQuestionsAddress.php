<?php
namespace EventEspresso\core\services\activation\system_questions;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class SystemQuestionsAddress
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class SystemQuestionsAddress extends SystemQuestionsBase {

	/**
	 * represents the value for one of the constants on EEM_Question_Group for this System Question Group
	 *
	 * @return int
	 */
	public function getQSGConstant() {
		return \EEM_Question_Group::system_address;
	}



	/**
	 * returns an array where the keys are the fields names from the esp_question_group table,
	 * and the values set this question group's details
	 *
	 * @return array
	 */
	public function getQuestionGroupData() {
		return array(
			'QSG_name'            => __( 'Address Information', 'event_espresso' ),
			'QSG_identifier'      => 'address-information-' . time(),
			'QSG_desc'            => '',
			'QSG_order'           => \EEM_Question_Group::system_address,
			'QSG_show_group_name' => 1,
			'QSG_show_group_desc' => 1,
			'QSG_system'          => \EEM_Question_Group::system_address,
			'QSG_deleted'         => 0
		);
	}



	/**
	 * returns a multi-dimensional array listing all of the system questions that belong to this group
	 * where the outer keys correspond to the QST_system field value for the question
	 * and the inner keys correspond to the QST_system field names from the esp_question table,
	 * and the values set each question's details
	 *
	 * @return array
	 */
	public function getSystemQuestions() {
		return array(
			'address' => array(
				'QST_display_text'  => __( 'Address', 'event_espresso' ),
				'QST_admin_label'   => __( 'Address - System Question', 'event_espresso' ),
				'QST_system'        => 'address',
				'QST_type'          => 'TEXT',
				'QST_required'      => 0,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 4,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wp_user_id,
				'QST_deleted'       => 0
			),
			'address2' => array(
				'QST_display_text'  => __( 'Address2', 'event_espresso' ),
				'QST_admin_label'   => __( 'Address2 - System Question', 'event_espresso' ),
				'QST_system'        => 'address2',
				'QST_type'          => 'TEXT',
				'QST_required'      => 0,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 5,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wp_user_id,
				'QST_deleted'       => 0
			),
			'city' => array(
				'QST_display_text'  => __( 'City', 'event_espresso' ),
				'QST_admin_label'   => __( 'City - System Question', 'event_espresso' ),
				'QST_system'        => 'city',
				'QST_type'          => 'TEXT',
				'QST_required'      => 0,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 6,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wp_user_id,
				'QST_deleted'       => 0
			),
			'state' => array(
				'QST_display_text'  => __( 'State/Province', 'event_espresso' ),
				'QST_admin_label'   => __( 'State/Province - System Question', 'event_espresso' ),
				'QST_system'        => 'state',
				'QST_type'          => 'STATE',
				'QST_required'      => 0,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 7,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wp_user_id,
				'QST_deleted'       => 0
			),
			'country' => array(
				'QST_display_text'  => __( 'Country', 'event_espresso' ),
				'QST_admin_label'   => __( 'Country - System Question', 'event_espresso' ),
				'QST_system'        => 'country',
				'QST_type'          => 'COUNTRY',
				'QST_required'      => 0,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 8,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wp_user_id,
				'QST_deleted'       => 0
			),
			'zip' => array(
				'QST_display_text'  => __( 'Zip/Postal Code', 'event_espresso' ),
				'QST_admin_label'   => __( 'Zip/Postal Code - System Question', 'event_espresso' ),
				'QST_system'        => 'zip',
				'QST_type'          => 'TEXT',
				'QST_required'      => 0,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 9,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wp_user_id,
				'QST_deleted'       => 0
			),
			'phone' => array(
				'QST_display_text'  => __( 'Phone Number', 'event_espresso' ),
				'QST_admin_label'   => __( 'Phone Number - System Question', 'event_espresso' ),
				'QST_system'        => 'phone',
				'QST_type'          => 'TEXT',
				'QST_required'      => 0,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 10,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wp_user_id,
				'QST_deleted'       => 0
			),
		);
	}



}
// End of file SystemQuestionsAddress.php
// Location: /core/config/system_questions/SystemQuestionsAddress.php