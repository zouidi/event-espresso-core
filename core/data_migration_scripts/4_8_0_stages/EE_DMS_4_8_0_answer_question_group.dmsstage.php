<?php if ( ! defined( 'EVENT_ESPRESSO_VERSION' )) {
	exit('No direct script access allowed');
}
/**
 *
 * EE_DMS_4_8_0_answer_question_group
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Brent Christensen
 *
 */
class EE_DMS_4_8_0_answer_question_group extends EE_Data_Migration_Script_Stage_Table{



	/**
	 * Just initializes the status of the migration
	 *
	 * @return EE_DMS_4_8_0_answer_question_group
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
		global $wpdb;
		if ( ! empty( $answer['QST_ID'] )) {
			$question_group_question_table = $wpdb->prefix . 'esp_question_group_question';
			$SQL = "SELECT QSG_ID FROM $question_group_question_table WHERE QST_ID = %d";
			$QSG_ID = $wpdb->get_col( $wpdb->prepare( $SQL, $answer['QST_ID'] ));
			$QSG_ID = is_array( $QSG_ID ) ? reset( $QSG_ID ) : $QSG_ID;
			if ( $QSG_ID != 0 ) {
				$success = $wpdb->update(
					$this->_old_table,
					array( 'QSG_ID' => $QSG_ID ),  // data
					array( 'QST_ID' => $answer['QST_ID'] ),  // where
					array( '%d' ),   // data format
					array( '%d' )  // where format
				);
				if ( ! $success && $QSG_ID != 0  ) {
					$this->add_error(
						sprintf(
							__( 'Could not update Answer Question Group
						for Answer ID=%1$d because "%2$s"', 'event_espresso' ),
							$answer['QST_ID'],
							$wpdb->last_error
						)
					);
				}
			} else {
				$this->add_error(
					sprintf(
						__( 'Could not update Answer for Answer ID=%1$d because
					the the Question Group could not be determined',
							'event_espresso' ),
						$answer['QST_ID']
					)
				);			}
		} else {
			$this->add_error(
				__( 'Could not update Answer because a valid row ID was not
				received.', 'event_espresso' )
			);
		}
		$this->add_error( $wpdb->last_query );
	}



}
// End of file EE_DMS_4_8_0_answer_question_group.dmsstage.php