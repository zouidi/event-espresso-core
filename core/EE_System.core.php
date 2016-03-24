<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}
/**
 *
 * EE_System
 *
 * @package		Event Espresso
 * @subpackage	core/
 * @author		Brent Christensen
 *
 * ------------------------------------------------------------------------
 */
final class EE_System {


	/**
	 *    instance of the EE_System object
	 *
	 * @var    $_instance
	 * @access    private
	 */
	private static $_instance;

	/**
	 * @access    protected
	 * @type    $config EE_Registry
	 */
	protected $registry;

	/**
	 * @access protected
	 * @type \EE_Request $request
	 */
	protected $request;

	/**
	 * @access protected
	 * @type \EE_Response $response
	 */
	protected $response;

	/**
	 * @access    protected
	 * @type    $config EE_Config
	 */
	protected $config;



	/**
	 *	@singleton method used to instantiate class object
	 *	@access public
	 *	@return EE_System
	 */
	public static function instance() {
		// check if class object is instantiated
		if ( ! self::$_instance instanceof EE_System ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}



	/**
	 * resets the instance and returns it
	 *
	 * @return EE_System
	 * @throws \EE_Error
	 */
	public static function reset() {
		//we need to reset the migration manager in order for it to detect DMSs properly
		\EE_Data_Migration_Manager::reset();
		//make sure none of the old hooks are left hanging around
		remove_all_actions( 'AHEE__EE_System__perform_activations_upgrades_and_migrations' );
		\EE_Activation_Manager::instance()->detect_activations_or_upgrades();
		\EE_Activation_Manager::instance()->perform_activations_upgrades_and_migrations();
		return self::instance();
	}



	/**
	 *    sets hooks for running rest of system
	 *    provides "AHEE__EE_System__construct__complete" hook for EE Addons to use as their starting point
	 *    starting EE Addons from any other point may lead to problems
	 *
	 * @access    private
	 */
	private function __construct() {
		do_action( 'AHEE__EE_System__construct__begin', $this );
		// load EE_Config, EE_Textdomain, etc
		add_action( 'AHEE__EE_Bootstrap__load_core_configuration', array( $this, 'load_core_configuration' ), 10 );
		// load EE_Config, EE_Textdomain, etc
		add_action(
			'AHEE__EE_Bootstrap__register_shortcodes_modules_and_widgets',
			array( $this, 'register_shortcodes_modules_and_widgets' ),
			10
		);
		// you wanna get going? I wanna get going... let's get going!
		add_action( 'AHEE__EE_Bootstrap__brew_espresso', array( $this, 'brew_espresso' ), 10 );
		// for doing stuff AFTER any plugins have completed loading
		add_action( 'AHEE__EE_Bootstrap__after_brew_espresso', array( $this, 'after_brew_espresso' ), 10 );
		//other housekeeping
		//exclude EE critical pages from wp_list_pages
		add_filter( 'wp_list_pages_excludes', array( $this, 'remove_pages_from_wp_list_pages' ), 10 );
		// ALL EE Addons should use the following hook point to attach their initial setup too
		// it's extremely important for EE Addons to register any class autoloaders
		// so that they can be available when the EE_Config loads
		do_action( 'AHEE__EE_System__construct__complete', $this );
	}



	/**
	 * @param \EE_Request $request
	 */
	public function set_request( \EE_Request $request ) {
		$this->request = $request;
	}



	/**
	 * @param \EE_Response $response
	 */
	public function set_response( \EE_Response $response ) {
		$this->response = $response;
	}



	/**
	 * @param EE_Registry $registry
	 */
	public function set_registry( $registry ) {
		$this->registry = $registry;
	}



	/**
	 * load_core_configuration
	 *
	 * this is hooked into 'AHEE__EE_Bootstrap__load_core_configuration'
	 * which runs during the WP 'plugins_loaded' action at priority 5
	 *
	 * @return void
	 */
	public function load_core_configuration(){
		do_action( 'AHEE__EE_System__load_core_configuration__begin', $this );
		//load textdomain
		EE_Load_Textdomain::load_textdomain();
		// load and setup EE_Config and EE_Network_Config
		$this->registry->load_core( 'Config' );
		$this->registry->load_core( 'Network_Config' );
		// setup autoloaders
		// enable logging?
		if ( $this->registry->CFG->admin->use_full_logging ) {
			$this->registry->load_core( 'Log' );
		}
		// check for activation errors
		$activation_errors = get_option( 'ee_plugin_activation_errors', FALSE );
		if ( $activation_errors ) {
			EE_Error::add_error( $activation_errors, __FILE__, __FUNCTION__, __LINE__ );
			update_option( 'ee_plugin_activation_errors', FALSE );
		}
		// get model names
		$this->_parse_model_names();

		//load caf stuff a chance to play during the activation process too.
		$this->_maybe_brew_regular();
		do_action( 'AHEE__EE_System__load_core_configuration__complete', $this );
	}


	/**
	 * cycles through all of the models/*.model.php files, and assembles an array of model names
	 *
	 * @return void
	 */
	private function _parse_model_names(){
		//get all the files in the EE_MODELS folder that end in .model.php
		$models = glob( EE_MODELS.'*.model.php');
		$model_names = array();
		$non_abstract_db_models = array();
		foreach( $models as $model ){
			// get model classname
			$classname = EEH_File::get_classname_from_filepath_with_standard_filename( $model );
			$short_name = str_replace( 'EEM_', '', $classname );
			$reflectionClass = new ReflectionClass($classname);
			if( $reflectionClass->isSubclassOf('EEM_Base') && ! $reflectionClass->isAbstract()){
				$non_abstract_db_models[ $short_name] = $classname;
			}
			$model_names[ $short_name ] = $classname;
		}
		$this->registry->models = apply_filters( 'FHEE__EE_System__parse_model_names', $model_names );
		$this->registry->non_abstract_db_models = apply_filters(
			'FHEE__EE_System__parse_implemented_model_names',
			$non_abstract_db_models
		);
	}



	/**
	 * The purpose of this method is to simply check for a file named "caffeinated/brewing_regular.php"
	 * for any hooks that need to be setup before our EE_System launches.
	 *
	 * @return void
	 */
	private function _maybe_brew_regular() {
		if (( ! defined( 'EE_DECAF' ) ||  EE_DECAF !== TRUE ) && is_readable( EE_CAFF_PATH . 'brewing_regular.php' )) {
			require_once EE_CAFF_PATH . 'brewing_regular.php';
			new EE_Brewing_Regular();
		}
	}



	/**
	 * register_shortcodes_modules_and_widgets
	 *
	 * generate lists of shortcodes and modules, then verify paths and classes
	 * This is hooked into 'AHEE__EE_Bootstrap__register_shortcodes_modules_and_widgets'
	 * which runs during the WP 'plugins_loaded' action at priority 7
	 *
	 * @access public
	 * @return void
	 */
	public function register_shortcodes_modules_and_widgets() {
		do_action( 'AHEE__EE_System__register_shortcodes_modules_and_widgets' );
		// check for addons using old hookpoint
		if ( has_action( 'AHEE__EE_System__register_shortcodes_modules_and_addons' )) {
			$this->_incompatible_addon_error();
		}
	}


	/**
	* _incompatible_addon_error
	*
	* @access public
	* @return void
	*/
	private function _incompatible_addon_error() {
		// get array of classes hooking into here
		$class_names = EEH_Class_Tools::get_class_names_for_all_callbacks_on_hook(
			'AHEE__EE_System__register_shortcodes_modules_and_addons'
		);
		if ( ! empty( $class_names ) ) {
			$msg = __(
				'The following plugins, addons, or modules appear to be incompatible with this version of Event Espresso and were automatically deactivated to avoid fatal errors:',
				'event_espresso'
			);
			$msg .= '<ul>';
			foreach ( $class_names as $class_name ) {
				$msg .= '<li><b>Event Espresso - ' . str_replace(
						array( 'EE_', 'EEM_', 'EED_', 'EES_', 'EEW_' ),
						'',
						$class_name
					) . '</b></li>';
			}
			$msg .= '</ul>';
			$msg .= __(
				'Compatibility issues can be avoided and/or resolved by keeping addons and plugins updated to the latest version.',
				'event_espresso'
			);
			// save list of incompatible addons to wp-options for later use
			add_option( 'ee_incompatible_addons', $class_names, '', 'no' );
			if ( is_admin() ) {
				EE_Error::add_error( $msg, __FILE__, __FUNCTION__, __LINE__ );
			}
		}
	}




	/**
	 * brew_espresso
	 *
	 * begins the process of setting hooks for initializing EE in the correct order
	 * This is happening on the 'AHEE__EE_Bootstrap__brew_espresso' hookpoint
	 * which runs during the WP 'plugins_loaded' action at priority 9
	 *
	 * @return void
	 */
	public function brew_espresso(){
		// filter switch for halting any further execution
		if ( apply_filters( 'FHEE__EE_System__brew_espresso', true, $this ) ) {
			do_action( 'AHEE__EE_System__brew_espresso__begin', $this );
			// load some final core systems
			add_action( 'init', array( $this, 'set_hooks_for_core' ), 1 );
			add_action( 'init', array( $this, 'load_CPTs_and_session' ), 5 );
			add_action( 'init', array( $this, 'load_controllers' ), 7 );
			add_action( 'init', array( $this, 'core_loaded_and_ready' ), 9 );
			add_action( 'init', array( $this, 'initialize' ), 10 );
			add_action( 'init', array( $this, 'initialize_last' ), 100 );
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 25 );
			if ( is_admin() && apply_filters( 'FHEE__EE_System__brew_espresso__load_pue', true ) ) {
				// pew pew pew
				$this->registry->load_core( 'PUE' );
				do_action( 'AHEE__EE_System__brew_espresso__after_pue_init' );
			}
			do_action( 'AHEE__EE_System__brew_espresso__complete', $this );
		} else {
			add_action( 'init', array( $this, 'load_controllers' ), 7 );
			add_action( 'init', array( $this, 'initialize_last' ), 100 );
			do_action( 'AHEE__EE_System__brew_espresso__halted', $this );
		}
	}




	/**
	 * after_brew_espresso
	 *
	 * @access public
	 * @return 	void
	 */
	public function after_brew_espresso() {
		do_action( 'AHEE__EE_System__after_brew_espresso' );
	}




	/**
	 * 	set_hooks_for_core
	 *
	 *  	@access public
	 *  	@return 	void
	 */
	public function set_hooks_for_core() {
		$this->_deactivate_incompatible_addons();
		do_action( 'AHEE__EE_System__set_hooks_for_core' );
	}



	/**
	 * Using the information gathered in EE_System::_incompatible_addon_error,
	 * deactivates any addons considered incompatible with the current version of EE
	 */
	private function _deactivate_incompatible_addons(){
		$incompatible_addons = get_option( 'ee_incompatible_addons', array() );
		if ( ! empty( $incompatible_addons )) {
			$active_plugins = get_option( 'active_plugins', array() );
			foreach ( $active_plugins as $active_plugin ) {
				foreach ( $incompatible_addons as $incompatible_addon ) {
					if ( strpos( $active_plugin,  $incompatible_addon ) !== FALSE ) {
						unset( $_GET['activate'] );
						espresso_deactivate_plugin( $active_plugin );
					}
				}
			}
		}
	}



	/**
	 * 	load_CPTs_and_session
	 *
	 *  	@access public
	 *  	@return 	void
	 */
	public function load_CPTs_and_session() {
		do_action( 'AHEE__EE_System__load_CPTs_and_session__start' );
		// register Custom Post Types
		$this->registry->load_core( 'Register_CPTs' );
		do_action( 'AHEE__EE_System__load_CPTs_and_session__complete' );
	}



	/**
	* load_controllers
	*
	* this is the best place to load any additional controllers that needs access to EE core.
	* it is expected that all basic core EE systems, that are not dependant on the current request are loaded at this time
	*
	* @access public
	* @return void
	 * @throws \EE_Error
	*/
	public function load_controllers() {
		do_action( 'AHEE__EE_System__load_controllers__start' );
		// let's get it started
		if ( ! is_admin() && ! EE_Maintenance_Mode::instance()->level() ) {
			do_action( 'AHEE__EE_System__load_controllers__load_front_controllers' );
			/** @var EE_Front_Controller $Front_Controller */
			$Front_Controller = $this->registry->load_core( 'Front_Controller' );
			$Front_Controller->set_request( $this->request );
			$Front_Controller->set_response( $this->response );
		} else if ( ! EE_FRONT_AJAX ) {
			do_action( 'AHEE__EE_System__load_controllers__load_admin_controllers' );
			$this->registry->load_core( 'Admin' );
		} else if ( EE_Maintenance_Mode::instance()->level() ) {
			// still need to make sure template helper functions are loaded in M-Mode
			$this->registry->load_helper( 'Template' );
		}
		do_action( 'AHEE__EE_System__load_controllers__complete' );
	}



	/**
	* core_loaded_and_ready
	*
	* all of the basic EE core should be loaded at this point and available regardless of M-Mode
	*
	* @access public
	* @return void
	*/
	public function core_loaded_and_ready() {
		do_action( 'AHEE__EE_System__core_loaded_and_ready' );
		do_action( 'AHEE__EE_System__set_hooks_for_shortcodes_modules_and_addons' );
//		add_action( 'wp_loaded', array( $this, 'set_hooks_for_shortcodes_modules_and_addons' ), 1 );
		$this->registry->load_core( 'Session' );
		new EventEspresso\Core\Services\adminToolbarItems( $this->registry, \EE_Maintenance_Mode::instance() );
	}



	/**
	* initialize
	*
	* this is the best place to begin initializing client code
	*
	* @access public
	* @return void
	*/
	public function initialize() {
		do_action( 'AHEE__EE_System__initialize' );
	}



	/**
	* initialize_last
	*
	* this is run really late during the WP init hookpoint, and ensures that mostly everything else that needs to initialize has done so
	*
	* @access public
	* @return void
	*/
	public function initialize_last() {
		do_action( 'AHEE__EE_System__initialize_last' );
	}




	/**
	* set_hooks_for_shortcodes_modules_and_addons
	*
	* this is the best place for other systems to set callbacks for hooking into other parts of EE
	* this happens at the very beginning of the wp_loaded hookpoint
	*
	* @access public
	* @return void
	*/
	public function set_hooks_for_shortcodes_modules_and_addons() {
//		do_action( 'AHEE__EE_System__set_hooks_for_shortcodes_modules_and_addons' );
	}




	/**
	* do_not_cache
	*
	* sets no cache headers and defines no cache constants for WP plugins
	*
	* @access public
	* @return void
	*/
	public static function do_not_cache() {
		// set no cache constants
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		if ( ! defined( 'DONOTCACHCEOBJECT' ) ) {
			define( 'DONOTCACHCEOBJECT', true );
		}
		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( 'DONOTCACHEDB', true );
		}
		// add no cache headers
		add_action( 'send_headers' , array( 'EE_System', 'nocache_headers' ), 10 );
		// plus a little extra for nginx and Google Chrome
		add_filter( 'nocache_headers', array( 'EE_System', 'extra_nocache_headers' ), 10, 1 );
		// prevent browsers from prefetching of the rel='next' link, because it may contain content that interferes with the registration process
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
	}



	/**
	 *    extra_nocache_headers
	 *
	 * @access    public
	 * @param $headers
	 * @return    array
	 */
	public static function extra_nocache_headers ( $headers ) {
		// for NGINX
		$headers['X-Accel-Expires'] = 0;
		// plus extra for Google Chrome since it doesn't seem to respect "no-cache", but WILL respect "no-store"
		$headers['Cache-Control'] = 'no-store, no-cache, must-revalidate, max-age=0';
		return $headers;
	}



	/**
	 * 	nocache_headers
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public static function nocache_headers() {
		nocache_headers();
	}





	/**
	 * simply hooks into "wp_list_pages_exclude" filter (for wp_list_pages method) and makes sure EE critical pages are never returned with the function.
	 *
	 *
	 * @param  array  $exclude_array any existing pages being excluded are in this array.
	 * @return array
	 */
	public function remove_pages_from_wp_list_pages( $exclude_array ) {
		return  array_merge( $exclude_array, $this->registry->CFG->core->get_critical_pages_array() );
	}



	/************************************ WP_ENQUEUE_SCRIPTS HOOK ******************************/



	/**
	 * 	wp_enqueue_scripts
	 *
	 *  	@access 	public
	 *  	@return 	void
	 */
	public function wp_enqueue_scripts() {
		if (
			// unlike other systems, EE_System_scripts loading is turned ON by default,
			// but prior to the init hook, can be turned off via:
			// add_filter( 'FHEE_load_EE_System_scripts', '__return_false' );
			apply_filters( 'FHEE_load_EE_System_scripts', TRUE )
			// similarly, jquery_validate loading is turned OFF by default,
			// but prior to the wp_enqueue_scripts hook, can be turned back on again via:
			// add_filter( 'FHEE_load_jquery_validate', '__return_true' );
			&& apply_filters( 'FHEE_load_jquery_validate', FALSE )
		) {
			// register jQuery Validate
			wp_register_script(
				'jquery-validate',
				EE_GLOBAL_ASSETS_URL . 'scripts/jquery.validate.min.js',
				array( 'jquery' ),
				'1.15.0',
				true
			);
		}
	}



	/******************************************************************************/
	/************************************ DEPRECATED ******************************/
	/******************************************************************************/



	/**
	 * @deprecated since version 4.8.36.rc.024
	 */
	const req_type_normal = 0;

	/**
	 * @deprecated since version 4.8.36.rc.024
	 */
	const req_type_new_activation = 1;

	/**
	 * @deprecated since version 4.8.36.rc.024
	 */
	const req_type_reactivation = 2;

	/**
	 * @deprecated since version 4.8.36.rc.024
	 */
	const req_type_upgrade = 3;

	/**
	 * @deprecated since version 4.8.36.rc.024
	 */
	const req_type_downgrade = 4;

	/**
	 * @deprecated since version 4.8.36.rc.024
	 */
	const req_type_activation_but_not_installed = 5;

	/**
	 * option prefix for recording the activation history (like core's "espresso_db_update") of addons
	 */
	const addon_activation_history_option_prefix = 'ee_addon_activation_history_';



	/**
	 * @deprecated
	 * @access public
	 * @return int
	 * @throws \EE_Error
	 */
	public function detect_req_type() {
		return \EE_Activation_Manager::instance()->detect_activation_type();
	}



	/**
	 * @deprecated
	 * @access public
	 * @return void
	 * @throws \EE_Error
	 */
	public function load_espresso_addons() {
		\EE_Load_Espresso_Core::instance()->loadAddonsAndSetCaps();
	}



	/**
	 * @deprecated
	 * @access public
	 * @return void
	 * @throws \EE_Error
	 */
	public function detect_activations_or_upgrades() {
		\EE_Activation_Manager::instance()->detect_activations_or_upgrades();
	}



	/**
	 * @deprecated
	 * @access public
	 * @return void
	 * @throws \EE_Error
	 */
	public function perform_activations_upgrades_and_migrations() {
		\EE_Activation_Manager::instance()->perform_activations_upgrades_and_migrations();
	}



}
// End of file EE_System.core.php
// Location: /core/EE_System.core.php
