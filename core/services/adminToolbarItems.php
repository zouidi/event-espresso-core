<?php
namespace EventEspresso\Core\Services;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class AdminToolbarItems
 * injects Event Espresso related links into the WordPress admin toolbar
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         4.9.0
 */
class AdminToolbarItems {

	/**
	 * @access protected
	 * @type   \EE_Registry $registry
	 */
	protected $registry;

	/**
	 * @access protected
	 * @type   \EE_Maintenance_Mode $maintenanceMode
	 */
	protected $maintenanceMode;



	/**
	 * AdminToolbarItems constructor.
	 *
	 * @param \EE_Registry         $registry
	 * @param \EE_Maintenance_Mode $maintenanceMode
	 */
	public function __construct( \EE_Registry $registry, \EE_Maintenance_Mode $maintenanceMode ) {
		$this->registry = $registry;
		$this->maintenanceMode = $maintenanceMode;
		add_action( 'admin_bar_menu', array( $this, 'addToolbarItems' ), 100 );
	}



	/**
	 *  espresso_toolbar_items
	 *
	 * @access public
	 * @param  \WP_Admin_Bar $admin_bar
	 * @throws \EE_Error
	 */
	public function addToolbarItems( \WP_Admin_Bar $admin_bar ) {
		// if in full M-Mode, or its an AJAX request, or user is NOT an admin
		if (
			defined( 'DOING_AJAX' )
			|| $this->maintenanceMode->level() === \EE_Maintenance_Mode::level_2_complete_maintenance
		    || ! $this->registry->CAP->current_user_can( 'ee_read_ee', 'ee_admin_bar_menu_top_level' )
		) {
			return;
		}
		do_action( 'AHEE_log', __FILE__, __FUNCTION__, '' );
		$this->registry->load_helper( 'URL' );
		$menu_class = 'espresso_menu_item_class';
		//we don't use the constants EVENTS_ADMIN_URL or REG_ADMIN_URL
		//because they're only defined in each of their respective constructors
		//and this might be a frontend request, in which case they aren't available
		$events_admin_url = admin_url( "admin.php?page=espresso_events" );
		$reg_admin_url = admin_url( "admin.php?page=espresso_registrations" );
		$extensions_admin_url = admin_url( "admin.php?page=espresso_packages" );
		//Top Level
		$admin_bar->add_menu(
			array(
				'id'    => 'espresso-toolbar',
				'title' => '<span class="ee-icon ee-icon-ee-cup-thick ee-icon-size-20"></span><span class="ab-label">'
				           . _x( 'Event Espresso', 'admin bar menu group label', 'event_espresso' )
				           . '</span>',
				'href'  => $events_admin_url,
				'meta'  => array(
					'title' => __( 'Event Espresso', 'event_espresso' ),
					'class' => $menu_class . 'first'
				),
			)
		);
		//Events
		if ( $this->registry->CAP->current_user_can(
			'ee_read_events',
			'ee_admin_bar_menu_espresso-toolbar-events'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-events',
					'parent' => 'espresso-toolbar',
					'title'  => __( 'Events', 'event_espresso' ),
					'href'   => $events_admin_url,
					'meta'   => array(
						'title'  => __( 'Events', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		if ( $this->registry->CAP->current_user_can(
			'ee_edit_events',
			'ee_admin_bar_menu_espresso-toolbar-events-new'
		)
		) {
			//Events Add New
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-events-new',
					'parent' => 'espresso-toolbar-events',
					'title'  => __( 'Add New', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array( 'action' => 'create_new' ),
						$events_admin_url
					),
					'meta'   => array(
						'title'  => __( 'Add New', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		if ( is_single() && ( get_post_type() === 'espresso_events' ) ) {
			//Current post
			global $post;
			if ( $this->registry->CAP->current_user_can(
				'ee_edit_event',
				'ee_admin_bar_menu_espresso-toolbar-events-edit',
				$post->ID
			)
			) {
				//Events Edit Current Event
				$admin_bar->add_menu(
					array(
						'id'     => 'espresso-toolbar-events-edit',
						'parent' => 'espresso-toolbar-events',
						'title'  => __( 'Edit Event', 'event_espresso' ),
						'href'   => \EEH_URL::add_query_args_and_nonce(
							array( 'action' => 'edit', 'post' => $post->ID ),
							$events_admin_url
						),
						'meta'   => array(
							'title'  => __( 'Edit Event', 'event_espresso' ),
							'target' => '',
							'class'  => $menu_class
						),
					)
				);
			}
		}
		//Events View
		if ( $this->registry->CAP->current_user_can(
			'ee_read_events',
			'ee_admin_bar_menu_espresso-toolbar-events-view'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-events-view',
					'parent' => 'espresso-toolbar-events',
					'title'  => __( 'View', 'event_espresso' ),
					'href'   => $events_admin_url,
					'meta'   => array(
						'title'  => __( 'View', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		if ( $this->registry->CAP->current_user_can(
			'ee_read_events',
			'ee_admin_bar_menu_espresso-toolbar-events-all'
		)
		) {
			//Events View All
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-events-all',
					'parent' => 'espresso-toolbar-events-view',
					'title'  => __( 'All', 'event_espresso' ),
					'href'   => $events_admin_url,
					'meta'   => array(
						'title'  => __( 'All', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		if ( $this->registry->CAP->current_user_can(
			'ee_read_events',
			'ee_admin_bar_menu_espresso-toolbar-events-today'
		)
		) {
			//Events View Today
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-events-today',
					'parent' => 'espresso-toolbar-events-view',
					'title'  => __( 'Today', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array( 'action' => 'default', 'status' => 'today' ),
						$events_admin_url
					),
					'meta'   => array(
						'title'  => __( 'Today', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		if ( $this->registry->CAP->current_user_can(
			'ee_read_events',
			'ee_admin_bar_menu_espresso-toolbar-events-month'
		)
		) {
			//Events View This Month
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-events-month',
					'parent' => 'espresso-toolbar-events-view',
					'title'  => __( 'This Month', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array( 'action' => 'default', 'status' => 'month' ),
						$events_admin_url
					),
					'meta'   => array(
						'title'  => __( 'This Month', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		//Registration Overview
		if ( $this->registry->CAP->current_user_can(
			'ee_read_registrations',
			'ee_admin_bar_menu_espresso-toolbar-registrations'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-registrations',
					'parent' => 'espresso-toolbar',
					'title'  => __( 'Registrations', 'event_espresso' ),
					'href'   => $reg_admin_url,
					'meta'   => array(
						'title'  => __( 'Registrations', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		//Registration Overview Today
		if ( $this->registry->CAP->current_user_can(
			'ee_read_registrations',
			'ee_admin_bar_menu_espresso-toolbar-registrations-today'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-registrations-today',
					'parent' => 'espresso-toolbar-registrations',
					'title'  => __( 'Today', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array( 'action' => 'default', 'status' => 'today' ),
						$reg_admin_url
					),
					'meta'   => array(
						'title'  => __( 'Today', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		//Registration Overview Today Completed
		if ( $this->registry->CAP->current_user_can(
			'ee_read_registrations',
			'ee_admin_bar_menu_espresso-toolbar-registrations-today-approved'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-registrations-today-approved',
					'parent' => 'espresso-toolbar-registrations-today',
					'title'  => __( 'Approved', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array(
							'action'      => 'default',
							'status'      => 'today',
							'_reg_status' => \EEM_Registration::status_id_approved
						),
						$reg_admin_url
					),
					'meta'   => array(
						'title'  => __( 'Approved', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		//Registration Overview Today Pending\
		if ( $this->registry->CAP->current_user_can(
			'ee_read_registrations',
			'ee_admin_bar_menu_espresso-toolbar-registrations-today-pending'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-registrations-today-pending',
					'parent' => 'espresso-toolbar-registrations-today',
					'title'  => __( 'Pending', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array(
							'action'     => 'default',
							'status'     => 'today',
							'reg_status' => \EEM_Registration::status_id_pending_payment
						),
						$reg_admin_url
					),
					'meta'   => array(
						'title'  => __( 'Pending Payment', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		//Registration Overview Today Incomplete
		if ( $this->registry->CAP->current_user_can(
			'ee_read_registrations',
			'ee_admin_bar_menu_espresso-toolbar-registrations-today-not-approved'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-registrations-today-not-approved',
					'parent' => 'espresso-toolbar-registrations-today',
					'title'  => __( 'Not Approved', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array(
							'action'      => 'default',
							'status'      => 'today',
							'_reg_status' => \EEM_Registration::status_id_not_approved
						),
						$reg_admin_url
					),
					'meta'   => array(
						'title'  => __( 'Not Approved', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		//Registration Overview Today Incomplete
		if ( $this->registry->CAP->current_user_can(
			'ee_read_registrations',
			'ee_admin_bar_menu_espresso-toolbar-registrations-today-cancelled'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-registrations-today-cancelled',
					'parent' => 'espresso-toolbar-registrations-today',
					'title'  => __( 'Cancelled', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array(
							'action'      => 'default',
							'status'      => 'today',
							'_reg_status' => \EEM_Registration::status_id_cancelled
						),
						$reg_admin_url
					),
					'meta'   => array(
						'title'  => __( 'Cancelled', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		//Registration Overview This Month
		if ( $this->registry->CAP->current_user_can(
			'ee_read_registrations',
			'ee_admin_bar_menu_espresso-toolbar-registrations-month'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-registrations-month',
					'parent' => 'espresso-toolbar-registrations',
					'title'  => __( 'This Month', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array( 'action' => 'default', 'status' => 'month' ),
						$reg_admin_url
					),
					'meta'   => array(
						'title'  => __( 'This Month', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		//Registration Overview This Month Approved
		if ( $this->registry->CAP->current_user_can(
			'ee_read_registrations',
			'ee_admin_bar_menu_espresso-toolbar-registrations-month-approved'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-registrations-month-approved',
					'parent' => 'espresso-toolbar-registrations-month',
					'title'  => __( 'Approved', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array(
							'action'      => 'default',
							'status'      => 'month',
							'_reg_status' => \EEM_Registration::status_id_approved
						),
						$reg_admin_url
					),
					'meta'   => array(
						'title'  => __( 'Approved', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		//Registration Overview This Month Pending
		if ( $this->registry->CAP->current_user_can(
			'ee_read_registrations',
			'ee_admin_bar_menu_espresso-toolbar-registrations-month-pending'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-registrations-month-pending',
					'parent' => 'espresso-toolbar-registrations-month',
					'title'  => __( 'Pending', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array(
							'action'      => 'default',
							'status'      => 'month',
							'_reg_status' => \EEM_Registration::status_id_pending_payment
						),
						$reg_admin_url
					),
					'meta'   => array(
						'title'  => __( 'Pending', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		//Registration Overview This Month Not Approved
		if ( $this->registry->CAP->current_user_can(
			'ee_read_registrations',
			'ee_admin_bar_menu_espresso-toolbar-registrations-month-not-approved'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-registrations-month-not-approved',
					'parent' => 'espresso-toolbar-registrations-month',
					'title'  => __( 'Not Approved', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array(
							'action'      => 'default',
							'status'      => 'month',
							'_reg_status' => \EEM_Registration::status_id_not_approved
						),
						$reg_admin_url
					),
					'meta'   => array(
						'title'  => __( 'Not Approved', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		//Registration Overview This Month Cancelled
		if ( $this->registry->CAP->current_user_can(
			'ee_read_registrations',
			'ee_admin_bar_menu_espresso-toolbar-registrations-month-cancelled'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-registrations-month-cancelled',
					'parent' => 'espresso-toolbar-registrations-month',
					'title'  => __( 'Cancelled', 'event_espresso' ),
					'href'   => \EEH_URL::add_query_args_and_nonce(
						array(
							'action'      => 'default',
							'status'      => 'month',
							'_reg_status' => \EEM_Registration::status_id_cancelled
						),
						$reg_admin_url
					),
					'meta'   => array(
						'title'  => __( 'Cancelled', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
		//Extensions & Services
		if ( $this->registry->CAP->current_user_can(
			'ee_read_ee',
			'ee_admin_bar_menu_espresso-toolbar-extensions-and-services'
		)
		) {
			$admin_bar->add_menu(
				array(
					'id'     => 'espresso-toolbar-extensions-and-services',
					'parent' => 'espresso-toolbar',
					'title'  => __( 'Extensions & Services', 'event_espresso' ),
					'href'   => $extensions_admin_url,
					'meta'   => array(
						'title'  => __( 'Extensions & Services', 'event_espresso' ),
						'target' => '',
						'class'  => $menu_class
					),
				)
			);
		}
	}


}
// End of file AdminToolbarItems.php
// Location: /core/services/AdminToolbarItems.php