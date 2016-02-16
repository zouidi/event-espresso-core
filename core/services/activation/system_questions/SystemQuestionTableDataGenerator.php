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
		$table_data_generators = $this->loadTableDataGenerators(
			glob( plugin_dir_path( __FILE__ ) . 'SystemQuestions*.php' ),
			'EventEspresso\\core\\services\\activation\\system_questions\\',
			'EventEspresso\\core\\services\\activation\\system_questions\\SystemQuestionsBase',
			$this->wpUserId(),
			array( 'SystemQuestionsBase' )
		);
		uasort( $table_data_generators, array( $this, 'sortSystemQuestionTables' ) );
		$this->setTableDataGenerators( $table_data_generators );
	}



	/**
	 * sorts table based on the return value of each object's tableInsertOrder() method
	 *
	 * @param SystemQuestionsBase $a
	 * @param SystemQuestionsBase $b
	 * @return int
	 */
	private function sortSystemQuestionTables( SystemQuestionsBase $a, SystemQuestionsBase $b ) {
		if ( $a->tableInsertOrder() == $b->tableInsertOrder() ) {
			return 0;
		}
		return ( $a->tableInsertOrder() < $b->tableInsertOrder() ) ? -1 : 1;
	}



	/**
	 * getExistingData
	 *
	 * @access protected
	 * @param string $field_name
	 * @param string $table_name
	 * @return array
	 */
	protected function getExistingData( $field_name, $table_name ) {
		global $wpdb;
		$SQL = "SELECT $field_name FROM $table_name WHERE $field_name != 0";
		// what we have
		$existing_data = $wpdb->get_col( $SQL );
		// check the response
		return is_array( $existing_data ) ? $existing_data : array();
	}



	/**
	 * initializeSystemQuestionGroups
	 *
	 * @access public
	 * @return void
	 */
	public function initializeSystemQuestionGroups() {
		// QUESTION GROUPS
		$table_name = TableDataGenerator::tableNameWithPrefix( 'esp_question_group' );
		$existing_question_groups = $this->getExistingData( 'QSG_system', $table_name );
		foreach ( $this->tableDataGenerators() as $classname => $table_data_generator ) {
			if ( $table_data_generator instanceof SystemQuestionsBase ) {
				$QSG_system = $table_data_generator->getQSGConstant();
				// record already exists, skip to next item
				if ( in_array( (string)$QSG_system, $existing_question_groups ) ) {
					continue;
				}
				$QSG_ID = $this->insertData(
					$table_name,
					$table_data_generator->getQuestionGroupData(),
					$table_data_generator->getQuestionGroupDataTypes()
				);
				$table_data_generator->setQsgID( $QSG_ID );
			}
		}
	}



	/**
	 * initializeSystemQuestionGroups
	 *
	 * @access public
	 * @return void
	 */
	public function initializeSystemQuestions() {
		// QUESTION GROUPS
		$table_name = TableDataGenerator::tableNameWithPrefix( 'esp_question' );
		$existing_questions = $this->getExistingData( 'QST_system', $table_name );
		foreach ( $this->tableDataGenerators() as $classname => $table_data_generator ) {
			$QSG_order = 0;
			if ( $table_data_generator instanceof SystemQuestionsBase ) {
				$system_questions = $table_data_generator->getSystemQuestions();
				foreach ( $system_questions as $system_question => $system_question_data ) {
					$QSG_order++;
					// record already exists, skip to next item
					if ( in_array( $system_question, $existing_questions ) ) {
						continue;
					}
					$QST_ID = $this->insertData(
						$table_name,
						$system_question_data,
						$table_data_generator->getQuestionDataTypes()
					);
					// now set a relation to it's question group
					$this->insertData(
						TableDataGenerator::tableNameWithPrefix( 'esp_question_group_question' ),
						array(
							'QSG_ID' => $table_data_generator->qsgID(),
							'QST_ID' => $QST_ID,
							'QGQ_order' => $QSG_order
						),
						array( '%d', '%d', '%d' )
					);
				}
			}
		}
	}



}
// End of file SystemQuestionTableDataGenerator.php
// Location: /core/config/system_questions/SystemQuestionTableDataGenerator.php