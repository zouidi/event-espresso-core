<?php
namespace EventEspresso\core\services\activation\system_questions;

use EventEspresso\core\services\activation\TableDataGenerator;


if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class SystemQuestionTableDataGenerator
 *
 * This class specifies details for what type of table data classes get loaded via TableDataGenerator,
 * ( instances of SystemQuestionsBase ) and then provides methods for inserting the data provided
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
	 * @param \EEM_Base $model
	 * @return array
	 */
	protected function getExistingData( $field_name, \EEM_Base $model ) {
		// what we have
		$existing_data = $model->get_col( array( array( $field_name => array( '!=', 0 ) ) ), $field_name );
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
		/** @var \EEM_Question_Group $EEM_Question_Group */
		$EEM_Question_Group = \EE_Registry::instance()->load_model( 'EEM_Question_Group' );
		// get existing system question groups
		$existing_question_groups = $this->getExistingData( 'QSG_system', $EEM_Question_Group );
		foreach ( $this->tableDataGenerators() as $classname => $table_data_generator ) {
			if ( $table_data_generator instanceof SystemQuestionsBase ) {
				$QSG_system = $table_data_generator->getQSGConstant();
				// record already exists, skip to next item
				if ( in_array( (string)$QSG_system, $existing_question_groups ) ) {
					continue;
				}
				$QSG_ID = $this->insertData( $EEM_Question_Group, $table_data_generator->getQuestionGroupData() );
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
		/** @var \EEM_Question $EEM_Question */
		$EEM_Question = \EE_Registry::instance()->load_model( 'EEM_Question' );
		/** @var \EEM_Question_Group_Question $EEM_Question_Group_Question */
		$EEM_Question_Group_Question = \EE_Registry::instance()->load_model( 'EEM_Question_Group_Question' );
		// get existing system questions
		$existing_questions = $this->getExistingData( 'QST_system', $EEM_Question );
		foreach ( $this->tableDataGenerators() as $classname => $table_data_generator ) {
			$QSG_order = 0;
			if ( $table_data_generator instanceof SystemQuestionsBase ) {
				$allowed_system_questions = $EEM_Question->allowed_system_questions_in_system_question_group(
					$table_data_generator->getQSGConstant()
				);
				$system_questions = $table_data_generator->getSystemQuestions();
				foreach ( $system_questions as $system_question => $system_question_data ) {
					$QSG_order++;
					// record already exists OR question not allowed for this question group, so skip to next item
					if (
						in_array( $system_question, $existing_questions )
						|| ! in_array( $system_question, $allowed_system_questions )
					) {
						continue;
					}
					$QST_ID = $this->insertData( $EEM_Question, $system_question_data );
					// now set a relation to it's question group
					$this->insertData(
						$EEM_Question_Group_Question,
						array(
							'QSG_ID'    => $table_data_generator->qsgID(),
							'QST_ID'    => $QST_ID,
							'QGQ_order' => $QSG_order
						)
					);
				}
			}
		}
	}



}
// End of file SystemQuestionTableDataGenerator.php
// Location: /core/config/system_questions/SystemQuestionTableDataGenerator.php