<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' )) {
	exit('No direct script access allowed');
}
/**
 *
 * EE_DMS_4_7_0_answer_question_group
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Brent Christensen
 *
 */
class EE_DMS_4_7_0_answer_question_group extends EE_Data_Migration_Script_Stage_Table{



	/**
	 * Just initializes the status of the migration
	 *
	 * @return EE_DMS_4_7_0_answer_question_group
	 */
	public function __construct() {
		global $wpdb;
		$this->_pretty_name = __( 'Answer - Question Groups',
			'event_espresso' );
		$this->_old_table = $wpdb->prefix.'esp_answer';
		parent::__construct();
	}



	/**
	 * updates the answer with the related question's Question Group ID
	 * @param array $answer an associative array where keys are column names and values are their values.
	 * @return null
	 */
	protected function _migrate_old_row( $answer ) {
		$QSG_ID = 0;
		$success = false;
		if ( ! empty( $answer['QST_ID'] )) {
			/** @type EEM_Question $EEM_Question */
			$EEM_Question = EE_Registry::instance()->load_model( 'Question' );
			$question = $EEM_Question->get_one_by_ID( $answer['QST_ID'] );
			if ( $question instanceof EE_Question ) {
				$question_groups = $question->question_groups();
				$question_group = is_array( $question_groups ) ? reset(
					$question_groups ) : null;
				if ( $question_group instanceof EE_Question_Group ) {
					$QSG_ID = $question_group->ID();
				}
			}
		}
		global $wpdb;
		if ( $QSG_ID ) {
			$success = $wpdb->update(
				$this->_old_table,
				array( 'QSG_ID' => $QSG_ID ),  // data
				array( 'QST_ID' => $answer['QST_ID'] ),  // where
				array( '%d' ),   // data format
				array( '%d' )  // where format
			);
		}
		if ( ! $success && $QSG_ID ) {
			$this->add_error(
				sprintf(
					__( 'Could not update Answer Question Group
						for Answer ID=%1$d because "%2$s"', 'event_espresso' ),
					$answer['QST_ID'],
					$wpdb->last_error
				)
			);
		} else if ( ! $success ) {
			$this->add_error(
				sprintf(
					__( 'Could not update Answer for Answer ID=%1$d because
					the the Question Group could not be determined',
						'event_espresso' ),
					$answer['QST_ID']
				)
			);
		}
	}



}
// End of file EE_DMS_4_7_0_answer_question_group.dmsstage.php