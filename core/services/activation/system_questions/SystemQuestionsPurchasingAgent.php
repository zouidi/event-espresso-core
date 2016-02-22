<?php
namespace EventEspresso\core\services\activation\system_questions;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class SystemQuestionsPurchasingAgent
 *
 * Provides the data required for adding records to the EE Question and Question Group tables
 * for the Purchasing Agent System questions
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class SystemQuestionsPurchasingAgent extends SystemQuestionsBase {

	/**
	 * @return int
	 */
	public function tableInsertOrder() {
		return 3;
	}



	/**
	 * represents the value for one of the constants on EEM_Question_Group for this System Question Group
	 *
	 * @return int
	 */
	public function getQSGConstant() {
		return \EEM_Question_Group::system_purchase_agent;
	}



	/**
	 * returns an array where the keys are the fields names from the esp_question_group table,
	 * and the values set this question group's details
	 *
	 * @return array
	 */
	public function getQuestionGroupData() {
		return array(
			'QSG_name'            => __( 'Purchasing Agent', 'event_espresso' ),
			'QSG_identifier'      => 'purchasing-agent-' . time(),
			'QSG_desc'            => __( 'Questions for those that are purchasing for others', 'event_espresso' ),
			'QSG_order'           => 3,
			'QSG_show_group_name' => 1,
			'QSG_show_group_desc' => 1,
			'QSG_system'          => \EEM_Question_Group::system_purchase_agent,
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
			'purchaser_fname' => array(
				'QST_display_text'  => __( 'First Name', 'event_espresso' ),
				'QST_admin_label'   => __( 'Purchasing Agent First Name - System Question', 'event_espresso' ),
				'QST_system'        => 'purchaser_fname',
				'QST_type'          => 'TEXT',
				'QST_required'      => 1,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 1,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wpUserId(),
				'QST_deleted'       => 0
			),
			'purchaser_lname' => array(
				'QST_display_text'  => __( 'Last Name', 'event_espresso' ),
				'QST_admin_label'   => __( 'Purchasing Agent Last Name - System Question', 'event_espresso' ),
				'QST_system'        => 'purchaser_lname',
				'QST_type'          => 'TEXT',
				'QST_required'      => 1,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 2,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wpUserId(),
				'QST_deleted'       => 0
			),
			'purchaser_email' => array(
				'QST_display_text'  => __( 'Email Address', 'event_espresso' ),
				'QST_admin_label'   => __( 'Purchasing Agent Email Address - System Question', 'event_espresso' ),
				'QST_system'        => 'purchaser_email',
				'QST_type'          => 'TEXT', // todo: in 4.9 this can be changed to EMAIL
				'QST_required'      => 1,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 3,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wpUserId(),
				'QST_deleted'       => 0
			),
			'purchaser_organization' => array(
				'QST_display_text'  => __( 'Organization', 'event_espresso' ),
				'QST_admin_label'   => __( 'Purchasing Agent Organization - System Question', 'event_espresso' ),
				'QST_system'        => 'purchaser_organization',
				'QST_type'          => 'TEXT',
				'QST_required'      => 0,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 4,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wpUserId(),
				'QST_deleted'       => 0
			),
		);
	}

}
// End of file SystemQuestionsPurchasingAgent.php
// Location: /core/config/system_questions/SystemQuestionsPurchasingAgent.php