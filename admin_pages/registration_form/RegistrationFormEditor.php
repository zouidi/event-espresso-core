<?php
namespace EventEspresso\admin_pages\registration_form;

use EventEspresso\core\libraries\form_sections\inputs\FormInputsLoader;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class RegistrationFormEditor
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    admin_pages
 * @author        Brent Christensen
 * @since         4.10.0
 *
 */
class RegistrationFormEditor {

	/*
	 * the current form section being edited. If new form then $question_group will be null until saved
	 * @var \EE_Question_Group $question_group
	 */
	protected $question_group;

	/*
	 * @var \Registration_Form_Admin_Page $reg_form_admin_page
	 */
	protected $reg_form_admin_page;

	/*
	 * @var RegistrationFormEditorFormInputForm $input_form_generator
	 */
	protected $input_form_generator;

	/*
	 * @var $editor_form \EE_Form_Section_Proper
	 */
	protected $editor_form;



	/**
	 * RegistrationFormEditor constructor
	 *
	 * @param \Registration_Form_Admin_Page $Registration_Form_Admin_Page
	 * @param RegistrationFormEditorFormInputForm $RegistrationFormEditorFormInputForm
	 */
	public function __construct(
		\Registration_Form_Admin_Page $Registration_Form_Admin_Page,
		RegistrationFormEditorFormInputForm $RegistrationFormEditorFormInputForm
	) {
		// set reg admin page
		$this->reg_form_admin_page = $Registration_Form_Admin_Page;
		$this->input_form_generator = $RegistrationFormEditorFormInputForm;
		// get copy of EE_Request
		$request_data = $this->reg_form_admin_page->get_request_data();
		// are we editing an existing Question Group or creating a new one ?
		$QSG_ID = isset( $request_data[ 'QST_ID' ] ) && ! empty( $request_data[ 'QSG_ID' ] )
			? absint( $request_data[ 'QSG_ID' ] )
			: 0;
		// find question group if applicable
		if ( $QSG_ID ) {
			/** @var \EEM_Question_Group $question_group_model */
			$question_group_model = $this->reg_form_admin_page->question_group_model();
			$this->question_group = $question_group_model->get_one_by_ID( $QSG_ID );
		} else {
			$this->question_group = \EE_Question_Group::new_instance();
			$this->question_group->set_order_to_latest();
		}
		//sidebars
		add_meta_box(
			'espresso_reg_form_editor_form_inputs_meta_box',
			__( 'Form Inputs', 'event_espresso' ),
			array( $this, 'formInputsMetaBox' ),
			$this->reg_form_admin_page->get_wp_page_slug(),
			'side'
		);
	}



	/**
	 * tweak page title
	 *
	 * @return string
	 */
	public function getAdminPageTitle() {
		$page_title = ucwords( str_replace( '_', ' ', $this->reg_form_admin_page->get_req_action() ) );
		return $this->question_group->ID()
			? $page_title . ' # ' . $this->question_group->ID()
			: $page_title;
	}



	/**
	 * tells the admin page which question group we are editing
	 * @return string
	 */
	public function getAdditionalHiddenFields() {
		if ( $this->question_group->ID() ) {
			return array( 'QSG_ID' => array( 'type' => 'hidden', 'value' => $this->question_group->ID() ) );
		} else {
			return array();
		}
	}



	/**
	 * @return string
	 */
	public function getRoute() {
		return $this->question_group->ID() ? 'insert_question_group' : 'update_question_group';
	}



	/**
	 * @return string
	 */
	public function getQuestionGroupID() {
		return $this->question_group->ID();
	}



	/**
	 * @return \EE_Form_Section_Proper
	 */
	protected function editorForm() {
		return new \EE_Form_Section_Proper(
			array(
				'name'            => $this->question_group->html_name(),
				'html_id'         => $this->question_group->html_id(),
				'layout_strategy' => new \EE_Admin_Two_Column_Layout(),
				'subsections'     => array(),
			)
		);
	}

	/**
	 * @return string
	 */
	public function getAdminPageContent() {
		\EE_Registry::instance()->load_helper( 'EEH_HTML' );
		$html = \EEH_HTML::div( '', 'ee-reg-form-editor-form-main-meta-box', 'ee-reg-form-editor-form-dv postbox' );
			$html .= \EEH_HTML::div(
				'',
				'ee-reg-form-editor-form-inputs-wrapper-dv',
				'ee-reg-form-editor-form-inputs-wrapper-dv'
			);
				$html .= \EEH_HTML::ul( 'ee-reg-form-editor-active-form-inputs-ul', 'sortable' );
				// empty list for now
				$html .= \EEH_HTML::ulx();
				$html .= \EEH_HTML::div( '', '', 'ee-reg-form-editor-form-new-input-dv droppable' );
					$html .= \EEH_HTML::h2( 'drag and drop form inputs to add', '', 'ee-reg-form-editor-form-new-input-hdr' );
					$html .= $this->editorForm()->get_html();
				$html .= \EEH_HTML::divx();
			$html .= \EEH_HTML::divx();
		$html .= \EEH_HTML::divx();
		do_action( 'AHEE__EE_Admin_Page__reg_form_editor_form_sections_meta_box__after_content' );
		return $html;
	}



	public function formInputsMetaBox() {
		\EE_Registry::instance()->load_helper( 'EEH_HTML' );
		$html = \EEH_HTML::div(
			'',
			'ee-reg-form-editor-form-inputs-meta-box',
			'ee-reg-form-editor-form-inputs-dv infolinks'
		);
		$html .= \EEH_HTML::ul( '', 'ee-reg-form-editor-form-inputs-ul draggable' );
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
		foreach ( FormInputsLoader::get( $exclude ) as $form_input => $form_input_class_name ) {
			$html .= \EEH_HTML::li(
				'',
				'ee-reg-form-editor-form-input-li-' . $form_input,
				'ee-reg-form-editor-form-input-li'
			);
			$html .= \EEH_HTML::div(
				$this->formatInputName( $form_input_class_name ),
				'ee-reg-form-editor-form-input-' . $form_input,
				'ee-reg-form-editor-form-input draggable button',
				'',
				'data-form_input="ee-reg-form-editor-active-form-inputs-li-' . $form_input . '"'
			);
			$html .= \EEH_HTML::li(
				'',
				'ee-reg-form-editor-active-form-inputs-li-' . $form_input,
				'ee-reg-form-editor-active-form-inputs-li',
				'display:none;'
			);
			$html .= \EEH_HTML::div( '', '', 'ee-reg-form-editor-active-form-inputs-controls-dv' );
			$html .= \EEH_HTML::span(
				'',
				'',
				'ee-form-input-control-config ee-config-form-input dashicons dashicons-admin-generic',
				'',
				'title="' . __( 'Click to Edit Settings', 'event_espresso' ) . '"'
			);
			$html .= \EEH_HTML::span(
				'',
				'',
				'ee-form-input-control-delete ee-delete-form-input dashicons dashicons-trash',
				'',
				'title="' . __( 'Click to Delete', 'event_espresso' ) . '"'
			);
			$html .= \EEH_HTML::span(
				'',
				'',
				'ee-form-input-control-sort dashicons dashicons-list-view',
				'',
				'title="' . __( 'Drag to Sort', 'event_espresso' ) . '"'
			);
			$html .= \EEH_HTML::divx(); // end 'ee-reg-form-editor-active-form-inputs-controls-dv'
			$html .= $this->input_form_generator->formHTML( $form_input, $form_input_class_name );
			$html .= \EEH_HTML::lix(); // end 'ee-reg-form-editor-active-form-inputs-li'
			$html .= \EEH_HTML::lix(); // end 'ee-reg-form-editor-form-input-li'
		}
		$html .= \EEH_HTML::ulx();
		$html .= \EEH_HTML::divx();
		echo $html;
		do_action( 'AHEE__EE_Admin_Page__reg_form_editor_form_sections_meta_box__after_content' );
	}



	protected function formatInputName( $form_input_class_name ) {
		$form_input_class_name = trim(
			str_replace(
				array( '\EE_', '_Input', '_' ), // find
				array( '', '', ' ' ),           // replace
				$form_input_class_name
			)
		);
		switch( $form_input_class_name ) {
			case 'Admin File Uploader' :
				$form_input_class_name = 'File Uploader';
				break;
			case 'Checkbox Multi' :
				$form_input_class_name = 'Checkbox';
				break;
			case 'Country Select' :
				$form_input_class_name = 'Country';
				break;
			case 'State Select' :
				$form_input_class_name = 'State/Province';
				break;
			case 'Yes No' :
				$form_input_class_name = 'Yes or No';
				break;
		}
		return $form_input_class_name;
	}



}
// End of file RegistrationFormEditor.php
// Location: admin_pages/registration_form/RegistrationFormEditor.php