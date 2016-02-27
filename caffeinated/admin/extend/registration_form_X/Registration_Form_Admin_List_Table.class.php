<?php
if (!defined('EVENT_ESPRESSO_VERSION') )
	exit('NO direct script access allowed');

/**
 *
 * Registration_Form_Admin_List_Table
 *
 * Class for preparing the table listing all the custom event questions
 *
 * note: anywhere there are no php docs it is because the docs are available in the parent class.
 *
 * @package		Registration_Form_Admin_List_Table
 * @subpackage	includes/core/admin/events/Registration_Form_Admin_List_Table.class.php
 * @author		Darren Ethier
 *
 * ------------------------------------------------------------------------
 */

class Registration_Form_Admin_List_Table extends EE_Admin_List_Table {



	public function __construct( $admin_page ) {
		parent::__construct($admin_page);
	}




	protected function _setup_data() {
		if ( isset( $this->_req_data[ 'status' ] ) && $this->_req_data[ 'status' ] == 'trash' ) {
			$this->_data = $this->get_form_sections( $this->_per_page, $this->_current_page, false, true );
		} else {
			$this->_data = $this->get_form_sections( $this->_per_page, $this->_current_page, false );
		}
		$this->_all_data_count = $this->get_form_sections( $this->_per_page, $this->_current_page, true );
	}




	protected function _set_properties() {
		$this->_wp_list_args = array(
			'singular' => __( 'Form Section', 'event_espresso' ),
			'plural' => __( 'Form Sections', 'event_espresso' ),
			'ajax' => TRUE, //for now,
			'screen' => $this->_admin_page->get_current_screen()->id
		);

		$this->_columns = array(
			'cb'          => '<input type="checkbox" />',
			'id'          => __( 'ID', 'event_espresso' ),
			'name'        => __( 'Form Section', 'event_espresso' ),
			'desc'        => __( 'Description', 'event_espresso' ),
			'identifier' => __( 'Identifier', 'event_espresso' ),
		);

		$this->_sortable_columns = array(
			'id' => array( 'QSG_ID' => FALSE ),
			'name' => array( 'QSG_name' => FALSE )
		);

		$this->_hidden_columns = array(
		);

	}



	//not needed
	protected function _get_table_filters() {
		return array();
	}



	protected function _add_view_counts() {
		$this->_views[ 'all' ][ 'count' ] = $this->get_form_sections(
			$this->_per_page,
			$this->_current_page,
			true
		);
		if ( EE_Registry::instance()->CAP->current_user_can( 'ee_delete_questions', 'espresso_registration_form_trash_question' ) ) {
			$this->_views[ 'trash' ][ 'count' ] = $this->get_form_sections(
				-1,
				$this->_current_page,
				true,
				true
			);
		}
	}



	public function column_cb( $item ) {
		$system_group = $item->get( 'QSG_system' );
		$has_questions_with_answers = $item->has_questions_with_answers();
		$lock_icon = $system_group === 0 && $this->_view == 'trash' && $has_questions_with_answers
			? 'ee-lock-icon ee-alternate-color'
			: 'ee-lock-icon ee-system-lock';
		if (
			$system_group > 0
			|| ( $system_group === 0 && $this->_view == 'trash' && $has_questions_with_answers )
			|| ! EE_Registry::instance()->CAP->current_user_can(
				'ee_delete_question_groups',
				'espresso_registration_form_trash_question_groups',
				$item->ID()
			)
		) {
			return '<span class="' . $lock_icon . '"></span>' . sprintf(
				'<input type="hidden" name="hdnchk[%1$d]" value="%1$d" />',
				$item->ID()
			);
		} else {
			return sprintf(
				'<input type="checkbox" id="QSG_ID[%1$d]" name="checkbox[%1$d]" value="%1$d" />',
				$item->ID()
			);
		}

	}



	public function column_id( EE_Question_Group $item ) {
		$content = $item->ID();
		$content .= '  <span class="show-on-mobile-view-only">' . $item->name() . '</span>';
		return $content;
	}



	public function column_name( EE_Question_Group $item ) {
		$actions = array();
		//return $item->name();
		if ( ! defined( 'REG_ADMIN_URL' ) ) {
			define( 'REG_ADMIN_URL', EVENTS_ADMIN_URL );
		}
		$edit_query_args = array(
			'action' => 'edit_question_group',
			'QSG_ID' => $item->ID()
		);
		$trash_query_args = array(
			'action' => 'trash_question_group',
			'QSG_ID' => $item->ID()
		);
		$restore_query_args = array(
			'action' => 'restore_question_group',
			'QSG_ID' => $item->ID()
		);
		$delete_query_args = array(
			'action' => 'delete_question_group',
			'QSG_ID' => $item->ID()
		);
		$edit_link = EE_Admin_Page::add_query_args_and_nonce( $edit_query_args, EE_FORMS_ADMIN_URL );
		$trash_link = EE_Admin_Page::add_query_args_and_nonce( $trash_query_args, EE_FORMS_ADMIN_URL );
		$restore_link = EE_Admin_Page::add_query_args_and_nonce( $restore_query_args, EE_FORMS_ADMIN_URL );
		$delete_link = EE_Admin_Page::add_query_args_and_nonce( $delete_query_args, EE_FORMS_ADMIN_URL );
		if ( EE_Registry::instance()->CAP->current_user_can(
			'ee_edit_question_group',
			'espresso_registration_form_edit_question_group',
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
		if ( $item->get( 'QSG_system' ) < 1 && $this->_view != 'trash'
		     && EE_Registry::instance()->CAP->current_user_can(
				'ee_delete_question_group',
				'espresso_registration_form_trash_question_group',
				$item->ID()
			)
		) {
			$actions[ 'delete' ] = '<a href="' . $trash_link . '" title="' . esc_attr__(
					'Delete Question Group',
					'event_espresso'
				) . '">' . __( 'Trash', 'event_espresso' ) . '</a>';
		}
		if ( $this->_view == 'trash' ) {
			if ( EE_Registry::instance()->CAP->current_user_can(
				'ee_delete_question_group',
				'espresso_registration_form_restore_question_group',
				$item->ID()
			)
			) {
				$actions[ 'restore' ] = '<a href="' . $restore_link . '" title="' . esc_attr__(
						'Restore Question Group',
						'event_espresso'
					) . '">' . __( 'Restore', 'event_espresso' ) . '</a>';
			}
			if ( ! $item->has_questions_with_answers()
			     && EE_Registry::instance()->CAP->current_user_can(
					'ee_delete_question_group',
					'espresso_registration_form_delete_question_group',
					$item->ID()
				)
			) {
				$actions[ 'delete' ] = '<a href="' . $delete_link . '" title="' . esc_attr__(
						'Delete Question Group Permanently',
						'event_espresso'
					) . '">' . __( 'Delete Permanently', 'event_espresso' ) . '</a>';
			}
		}
		$content = EE_Registry::instance()->CAP->current_user_can(
			'ee_edit_question_group',
			'espresso_registration_form_edit_question_group',
			$item->ID()
		) ? '<strong><a class="row-title" href="' . $edit_link . '">' . $item->name() . '</a></strong>' : $item->name();
		$content .= $this->row_actions( $actions );
		return $content;
	}



	public function column_identifier( EE_Question_Group $item ) {
		return $item->identifier();
	}



	public function column_description( EE_Question_Group $item ) {
		return $item->desc();
	}








} //end class Registration_Form_Admin_List_Table
