<?php
namespace EventEspresso\core\services\activation\system_questions;

use EventEspresso\core\services\activation\TableDataGenerator;


if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class SystemQuestionTableDataGenerator
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class SystemQuestionTableDataGenerator extends TableDataGenerator {

	/**
	 * SystemQuestionTableDataGenerator constructor.
	 *
	 * @param int $wp_user_id
	 */
	public function __construct( $wp_user_id ) {
		parent::__construct( $wp_user_id );
		$this->loadSystemQuestionTableDataGenerators();
	}



	/**
	 * loadSystemQuestionGeneratorClasses
	 *
	 * @access protected
	 * @return void
	 */
	protected function loadSystemQuestionTableDataGenerators() {
		$this->table_data_generators = $this->loadTableDataGenerators(
			glob( plugin_dir_path( __FILE__ ) . 'SystemQuestions*.php' ),
			'EventEspresso\\core\\services\\activation\\system_questions\\',
			'EventEspresso\\core\\services\\activation\\system_questions\\SystemQuestionsBase',
			$this->wp_user_id,
			array( 'SystemQuestionsBase' )
		);
	}



	/**
	 * getExistingSystemQuestionGroups
	 *
	 * @access protected
	 * @param string $table_name
	 * @return array
	 */
	protected function getExistingSystemQuestionGroups( $table_name ) {
		global $wpdb;
		$SQL = "SELECT 'QSG_system' FROM $table_name WHERE 'QSG_system' != '0'";
		// what we have
		$existing_question_groups = $wpdb->get_col( $SQL );
		// check the response
		return is_array( $existing_question_groups ) ? $existing_question_groups : array();
	}



	/**
	 * initializeSystemQuestionGroups
	 *
	 * @access public
	 * @return array
	 */
	public function initializeSystemQuestionGroups() {
		// QUESTION GROUPS
		$table_name = TableDataGenerator::tableNameWithPrefix( 'esp_question_group' );
		$existing_question_groups = $this->getExistingSystemQuestionGroups( $table_name );
		$QSG_IDs = array();
		foreach ( $this->table_data_generators as $classname => $table_data_generator ) {
			if ( $table_data_generator instanceof SystemQuestionsBase ) {
				$QSG_system = $table_data_generator->getQSGConstant();
				\EEH_Debug_Tools::printr( $classname, '$classname', __FILE__, __LINE__ );
				\EEH_Debug_Tools::printr( $QSG_system, '$QSG_system', __FILE__, __LINE__ );
				// record already exists, skip to next item
				if ( in_array( (string)$QSG_system, $existing_question_groups ) ) {
					continue;
				}
				$QSG_IDs[ $QSG_system ] = $this->insertData(
					$table_name,
					$table_data_generator->getQuestionGroupData(),
					$table_data_generator->getQuestionGroupDataTypes()
				);
			}
		}
		return $QSG_IDs;
	}



	/**
	 * initializeSystemQuestionGroups
	 *
	 * @access public
	 * @return void
	 */
	public function initializeSystemQuestions() {
		// QUESTION GROUPS
		$table_name = TableDataGenerator::tableNameWithPrefix( 'esp_question_group' );
		$existing_question_groups = $this->getExistingSystemQuestionGroups( $table_name );
		$QSG_IDs = array();
		foreach ( $this->table_data_generators as $classname => $table_data_generator ) {
			if ( $table_data_generator instanceof SystemQuestionsBase ) {
				$QSG_system = $table_data_generator->getQSGConstant();
				if ( in_array( (string)$QSG_system, $existing_question_groups ) ) {
				}
				$QSG_IDs[ $QSG_system ] = $this->insertData(
					$table_name,
					$table_data_generator->getQuestionGroupData(),
					$table_data_generator->getQuestionGroupDataTypes()
				);
			}
		}
		wp_die();

		// QUESTIONS
		global $wpdb;
		$table_name = TableDataGenerator::tableNameWithPrefix( 'esp_question' );
		$SQL = "SELECT QST_system FROM $table_name WHERE QST_system != ''";
		// what we have
		$questions = $wpdb->get_col( $SQL );
		// what we should have
		$QST_systems = array(
			'fname',
			'lname',
			'email',
			'address',
			'address2',
			'city',
			'state',
			'country',
			'zip',
			'phone',
			'purchase_fname',
			'purchase_lname',
			'purchase_email',
			'organization',
		);
		$order_for_group_1 = 1;
		$order_for_group_2 = 1;
		$order_for_group_3 = 1;
		// loop thru what we should have and compare to what we have
		foreach ( $QST_systems as $QST_system ) {
			// reset values array
			$QST_values = array();
			// if we don't have what we should have
			if ( ! in_array( $QST_system, $questions ) ) {
				// add it
				switch ( $QST_system ) {

					case 'organization':
						$QST_values = array(
						);
						break;
				}
				if ( ! empty( $QST_values ) ) {
					// insert system question
					$wpdb->insert(
						$table_name,
						$QST_values,
						array( '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%d', '%d', '%d' )
					);
					$QST_ID = $wpdb->insert_id;
					// QUESTION GROUP QUESTIONS
					if ( in_array( $QST_system, array( 'fname', 'lname', 'email' ) ) ) {
						$system_question_we_want = \EEM_Question_Group::system_personal;
					} else {
						$system_question_we_want = \EEM_Question_Group::system_address;
					}
					if ( isset( $QSG_IDs[ $system_question_we_want ] ) ) {
						$QSG_ID = $QSG_IDs[ $system_question_we_want ];
					} else {
						$id_col = \EEM_Question_Group::instance()->get_col(
							array( array( 'QSG_system' => $system_question_we_want ) )
						);
						if ( is_array( $id_col ) ) {
							$QSG_ID = reset( $id_col );
						} else {
							//ok so we didn't find it in the db either?? that's weird because we should have inserted it at the start of this method
							\EE_Log::instance()->log(
								__FILE__,
								__FUNCTION__,
								sprintf(
									__(
										'Could not associate question %1$s to a question group because no system question group existed',
										'event_espresso'
									),
									$QST_ID
								),
								'error'
							);
							continue;
						}
					}
					switch ( $QSG_ID ) {
					}
					//$QSG_order ( $QSG_ID == 1 ) ? $order_for_group_1++ : $order_for_group_2++;
					// add system questions to groups
					$wpdb->insert(
						TableDataGenerator::tableNameWithPrefix( 'esp_question_group_question' ),
						array( 'QSG_ID' => $QSG_ID, 'QST_ID' => $QST_ID, 'QGQ_order' => $QSG_order ),
						array( '%d', '%d', '%d' )
					);
				}
			}
		}
	}
}
// End of file SystemQuestionTableDataGenerator.php
// Location: /core/config/system_questions/SystemQuestionTableDataGenerator.php