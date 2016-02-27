<?php
namespace EventEspresso\admin_pages\registration_form;

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
	 * @var \EE_Question_group $question_group
	 */
	protected $question_group = null;

	/*
	 * @var array $editor_form
	 */
	protected $editor_form = null;



	/**
	 * RegistrationFormEditor constructor
	 *
	 * @param \EE_Question_group                 $question_group
	 */
	public function __construct(
		\EE_Question_group $question_group
	) {
		$this->question_group = $question_group;
		$this->editor_form = new \EE_Form_Section_Proper(
			array(
				'name'            => $question_group->html_name(),
				'html_id'         => $question_group->html_id(),
				'layout_strategy' => new \EE_Div_Per_Section_Layout(),
				'subsections'     => array(

				),
			)
		);
	}



	/**
	 * @return string
	 */
	public function getAdminPageContent() {
		return $this->editor_form->get_html();
	}

}
// End of file RegistrationFormEditor.php
// Location: admin_pages/registration_form/RegistrationFormEditor.php