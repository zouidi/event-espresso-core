<?php
namespace EventEspresso\admin_pages\registration_form;

defined( 'ABSPATH' ) || exit;



/**
 * Class FormSectionListTable
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class FormSectionListTable extends \EE_Admin_List_Table {



	/**
	 * @var \Registration_Form_Admin_Page $adminPage
	 */
	protected $adminPage;



	/**
	 * FormSectionListTable constructor.
	 *
	 * @param \Registration_Form_Admin_Page $admin_page
	 */
	public function __construct( \Registration_Form_Admin_Page $admin_page ) {
		$this->adminPage = $admin_page;
		parent::__construct( $admin_page );
	}



	protected function _setup_data() {
		$this->_data = $this->_view !== 'trash' ? $this->adminPage->get_form_sections(
			$this->_per_page,
			$this->_current_page,
			false
		) : $this->adminPage->get_trashed_form_sections( $this->_per_page, $this->_current_page, false );
		$this->_all_data_count = $this->_view !== 'trash' ? $this->adminPage->get_form_sections(
			$this->_per_page,
			$this->_current_page,
			true
		) : $this->adminPage->get_trashed_form_sections( $this->_per_page, $this->_current_page, true );
	}



	protected function _set_properties() {
		$this->_wp_list_args = array(
			'singular' => __( 'question group', 'event_espresso' ),
			'plural'   => __( 'question groups', 'event_espresso' ),
			'ajax'     => true, //for now,
			'screen'   => $this->adminPage->get_current_screen()->id
		);
		$this->_columns = array(
			'cb'              => '<input type="checkbox" />',
			'id'              => __( 'ID', 'event_espresso' ),
			'name'            => __( 'Form Section Name', 'event_espresso' ),
			'description'     => __( 'Description', 'event_espresso' ),
		);
		$this->_sortable_columns = array(
			'id'   => array( 'QSG_ID' => false ),
			'name' => array( 'QSG_name' => false )
		);
		$this->_hidden_columns = array(
			'id'
		);
		$this->_ajax_sorting_callback = 'update_form_section_order';
	}



	/**
	 * _get_table_filters
	 * We use this to assemble and return any filters that are associated with this table that help further refine what get's shown in the table.
	 *
	 * @abstract
	 * @access protected
	 * @return string
	 */
	protected function _get_table_filters() {
		return array();
	}



	protected function _add_view_counts() {
		$this->_views['all']['count'] = $this->adminPage->get_form_sections(
			$this->_per_page,
			$this->_current_page,
			true
		);
		if ( \EE_Registry::instance()->CAP->current_user_can(
			'ee_delete_question_groups',
			'espresso_registration_form_trash_form_section'
		)
		) {
			$this->_views['trash']['count'] = $this->adminPage->get_trashed_form_sections(
				$this->_per_page,
				$this->_current_page,
				true
			);
		}
	}



	/**
	 * @param \EE_Question_Group $item
	 * @return string|void
	 * @throws \EE_Error
	 */
	public function column_cb( $item ) {
		$system_group = $item->get( 'QSG_system' );
		$has_questions_with_answers = $item->has_questions_with_answers();
		$lock_icon = $system_group === 0 && $this->_view === 'trash' && $has_questions_with_answers
			? 'ee-lock-icon ee-alternate-color' : 'ee-lock-icon ee-system-lock';
		return $system_group > 0
		       || ( $system_group === 0 && $this->_view === 'trash' && $has_questions_with_answers )
		       || ! \EE_Registry::instance()->CAP->current_user_can(
			'ee_delete_question_groups',
			'espresso_registration_form_trash_form_sections',
			$item->ID()
		)
			? '<span class="' . $lock_icon . '"></span>' . sprintf(
				'<input type="hidden" name="hdnchk[%1$d]" value="%1$d" />',
				$item->ID()
			)
			: sprintf(
				'<input type="checkbox" id="QSG_ID[%1$d]" name="checkbox[%1$d]" value="%1$d" />',
				$item->ID()
			);
	}



	/**
	 * @param \EE_Question_Group $item
	 * @return mixed|string
	 * @throws \EE_Error
	 */
	public function column_id( \EE_Question_Group $item ) {
		$content = $item->ID();
		$content .= '  <span class="show-on-mobile-view-only">' . $item->name() . '</span>';
		return $content;
	}



	/**
	 * @param \EE_Question_Group $item
	 * @return string
	 * @throws \EE_Error
	 */
	public function column_name( \EE_Question_Group $item ) {
		$actions = array();
		//return $item->name();
		if ( ! defined( 'REG_ADMIN_URL' ) ) {
			define( 'REG_ADMIN_URL', EVENTS_ADMIN_URL );
		}
		$edit_query_args = array(
			'action' => 'add_edit_form_section',
			'QSG_ID' => $item->ID()
		);
		$trash_query_args = array(
			'action' => 'trash_form_section',
			'QSG_ID' => $item->ID()
		);
		$restore_query_args = array(
			'action' => 'restore_form_section',
			'QSG_ID' => $item->ID()
		);
		$delete_query_args = array(
			'action' => 'delete_form_section',
			'QSG_ID' => $item->ID()
		);
		$edit_link = \EE_Admin_Page::add_query_args_and_nonce( $edit_query_args, EE_FORMS_ADMIN_URL );
		$trash_link = \EE_Admin_Page::add_query_args_and_nonce( $trash_query_args, EE_FORMS_ADMIN_URL );
		$restore_link = \EE_Admin_Page::add_query_args_and_nonce( $restore_query_args, EE_FORMS_ADMIN_URL );
		$delete_link = \EE_Admin_Page::add_query_args_and_nonce( $delete_query_args, EE_FORMS_ADMIN_URL );
		if ( \EE_Registry::instance()->CAP->current_user_can(
			'ee_edit_question_group',
			'espresso_registration_form_edit_form_section',
			$item->ID()
		)
		) {
			$actions = array(
				'edit' => '<a href="'
				          . $edit_link
				          . '" title="'
				          . esc_attr__( 'Edit Question Group', 'event_espresso' )
				          . '">'
				          . __( 'Edit', 'event_espresso' )
				          . '</a>'
			);
		}
		if (
			$this->_view !== 'trash'
			&& $item->get( 'QSG_system' ) < 1
			&& \EE_Registry::instance()->CAP->current_user_can(
				'ee_delete_question_group',
				'espresso_registration_form_trash_form_section',
				$item->ID()
			)
		) {
			$actions['delete'] = '<a href="' . $trash_link . '" title="' . esc_attr__(
					'Delete Question Group',
					'event_espresso'
				) . '">' . __( 'Trash', 'event_espresso' ) . '</a>';
		}
		if ( $this->_view === 'trash' ) {
			if ( \EE_Registry::instance()->CAP->current_user_can(
				'ee_delete_question_group',
				'espresso_registration_form_restore_form_section',
				$item->ID()
			)
			) {
				$actions['restore'] = '<a href="' . $restore_link . '" title="' . esc_attr__(
						'Restore Question Group',
						'event_espresso'
					) . '">' . __( 'Restore', 'event_espresso' ) . '</a>';
			}
			if ( ! $item->has_questions_with_answers()
			     && \EE_Registry::instance()->CAP->current_user_can(
					'ee_delete_question_group',
					'espresso_registration_form_delete_form_section',
					$item->ID()
				)
			) {
				$actions['delete'] = '<a href="' . $delete_link . '" title="' . esc_attr__(
						'Delete Question Group Permanently',
						'event_espresso'
					) . '">' . __( 'Delete Permanently', 'event_espresso' ) . '</a>';
			}
		}
		$content = \EE_Registry::instance()->CAP->current_user_can(
			'ee_edit_question_group',
			'espresso_registration_form_edit_form_section',
			$item->ID()
		) ? '<strong><a class="row-title" href="' . $edit_link . '">' . $item->name() . '</a></strong>' : $item->name();
		$content .= $this->row_actions( $actions );
		return $content;
	}



	/**
	 * @param \EE_Question_Group $item
	 * @return string
	 */
	public function column_identifier( \EE_Question_Group $item ) {
		return $item->identifier();
	}



	/**
	 * @param \EE_Question_Group $item
	 * @return string
	 */
	public function column_description( \EE_Question_Group $item ) {
		return $item->desc();
	}





}
// End of file FormSectionListTable.php
// Location: EventEspresso\admin_pages\registration_form/FormSectionListTable.php