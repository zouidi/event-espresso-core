<?php
namespace EventEspresso\admin_pages\registration_form;

use EventEspresso\core\libraries\form_sections\strategies\validation\ValidationStrategiesLoader;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class RegistrationFormEditorFormInputForm
 *
 * No the name of this class is not some copy pasta mistake !!!
 * On the admin page where we edit the Registration Form,
 * we need forms for configuring the individual reg form inputs.
 * So this class generates the "Registration Form Editor" "Form Input" Forms
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class RegistrationFormEditorFormInputForm {

	/**
	 * @var \EE_Form_Input_Base $form_input
	 */
	protected $form_input;

	/**
	 * form fields for EEM_Question
	 *
	 * @var \EE_Model_Field_Base[] $form_input_config_fields
	 */
	protected $form_input_config_fields;

	/**
	 * true if the input type is an instance of \EE_Form_Input_With_Options_Base
	 *
	 * @var boolean $input_has_options
	 */
	protected $input_has_options;



	/**
	 * RegistrationFormEditorFormInputForm constructor.
	 *
	 * @param \EEM_Question $EEM_Question
	 */
	public function __construct( \EEM_Question $EEM_Question) {
		$this->form_input_config_fields = $EEM_Question->field_settings();
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
		$this->form_input = $this->formInput( $form_input_class_name );
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
			$html .= $this->getInputOptions( $form_input )->get_html();
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
	 * @param string $form_input
	 * @return \EE_Form_Section_Proper (
	 */
	public function formInputForm( $form_input ) {
		return new \EE_Form_Section_Proper(
			array(
				'name'            => "{$form_input}_form_clone",
				'html_id'         => "{$form_input}-form_clone",
				'html_class'      => "ee-new-form-input-dv",
				'layout_strategy' => new \EE_Div_Per_Section_Layout(),
				'subsections'     => array(
					$form_input => $this->form_input,
				),
			)
		);
	}



	/**
	 * @param string $form_input_class_name
	 * @return \EE_Form_Input_Base
	 */
	public function formInput( $form_input_class_name ) {
		if ( is_subclass_of( $form_input_class_name, 'EE_Form_Input_With_Options_Base' ) ) {
			return new $form_input_class_name( array( '' => __( ' - please add options via the settings - ', 'event_espresso' ) ) );
		}
		return new $form_input_class_name();
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
	 * @param string $form_input
	 * @return \EE_Form_Section_Proper
	 * @throws \EE_Error
	 */
	public function getInputOptions( $form_input ) {
		if ( ! $this->form_input instanceof \EE_Form_Input_With_Options_Base ) {
			throw new \EE_Error(
				sprintf(
					__(
						'The class "%1$s" needs to be a sub class of EE_Form_Input_With_Options_Base to have options.',
						'event_espresso'
					),
					get_class( $this->form_input )
				)
			);
		}
		$options = $this->form_input->options();
		return new \EE_Form_Section_Proper(
			array(
				'name'            => "{$form_input}_input_options_clone",
				'html_id'         => "{$form_input}-input_options_clone",
				'layout_strategy' => new \EE_Div_Per_Section_Layout(),
				'subsections'     => array(
					new \EE_Form_Section_HTML( print_r( $options, true ))
				),
			)
		);
	}



	/**
	 * @param string $form_input
	 * @return \EE_Form_Section_Proper
	 */
	public function getBasicSettings( $form_input ) {
		$subsections = array();
		foreach ( $this->form_input_config_fields as $field_name => $form_input_config_field ) {
			$config_input = $this->getBasicConfigInput( $form_input, $field_name );
			if ( $config_input instanceof \EE_Form_Input_Base ) {
				$input_name = $config_input->html_label_text();
				$input_name = ! empty( $input_name )
					? $input_name
					: str_replace( 'QST_', '', $form_input_config_field->get_name() );
				$subsections[ $input_name ] = $config_input;
			}
		}
		return new \EE_Form_Section_Proper(
			array(
				'name'            => "{$form_input}-settings_clone",
				'html_id'         => "{$form_input}-settings_clone",
				'layout_strategy' => new \EE_Admin_Two_Column_Layout(),
				'subsections'     => $subsections,
			)
		);
	}



	/**
	 * @param string $form_input
	 * @param string $field_name
	 * @return string
	 */
	protected function getBasicConfigInput( $form_input, $field_name ) {
		// what kind of field is it ?
		switch ( $field_name ) {

			case 'QST_display_text' :
				return new \EE_Text_Input(
					array(
						'html_label_text' => __( 'Label Text', 'event_espresso' ),
						'html_class' => 'ee-reg-form-label-text-js',
						//'html_other_attributes' => 'data-target="'.$form_input.'-form_clone-'.$form_input.'"',
						'html_other_attributes' => 'data-target="'.$form_input.'_form_clone['.$form_input.']"',
					)
				);

			case 'QST_desc' :
				return new \EE_Text_Area_Input(
					array(
						'html_label_text' => __( 'Description', 'event_espresso' ),
					)
				);

			case 'QST_html_class' :
				return new \EE_Text_Input(
					array(
						'html_label_text' => __( 'Input CSS Classes', 'event_espresso' ),
					)
				);

			case 'QST_html_label_class' :
				return new \EE_Text_Input(
					array(
						'html_label_text' => __( 'Label CSS Classes', 'event_espresso' ),
					)
				);

		}
		return null;
	}



	/**
	 * @param string $form_input
	 * @param string $form_input_class_name
	 * @return \EE_Form_Section_Proper
	 */
	public function getValidationSettings( $form_input, $form_input_class_name ) {
		//$subsections = array();
		$subsections = $this->getValidationStrategies( $form_input, $form_input_class_name );
		//$validation_field = $this->form_input_config_fields['QST_validation'];
		//$validation_rules = $this->getValidationStrategies( $form_input_class_name );
		//$config_input = $this->getValidationConfigInput( $form_input_class_name );
		//if ( $config_input instanceof \EE_Form_Input_Base ) {
		//	$subsections[ __( 'Validation Rules', 'event_espresso' ) ] = $config_input;
		//}
		return new \EE_Form_Section_Proper(
			array(
				'name'            => "{$form_input}-settings_clone",
				'html_id'         => "{$form_input}-settings_clone",
				'layout_strategy' => new \EE_Admin_Two_Column_Layout(),
				'subsections'     => $subsections,
			)
		);
	}



	/**
	 * @param string $form_input
	 * @param string $form_input_class_name
	 * @return array
	 */
	protected function getValidationStrategies( $form_input, $form_input_class_name ) {
		// get validations strategies but only include those that are optional
		$validation_strategies = ValidationStrategiesLoader::get(
			/** @var \EE_Form_Input_Base $form_input_class_name */
			$form_input_class_name::optional_validation_strategies(),
			true
		);
		$subsections = array();
		foreach ( $validation_strategies as $validation_key => $validation_strategy ) {
			//\EEH_Debug_Tools::printr( $key, '$key', __FILE__, __LINE__ );
			//\EEH_Debug_Tools::printr( $validation_strategy, '$validation_strategy', __FILE__, __LINE__ );
			/** @var \EE_Validation_Strategy_Base $validation_strategy */
			//\EEH_Debug_Tools::printr( $validation_strategy::generally_applicable(), '$validation_strategy::generally_applicable()', __FILE__, __LINE__ );
			unset( $validation_strategies[ $validation_key ] );
			if ( ! $validation_strategy::generally_applicable() ) {
				continue;
			}
			$validation_strategies[ ucwords( str_replace( '_', ' ', $validation_key ) ) ] = $validation_strategy;
		}
		$subsections[ $form_input . '_validation_strategies' ] = $this->getValidationStrategiesInput(
			$validation_strategies
		);
		$subsections[ $form_input . '_validation_message' ] = $this->getFailedValidationMessage();
		// convert underscores in labels to spaces
		//$validation_strategies = array_map(
		//	function( $value ) {
		//		return str_replace( '_', ' ', $value );
		//	},
		//	$validation_strategies
		//);
		//return array_flip( $validation_strategies );
		return $subsections;
	}



	/**
	 * @param array $validation_strategies
	 * @return \EE_Checkbox_Multi_Input
	 */
	protected function getValidationStrategiesInput( array $validation_strategies ) {
		return new \EE_Checkbox_Multi_Input(
			array_flip( $validation_strategies ),
			array(
				'html_label_text' =>  __( 'Apply Validation Rules', 'event_espresso' ),
			)
		);
	}



	/**
	 * @return \EE_Text_Input
	 */
	protected function getFailedValidationMessage() {
		return new \EE_Text_Area_Input(
			array(
				'html_label_text' =>  __( 'Failed Validation Message', 'event_espresso' ),
			)
		);
	}



	//protected function processValidationLabels( $value ) {
	//	return str_replace( '_', ' ', $value );
	//}

//'QST_ID' => new EE_Primary_Key_Int_Field( 'QST_ID', __( 'Question ID', 'event_espresso' ) ),
//'QSG_ID' => new EE_Integer_Field( 'QSG_ID', __( 'The Question Group that this belongs to.', 'event_espresso' ), true, null ),
//'QST_admin_label' => new EE_Plain_Text_Field( 'QST_admin_label', __( 'Question Label (admin-only)', 'event_espresso' ), true, '' ),
//'QST_display_text' => new EE_Full_HTML_Field( 'QST_display_text', __( 'Question Text', 'event_espresso' ), true, '' ),
//'QST_identifier' => new EE_Plain_Text_Field( 'QST_identifier', __( 'Internal string ID for question', 'event_espresso' ), TRUE, NULL ),
//'QST_system' => new EE_Plain_Text_Field( 'QST_system', __( 'System Question type ID', 'event_espresso' ), TRUE, NULL ),
//'QST_type' => new EE_Enum_Text_Field( 'QST_type', __( 'Question Type', 'event_espresso' ), false, 'TEXT', $this->_allowed_question_types ),
//'QST_desc' => new EE_Full_HTML_Field( 'QST_desc', __( 'Description of Question', 'event_espresso' ), true, '' ),
//'QST_html_name' => new EE_Plain_Text_Field( 'QST_html_name', __( 'HTML name property', 'event_espresso' ), true, null ),
//'QST_html_id' => new EE_Plain_Text_Field( 'QST_html_id', __( 'HTML CSS "id" property', 'event_espresso' ), true, null ),
//'QST_html_class' => new EE_Plain_Text_Field( 'QST_html_class', __( 'HTML CSS "class" property', 'event_espresso' ), true, null ),
//'QST_html_label_id' => new EE_Plain_Text_Field( 'QST_html_label_id', __( "HTML CSS \"id\" property for the Question's label", 'event_espresso' ), true, null ),
//'QST_html_label_class' => new EE_Plain_Text_Field( 'QST_html_label_class', __( "HTML CSS \"class\" property for the Question's label", 'event_espresso' ), true, null ),
//'QST_default_value' => new EE_Plain_Text_Field( 'QST_default_value', __( 'Default value for input', 'event_espresso' ), true, null ),
//'QST_validation' => new EE_Full_HTML_Field( 'QST_validation', __( 'List of validations to be applied to Question plus any custom validation messages', 'event_espresso' ), false, false ),
//'QST_order' => new EE_Integer_Field( 'QST_order', __( 'Question Order', 'event_espresso' ), false, 0 ),
//'QST_admin_only' => new EE_Boolean_Field( 'QST_admin_only', __( 'Admin-Only Question?', 'event_espresso' ), false, false ),
//'QST_max' => new EE_Infinite_Integer_Field( 'QST_max', __( 'Max Size', 'event_espresso' ), false, EE_INF ),
//'QST_wp_user' => new EE_WP_User_Field( 'QST_wp_user', __( 'Question Creator ID', 'event_espresso' ), false ),
//'QST_deleted' => new EE_Trashed_Flag_Field( 'QST_deleted', __( 'Flag Indicating question was deleted', 'event_espresso' ), false, false )
}
// End of file RegistrationFormEditorFormInputForm.php
// Location: /RegistrationFormEditorFormInputForm.php