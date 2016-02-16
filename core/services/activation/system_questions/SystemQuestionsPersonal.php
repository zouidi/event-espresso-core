<?php
namespace EventEspresso\core\services\activation\system_questions;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class SystemQuestionsPersonal
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class SystemQuestionsPersonal extends SystemQuestionsBase {



	/**
	 * @return int
	 */
	public function tableInsertOrder() {
		return 1;
	}



	/**
	 * represents the value for one of the constants on EEM_Question_Group for this System Question Group
	 *
	 * @return int
	 */
	public function getQSGConstant() {
		return \EEM_Question_Group::system_personal;
	}



	/**
	 * returns an array where the keys are the fields names from the esp_question_group table,
	 * and the values set this question group's details
	 *
	 * @return array
	 */
	public function getQuestionGroupData() {
		return array(
			'QSG_name'            => __( 'Personal Information', 'event_espresso' ),
			'QSG_identifier'      => 'personal-information-' . time(),
			'QSG_desc'            => '',
			'QSG_order'           => 1,
			'QSG_show_group_name' => 1,
			'QSG_show_group_desc' => 1,
			'QSG_system'          => \EEM_Question_Group::system_personal,
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
	public function getSystemQuestions(){
		return array(
			'fname' => array(
				'QST_display_text'  => __( 'First Name', 'event_espresso' ),
				'QST_admin_label'   => __( 'First Name - System Question', 'event_espresso' ),
				'QST_system'        => 'fname',
				'QST_type'          => 'TEXT',
				'QST_required'      => 1,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 1,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wpUserId(),
				'QST_deleted'       => 0
			),
			'lname' => array(
				'QST_display_text'  => __( 'Last Name', 'event_espresso' ),
				'QST_admin_label'   => __( 'Last Name - System Question', 'event_espresso' ),
				'QST_system'        => 'lname',
				'QST_type'          => 'TEXT',
				'QST_required'      => 1,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 2,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wpUserId(),
				'QST_deleted'       => 0
			),
			'email' => array(
				'QST_display_text'  => __( 'Email Address', 'event_espresso' ),
				'QST_admin_label'   => __( 'Email Address - System Question', 'event_espresso' ),
				'QST_system'        => 'email',
				'QST_type'          => 'TEXT', // todo: in 4.9 this can be changed to EMAIL
				'QST_required'      => 1,
				'QST_required_text' => __( 'This field is required', 'event_espresso' ),
				'QST_order'         => 3,
				'QST_admin_only'    => 0,
				'QST_wp_user'       => $this->wpUserId(),
				'QST_deleted'       => 0
			),
		);
	}


}
// End of file SystemQuestionsPersonal.php
// Location: /core/config/system_questions/SystemQuestionsPersonal.php