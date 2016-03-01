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
	 * form fields for EEM_Question
	 *
	 * @var \EE_Model_Field_Base[] $form_input_config_fields
	 */
	protected $form_input_config_fields;



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
	 */
	public function formHTML( $form_input, $form_input_class_name ) {
		$input_form_form = $this->formInputForm( $form_input, $form_input_class_name );
		$input_settings = $this->formInputSettings( $form_input );
		$html = \EEH_HTML::div( '', '', 'ee-reg-form-editor-form-new-input-form' );
		$html .= $input_form_form->get_html();
		$html .= \EEH_HTML::div( '', '', 'ee-new-form-input-settings-dv' );
		$html .= $input_settings->get_html();
		$html .= \EEH_HTML::divx(); // end 'ee-new-form-input-settings-dv'
		$html .= \EEH_HTML::divx(); // end 'ee-reg-form-editor-form-new-input-form'
		return $html;
	}



	/**
	 * @param string $form_input
	 * @param string $form_input_class_name
	 * @return \EE_Form_Section_Proper(
	 */
	public function formInputForm( $form_input, $form_input_class_name ) {
		return new \EE_Form_Section_Proper(
			array(
				'name'            => "{$form_input}_form_clone",
				'html_id'         => "{$form_input}-form_clone",
				'html_class'      => "ee-new-form-input-dv",
				'layout_strategy' => new \EE_Div_Per_Section_Layout(),
				'subsections'     => array(
					$form_input => $this->formInput( $form_input_class_name ),
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
			return new $form_input_class_name( array( '' => __( ' - please select an option - ', 'event_espresso' ) ) );
		}
		return new $form_input_class_name(
			array( '' => __( ' - please select an option - ', 'event_espresso' ) )
		);
	}



	/**
	 * @param string $form_input
	 * @return \EE_Form_Section_Proper
	 */
	public function formInputSettings( $form_input ) {
		$subsections = array();
		foreach ( $this->form_input_config_fields as $form_input_config_field ) {
			$subsections = $this->getConfigInput( $subsections, $form_input_config_field );
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
	 * @param array                $subsections
	 * @param \EE_Model_Field_Base $form_input_config_field
	 * @return string
	 * @throws \EE_Error
	 */
	protected function getConfigInput( array $subsections, \EE_Model_Field_Base $form_input_config_field ) {
		$config_input = null;
		// what kind of field is it ?
		switch ( $form_input_config_field->get_name() ) {
			case 'QST_html_class' :
			case 'QST_html_label_class' :
				$config_input = new \EE_Text_Input();
				break;
			case 'QST_display_text' :
				$config_input = new \EE_Text_Input(
					array(
						'html_label_text' => __( 'Form Input Label', 'event_espresso' )
					)
				);
				break;
			case 'QST_desc' :
				$config_input = new \EE_Text_Area_Input(
					array(
						'html_label_text' => __( 'Description', 'event_espresso' )
					)
				);
				break;
			case 'QST_validation' :
				$config_input = new \EE_Checkbox_Multi_Input(
					ValidationStrategiesLoader::get(),
					array(
						'html_label_text' => __( 'Validation Rules', 'event_espresso' )
					)
				);
				break;
		}
		if ( $config_input !== null ) {
			$input_name = ! empty( $input_name )
				? $input_name
				: str_replace( 'QST_', '', $form_input_config_field->get_name() );
			$subsections[ $input_name ] = $config_input;
		}
		return $subsections;
	}

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