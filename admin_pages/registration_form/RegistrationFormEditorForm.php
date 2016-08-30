<?php
namespace EventEspresso\admin_pages\registration_form;

use EventEspresso\core\libraries\form_sections\strategies\validation\ValidationStrategiesLoader;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class RegistrationFormEditorForm
 *
 * No, the name of this class is not some copy pasta mistake !!!
 * On the admin page where we edit the Registration Form,
 * we need a form for configuring the individual reg form inputs.
 * So this class generates the "Registration Form Editor" Form
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class RegistrationFormEditorForm {

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
	 * RegistrationFormEditorForm constructor.
	 *
	 * @param \EEM_Question $EEM_Question
	 */
	public function __construct( \EEM_Question $EEM_Question) {
		$this->form_input_config_fields = $EEM_Question->field_settings();
	}



	/**
	 * @param array $available_form_inputs
	 * @return \EE_Form_Section_Proper
	 */
	public function rawForm( array $available_form_inputs ) {
		$subsections = array();
		foreach ( $available_form_inputs as $form_input => $form_input_class_name ) {
			$subsections[ $form_input ] = $this->rawFormInput( $form_input, $form_input_class_name );
		}
		return new \EE_Form_Section_Proper(
			array(
				'name'            => "reg_form",
				'layout_strategy' => new \EE_Div_Per_Section_Layout(),
				'subsections'     => $subsections
			)
		);
	}



	/**
	 * @param string $form_input
	 * @param string $form_input_class_name
	 * @return string
	 * @throws \EE_Error
	 */
	public function rawFormInput( $form_input, $form_input_class_name ) {
		if ( ! is_subclass_of( $form_input_class_name, 'EE_Form_Input_Base' ) ) {
			throw new \EE_Error(
				sprintf(
					__( 'The class "%1$s" needs to be a sub class of  EE_Form_Input_Base.', 'event_espresso' ),
					$form_input_class_name
				)
			);
		}
		$this->form_input = $this->formInput( $form_input, $form_input_class_name );
		$this->input_has_options = $this->form_input instanceof \EE_Form_Input_With_Options_Base ? true :false;
		if ( $this->input_has_options ) {
			$subsections = $this->getInputOptions( $form_input, $this->form_input->options() );
		} else {
			$subsections = array();
		}
		$subsections = array_merge(
			$subsections,
			$this->getBasicSettingsSubsections( $form_input ),
			$this->getValidationStrategies( $form_input, $form_input_class_name )
		);
		return new \EE_Form_Section_Proper(
			array(
				'name'            => "reg_form[clone]",
				'html_id'         => "reg-form-clone",
				'layout_strategy' => new \EE_Div_Per_Section_Layout(),
				'subsections'     => $subsections
			)
		);
	}



	/**
	 * @param string $form_input
	 * @return \EE_Form_Section_Proper (
	 */
	public function formInputForm( $form_input ) {
		return new \EE_Form_Section_Proper(
			array(
				'name'            => "reg_form[clone]",
				'html_id'         => "reg-form-clone",
				'html_class'      => "ee-new-form-input-dv",
				'layout_strategy' => new \EE_Div_Per_Section_Layout(),
				'subsections'     => array(
					$form_input     => $this->form_input,
				),
			)
		);
	}



	/**
	 * @param string $form_input
	 * @param string $form_input_class_name
	 * @param array  $options
	 * @return \EE_Form_Input_Base
	 */
	public function formInput( $form_input, $form_input_class_name, $options = array() ) {
		if ( is_subclass_of( $form_input_class_name, 'EE_Form_Input_With_Options_Base' ) ) {
			$options = ! empty( $options )
				? $options
				: array(
					'option 1' => 'option 1 description',
					'option 2' => 'option 2 description',
				);
			$this->form_input = new $form_input_class_name(
				$options,
				array(
					'name' => $form_input,
				)
			);
		} else {
			$this->form_input = new $form_input_class_name(
				array(
					'name' => $form_input,
				)
			);
		}

		return $this->form_input;
	}





	/**
	 * @param string $form_input
	 * @return \EE_Form_Section_Proper
	 * @throws \EE_Error
	 */
	public function getInputOptionsForm( $form_input ) {
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
		return new \EE_Form_Section_Proper(
			array(
				'name'            => 'input_options[clone]',
				'html_id'         => 'input-options-clone',
				'layout_strategy' => new \EE_Admin_Two_Column_Layout(), // EE_Div_Per_Section_Layout
				'subsections'     => $this->getInputOptions( $form_input, $this->form_input->options() )
			)
		);
	}



	/**
	 * @param string $form_input
	 * @param array  $options
	 * @return array
	 */
	public function getInputOptions( $form_input, $options ) {
		//if ( $form_input == 'checkbox_multi' ) {
		//	\EEH_Debug_Tools::printr( $options, '$options', __FILE__, __LINE__ );
		//}
		$order = 0;
		$subsections = array();
		foreach ( $options as $key => $value ) {
			$order++;
			$subsections[ 'value-' . $key ] = new \EE_Text_Input(
				array(
					'html_name'  => "input_options[clone][{$order}][value]",
					'html_id'    => "input-options-{$form_input}-clone-{$order}-value",
					'html_class' => "ee-reg-form-option-label-text-js",
					'default'    => $key,
					'other_html_attributes' => 'data-target="reg-form-clone-'
					                           . str_replace( '_', '-', $form_input ) . '-'
					                           . sanitize_key( $key ) . '-option-lbl"',
				)
			);
			$subsections[ 'desc-' . $key ] = new \EE_Text_Input(
				array(
					'html_name' => "input_options[clone][{$order}][desc]",
					'html_id'   => "input-options-{$form_input}-clone-{$order}-desc",
					'default'   => $value
				)
			);
		}
		return $subsections;
	}



	/**
	 * @param string $form_input
	 * @return \EE_Form_Section_Proper
	 */
	public function getBasicSettings( $form_input ) {
		return new \EE_Form_Section_Proper(
			array(
				'name'            => "settings[clone]",
				'html_id'         => "settings-clone",
				'layout_strategy' => new \EE_Admin_Two_Column_Layout(),
				'subsections'     => $this->getBasicSettingsSubsections( $form_input )
			)
		);
	}



	/**
	 * @param string $form_input
	 * @return array
	 */
	protected function getBasicSettingsSubsections( $form_input ) {
		$subsections = array();
		foreach ( $this->form_input_config_fields as $field_name => $form_input_config_field ) {
			$config_input = $this->getBasicConfigInput( $form_input, $field_name );
			if ( $config_input instanceof \EE_Form_Input_Base ) {
				$field_name = str_replace( 'QST_', '', $field_name );
				$subsections[ $field_name ] = $config_input;
			}
		}
		return $subsections;
	}



	/**
	 * @param string $form_input
	 * @param string $field_name
	 * @return \EE_Form_Input_Base|null
	 */
	protected function getBasicConfigInput( $form_input, $field_name ) {
		// what kind of field is it ?
		switch ( $field_name ) {

			case 'QST_display_text' :
				return new \EE_Text_Input(
					array(
						'html_label_text'       => __( 'Label Text', 'event_espresso' ),
						'html_class'            => 'ee-reg-form-label-text-js',
						'other_html_attributes' => 'data-target="reg-form-clone-' . str_replace( '_', '-', $form_input ) . '-lbl"',
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
		return new \EE_Form_Section_Proper(
			array(
				'name'    => "settings[clone]",
				'html_id' => "settings-clone",
				'layout_strategy' => new \EE_Admin_Two_Column_Layout(),
				'subsections'     => $this->getValidationStrategies( $form_input, $form_input_class_name ),
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
	 * @return \EE_Text_Area_Input
	 */
	protected function getFailedValidationMessage() {
		return new \EE_Text_Area_Input(
			array(
				'html_label_text' =>  __( 'Failed Validation Message', 'event_espresso' ),
			)
		);
	}
}
// End of file RegistrationFormEditorForm.php
// Location: /RegistrationFormEditorForm.php