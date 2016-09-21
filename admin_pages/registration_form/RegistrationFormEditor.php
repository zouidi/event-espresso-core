<?php
namespace EventEspresso\admin_pages\registration_form;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * RegistrationFormEditor
 *
 * Class for controlling what appears on the Registration Form Editor Admin page
 * when editing a top level Form Section. Contains all callbacks for metaboxes.
 *
 * @package       Event Espresso
 * @subpackage    admin_pages
 * @author        Brent Christensen
 * @since         4.10.0
 *
 */
class RegistrationFormEditor {

	/*
	 * array of form input classes that exist on the system
	 * @var array $question_group
	 */
	protected $available_form_inputs;

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
	 * @var RegistrationFormEditorFormDisplay $input_form_generator
	 */
	protected $input_form_generator;

	/*
	 * @var $editor_form \EE_Form_Section_Proper
	 */
	protected $editor_form;



	/**
	 * RegistrationFormEditor constructor
	 *
	 * @param \Registration_Form_Admin_Page     $Registration_Form_Admin_Page
	 * @param RegistrationFormEditorFormDisplay $RegistrationFormEditorFormInputForm
	 * @param \EE_Question_Group                $question_group
	 * @throws \EE_Error
	 */
	public function __construct(
		\Registration_Form_Admin_Page $Registration_Form_Admin_Page,
		RegistrationFormEditorFormDisplay $RegistrationFormEditorFormInputForm,
		\EE_Question_Group $question_group
	) {
		// set reg admin page
		$this->reg_form_admin_page = $Registration_Form_Admin_Page;
		$this->input_form_generator = $RegistrationFormEditorFormInputForm;
		$this->available_form_inputs = $this->reg_form_admin_page->getAvailableFormInputs();
		$this->question_group = $question_group;
	}



	/**
	 * tweak page title
	 *
	 * @return string
	 * @throws \EE_Error
	 */
	public function getAdminPageTitle() {
		$page_title = ucwords( str_replace( '_', ' ', $this->reg_form_admin_page->get_req_action() ) );
		return $this->question_group->ID()
			? $page_title . ' # ' . $this->question_group->ID()
			: $page_title;
	}



	/**
	 * tells the admin page which question group we are editing
	 *
	 * @return string
	 * @throws \EE_Error
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
	 * @throws \EE_Error
	 */
	public function getRoute() {
		return $this->question_group->ID() ? 'update_form_section' : 'insert_form_section';
	}



	/**
	 * @return string
	 * @throws \EE_Error
	 */
	public function getQuestionGroupID() {
		return $this->question_group->ID();
	}



	/**
	 * getAdminPageContent - HTML for main meta box
	 *
	 * @return string
	 * @throws \EE_Error
	 */
	public function getAdminPageContent() {
		\EE_Registry::instance()->load_helper( 'EEH_HTML' );
		$html = \EEH_HTML::div( '', 'ee-reg-form-editor-form-main-meta-box', 'ee-reg-form-editor-form-dv postbox' );
			$html .= \EEH_HTML::div(
				'',
				'ee-reg-form-editor-form-inputs-wrapper-dv',
				'ee-reg-form-editor-form-inputs-wrapper-dv'
			);
				$html .= \EEH_HTML::ul( 'ee-reg-form-editor-active-form-ul', 'sortable' );
		// foreach ( $this->available_form_inputs as $form_input => $form_input_class_name ) {
		// 	\EEH_Debug_Tools::printr( $form_input, '$form_input', __FILE__, __LINE__ );
		// 	\EEH_Debug_Tools::printr( $form_input_class_name, '$form_input_class_name', __FILE__, __LINE__ );
		// }
				if ( $this->question_group->ID() ) {
					foreach ( $this->question_group->questions() as $question ) {
						$html .= $this->formatInputListItem(
							$question->identifier(),
							$question->get_form_input_class_name(),
							true,
							$question
						);
					}
				}
				$html .= \EEH_HTML::ulx();
				$html .= \EEH_HTML::div( '', '', 'ee-reg-form-editor-form-new-input-dv droppable' );
				$html .= \EEH_HTML::h2( 'drag and drop form inputs to add', '', 'ee-reg-form-editor-form-new-input-hdr' );
				$html .= \EEH_HTML::divx();
			$html .= \EEH_HTML::divx();
		$html .= \EEH_HTML::divx();
		do_action( 'AHEE__EE_Admin_Page__reg_form_editor_form_sections_meta_box__after_content' );
		$input_list = new \EE_Hidden_Input(
			array(
				'html_id'   => 'reg_form-input_list',
				'html_name' => 'reg_form_input_list',
			)
		);
		$html .= $input_list->get_html_for_input();
		return $html;
	}



	/**
	 * formLayoutMetaBox - HTML for Form Inputs meta box
	 *
	 * @return void
	 */
	public function formLayoutMetaBox() {
		$html = \EEH_HTML::div(
			'',
			'ee-reg-form-editor-form-layout-meta-box',
			'ee-reg-form-editor-form-layout-dv infolinks'
		);
		$html .= \EEH_HTML::ul( '', 'ee-reg-form-editor-form-layout-ul draggable' );
		$html .= \EEH_HTML::li(
			'',
			'ee-reg-form-editor-form-layout-li-html',
			'ee-reg-form-editor-form-layout-li'
		);
		$html .= \EEH_HTML::div(
			'HTML',
			'ee-reg-form-editor-form-layout-html',
			'ee-reg-form-editor-form-layout draggable button',
			'',
			'data-form_input="ee-reg-form-editor-active-form-layout-li-html"'
		);
		$html .= \EEH_HTML::lix(); // end 'ee-reg-form-editor-form-layout-li'
		$html .= \EEH_HTML::ulx();
		$html .= \EEH_HTML::divx();
		echo $html;
		do_action( 'AHEE__EE_Admin_Page__reg_form_editor_form_layout_meta_box__after_content' );
	}



	/**
	 * addMetaBoxes
	 */
	public function addMetaBoxes() {
		\EE_Registry::instance()->load_helper( 'EEH_HTML' );
		//sidebars
		add_meta_box(
			'espresso_reg_form_editor_form_layout_meta_box',
			__( 'Form Layout', 'event_espresso' ),
			array( $this, 'formLayoutMetaBox' ),
			$this->reg_form_admin_page->get_wp_page_slug(),
			'side'
		);
		add_meta_box(
			'espresso_reg_form_editor_form_inputs_meta_box',
			__( 'Form Inputs', 'event_espresso' ),
			array( $this, 'formInputsMetaBox' ),
			$this->reg_form_admin_page->get_wp_page_slug(),
			'side'
		);
	}



	/**
	 * formInputsMetaBox - HTML for Form Inputs meta box
	 *
	 * @return void
	 * @throws \EE_Error
	 */
	public function formInputsMetaBox() {
		$html = \EEH_HTML::div(
			'',
			'ee-reg-form-editor-form-inputs-meta-box',
			'ee-reg-form-editor-form-inputs-dv infolinks'
		);
		$html .= \EEH_HTML::ul( '', 'ee-reg-form-editor-form-inputs-ul draggable' );
		foreach ( $this->available_form_inputs as $form_input => $form_input_class_name ) {
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
				'data-form_input="ee-reg-form-editor-active-form-li-' . $form_input . '"'
			);
			$html .= $this->formatInputListItem( $form_input, $form_input_class_name );
			$html .= \EEH_HTML::lix(); // end 'ee-reg-form-editor-form-input-li'
		}
		$html .= \EEH_HTML::ulx();
		$html .= \EEH_HTML::divx();
		echo $html;
		do_action( 'AHEE__EE_Admin_Page__reg_form_editor_form_sections_meta_box__after_content' );
	}



	/**
	 * formatInputListItem
	 *
	 * @param string       $form_input
	 * @param string       $form_input_class_name
	 * @param bool         $display
	 * @param \EE_Question $question
	 * @return string
	 * @throws \EE_Error
	 */
	protected function formatInputListItem(
		$form_input,
		$form_input_class_name,
		$display = false,
		\EE_Question $question = null
	) {
		$html = \EEH_HTML::li(
			'',
			'ee-reg-form-editor-active-form-li-' . $form_input,
			'ee-reg-form-editor-active-form-li',
			! $display ? 'display:none;' : ''
		);
		$html .= \EEH_HTML::div( '', '', 'ee-reg-form-editor-active-form-controls-dv' );
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
			'ee-form-input-control-sort dashicons dashicons-arrow-up-alt2',
			'',
			'title="' . __( 'Drag to Sort', 'event_espresso' ) . '"'
		);
		$html .= \EEH_HTML::span(
			'',
			'',
			'ee-form-input-control-sort dashicons dashicons-arrow-down-alt2', //list-view
			'',
			'title="' . __( 'Drag to Sort', 'event_espresso' ) . '"'
		);
		$html .= \EEH_HTML::divx(); // end 'ee-reg-form-editor-active-form-controls-dv'
		$html .= $this->input_form_generator->formHTML( $form_input, $form_input_class_name, $question );
		$html .= \EEH_HTML::lix(); // end 'ee-reg-form-editor-active-form-li'
		return $html;
	}



	/**
	 * formatInputName - changes class names to something more friendly
	 *
	 * @param $form_input_class_name
	 * @return string
	 */
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