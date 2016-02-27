<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 * Question Group Model
 *
 * @package			Event Espresso
 * @subpackage		includes/models/
 * @author			Michael Nelson
 *
 * ------------------------------------------------------------------------
 */
require_once ( EE_MODELS . 'EEM_Soft_Delete_Base.model.php' );
require_once( EE_CLASSES . 'EE_Question_Group.class.php');
class EEM_Question_Group extends EEM_Soft_Delete_Base {

	const system_personal = 1;
	const system_address = 2;

  	// private instance of the Attendee object
	protected static $_instance = NULL;


	protected function __construct( $timezone = NULL ) {

		$this->singular_item = __('Question Group','event_espresso');
		$this->plural_item = __('Question Groups','event_espresso');

		$this->_tables = array(
			'Question_Group'=>new EE_Primary_Table('esp_question_group','QSG_ID')
		);

		$this->_fields = array(
			'Question_Group'=>array(
				'QSG_ID'=>new EE_Primary_Key_Int_Field('QSG_ID', __('Question Group ID','event_espresso')),
				'QSG_name'=>new EE_Plain_Text_Field('QSG_name', __('Question Group Name','event_espresso'), false, ''),
				'QSG_identifier'=>new EE_Plain_Text_Field('QSG_identifier', __('Text ID for question Group','event_espresso'), false, ''),
				'QSG_desc'=>new EE_Full_HTML_Field('QSG_desc', __('Description of Question Group','event_espresso'), true, ''),
				'QSG_parent' => new EE_Integer_Field('QSG_parent',__('The Question Group that this is a child of.','event_espresso'),true,null),
				'QSG_html_name' => new EE_Plain_Text_Field('QSG_html_name',__('Form Section Name','event_espresso'),true,null),
				'QSG_html_id' => new EE_Plain_Text_Field('QSG_html_id',__('HTML CSS id for question Group','event_espresso'),true,null),
				'QSG_html_class' => new EE_Plain_Text_Field('QSG_html_class',__('HTML CSS class for question Group','event_espresso'),true,null),
				'QSG_html_content' => new EE_Plain_Text_Field('QSG_html_content',__('Actual content if this is an HTML Form Section','event_espresso'),true,null),
				'QSG_order'=>new EE_Integer_Field('QSG_order', __('Order in which to show the question group','event_espresso'), true, 0),
				'QSG_wp_user' => new EE_WP_User_Field('QSG_wp_user', __('Question Group Creator ID', 'event_espresso'), FALSE ),
				'QSG_system'=>new EE_Integer_Field('QSG_system', __('Indicate IF this is a system group and if it is what system group it corresponds to.','event_espresso'), false, 0),
				'QSG_deleted'=>new EE_Trashed_Flag_Field('QSG_deleted', __('Flag indicating this question group was deleted','event_espresso'), false, false)
			)
		);

		$this->_model_relations = array(
			'Question'=>new EE_Has_Many_Relation(),
			'Event'=>new EE_HABTM_Relation('Event_Question_Group'),
			'Event_Question_Group'=>new EE_Has_Many_Relation(),
			'WP_User' => new EE_Belongs_To_Relation(),
		);

		//this model is generally available for reading
		$this->_cap_restriction_generators[ EEM_Base::caps_read ] = new EE_Restriction_Generator_Public();
		$this->_cap_restriction_generators[ EEM_Base::caps_read_admin ] = new EE_Restriction_Generator_Reg_Form('QSG_system');
		$this->_cap_restriction_generators[ EEM_Base::caps_edit ] = new EE_Restriction_Generator_Reg_Form('QSG_system');
		$this->_cap_restriction_generators[ EEM_Base::caps_delete ] = new EE_Restriction_Generator_Reg_Form('QSG_system');
		parent::__construct( $timezone );

	}
	/**
	 * searches the db for the question group with the latest question order and returns that value.
	 * @access public
	 * @return int
	 */
	public function get_latest_question_group_order() {
		$columns_to_select = array(
			'max_order' => array("MAX(QSG_order)","%d")
			);
		$max = $this->_get_all_wpdb_results(array(), ARRAY_A, $columns_to_select );
		return $max[0]['max_order'];
	}


}
// End of file EEM_Question_Group.model.php
// Location: /includes/models/EEM_Question_Group.model.php
