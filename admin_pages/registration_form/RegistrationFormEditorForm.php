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
	 * @throws \EE_Error
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
	 * @param string $identifier
	 * @return string
	 * @throws \EE_Error
	 */
	public function rawFormInput( $form_input, $form_input_class_name, $identifier = 'clone' ) {
		if ( ! is_subclass_of( $form_input_class_name, 'EE_Form_Input_Base' ) ) {
			throw new \EE_Error(
				sprintf(
					__( 'The class "%1$s" needs to be a sub class of  EE_Form_Input_Base.', 'event_espresso' ),
					$form_input_class_name
				)
			);
		}
		$this->form_input = $this->formInput( $form_input, $form_input_class_name );
		$subsections = array();
		if ( $this->form_input instanceof \EE_Form_Input_With_Options_Base ) {
			$subsections = $this->getInputOptions( $form_input, $this->form_input->options() );
		}
		$subsections = array_merge(
			$subsections,
			$this->getBasicSettingsSubsections( $form_input ),
			$this->getValidationStrategies( $form_input, $form_input_class_name )
		);
		return new \EE_Form_Section_Proper(
			array(
				'name'            => "reg_form[{$identifier}]",
				'html_id'         => "reg-form-{$identifier}",
				'layout_strategy' => new \EE_Div_Per_Section_Layout(),
				'subsections'     => $subsections
			)
		);
	}



	/**
	 * @param string $form_input
	 * @param string $identifier
	 * @return \EE_Form_Section_Proper (
	 * @throws \EE_Error
	 */
	public function formInputForm( $form_input, $identifier = 'clone' ) {
		return new \EE_Form_Section_Proper(
			array(
				'name'            => "reg_form[{$identifier}]",
				'html_id'         => "reg-form-{$identifier}",
				'html_class'      => "ee-new-form-input-dv",
				'layout_strategy' => new \EE_Div_Per_Section_Layout(),
				'subsections'     => array(
					$form_input     => $this->form_input,
				),
			)
		);
	}



	/**
	 * @param string       $form_input
	 * @param string       $form_input_class_name
	 * @param \EE_Question $question
	 * @param array        $options
	 * @return \EE_Form_Input_Base
	 */
	public function formInput( $form_input, $form_input_class_name, \EE_Question $question = null, $options = array() ) {
		$is_question = $question instanceof \EE_Question;
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
					'name' => $is_question ? $question->html_name() : $form_input,
					'html_label_text' => $is_question ? $question->display_text() : $form_input,
				)
			);
		} else {
			$this->form_input = new $form_input_class_name(
				array(
					'name' => $is_question ? $question->html_name() : $form_input,
					'html_label_text' => $is_question ? $question->display_text() : $form_input,
				)
			);
		}

		return $this->form_input;
	}



	/**
	 * @param string $form_input
	 * @param string $identifier
	 * @return \EE_Form_Section_Proper
	 * @throws \EE_Error
	 */
	public function getInputOptionsForm( $form_input, $identifier = 'clone' ) {
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
				'name'            => "input_options[{$identifier}]",
				'html_id'         => "input-options-{$identifier}",
				'layout_strategy' => new \EE_Admin_Two_Column_Layout(), // EE_Div_Per_Section_Layout
				'subsections'     => $this->getInputOptions( $form_input, $this->form_input->options() )
			)
		);
	}



	/**
	 * @param string $form_input
	 * @param string $identifier
	 * @param array  $options
	 * @return array
	 */
	public function getInputOptions( $form_input, $options, $identifier = 'clone' ) {
		//if ( $form_input == 'checkbox_multi' ) {
		//	\EEH_Debug_Tools::printr( $options, '$options', __FILE__, __LINE__ );
		//}
		$order = 0;
		$subsections = array();
		foreach ( $options as $key => $value ) {
			$order++;
			$subsections[ 'value-' . $key ] = new \EE_Text_Input(
				array(
					'html_name'  => "input_options[{$identifier}][{$order}][value]",
					'html_id'    => "input-options-{$form_input}-{$identifier}-{$order}-value",
					'html_class' => "ee-reg-form-option-label-text-js",
					'default'    => $key,
					'other_html_attributes' => "data-target=\"reg-form-{$identifier}-"
					                           . str_replace( '_', '-', $form_input ) . '-'
					                           . sanitize_key( $key ) . '-option-lbl"',
				)
			);
			$subsections[ 'desc-' . $key ] = new \EE_Text_Input(
				array(
					'html_name' => "input_options[{$identifier}][{$order}][desc]",
					'html_id'   => "input-options-{$form_input}-{$identifier}-{$order}-desc",
					'default'   => $value
				)
			);
		}
		return $subsections;
	}



	/**
	 * @param string       $form_input
	 * @param string       $identifier
	 * @param \EE_Question $question
	 * @return \EE_Form_Section_Proper
	 * @throws \EE_Error
	 */
	public function getBasicSettings( $form_input, $identifier = 'clone', \EE_Question $question = null ) {
		return new \EE_Form_Section_Proper(
			array(
				'name'            => "settings[{$identifier}]",
				'html_id'         => "settings-{$identifier}",
				'layout_strategy' => new \EE_Admin_Two_Column_Layout(),
				'subsections'     => $this->getBasicSettingsSubsections( $form_input, $identifier, $question )
			)
		);
	}



	/**
	 * @param string       $form_input
	 * @param string       $identifier
	 * @param \EE_Question $question
	 * @return array
	 */
	protected function getBasicSettingsSubsections( $form_input, $identifier = 'clone', \EE_Question $question = null ) {
		$subsections = array(
			'identifier' => new \EE_Fixed_Hidden_Input(
				array(
					'default'    => $question instanceof \EE_Question ? $identifier : "{$form_input}-{$identifier}",
					'html_class' => 'ee-reg-form-hidden-form_input-js',
				)
			)
		);
		foreach ( $this->form_input_config_fields as $field_name => $form_input_config_field ) {
			$config_input = $this->getBasicConfigInput( $form_input, $identifier, $field_name, $question );
			if ( $config_input instanceof \EE_Form_Input_Base ) {
				$field_name = (string) str_replace( 'QST_', '', $field_name );
				$subsections[ $field_name ] = $config_input;
			}
		}
		return $subsections;
	}



	/**
	 * @param string       $form_input
	 * @param string       $identifier
	 * @param string       $field_name
	 * @param \EE_Question $question
	 * @return \EE_Form_Input_Base|null
	 */
	protected function getBasicConfigInput(
		$form_input,
		$identifier = 'clone',
		$field_name,
		\EE_Question $question = null
	) {
		// what kind of field is it ?
		switch ( $field_name ) {

			case 'QST_display_text' :
				return new \EE_Text_Input(
					array(
						'html_label_text'       => __( 'Label Text', 'event_espresso' ),
						'html_class'            => 'ee-reg-form-label-text-js',
						'other_html_attributes' => "data-target=\"reg-form-{$identifier}-" . str_replace( '_', '-', $form_input ) . '-lbl"',
						'default'               => $question instanceof \EE_Question ? $question->display_text() : ''
					)
				);

			case 'QST_desc' :
				return new \EE_Text_Area_Input(
					array(
						'html_label_text' => __( 'Description', 'event_espresso' ),
						'default'         => $question instanceof \EE_Question ? $question->desc() : "",
					)
				);

			case 'QST_html_class' :
				return new \EE_Text_Input(
					array(
						'html_label_text' => __( 'Input CSS Classes', 'event_espresso' ),
						'default'         => $question instanceof \EE_Question ? $question->html_class() : "{$form_input}",
					)
				);

			case 'QST_html_label_class' :
				return new \EE_Text_Input(
					array(
						'html_label_text' => __( 'Label CSS Classes', 'event_espresso' ),
						'default'         => $question instanceof \EE_Question ? $question->html_label_class() : "{$form_input}-lbl",
					)
				);

		}
		return null;
	}



	/**
	 * @param string       $form_input
	 * @param string       $identifier
	 * @param string       $form_input_class_name
	 * @param \EE_Question $question
	 * @return \EE_Form_Section_Proper
	 * @throws \EE_Error
	 */
	public function getValidationSettings(
		$form_input,
		$identifier = 'clone',
		$form_input_class_name,
		\EE_Question $question = null
	) {
		return new \EE_Form_Section_Proper(
			array(
				'name'    => "settings[{$identifier}]",
				'html_id' => "settings-{$identifier}",
				'layout_strategy' => new \EE_Admin_Two_Column_Layout(),
				'subsections'     => $this->getValidationStrategies( $form_input, $form_input_class_name, $question ),
			)
		);
	}



	/**
	 * @param string       $form_input
	 * @param string       $form_input_class_name
	 * @param \EE_Question $question
	 * @return array
	 */
	protected function getValidationStrategies( $form_input, $form_input_class_name, \EE_Question $question = null ) {
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
		$subsections['validation_strategies' ] = $this->getValidationStrategiesInput(
			$validation_strategies,
			$question
		);
		$subsections['validation_message' ] = $this->getFailedValidationMessage();
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
	 * @param array        $validation_strategies
	 * @param \EE_Question $question
	 * @return \EE_Checkbox_Multi_Input
	 */
	protected function getValidationStrategiesInput( array $validation_strategies, \EE_Question $question = null ) {
		return new \EE_Checkbox_Multi_Input(
			array_flip( $validation_strategies ),
			array(
				'html_label_text' =>  __( 'Apply Validation Rules', 'event_espresso' ),
				'default' => $question instanceof \EE_Question ? $question->validation_strategies() : '',
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