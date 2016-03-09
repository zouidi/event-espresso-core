<?php
namespace EventEspresso\admin_pages\registration_form;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class RegistrationFormEditorFormDisplay
 *
 * A class that adds the extra HTML output necessary for displaying a "Registration Form Editor" Form
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class RegistrationFormEditorFormDisplay {

	/**
	 * @var RegistrationFormEditorForm $reg_form_editor_form
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
	 * @param RegistrationFormEditorForm $RegistrationFormEditorFormInputForm
	 */
	public function __construct( RegistrationFormEditorForm $RegistrationFormEditorFormInputForm) {
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
		$this->form_input = $this->reg_form_editor_form->formInput( $form_input, $form_input_class_name );
		$this->input_has_options = $this->form_input instanceof \EE_Form_Input_With_Options_Base ? true :false;
		$html = \EEH_HTML::div( '', '', 'ee-reg-form-editor-form-new-input-form' );
		$html .= $this->reg_form_editor_form->formInputForm( $form_input )->get_html();
		$html .= \EEH_HTML::div( '', '', 'ee-new-form-input-settings-dv' );
		$html .= $this->getTabs( $form_input );
		if ( $this->input_has_options ) {
			// form input settings
			$html .= \EEH_HTML::div(
				'',
				'ee-reg-form-input-settings-tab-panel-0-clone-' . $form_input,
				'ee-reg-form-input-settings-tab-panel-dv'
			);
			$html .= $this->getInputOptionsFormHeader();
			$html .= $this->getInputOptionsForm( $form_input );
			//$html .= $this->reg_form_editor_form->getInputOptions( $form_input )->get_html();
			$html .= $this->getInputOptionsFormFooter();

			$html .= \EEH_HTML::divx(); // end 'ee-reg-form-input-settings-tab-panel-dv'
		}
		// form input settings
		$html .= \EEH_HTML::div(
			'',
			'ee-reg-form-input-settings-tab-panel-1-clone-' . $form_input,
			'ee-reg-form-input-settings-tab-panel-dv'
		);
		$html .= $this->reg_form_editor_form->getBasicSettings( $form_input )->get_html();
		$html .= \EEH_HTML::divx(); // end 'ee-reg-form-input-settings-tab-panel-dv'
		// validation settings
		$html .= \EEH_HTML::div(
			'',
			'ee-reg-form-input-settings-tab-panel-2-clone-' . $form_input,
			'ee-reg-form-input-settings-tab-panel-dv'
		);
		$html .= $this->reg_form_editor_form->getValidationSettings( $form_input, $form_input_class_name )->get_html();
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
					'#ee-reg-form-input-settings-tab-panel-' . $panel . '-clone-' . $form_input,
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
	 * @param $form_input
	 * @return string
	 */
	public function getInputOptionsForm( $form_input ) {
		$html = '';
		if ( $this->input_has_options ) {
			$options = $this->reg_form_editor_form->getInputOptions( $form_input, $this->form_input->options() );
			$order = 2;
			foreach( (array)$options as $key => $input ) {
				if ( $input instanceof \EE_Form_Input_Base ) {
					$html .= $this->getInputOptionsInputs( $key, $input );
					$order++;
				}
			}
			$order = floor( $order /2 );
			$hidden = $order > 2 ? true : false;
			$html .= $this->addEmptyInputOptionsInputs( $form_input, 0, $hidden );
			//if ( $order <= 2 ) {
			//	$html .= $this->addEmptyInputOptionsInputs( $form_input, 0, true );
			//}
			$html .= $this->getNewInputOptionOrderInput( $order );
		}
		return $html;
	}



	/**
	 * @param                     $key
	 * @param \EE_Form_Input_Base $input
	 * @param bool                $hidden
	 * @return array
	 */
	protected function getInputOptionsInputs( $key, \EE_Form_Input_Base $input, $hidden = false ) {
		$html = '';
		$hide_row = $hidden ? 'display:none;' : '';
		$new_row = $hidden ? 'ee-input-option-new-row' : 'ee-input-option-sortable-row';
		switch ( substr( $key, 0, 4 ) ) {
			case 'valu' :
				$html .= \EEH_HTML::tr( '', '', $new_row, $hide_row );
				$html .= \EEH_HTML::td();
				$html .= $input->get_html_for_input();
				$html .= \EEH_HTML::tdx();
				break;
			case 'desc' :
				$html .= \EEH_HTML::td();
				$html .= $input->get_html_for_input();
				$html .= \EEH_HTML::tdx();
				$html .= \EEH_HTML::td();
				$html .= \EEH_HTML::div(
					\EEH_HTML::span(
						'',
						'',
						'ee-input-option-add dashicons dashicons-plus-alt',
						'',
						'title="' . __( 'Click to Add a New Option', 'event_espresso' ) . '"'
					) . \EEH_HTML::span(
						'',
						'',
						'ee-input-option-delete dashicons dashicons-trash',
						'',
						'title="' . __( 'Click to Delete This Option', 'event_espresso' ) . '"'
					) . \EEH_HTML::span(
						'',
						'',
						'ee-input-option-sort dashicons dashicons-arrow-up-alt2',
						'',
						'title="' . __( 'Drag to Sort', 'event_espresso' ) . '"'
					) . \EEH_HTML::span(
						'',
						'',
						'ee-input-option-sort dashicons dashicons-arrow-down-alt2', //list-view
						'',
						'title="' . __( 'Drag to Sort', 'event_espresso' ) . '"'
					),
					'',
					'ee-input-option-controls'
				);
				$html .= \EEH_HTML::tdx();
				$html .= \EEH_HTML::trx();
				break;
		}
		return $html;
	}



	/**
	 * @param      $form_input
	 * @param int  $order
	 * @param bool $hidden
	 * @return string
	 */
	protected function addEmptyInputOptionsInputs( $form_input, $order = 0, $hidden = true ) {
		$order = $order !== 0 ? $order : 'order';
		$html = $this->getInputOptionsInputs(
			'value',
			new \EE_Text_Input(
				array(
					'html_name' => "input_options[clone][{$order}][value]",
					'html_id'   => "input-options-{$form_input}-clone-{$order}-value",
					'default'   => ''
				)
			),
			$hidden
		);
		$html .= $this->getInputOptionsInputs(
			'desc',
			new \EE_Text_Input(
				array(
					'html_name' => "input_options[clone][{$order}][desc]",
					'html_id'   => "input-options-{$form_input}-clone-{$order}-desc",
					'default'   => ''
				)
			),
			$hidden
		);
		return $html;
	}



	/**
	 * @param int $order
	 * @return string
	 */
	public function getNewInputOptionOrderInput( $order ) {
		$order_input = new \EE_Hidden_Input(
			array(
				'html_name' => 'ee_new_input_option_order',
				'html_id'   => 'ee_new_input_option_order',
				'default'   => $order,
			)
		);
		$html = \EEH_HTML::tr( '', '', 'ee_new_input_option_order', 'display:none;' );
		$html .= \EEH_HTML::td();
		$html .= $order_input->get_html_for_input();
		$html .= \EEH_HTML::tdx();
		$html .= \EEH_HTML::trx();
		return $html;
	}



	/**
	 * @return string
	 */
	public function getInputOptionsFormHeader() {
		$html = \EEH_HTML::table( '', '', 'input-options-table form-table' );
		$html .= \EEH_HTML::thead(
			\EEH_HTML::tr(
				\EEH_HTML::th( __( 'Value', 'event_espresso' ), '', 'option-value-header' ) .
				\EEH_HTML::th(
					__( 'Description (optional, only shown on registration form)', 'event_espresso' ),
					'',
					'option-desc-header'
				)
			)
		);
		$html .= \EEH_HTML::tbody( '', '', 'ee-input-options-table-body' );
		return $html;
	}





	/**
	 * @return string
	 */
	public function getInputOptionsFormFooter() {
		return \EEH_HTML::tbodyx() . \EEH_HTML::tablex( '', 'question-options-table' );
	}




}
// End of file RegistrationFormEditorForm.php
// Location: /RegistrationFormEditorForm.php