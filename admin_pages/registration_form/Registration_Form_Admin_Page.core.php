<?php
use EventEspresso\admin_pages\registration_form\RegistrationFormEditor;
use EventEspresso\admin_pages\registration_form\RegistrationFormEditorForm;
use EventEspresso\admin_pages\registration_form\RegistrationFormEditorFormDisplay;
use EventEspresso\core\libraries\form_sections\inputs\FormInputsLoader;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'NO direct script access allowed' );
}



/**
 *
 * Registration_Form_Admin_Page
 *
 * This contains the logic for setting up the Custom Forms related pages.  Any methods without phpdoc comments have inline docs with parent class.
 *
 * NOTE:  TODO: This is a straight conversion from the legacy 3.1 questions and question groups related pages.  It is NOT optimized and will need modification to fully use the new system (and also will need adjusted when Questions and Questions groups model is implemented)
 *
 * @package		Registration_Form_Admin_Page
 * @subpackage	includes/core/admin/Registration_Form_Admin_Page.core.php
 * @author		Darren Ethier
 *
 */
class Registration_Form_Admin_Page extends EE_Admin_Page {

	/**
	 * _question
	 * holds the specific question object for the question details screen
	 *
	 * @var EE_Question $_question
	 */
	protected $_question;

	/**
	 * _question_group
	 * holds the specific question group object for the question group details screen
	 *
	 * @var EE_Question_Group $_question_group
	 */
	protected $_question_group;

	/**
	 *_question_model EEM_Question model instance (for queries)
	 *
	 * @var EEM_Question $_question_model;
 */
	protected $_question_model;

	/**
	 * _question_group_model EEM_Question_group instance (for queries)
	 *
	 * @var EEM_Question_Group $_question_group_model
	 */
	protected $_question_group_model;



	/**
	 * 		@Constructor
	 *
	 * 		@param bool $routing indicate whether we want to just load the object and handle routing or just load the object.
	 * 		@access public
	 */
	public function __construct( $routing = TRUE ) {
		require_once( EE_MODELS . 'EEM_Question.model.php' );
		require_once( EE_MODELS . 'EEM_Question_Group.model.php' );
		$this->_question_model=  EEM_Question::instance();
		$this->_question_group_model=EEM_Question_Group::instance();
		parent::__construct( $routing );
	}



	/**
	 * @return EEM_Question
	 */
	public function question_model() {
		return $this->_question_model;
	}



	/**
	 * @return EEM_Question_Group
	 */
	public function question_group_model() {
		return $this->_question_group_model;
	}





	protected function _init_page_props() {
		$this->page_slug = REGISTRATION_FORM_PG_SLUG;
		$this->page_label = esc_html__('Registration Form', 'event_espresso');
		$this->_admin_base_url = REGISTRATION_FORM_ADMIN_URL;
		$this->_admin_base_path = REGISTRATION_FORM_ADMIN;
	}




	protected function _ajax_hooks() {}





	protected function _define_page_props() {
		$this->_admin_page_title = esc_html__('Registration Form', 'event_espresso');
		$this->_labels = array(
			'buttons' => array(
				'edit_question' => esc_html__('Edit Question', 'event_espresso')
			)
		);
	}



	/**
	 *_set_page_routes
	 */
	protected function _set_page_routes() {
		$qsg_id = ! empty( $this->_req_data['QSG_ID'] ) ? $this->_req_data['QSG_ID'] : 1;
		$this->_page_routes = array(

			'default' => array(
				'func' => 'form_sections_list_table',
				'capability' => 'ee_read_question_groups',
				'obj_id' => $qsg_id,
				'args' => array('edit'),
			),

			'add_edit_form_section' => array(
				'func' => 'add_edit_form_section',
				'capability' => 'ee_read_question_groups',
				'obj_id' => $qsg_id,
				'args' => array( 'edit' ),
			),

			'form_section_preview' => array(
				'func' => '_form_section_preview',
				'capability' => 'ee_read_question_groups',
				'obj_id' => $qsg_id,
				'args' => array( 'edit' ),
			),

			'insert_form_section' => array(
				'func'       => '_insert_or_update_form_section',
				'args'       => array( 'new_form_section' => true ),
				'capability' => 'ee_edit_question_groups',
				'noheader'   => true,
			),

			'update_form_section' => array(
				'func'       => '_insert_or_update_form_section',
				'args'       => array( 'new_form_section' => false ),
				'capability' => 'ee_edit_question_group',
				'obj_id'     => $qsg_id,
				'noheader'   => true,
			),
		);
	}





	protected function _set_page_config() {
		$this->_page_config = array(

			'default' => array(
				'nav' => array(
					'label' => esc_html__('Form Sections'),
					'order' => 10
				),
				//'list_table' => 'Registration_Form_Admin_List_Table',
				'metaboxes' => array(),
                'help_tabs' => array(
					// 'registration_form_questions_overview_help_tab' => array(
					// 	'title' => esc_html__('Questions Overview', 'event_espresso'),
					// 	'filename' => 'registration_form_questions_overview'
					// ),
					// 'registration_form_questions_overview_table_column_headings_help_tab' => array(
					// 	'title' => esc_html__('Questions Overview Table Column Headings', 'event_espresso'),
					// 	'filename' => 'registration_form_questions_overview_table_column_headings'
					// ),
					// 'registration_form_questions_overview_views_bulk_actions_search_help_tab' => array(
					// 	'title' => esc_html__('Question Overview Views & Bulk Actions & Search', 'event_espresso'),
					// 	'filename' => 'registration_form_questions_overview_views_bulk_actions_search'
					// )
				),
				// 'help_tour' => array( 'Registration_Form_Questions_Overview_Help_Tour'),
				'require_nonce' => false,
				// 'qtips' => array(
				// 	'EE_Registration_Form_Tips'
				// )
			),

			'edit_form_section' => array(
				'nav' => array(
					'label' => esc_html__('Add/Edit Form Section', 'event_espresso'),
					'order' => 15,
					'persistent' => false,
					'url' => isset( $this->_req_data['QSG_ID'] )
						? add_query_arg(
							array('QSG_ID' => $this->_req_data['QSG_ID'] ),
							$this->_current_page_view_url
						)
						: $this->_admin_base_url
				),
				'metaboxes' => array_merge( $this->_default_espresso_metaboxes, array('_publish_post_box' ) ),
				'help_tabs' => array(),
                'help_tour' => array(),
				'require_nonce' => false
			),
		);
	}


	protected function _add_screen_options() {
		//todo
	}

	protected function _add_screen_options_default() {
		$page_title = $this->_admin_page_title;
		$this->_admin_page_title = __('Forms', 'event_espresso');
		$this->_per_page_screen_option();
		$this->_admin_page_title = $page_title;
	}

	//none of the below group are currently used for Event Categories
	protected function _add_feature_pointers() {}
	public function load_scripts_styles() {
		wp_register_style( 'espresso_registration', REGISTRATION_FORM_ASSETS_URL . 'espresso_registration_form_admin.css', array( 'dashicons' ), EVENT_ESPRESSO_VERSION );
		wp_enqueue_style('espresso_registration');
	}
	public function admin_init() {}
	public function admin_notices() {}
	public function admin_footer_scripts() {}



	public function load_scripts_styles_default() {
		$this->load_scripts_styles_forms();
		wp_register_script(
			'espresso_registration_form_single',
			REGISTRATION_FORM_ASSETS_URL . 'espresso_registration_form_admin.js',
			array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ),
			EVENT_ESPRESSO_VERSION . time(),
			true
		);
		wp_enqueue_script( 'espresso_registration_form_single' );
	}




	public function load_scripts_styles_add_question() {
	}
	public function load_scripts_styles_edit_question() {
		$this->load_scripts_styles_forms();
		wp_register_script( 'espresso_registration_form_single', REGISTRATION_FORM_ASSETS_URL . 'espresso_registration_form_admin.js', array('jquery-ui-sortable'), EVENT_ESPRESSO_VERSION, TRUE );
		wp_enqueue_script( 'espresso_registration_form_single' );
	}




	public function recaptcha_info_help_tab() {
		$template = REGISTRATION_FORM_TEMPLATE_PATH . 'recaptcha_info_help_tab.template.php';
		EEH_Template::display_template($template, array());
	}





	public function load_scripts_styles_forms() {
		//styles
		wp_enqueue_style('espresso-ui-theme');
		//scripts
		wp_enqueue_script('ee_admin_js');
	}






	protected function _set_list_table_views_forms() {
		$this->_views = array(
			'all' => array(
				'slug' => 'all',
				'label' => __('View All Forms', 'event_espresso'),
				'count' => 0,
			)
		);

		if ( EE_Registry::instance()->CAP->current_user_can( 'ee_delete_questions', 'espresso_registration_form_trash_questions' ) ) {
			$this->_views['trash'] = array(
				'slug' => 'trash',
				'label' => __('Trash', 'event_espresso'),
				'count' => 0,
			);
		}
	}






	protected function _set_list_table_views_default() {
		$this->_views = array(
			'all' => array(
				'slug' => 'all',
				'label' => esc_html__('View All Questions', 'event_espresso'),
				'count' => 0,
//				'bulk_action' => array(
//					'trash_questions' => esc_html__('Trash', 'event_espresso'),
//					)
				)
		);

		if ( EE_Registry::instance()->CAP->current_user_can( 'ee_delete_questions', 'espresso_registration_form_trash_questions' ) ) {
			$this->_views['trash'] = array(
				'slug' => 'trash',
				'label' => esc_html__('Trash', 'event_espresso'),
				'count' => 0,
//				'bulk_action' => array(
//					'delete_questions' => esc_html__('Delete Permanently', 'event_espresso'),
//					'restore_questions' => esc_html__('Restore', 'event_espresso'),
				);
		}
	}

	/**
	 * This just previews the question groups tab that comes in caffeinated.
	 * @return string html
	 */
	//protected function _form_sections_preview() {
	//	$this->_admin_page_title = __('Form Sections (Preview)', 'event_espresso');
	//	$this->_template_args['preview_img'] = '<img src="' . REGISTRATION_FORM_ASSETS_URL . 'caf_reg_form_preview.jpg" alt="' . esc_attr__( 'Preview Question Groups Overview List Table screenshot', 'event_espresso' ) . '" />';
	//	$this->_template_args['preview_text'] = '<strong>'.__( 'Form Sections is a feature that is only available in the Caffeinated version of Event Espresso.  With the Form Sections feature you are able to create completely new registration forms that can be assigned to different events making it easier than ever to perfect your event registrant\'s experience.', 'event_espresso' ).'</strong>';
	//	$this->display_admin_caf_preview_page( 'question_groups_tab' );
	//}






	/**
	 * _edit_form
	 */
	protected function _edit_form() {
		do_action( 'AHEE_log', __FILE__, __FUNCTION__, '' );
		$reg_form_editor = new RegistrationFormEditor(
			$this,
			new RegistrationFormEditorFormDisplay(
				new RegistrationFormEditorForm(
					$this->_question_model
				)
			)
		);
		// tweak page title
		$this->_admin_page_title = $reg_form_editor->getAdminPageTitle();
		// set route and additional hidden fields
		$this->_set_add_edit_form_tags(
			$reg_form_editor->getRoute(),
			$reg_form_editor->getAdditionalHiddenFields()
		);
		$this->_set_publish_post_box_vars( 'id', $reg_form_editor->getQuestionGroupID() );
		$reg_form_editor->addMetaBoxes();
		$this->_template_args[ 'admin_page_content' ] = $reg_form_editor->getAdminPageContent();
		// the details template wrapper
		$this->display_admin_page_with_sidebar();
	}



	/**
	 * @return array
	 */
	public function getAvailableFormInputs() {
		$exclude = array(
			'credit_card',
			'credit_card_month',
			'credit_card_year',
			'cvv',
			'hidden',
			'fixed_hidden',
			'select_multi_model',
			//'submit',
		);
		return FormInputsLoader::get( $exclude );
	}

	protected function _insert_or_update_question_group( $new_question_group = true ) {
		//$reg_form_editor_form = new RegistrationFormEditorForm(
		//	$this->_question_model
		//);
		//$form = $reg_form_editor_form->rawForm( $this->getAvailableFormInputs() );
		unset( $_REQUEST['reg_form']['clone'] );
		unset( $_REQUEST['settings']['clone'] );
		$reg_form_input_list = explode( ',', sanitize_text_field( $_REQUEST['reg_form_input_list'] ) );
		\EEH_Debug_Tools::printr( $reg_form_input_list, '$reg_form_input_list', __FILE__, __LINE__ );
		foreach ( $reg_form_input_list as $reg_form_input ) {
			$reg_form_input = explode( '-', $reg_form_input );
			if ( isset( $reg_form_input[1], $_REQUEST['settings'][ $reg_form_input[1] ] ) ) {
				$input_type = $reg_form_input[0];
				$input_settings = $_REQUEST['settings'][ $reg_form_input[1] ];
				\EEH_Debug_Tools::printr( $input_type, '$input_type', __FILE__, __LINE__ );
				\EEH_Debug_Tools::printr( $input_settings, '$input_settings', __FILE__, __LINE__ );
				if ( isset( $_REQUEST['input_options'][ $reg_form_input[1] ] ) ) {
					$input_options = $_REQUEST['input_options'][ $reg_form_input[1] ];
					\EEH_Debug_Tools::printr( $input_options, '$input_options', __FILE__, __LINE__ );
				}
			}
		}

		//\EEH_Debug_Tools::printr( $_REQUEST['reg_form'], '$_REQUEST[reg_form]', __FILE__, __LINE__ );
		//\EEH_Debug_Tools::printr( $_REQUEST['settings'], '$_REQUEST[settings]', __FILE__, __LINE__ );
		//\EEH_Debug_Tools::printr( $form, '$form', __FILE__, __LINE__ );
		die();
	}



	/**
	 * @param bool|true $new_question
	 * @throws \EE_Error
	 */
	//protected function _insert_or_update_question( $new_question = TRUE) {
	//	do_action( 'AHEE_log', __FILE__, __FUNCTION__, '' );
	//	$set_column_values=$this->_set_column_values_for($this->_question_model);
	//	if($new_question){
	//		$ID=$this->_question_model->insert($set_column_values);
	//		$success = $ID ? true : false;
	//		$action_desc = 'added';
	//	}else{
	//		$ID=absint($this->_req_data['QST_ID']);
	//		$pk=$this->_question_model->primary_key_name();
	//		$wheres=array($pk=>$ID);
	//		unset($set_column_values[$pk]);
	//		$success= $this->_question_model->update($set_column_values,array($wheres));
	//		$action_desc='updated';
	//	}
	//
	//	if ($ID){
	//		//save the related options
	//		//trash removed options, save old ones
	//		//get list of all options
	//		/** @type EE_Question $question */
	//		$question=$this->_question_model->get_one_by_ID($ID);
	//		$options=$question->options();
	//		if(! empty($options)){
	//			foreach($options as $option_ID=>$option){
	//				$option_req_index=$this->_get_option_req_data_index($option_ID);
	//				if($option_req_index!==FALSE){
	//					$option->save($this->_req_data['question_options'][$option_req_index]);
	//				}else{
	//					//not found, remove it
	//					$option->delete();
	//				}
	//			}
	//		}
	//		//save new related options
	//		foreach($this->_req_data['question_options'] as $index=>$option_req_data){
	//			if( empty($option_req_data['QSO_ID'] ) && (  ( isset( $option_req_data['QSO_value'] ) && $option_req_data['QSO_value'] !== '' ) || ! empty( $option_req_data['QSO_desc'] ) ) ) {//no ID! save it!
	//				if( ! isset( $option_req_data['QSO_value'] ) || $option_req_data['QSO_value'] === ''  ){
	//					$option_req_data['QSO_value']=$option_req_data['QSO_desc'];
	//				}
	//				$new_option=EE_Question_Option::new_instance( array( 'QSO_value' => $option_req_data['QSO_value'], 'QSO_desc' => $option_req_data['QSO_desc'], 'QSO_order' => $option_req_data['QSO_order'], 'QST_ID' => $question->ID()));
	//				$new_option->save();
	//			}
	//		}
	//	}
	//	$query_args = array( 'action' => 'edit_question', 'QST_ID' => $ID );
	//	if ( $success !== FALSE ) {
	//		$msg = $new_question ? sprintf( __('The %s has been created', 'event_espresso'), $this->_question_model->item_name() ) : sprintf( __('The %s has been updated', 'event_espresso' ), $this->_question_model->item_name() );
	//		EE_Error::add_success( $msg );
	//	}
	//
	//	$this->_redirect_after_action( FALSE, '', $action_desc, $query_args, TRUE);
	//}
	//
	//
	//
	///**
	// * Upon saving a question, there should be an array of 'question_options'. This array is index numerically, but not by ID
	// * (this is done because new question options don't have an ID, but we may want to add multiple simultaneously).
	// * So, this function gets the index in that request data array called question_options. Returns FALSE if not found.
	// * @param int $ID of the question option to find
	// * @return int index in question_options array if successful, FALSE if unsuccessful
	// */
	//protected function _get_option_req_data_index($ID){
	//	$req_data_for_question_options=$this->_req_data['question_options'];
	//	foreach($req_data_for_question_options as $num=>$option_data){
	//		if(array_key_exists('QSO_ID',$option_data) && intval($option_data['QSO_ID'])==$ID){
	//			return $num;
	//		}
	//	}
	//	return FALSE;
	//}



	/**
	 * Extracts the question field's values from the POST request to update or insert them
	 *
	 * @param \EEM_Base $model
	 * @return array where each key is the name of a model's field/db column, and each value is its value.
	 */
//	protected function _set_column_values_for( EEM_Base $model ) {
//		do_action( 'AHEE_log', __FILE__, __FUNCTION__, '' );
//		$set_column_values = array();
//		//some initial checks for proper values.
//		//if QST_admin_only, then no matter what QST_required is we disable.
//		if ( ! empty( $this->_req_data[ 'QST_admin_only' ] ) ) {
//			$this->_req_data[ 'QST_required' ] = 0;
//		}
//		foreach ( $model->field_settings() as $fieldName => $settings ) {
//			// basically if QSG_identifier is empty or not set
//			if ( $fieldName == 'QSG_identifier'
//			     && ( isset( $this->_req_data[ 'QSG_identifier' ] )
//			          && empty( $this->_req_data[ 'QSG_identifier' ] ) )
//			) {
//				$QSG_name = isset( $this->_req_data[ 'QSG_name' ] ) ? $this->_req_data[ 'QSG_name' ] : '';
//				$set_column_values[ $fieldName ] = sanitize_title( $QSG_name ) . '-' . uniqid();
////				dd($set_column_values);
//			} //if the admin label is blank, use a slug version of the question text
//			else if ( $fieldName == 'QST_admin_label'
//			          && ( isset( $this->_req_data[ 'QST_admin_label' ] )
//			               && empty( $this->_req_data[ 'QST_admin_label' ] ) )
//			) {
//				$QST_text = isset( $this->_req_data[ 'QST_display_text' ] ) ? $this->_req_data[ 'QST_display_text' ]
//					: '';
//				$set_column_values[ $fieldName ] = sanitize_title( wp_trim_words( $QST_text, 10 ) );
//			} else if ( $fieldName == 'QST_admin_only' && ( ! isset( $this->_req_data[ 'QST_admin_only' ] ) ) ) {
//				$set_column_values[ $fieldName ] = 0;
//			} else if ( $fieldName == 'QST_max' ) {
//				$qst_system = EEM_Question::instance()->get_var(
//					array(
//						array(
//							'QST_ID' => isset( $this->_req_data[ 'QST_ID' ] ) ? $this->_req_data[ 'QST_ID' ] : 0
//						)
//					),
//					'QST_system'
//				);
//				$max_max = EEM_Question::instance()->absolute_max_for_system_question( $qst_system );
//				if ( empty( $this->_req_data[ 'QST_max' ] ) || $this->_req_data[ 'QST_max' ] > $max_max ) {
//					$set_column_values[ $fieldName ] = $max_max;
//				}
//			}
//			//only add a property to the array if it's not null (otherwise the model should just use the default value)
//			if (
//				! isset( $set_column_values[ $fieldName ] ) && isset( $this->_req_data[ $fieldName ] )
//			) {
//				$set_column_values[ $fieldName ] = $this->_req_data[ $fieldName ];
//			}
//		}
//		return $set_column_values;//validation fo this data to be performed by the model before insertion.
//	}



	/***************************************		REGISTRATION SETTINGS 		***************************************/





	protected function _reg_form_settings() {

		$this->_template_args['values'] = $this->_yes_no_values;

		$this->_template_args['use_captcha'] = isset( EE_Registry::instance()->CFG->registration->use_captcha ) ? EE_Registry::instance()->CFG->registration->use_captcha : FALSE;
		$this->_template_args['show_captcha_settings'] = $this->_template_args['use_captcha'] ? 'style="display:table-row;"': '';

		$this->_template_args['recaptcha_publickey'] = isset( EE_Registry::instance()->CFG->registration->recaptcha_publickey ) ? stripslashes( EE_Registry::instance()->CFG->registration->recaptcha_publickey ) : '';
		$this->_template_args['recaptcha_privatekey'] = isset( EE_Registry::instance()->CFG->registration->recaptcha_privatekey ) ? stripslashes( EE_Registry::instance()->CFG->registration->recaptcha_privatekey ) : '';
		$this->_template_args['recaptcha_width'] = isset( EE_Registry::instance()->CFG->registration->recaptcha_width ) ? absint( EE_Registry::instance()->CFG->registration->recaptcha_width ) : 500;

		$this->_template_args['recaptcha_theme_options'] = array(
				array('id'  => 'red','text'=> __('Red', 'event_espresso')),
				array('id'  => 'white','text'=> __('White', 'event_espresso')),
				array('id'  => 'blackglass','text'=> __('Blackglass', 'event_espresso')),
				array('id'  => 'clean','text'=> __('Clean', 'event_espresso'))
			);
		$this->_template_args['recaptcha_theme'] = isset( EE_Registry::instance()->CFG->registration->recaptcha_theme ) ? EE_Registry::instance()->CFG->registration->get_pretty( 'recaptcha_theme' ) : 'clean';

		$this->_template_args['recaptcha_language_options'] = array(
				array('id'  => 'en','text'=> __('English', 'event_espresso')),
				array('id'  => 'es','text'=> __('Spanish', 'event_espresso')),
				array('id'  => 'nl','text'=> __('Dutch', 'event_espresso')),
				array('id'  => 'fr','text'=> __('French', 'event_espresso')),
				array('id'  => 'de','text'=> __('German', 'event_espresso')),
				array('id'  => 'pt','text'=> __('Portuguese', 'event_espresso')),
				array('id'  => 'ru','text'=> __('Russian', 'event_espresso')),
				array('id'  => 'tr','text'=> __('Turkish', 'event_espresso'))
			);
		$this->_template_args['recaptcha_language'] = isset( EE_Registry::instance()->CFG->registration->recaptcha_language ) ? EE_Registry::instance()->CFG->registration->recaptcha_language : 'en';

		$this->_set_add_edit_form_tags( 'update_reg_form_settings' );
		$this->_set_publish_post_box_vars( NULL, FALSE, FALSE, NULL, FALSE );
		$this->_template_args['admin_page_content'] = EEH_Template::display_template( REGISTRATION_FORM_TEMPLATE_PATH . 'reg_form_settings.template.php', $this->_template_args, TRUE );
		$this->display_admin_page_with_sidebar();
	}




} //ends Registration_Form_Admin_Page class
