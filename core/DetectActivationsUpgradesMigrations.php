<?php
namespace EventEspresso\core;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class DetectActivationsUpgradesMigrations
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class DetectActivationsUpgradesMigrations {

	/**
	 * indicates this is a 'normal' request. Ie, not activation, nor upgrade, nor activation.
	 * So examples of this would be a normal GET request on the frontend or backend, or a POST, etc
	 */
	const req_type_normal = 0;

	/**
	 * Indicates this is a brand new installation of EE so we should install
	 * tables and default data etc
	 */
	const req_type_new_activation = 1;

	/**
	 * we've detected that EE has been reactivated (or EE was activated during maintenance mode,
	 * and we just exited maintenance mode). We MUST check the database is setup properly
	 * and that default data is setup too
	 */
	const req_type_reactivation = 2;

	/**
	 * indicates that EE has been upgraded since its previous request.
	 * We may have data migration scripts to call and will want to trigger maintenance mode
	 */
	const req_type_upgrade = 3;

	/**
	 * TODO  will detect that EE has been DOWNGRADED. We probably don't want to run in this case...
	 */
	const req_type_downgrade = 4;

	/**
	 * option prefix for recording the activation history (like core's "espresso_db_update") of addons
	 */
	const addon_activation_history_option_prefix = 'ee_addon_activation_history_';



	/**
	 * DetectActivationsUpgradesMigrations constructor.
	 *
	 * @param    \EE_Request  $request
	 * @param    \EE_Response $response
	 */
	public function __construct( \EE_Request $request, \EE_Response $response ) {
		$this->request = $request;
		$this->response = $response;
		// detect whether install or upgrade
		add_action(
			'AHEE__EE_System__detect_activations_or_upgrades__begin',
			array( $this, 'detect_if_activation_or_upgrade' ),
			1
		);
	}



	/**
	 * detect_if_activation_or_upgrade
	 *
	 * Takes care of detecting whether this is a brand new install or code upgrade,
	 * and either setting up the DB or setting up maintenance mode etc.
	 *
	 * @access public
	 * @param \EE_System $system
	 */
	public function detect_if_activation_or_upgrade( \EE_System $system ) {
		do_action( 'AHEE__EE_System___detect_if_activation_or_upgrade__begin' );
		// load M-Mode class
		\EE_Registry::instance()->load_core( 'Maintenance_Mode' );
		// check if db has been updated, or if its a brand-new installation
		$espresso_db_update = $this->fix_espresso_db_upgrade_option();
		$request_type = $this->detect_req_type( $espresso_db_update );
		//\EEH_Debug_Tools::printr( $request_type, '$request_type', __FILE__, __LINE__ );
		if ( $request_type != DetectActivationsUpgradesMigrations::req_type_normal ) {
			\EE_Registry::instance()->load_helper( 'Activation' );
		}
		switch ( $request_type ) {

			case DetectActivationsUpgradesMigrations::req_type_new_activation:
				do_action( 'AHEE__EE_System__detect_if_activation_or_upgrade__new_activation' );
				$this->_handle_core_version_change( $espresso_db_update );
				break;

			case DetectActivationsUpgradesMigrations::req_type_reactivation:
				do_action( 'AHEE__EE_System__detect_if_activation_or_upgrade__reactivation' );
				$this->_handle_core_version_change( $espresso_db_update );
				break;

			case DetectActivationsUpgradesMigrations::req_type_upgrade:
				do_action( 'AHEE__EE_System__detect_if_activation_or_upgrade__upgrade' );
				//migrations may be required now that we've upgraded
				\EE_Maintenance_Mode::instance()->set_maintenance_mode_if_db_old();
				$this->_handle_core_version_change( $espresso_db_update );
				break;

			case DetectActivationsUpgradesMigrations::req_type_downgrade:
				do_action( 'AHEE__EE_System__detect_if_activation_or_upgrade__downgrade' );
				//its possible migrations are no longer required
				\EE_Maintenance_Mode::instance()->set_maintenance_mode_if_db_old();
				$this->_handle_core_version_change( $espresso_db_update );
				break;

			case DetectActivationsUpgradesMigrations::req_type_normal:
			default:
				break;
		}
		do_action( 'AHEE__EE_System__detect_if_activation_or_upgrade__complete' );
	}



	/**
	 * Updates the list of installed versions and sets hooks for
	 * initializing the database later during the request
	 *
	 * @param array $espresso_db_update
	 */
	protected function _handle_core_version_change( $espresso_db_update ) {
		$this->update_list_of_installed_versions( $espresso_db_update );
		//get ready to verify the DB is ok (provided we aren't in maintenance mode, of course)
		add_action(
			'AHEE__EE_System__perform_activations_upgrades_and_migrations',
			array( $this, 'initialize_db_if_no_migrations_required' )
		);
	}



	/**
	 * standardizes the wp option 'espresso_db_upgrade' which actually stores
	 * information about what versions of EE have been installed and activated,
	 * NOT necessarily the state of the database
	 *
	 * @param null $espresso_db_update
	 * @internal param array $espresso_db_update_value the value of the WordPress option. If not supplied, fetches it from the options table
	 * @return array the correct value of 'espresso_db_upgrade', after saving it, if it needed correction
	 */
	private function fix_espresso_db_upgrade_option( $espresso_db_update = null ) {
		do_action( 'FHEE__EE_System__manage_fix_espresso_db_upgrade_option__begin', $espresso_db_update );
		if ( ! $espresso_db_update ) {
			$espresso_db_update = get_option( 'espresso_db_update' );
		}
		// check that option is an array
		if ( ! is_array( $espresso_db_update ) ) {
			// if option is FALSE, then it never existed
			if ( $espresso_db_update === false ) {
				// make $espresso_db_update an array and save option with autoload OFF
				$espresso_db_update = array();
				add_option( 'espresso_db_update', $espresso_db_update, '', 'no' );
			} else {
				// option is NOT FALSE but also is NOT an array, so make it an array and save it
				$espresso_db_update = array( $espresso_db_update => array() );
				update_option( 'espresso_db_update', $espresso_db_update );
			}
		} else {
			$corrected_db_update = array();
			//if IS an array, but is it an array where KEYS are version numbers, and values are arrays?
			foreach ( $espresso_db_update as $should_be_version_string => $should_be_array ) {
				if ( is_int( $should_be_version_string ) && ! is_array( $should_be_array ) ) {
					//the key is an int, and the value IS NOT an array
					//so it must be numerically-indexed, where values are versions installed...
					//fix it!
					$version_string = $should_be_array;
					$corrected_db_update[ $version_string ] = array( 'unknown-date' );
				} else {
					//ok it checks out
					$corrected_db_update[ $should_be_version_string ] = $should_be_array;
				}
			}
			$espresso_db_update = $corrected_db_update;
			update_option( 'espresso_db_update', $espresso_db_update );
		}
		do_action( 'FHEE__EE_System__manage_fix_espresso_db_upgrade_option__complete', $espresso_db_update );
		return $espresso_db_update;
	}



	/**
	 * Does the traditional work of setting up the plugin's database and adding default data.
	 * If migration script/process did not exist, this is what would happen on every activation/reactivation/upgrade.
	 * NOTE: if we're in maintenance mode (which would be the case if we detect there are data
	 * migration scripts that need to be run and a version change happens), enqueues core for database initialization,
	 * so that it will be done when migrations are finished
	 *
	 * @param boolean $initialize_addons_too if true, we double-check addons' database tables etc too;
	 *                                       however,
	 * @param boolean $verify_db_schema      if true will re-check the database tables have the correct schema.
	 *                                       This is a resource-intensive job so only do it when necessary
	 * @return void
	 */
	public function initialize_db_if_no_migrations_required( $initialize_addons_too = false, $verify_db_schema = true ) {
		$request_type = $this->detect_req_type();
		//only initialize system if we're not in maintenance mode.
		if ( \EE_Maintenance_Mode::instance()->level() != \EE_Maintenance_Mode::level_2_complete_maintenance ) {
			update_option( 'ee_flush_rewrite_rules', true );
			if ( $verify_db_schema ) {
				\EEH_Activation::initialize_db_and_folders();
			}
			\EEH_Activation::initialize_db_content();
			\EEH_Activation::system_initialization();
			if ( $initialize_addons_too ) {
				$this->initialize_addons();
			}
		} else {
			\EE_Data_Migration_Manager::instance()->enqueue_db_initialization_for( 'Core' );
		}
		if ( $request_type == DetectActivationsUpgradesMigrations::req_type_new_activation
		     || $request_type == DetectActivationsUpgradesMigrations::req_type_reactivation
		     || $request_type == DetectActivationsUpgradesMigrations::req_type_upgrade
		) {
			add_action( 'AHEE__EE_System__load_CPTs_and_session__start', array( $this, 'redirect_to_about_ee' ), 9 );
		}
	}



	/**
	 * Initializes the db for all registered addons
	 */
	public function initialize_addons() {
		//foreach registered addon, make sure its db is up-to-date too
		foreach ( \EE_Registry::instance()->addons as $addon ) {
			$addon->initialize_db_if_no_migrations_required();
		}
	}



	/**
	 * Adds the current code version to the saved wp option which stores a list of all ee versions ever installed.
	 *
	 * @param    array  $version_history
	 * @param    string $current_version_to_add version to be added to the version history
	 * @return    boolean success as to whether or not this option was changed
	 */
	public function update_list_of_installed_versions( $version_history = null, $current_version_to_add = null ) {
		if ( ! $version_history ) {
			$version_history = $this->fix_espresso_db_upgrade_option( $version_history );
		}
		if ( $current_version_to_add == null ) {
			$current_version_to_add = espresso_version();
		}
		$version_history[ $current_version_to_add ][] = date( 'Y-m-d H:i:s', time() );
		// re-save
		return update_option( 'espresso_db_update', $version_history );
	}



	/**
	 * Detects if the current version indicated in the has existed in the list of
	 * previously-installed versions of EE (espresso_db_update). Does NOT modify it (ie, no side-effect)
	 *
	 * @param array $espresso_db_update array from the wp option stored under the name 'espresso_db_update'.
	 *                                  If not supplied, fetches it from the options table.
	 *                                  Also, caches its result so later parts of the code can also know whether there's been an
	 *                                  update or not. This way we can add the current version to espresso_db_update,
	 *                                  but still know if this is a new install or not
	 * @return int one of the constants on DetectActivationsUpgradesMigrations::req_type_
	 */
	public function detect_req_type( $espresso_db_update = null ) {
		if ( $this->request->activation_type() === null ) {
			$espresso_db_update = ! empty( $espresso_db_update )
				? $espresso_db_update
				: $this->fix_espresso_db_upgrade_option();
			$this->request->set_activation_type(
				$this->detect_req_type_given_activation_history(
					$espresso_db_update,
					'ee_espresso_activation',
					espresso_version()
				)
			);
		}
		return $this->request->activation_type();
	}



	/**
	 * Determines the request type for any ee addon, given three piece of info: the current array of activation histories (for core that' 'espresso_db_update' wp option); the name of the wordpress option which is temporarily set upon activation of the plugin (for core it's 'ee_espresso_activation'); and the version that this plugin
	 * was just activated to (for core that will always be espresso_version())
	 *
	 * @param array  $activation_history_for_addon     the option's value which stores the activation history for this ee plugin.
	 *                                                 for core that's 'espresso_db_update'
	 * @param string $activation_indicator_option_name the name of the wordpress option that is temporarily set to indicate that this plugin was just activated
	 * @param string $version_to_upgrade_to            the version that was just upgraded to (for core that will be espresso_version())
	 * @return int one of the constants on DetectActivationsUpgradesMigrations::req_type_*
	 */
	public static function detect_req_type_given_activation_history(
		$activation_history_for_addon,
		$activation_indicator_option_name,
		$version_to_upgrade_to
	) {
		$version_is_higher = self::_new_version_is_higher( $activation_history_for_addon, $version_to_upgrade_to );
		if ( $activation_history_for_addon ) {
			//it exists, so this isn't a completely new install
			//check if this version already in that list of previously installed versions
			if ( ! isset( $activation_history_for_addon[ $version_to_upgrade_to ] ) ) {
				//it a version we haven't seen before
				if ( $version_is_higher === 1 ) {
					$req_type = DetectActivationsUpgradesMigrations::req_type_upgrade;
				} else {
					$req_type = DetectActivationsUpgradesMigrations::req_type_downgrade;
				}
				delete_option( $activation_indicator_option_name );
			} else {
				// its not an update. maybe a reactivation?
				if ( get_option( $activation_indicator_option_name, false ) ) {
					if ( $version_is_higher === -1 ) {
						$req_type = DetectActivationsUpgradesMigrations::req_type_downgrade;
					} elseif ( $version_is_higher === 0 ) {
						//we've seen this version before, but it's an activation. must be a reactivation
						$req_type = DetectActivationsUpgradesMigrations::req_type_reactivation;
					} else {//$version_is_higher === 1
						$req_type = DetectActivationsUpgradesMigrations::req_type_upgrade;
					}
					delete_option( $activation_indicator_option_name );
				} else {
					//we've seen this version before and the activation indicate doesn't show it was just activated
					if ( $version_is_higher === -1 ) {
						$req_type = DetectActivationsUpgradesMigrations::req_type_downgrade;
					} elseif ( $version_is_higher === 0 ) {
						//we've seen this version before and it's not an activation. its normal request
						$req_type = DetectActivationsUpgradesMigrations::req_type_normal;
					} else {//$version_is_higher === 1
						$req_type = DetectActivationsUpgradesMigrations::req_type_upgrade;
					}
				}
			}
		} else {
			//brand new install
			$req_type = DetectActivationsUpgradesMigrations::req_type_new_activation;
			delete_option( $activation_indicator_option_name );
		}
		return $req_type;
	}



	/**
	 * Detects if the $version_to_upgrade_to is higher than the most recent version in
	 * the $activation_history_for_addon
	 *
	 * @param array  $activation_history_for_addon (keys are versions, values are arrays of times activated,
	 *                                             sometimes containing 'unknown-date'
	 * @param string $version_to_upgrade_to        (current version)
	 * @return int results of version_compare( $version_to_upgrade_to, $most_recently_active_version ).
	 *                                             ie, -1 if $version_to_upgrade_to is LOWER (downgrade);
	 *                                             0 if $version_to_upgrade_to MATCHES (reactivation or normal request);
	 *                                             1 if $version_to_upgrade_to is HIGHER (upgrade) ;
	 */
	protected static function _new_version_is_higher( $activation_history_for_addon, $version_to_upgrade_to ) {
		//find the most recently-activated version
		$most_recently_active_version_activation = '1970-01-01 00:00:00';
		$most_recently_active_version = '0.0.0.dev.000';
		if ( is_array( $activation_history_for_addon ) ) {
			foreach ( $activation_history_for_addon as $version => $times_activated ) {
				//check there is a record of when this version was activated. Otherwise,
				//mark it as unknown
				if ( ! $times_activated ) {
					$times_activated = array( 'unknown-date' );
				}
				if ( is_string( $times_activated ) ) {
					$times_activated = array( $times_activated );
				}
				foreach ( $times_activated as $an_activation ) {
					if ( $an_activation != 'unknown-date'
					     && $an_activation
					        > $most_recently_active_version_activation
					) {
						$most_recently_active_version = $version;
						$most_recently_active_version_activation = $an_activation == 'unknown-date'
							? '1970-01-01 00:00:00' : $an_activation;
					}
				}
			}
		}
		return version_compare( $version_to_upgrade_to, $most_recently_active_version );
	}



	/**
	 * This redirects to the about EE page after activation
	 *
	 * @return void
	 */
	public function redirect_to_about_ee() {
		$notices = \EE_Error::get_notices( false );
		//if current user is an admin and it's not an ajax request
		if ( \EE_Registry::instance()->CAP->current_user_can( 'manage_options', 'espresso_about_default' )
		     && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		     && ! isset( $notices['errors'] )
		) {
			$query_params = array( 'page' => 'espresso_about' );
			if ( $this->detect_req_type() == DetectActivationsUpgradesMigrations::req_type_new_activation ) {
				$query_params['new_activation'] = true;
			}
			if ( $this->detect_req_type() == DetectActivationsUpgradesMigrations::req_type_reactivation ) {
				$query_params['reactivation'] = true;
			}
			$url = add_query_arg( $query_params, admin_url( 'admin.php' ) );
			wp_safe_redirect( $url );
			exit();
		}
	}



	/**
	 * resets the instance and returns it
	 *
	 * @return EE_System
	 */
	public static function reset() {
		//we need to reset the migration manager in order for it to detect DMSs properly
		\EE_Data_Migration_Manager::reset();
		//make sure none of the old hooks are left hanging around
		remove_all_actions( 'AHEE__EE_System__perform_activations_upgrades_and_migrations' );
		self::instance()->detect_activations_or_upgrades();
		self::instance()->perform_activations_upgrades_and_migrations();
		return self::instance();
	}


}
// End of file DetectActivationsUpgradesMigrations.php
// Location: /DetectActivationsUpgradesMigrations.php