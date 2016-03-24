<?php
if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class EE_Activation_Manager
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Michael Nelson, Brent Christensen
 * @since         4.8.36.rc.024
 *
 */
class EE_Activation_Manager {

	/**
	 * indicates this is a 'normal' request. ie, not an activation, nor upgrade, nor reactivation.
	 * So examples of this would be a normal GET request on the frontend or backend, or a POST, etc
	 */
	const activation_type_none = 0;

	/**
	 * Indicates this is a brand new installation of EE so we should install
	 * tables and default data etc
	 */
	const activation_type_new = 1;

	/**
	 * we've detected that EE has been reactivated (or EE was activated during maintenance mode,
	 * and we just exited maintenance mode). We MUST check the database is setup properly
	 * and that default data is setup too
	 */
	const activation_type_reactivation = 2;

	/**
	 * indicates that EE has been upgraded since its previous request.
	 * We may have data migration scripts to call and will want to trigger maintenance mode
	 */
	const activation_type_upgrade = 3;

	/**
	 * TODO  will detect that EE has been DOWNGRADED. We probably don't want to run in this case...
	 */
	const activation_type_downgrade = 4;

	/**
	 * option prefix for recording the activation history (like core's "espresso_db_update") of addons
	 */
	const addon_activation_history_option_prefix = 'ee_addon_activation_history_';

	/**
	 * option name for the array of data that tracks EE core db versions
	 */
	const db_version_option_name = 'espresso_db_update';

	/**
	 * option name for the data that is temporarily set to indicate that this plugin was just activated
	 */
	const activation_indicator_option_name = 'ee_espresso_activation';



	/**
	 * @access private
	 * @type   EE_Load_Espresso_Core $_instance
	 */
	private static $_instance;

	/**
	 * @access protected
	 * @type   \EE_Maintenance_Mode $maintenanceMode
	 */
	protected $maintenanceMode;

	/**
	 * EspressoCore object for the current site
	 *
	 * @access protected
	 * @type   EspressoCore $espressoCore
	 */
	protected $espressoCore;



	/**
	 * @singleton method used to instantiate class object
	 * @access    public
	 * @param \EE_Maintenance_Mode $maintenanceMode
	 * @return \EE_Activation_Manager
	 * @throws \EE_Error
	 */
	public static function instance( \EE_Maintenance_Mode $maintenanceMode = null ) {
		// check if class object is instantiated
		if ( ! self::$_instance instanceof EE_Activation_Manager ) {
			if ( ! $maintenanceMode instanceof \EE_Maintenance_Mode ) {
				throw new \EE_Error(
					__(
						'A valid instance of the EE_Maintenance_Mode class is required to instantiate EE_Activation_Manager.',
						'event_espresso'
					)
				);
			}
			self::$_instance = new self( $maintenanceMode );
		}
		return self::$_instance;
	}



	/**
	 * EE_Activation_Manager constructor.
	 *
	 * @param \EE_Maintenance_Mode $maintenanceMode
	 */
	private function __construct( \EE_Maintenance_Mode $maintenanceMode  ) {
		$this->maintenanceMode = $maintenanceMode;
		// detect whether install or upgrade
		add_action(
			'AHEE__EE_Bootstrap__detect_activations_or_upgrades',
			array( $this, 'detect_activations_or_upgrades' ),
			10
		);
	}



	/**
	 * @param EspressoCore $espressoCore
	 * @throws \EE_Error
	 */
	public function setEspressoCore( $espressoCore ) {
		$this->espressoCore = $espressoCore;
		if ( ! $this->espressoCore->registry() instanceof \EE_Registry ) {
			throw new \EE_Error(
				__(
					'A valid instance of the EE_Registry class is required for EE_Activation_Manager to function properly.',
					'event_espresso'
				)
			);
		}
		if ( ! $this->espressoCore->registry()->request() instanceof \EE_Request ) {
			throw new \EE_Error(
				__(
					'A valid instance of the EE_Request class is required for EE_Activation_Manager to function properly.',
					'event_espresso'
				)
			);
		}
	}



	/**
	 * detect_activations_or_upgrades
	 *
	 * Checks for activation or upgrade of core first;
	 * then also checks if any registered addons have been activated or upgraded
	 * This is hooked into 'AHEE__EE_Bootstrap__detect_activations_or_upgrades'
	 * which runs during the WP 'plugins_loaded' action at priority 3
	 *
	 * @access public
	 * @return void
	 * @throws \EE_Error
	 */
	public function detect_activations_or_upgrades() {
		do_action( 'AHEE__EE_System__detect_activations_or_upgrades__begin', $this );
		// check if db has been updated, or if its a brand-new installation
		$this->espressoCore->registry()->request()->set_activation_type( $this->detect_activation_type() );
		$this->process_activation_type();
		foreach ( $this->espressoCore->registry()->addons as $addon ) {
			//detect the request type for that addon
			$addon->detect_activation_or_upgrade();
		}
		do_action( 'AHEE__EE_Activation_Manager__detect_activations_or_upgrades__end', $this );
	}



	/**
	 * Detects if the current version indicated in the has existed in the list of
	 * previously-installed versions of EE (espresso_db_update). Does NOT modify it (ie, no side-effect)
	 *
	 * @return int one of the constants on EE_Activation_Manager::activation_type_
	 */
	public function detect_activation_type() {
		$activation_type = $this->espressoCore->registry()->request()->activation_type();
		if ( is_int( $activation_type ) ) {
			return $activation_type;
		}
		$activation_type = EE_Activation_Manager::detect_activation_type_given_activation_history(
			$this->get_db_version_history(),
			EE_Activation_Manager::activation_indicator_option_name,
			EVENT_ESPRESSO_VERSION
		);
		$this->espressoCore->registry()->request()->set_activation_type( $activation_type );
		return $activation_type;
	}



	/**
	 * Determines the request type for any ee addon, given three piece of info: the current array of activation histories (for core that' 'espresso_db_update' wp option); the name of the wordpress option which is temporarily set upon activation of the plugin (for core it's 'ee_espresso_activation'); and the version that this plugin
	 * was just activated to (for core that will always be espresso_version())
	 *
	 * @param array  $activation_history_for_addon     the option's value which stores the activation history for this ee plugin.
	 *                                                 for core that's 'espresso_db_update'
	 * @param string $activation_indicator_option_name the name of the wordpress option that is temporarily set to indicate that this plugin was just activated
	 * @param string $version_to_upgrade_to            the version that was just upgraded to (for core that will be espresso_version())
	 * @return int one of the constants on EE_Activation_Manager::activation_type_*
	 */
	public static function detect_activation_type_given_activation_history(
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
					$activation_type = EE_Activation_Manager::activation_type_upgrade;
				} else {
					$activation_type = EE_Activation_Manager::activation_type_downgrade;
				}
				delete_option( $activation_indicator_option_name );
			} else {
				// its not an update. maybe a reactivation?
				if ( get_option( $activation_indicator_option_name, false ) ) {
					if ( $version_is_higher === -1 ) {
						$activation_type = EE_Activation_Manager::activation_type_downgrade;
					} elseif ( $version_is_higher === 0 ) {
						//we've seen this version before, but it's an activation. must be a reactivation
						$activation_type = EE_Activation_Manager::activation_type_reactivation;
					} else {//$version_is_higher === 1
						$activation_type = EE_Activation_Manager::activation_type_upgrade;
					}
					delete_option( $activation_indicator_option_name );
				} else {
					//we've seen this version before and the activation indicate doesn't show it was just activated
					if ( $version_is_higher === -1 ) {
						$activation_type = EE_Activation_Manager::activation_type_downgrade;
					} elseif ( $version_is_higher === 0 ) {
						//we've seen this version before and it's not an activation. its normal request
						$activation_type = EE_Activation_Manager::activation_type_none;
					} else {//$version_is_higher === 1
						$activation_type = EE_Activation_Manager::activation_type_upgrade;
					}
				}
			}
		} else {
			//brand new install
			$activation_type = EE_Activation_Manager::activation_type_new;
			delete_option( $activation_indicator_option_name );
		}
		return $activation_type;
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
					if ( $an_activation !== 'unknown-date'
					     && $an_activation
					        > $most_recently_active_version_activation
					) {
						$most_recently_active_version = $version;
						$most_recently_active_version_activation = $an_activation === 'unknown-date'
							? '1970-01-01 00:00:00' : $an_activation;
					}
				}
			}
		}
		return version_compare( $version_to_upgrade_to, $most_recently_active_version );
	}



	/**
	 * get_db_version
	 *
	 * retrieves the data from the "espresso_db_update" option and sets it to the db_version
	 *
	 * @access public
	 */
	public function get_db_version_history() {
		$db_version_history = $this->espressoCore->db_version_history();
		if ( empty( $db_version_history ) ) {
			$db_version_history = $this->fix_espresso_db_upgrade_option( $db_version_history );
			$this->espressoCore->set_db_version_history( $db_version_history );
		}
		return $db_version_history;
	}



	/**
	 * standardizes the wp option 'espresso_db_upgrade' which actually stores
	 * information about what versions of EE have been installed and activated,
	 * NOT necessarily the state of the database
	 *
	 * @param array() $db_version_history
	 * @internal param array $espresso_db_update_value the value of the WordPress option. If not supplied, fetches it from the options table
	 * @return array the correct value of 'espresso_db_upgrade', after saving it, if it needed correction
	 */
	private function fix_espresso_db_upgrade_option( $db_version_history = null ) {
		do_action( 'FHEE__EE_System__manage_fix_espresso_db_upgrade_option__begin', $db_version_history );
		if ( empty( $db_version_history ) ) {
			$db_version_history = get_option( EE_Activation_Manager::db_version_option_name );
		}
		// check that option is an array
		if ( ! is_array( $db_version_history ) ) {
			// if option is FALSE, then it never existed
			if ( $db_version_history === false ) {
				// make $espresso_db_update an array and save option with autoload OFF
				$db_version_history = array();
				add_option( EE_Activation_Manager::db_version_option_name, $db_version_history, '', 'no' );
			} else {
				// option is NOT FALSE but also is NOT an array, so make it an array and save it
				$db_version_history = array( $db_version_history => array() );
				update_option( EE_Activation_Manager::db_version_option_name, $db_version_history );
			}
		} else {
			$corrected_db_update = array();
			//if IS an array, but is it an array where KEYS are version numbers, and values are arrays?
			foreach ( $db_version_history as $should_be_version_string => $should_be_array ) {
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
			$db_version_history = $corrected_db_update;
			update_option( EE_Activation_Manager::db_version_option_name, $db_version_history );
		}
		do_action( 'FHEE__EE_System__manage_fix_espresso_db_upgrade_option__complete', $db_version_history );
		return $db_version_history;
	}



	/**
	 * process_activation_type
	 *
	 * Takes care of detecting whether this is a brand new install or code upgrade,
	 * and either setting up the DB or setting up maintenance mode etc.
	 *
	 * @access public
	 */
	public function process_activation_type() {
		do_action( 'AHEE__EE_System___detect_if_activation_or_upgrade__begin' );
		if (
			$this->espressoCore->registry()->request()->activation_type() !== EE_Activation_Manager::activation_type_none
		) {
			// NOT a normal request so do NOT proceed with regular application execution
			add_filter( 'FHEE__EE_System__brew_espresso', '__return_false' );
			// perform any necessary activations upgrades and migrations instead
			add_action( 'init', array( $this, 'perform_activations_upgrades_and_migrations' ), 3 );
			// update the db version history
			$this->_handle_core_version_change( $this->espressoCore->db_version_history() );
		}
		do_action( 'AHEE__EE_System__detect_if_activation_or_upgrade__complete' );
	}



	/**
	 *    perform_activations_upgrades_and_migrations
	 *
	 * @access public
	 * @return void
	 * @throws \EE_Error
	 */
	public function perform_activations_upgrades_and_migrations() {
		switch ( $this->espressoCore->registry()->request()->activation_type() ) {
			case EE_Activation_Manager::activation_type_new:
				do_action( 'AHEE__EE_System__detect_if_activation_or_upgrade__new_activation' );

				break;
			case EE_Activation_Manager::activation_type_reactivation:
				do_action( 'AHEE__EE_System__detect_if_activation_or_upgrade__reactivation' );
				break;
			case EE_Activation_Manager::activation_type_upgrade:
				do_action( 'AHEE__EE_System__detect_if_activation_or_upgrade__upgrade' );
				//migrations may be required now that we've upgraded
				$this->maintenanceMode->set_maintenance_mode_if_db_old();
				break;
			case EE_Activation_Manager::activation_type_downgrade:
				do_action( 'AHEE__EE_System__detect_if_activation_or_upgrade__downgrade' );
				//its possible migrations are no longer required
				$this->maintenanceMode->set_maintenance_mode_if_db_old();
				break;
			case EE_Activation_Manager::activation_type_none:
			default:
				break;
		}		//first check if we had previously attempted to setup EE's directories but failed
		// load activation helper if need be
		$this->espressoCore->registry()->load_helper( 'Activation' );
		if ( EEH_Activation::upload_directories_incomplete() ) {
			EEH_Activation::create_upload_directories();
		}
		do_action( 'AHEE__EE_System__perform_activations_upgrades_and_migrations', $this );
	}



	/**
	 * Updates the list of installed versions and sets hooks for
	 * initializing the database later during the request
	 *
	 * @param array $db_version_history
	 */
	protected function _handle_core_version_change( $db_version_history ) {
		$this->update_list_of_installed_versions( $db_version_history );
		//get ready to verify the DB is ok (provided we aren't in maintenance mode, of course)
		add_action(
			'AHEE__EE_System__perform_activations_upgrades_and_migrations',
			array( $this, 'initialize_db_if_no_migrations_required' )
		);
	}



	/**
	 * Adds the current code version to the saved wp option which stores a list of all ee versions ever installed.
	 *
	 * @param    array  $db_version_history
	 * @param    string $current_version_to_add version to be added to the version history
	 * @return    boolean success as to whether or not this option was changed
	 */
	protected function update_list_of_installed_versions( $db_version_history = null, $current_version_to_add = null ) {
		// check db version history is set
		$db_version_history = ! empty( $db_version_history )
			? $db_version_history
			: $this->get_db_version_history();
		// check current version is set
		$current_version_to_add = ! empty( $current_version_to_add )
			? $current_version_to_add
			: EVENT_ESPRESSO_VERSION;
		// add this version and save
		$db_version_history[ $current_version_to_add ][] = date( 'Y-m-d H:i:s', time() );
		return update_option( EE_Activation_Manager::db_version_option_name, $db_version_history );
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
	 * @throws \EE_Error
	 */
	public function initialize_db_if_no_migrations_required(
		$initialize_addons_too = false,
		$verify_db_schema = true
	) {
		$this->detect_activation_type();
		//only initialize system if we're not in maintenance mode.
		if ( $this->maintenanceMode->level() !== \EE_Maintenance_Mode::level_2_complete_maintenance ) {
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
			EE_Data_Migration_Manager::instance()->enqueue_db_initialization_for( 'Core' );
		}
		$activation_type = $this->espressoCore->registry()->request()->activation_type();
		if (
			$activation_type === EE_Activation_Manager::activation_type_new
			|| $activation_type === EE_Activation_Manager::activation_type_upgrade
			//|| $activation_type == EE_Activation_Manager::activation_type_reactivation
		) {
			add_action( 'AHEE__EE_System__load_CPTs_and_session__start', array( $this, 'redirect_to_about_ee' ), 9 );
		}
	}



	/**
	 * Initializes the db for all registered addons
	 */
	public function initialize_addons() {
		//foreach registered addon, make sure its db is up-to-date too
		foreach ( $this->espressoCore->registry()->addons as $addon ) {
			$addon->initialize_db_if_no_migrations_required();
		}
	}



	/**
	 * This redirects to the about EE page after activation
	 *
	 * @return void
	 */
	public function redirect_to_about_ee() {
		$notices = \EE_Error::get_notices( false );
		//if current user is an admin and it's not an ajax request
		if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		     && ! isset( $notices['errors'] )
		     && $this->espressoCore->registry()->CAP->current_user_can( 'manage_options', 'espresso_about_default' )
		) {
			$query_params = array( 'page' => 'espresso_about' );
			if ( $this->detect_activation_type() === EE_Activation_Manager::activation_type_new ) {
				$query_params['new_activation'] = true;
			} else if ( $this->detect_activation_type() === EE_Activation_Manager::activation_type_reactivation ) {
				$query_params['reactivation'] = true;
			}
			$url = add_query_arg( $query_params, admin_url( 'admin.php' ) );
			wp_safe_redirect( $url );
			exit();
		}
	}


}
// End of file EE_Activation_Manager.php
// Location: /EE_Activation_Manager.php