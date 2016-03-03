<?php
namespace EventEspresso\admin_pages\registration_form;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class RegistrationFormEditorFormDisplay
 *
 * No the name of this class is not some copy pasta mistake !!!
 * On the admin page where we edit the Registration Form,
 * we need forms for configuring the individual reg form inputs.
 * So this class displays the "Registration Form Editor" "Form Input" Forms
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class RegistrationFormEditorFormDisplay {

	/**
	 * @var RegistrationFormEditorFormInputForm $reg_form_editor_form
	 */
	protected $reg_form_editor_form;

	/**
	 * @var \EE_Form_Input_Base $form_input
	 */
	protected $form_input;

	/**
	 * true if the input type is an instance of \EE_Form_Input_With_Options_Base
	 *
	 * @var boolean $input_has_options
	 */
	protected $input_has_options;



	/**
	 * RegistrationFormEditorFormDisplay constructor.
	 *
	 * @param RegistrationFormEditorFormInputForm $RegistrationFormEditorFormInputForm
	 */
	public function __construct( RegistrationFormEditorFormInputForm $RegistrationFormEditorFormInputForm) {
		$this->reg_form_editor_form = $RegistrationFormEditorFormInputForm;
	}



	/**
	 * @param string $form_input
	 * @param string $form_input_class_name
	 * @return string
	 * @throws \EE_Error
	 */
	public function formHTML( $form_input, $form_input_class_name ) {
		if ( ! is_subclass_of( $form_input_class_name, 'EE_Form_Input_Base' ) ) {
			throw new \EE_Error(
				sprintf(
					__( 'The class "%1$s" needs to be a sub class of  EE_Form_Input_Base.', 'event_espresso' ),
					$form_input_class_name
				)
			);
		}
		$this->form_input = $this->reg_form_editor_form->formInput( $form_input_class_name );
		$this->input_has_options = $this->form_input instanceof \EE_Form_Input_With_Options_Base ? true :false;
		$html = \EEH_HTML::div( '', '', 'ee-reg-form-editor-form-new-input-form' );
		$html .= $this->formInputForm( $form_input )->get_html();
		$html .= \EEH_HTML::div( '', '', 'ee-new-form-input-settings-dv' );
		$html .= $this->getTabs( $form_input );
		if ( $this->input_has_options ) {
			// form input settings
			$html .= \EEH_HTML::div(
				'',
				'ee-reg-form-input-settings-tab-panel-0-' . $form_input . '_clone',
				'ee-reg-form-input-settings-tab-panel-dv'
			);
			$option_subsections = array_merge(
				$this->getInputOptionFormHeader(),
				$this->getInputOptions( $form_input )->get_html(),
				$this->getInputOptionFormFooter()
			);
			$html .= \EEH_HTML::divx(); // end 'ee-reg-form-input-settings-tab-panel-dv'
		}
		// form input settings
		$html .= \EEH_HTML::div(
			'',
			'ee-reg-form-input-settings-tab-panel-1-' . $form_input . '_clone',
			'ee-reg-form-input-settings-tab-panel-dv'
		);
		$html .= $this->getBasicSettings( $form_input )->get_html();
		$html .= \EEH_HTML::divx(); // end 'ee-reg-form-input-settings-tab-panel-dv'
		// validation settings
		$html .= \EEH_HTML::div(
			'',
			'ee-reg-form-input-settings-tab-panel-2-' . $form_input . '_clone',
			'ee-reg-form-input-settings-tab-panel-dv'
		);
		$html .= $this->getValidationSettings( $form_input, $form_input_class_name )->get_html();
		$html .= \EEH_HTML::divx(); // end 'ee-reg-form-input-settings-tab-panel-dv'
		$html .= \EEH_HTML::divx(); // end 'ee-new-form-input-settings-dv'
		$html .= \EEH_HTML::divx(); // end 'ee-reg-form-editor-form-new-input-form'
		return $html;
	}



	/**
	 * @param  string $form_input
	 * @return string
	 */
	public function getTabs( $form_input ) {
		$tabs = array(
			0 => 'Input Options',
			1 => 'Basic Settings',
			2 => 'Validation Rules',
		);
		if ( ! $this->input_has_options ) {
			unset( $tabs[0] );
		}
		$html = \EEH_HTML::ul( '', 'ee-reg-form-input-settings-tab-ul');
		$order = 1;
		foreach ( $tabs as $panel => $tab_label ) {
			$active = $order == 1 ? ' ee-reg-form-input-settings-tab-active' : '';
			$html .= \EEH_HTML::li(
				\EEH_HTML::link(
					'#ee-reg-form-input-settings-tab-panel-' . $panel . '-' . $form_input . '_clone',
					$tab_label,
					'',
					'',
					'ee-reg-form-input-settings-tab-js ee-reg-form-input-settings-tab-li' . $active
				)
			);
			$order++;
		}
		$html .= \EEH_HTML::ulx();
		return $html;
	}







	/**
	 * @return array
	 */
	public function getInputOptionFormHeader() {
		return array(
			'table_header' => new \EE_Form_Section_HTML(
				\EEH_HTML::table( '', '', 'question-options-table' ) .
				\EEH_HTML::thead(
					\EEH_HTML::tr(
						\EEH_HTML::th( __( 'Value', 'event_espresso' ), '', 'option-value-header' ) .
						\EEH_HTML::th(
							__( 'Description (optional, only shown on registration form)', 'event_espresso' ),
							'',
							'option-desc-header'
						)
					)
				) .
				\EEH_HTML::tbody()
			)
		);
	}





	/**
	 * @return array
	 */
	public function getInputOptionFormFooter() {
		return array(
			'table_end' => new \EE_Form_Section_HTML(
				\EEH_HTML::tbodyx() .
				\EEH_HTML::tablex( '', 'question-options-table' )
			)
		);
	}




}
// End of file RegistrationFormEditorFormInputForm.php
// Location: /RegistrationFormEditorFormInputForm.php