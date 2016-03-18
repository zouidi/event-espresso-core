<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
* EE_Registry Class
 *
 * Centralized Application Data Storage and Management
 *
 * @package				Event Espresso
 * @subpackage			core
 * @author					Brent Christensen
 */
final class EE_Registry {


	/**
	* 	EE_Cart Object
	* 	@access 	public
	*	@var 	EE_Cart $CART
	*/
	public $CART;

	/**
	* 	EE_Config Object
	* 	@access 	public
	*	@var 	EE_Config $CFG
	*/
	public $CFG;

	/**
	 * EE_Network_Config Object
	 * @access public
	 * @var EE_Network_Config $NET_CFG
	 */
	public $NET_CFG;

	/**
	* 	StdClass object for storing library classes in
	* 	@public LIB
	*/
	public $LIB;

	/**
	 * 	EE_Request_Handler Object
	 * 	@access 	public
	 *	@var 	EE_Request_Handler	$REQ
	 */
	public $REQ;

	/**
	* 	EE_Session Object
	* 	@access 	public
	* 	@var 	EE_Session	 $SSN
	*/
	public $SSN;



	/**
	 * holds the ee capabilities object.
	 *
	 * @since 4.5.0
	 *
	 * @var EE_Capabilities
	 */
	public $CAP;


	/**
	 * 	$addons - StdClass object for holding addons which have registered themselves to work with EE core
	 * 	@access 	public
	 *	@var 	EE_Addon[]
	 */
	public $addons;

	/**
	 * 	$models
	 * 	@access 	public
	 *	@var 	EEM_Base[]   	$models keys are 'short names' (eg Event), values are class names (eg 'EEM_Event')
	 */
	public $models = array();

	/**
	 * 	$modules
	 * 	@access 	public
	 *	@var 	EED_Module[] $modules
	 */
	public $modules;

	/**
	 * 	$shortcodes
	 * 	@access 	public
	 *	@var 	EES_Shortcode[]  $shortcodes
	 */
	public $shortcodes;

	/**
	 * 	$widgets
	 * 	@access 	public
	 *	@var 	WP_Widget[]  $widgets
	 */
	public $widgets;




	/**
	 * $non_abstract_db_models
	 * @access public
	 * @var array this is an array of all implemented model names (i.e. not the parent abstract models, or models
	 * which don't actually fetch items from the DB in the normal way (ie, are not children of EEM_Base)).
	 * Keys are model "shortnames" (eg "Event") as used in model relations, and values are
	 * classnames (eg "EEM_Event")
	 */
	public $non_abstract_db_models = array();




	/**
	* 	$i18n_js_strings - internationalization for JS strings
	*  	usage:   EE_Registry::i18n_js_strings['string_key'] = __( 'string to translate.', 'event_espresso' );
	*  	in js file:  var translatedString = eei18n.string_key;
	*
	* 	@access 	public
	*	@var 	array
	*/
	public static $i18n_js_strings = array();

	/**
	* 	$main_file - path to espresso.php
	*
	* 	@access 	public
	*	@var 	array
	*/
	public $main_file;

	/**
	 * @access    protected
	 * @type    \EE_Request $_request
	 */
	protected $request;

	/**
	 * @access    protected
	 * @type    \EE_Response $_response
	 */
	protected $response;



	/**
	 * @singleton method used to instantiate class object
	 * @access public
	 * @return EE_Registry instance
	 * @throws \EE_Error
	 */
	public static function instance() {
		// check if class object is instantiated
		if ( ! \EE_Load_Espresso_Core::getRegistryForBlog() instanceof EE_Registry ) {
			\EE_Load_Espresso_Core::setRegistryForBlog( new self() );
		}
		return \EE_Load_Espresso_Core::getRegistryForBlog();
	}



	/**
	 *private constructor to prevent direct creation
	 *
	 * @Constructor
	 * @access private
	 * @return EE_Registry
	 */
	private function __construct() {
		$this->load_core( 'Base' );
		// class library
		$this->LIB = new \stdClass();
		$this->addons = new \stdClass();
		$this->modules = new \stdClass();
		$this->shortcodes = new \stdClass();
		$this->widgets = new \stdClass();
		add_action( 'AHEE__EE_System__set_hooks_for_core', array( $this, 'init' ));
	}



	/**
	 * @return EE_Request
	 */
	public function request() {
		return $this->request;
	}



	/**
	 * @return EE_Response
	 */
	public function response() {
		return $this->response;
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
	 * 	init
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public function init() {
		// Get current page protocol
		$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
		// Output admin-ajax.php URL with same protocol as current page
		self::$i18n_js_strings['ajax_url'] = admin_url( 'admin-ajax.php', $protocol );
		self::$i18n_js_strings['wp_debug'] = defined( 'WP_DEBUG' ) ? WP_DEBUG : FALSE;
	}



	/**
	 * localize_i18n_js_strings
	 *
	 * @return string
	 */
	public static function localize_i18n_js_strings() {
		$i18n_js_strings = (array)EE_Registry::$i18n_js_strings;
		foreach ( $i18n_js_strings as $key => $value ) {
			if ( is_scalar( $value ) ) {
				$i18n_js_strings[ $key ] = html_entity_decode( (string)$value, ENT_QUOTES, 'UTF-8' );
			}
		}

		return "/* <![CDATA[ */ var eei18n = " . wp_json_encode( $i18n_js_strings ) . '; /* ]]> */';
	}



	/**
	 * @param mixed string | EED_Module $module
	 */
	public function add_module( $module ) {
		if ( $module instanceof EED_Module ) {
			$module_class = get_class( $module );
			$this->modules->{$module_class} = $module;
		} else {
			if ( ! class_exists( 'EE_Module_Request_Router' )) {
				$this->load_core( 'Module_Request_Router' );
			}
			$this->modules->{$module} = EE_Module_Request_Router::module_factory(
				$module,
				$this->request,
				$this->response
			);
		}
	}



	/**
	 * @param string $module_name
	 * @return mixed EED_Module | NULL
	 */
	public function get_module( $module_name = '' ) {
		return isset( $this->modules->{$module_name} ) ? $this->modules->{$module_name} : NULL;
	}


	/**
	 *    loads core classes - must be singletons
	 *
	 * @access    public
	 * @param string $class_name - simple class name ie: session
	 * @param array  $arguments
	 * @param bool   $load_only
	 * @return mixed
	 */
	public function load_core ( $class_name, $arguments = array(), $load_only = FALSE ) {
		$core_paths = apply_filters(
			'FHEE__EE_Registry__load_core__core_paths',
			array(
				EE_CORE,
				EE_ADMIN,
				EE_CPTS,
				EE_CORE . 'data_migration_scripts' . DS
			)
		);
		// retrieve instantiated class
		return $this->_load( $core_paths, 'EE_' , $class_name, 'core', $arguments, FALSE, TRUE, $load_only );
	}



	/**
	 *    loads data_migration_scripts
	 *
	 * @access    public
	 * @param string $class_name - class name for the DMS ie: EE_DMS_Core_4_2_0
	 * @param array  $arguments
	 * @return EE_Data_Migration_Script_Base
	 */
	public function load_dms ( $class_name, $arguments = array() ) {
		// retrieve instantiated class
		return $this->_load( EE_Data_Migration_Manager::instance()->get_data_migration_script_folders(), 'EE_DMS_' , $class_name, 'dms', $arguments, FALSE, FALSE, FALSE );
	}



	/**
	 *	loads object creating classes - must be singletons
	 *
	 *	@param string $class_name - simple class name ie: attendee
	 *	@param array  $arguments - an array of arguments to pass to the class
	 *	@param bool   $from_db    - some classes are instantiated from the db and thus call a different method to instantiate
	 *	@param bool   $cache      if you don't want the class to be stored in the internal cache (non-persistent) then set this to FALSE (ie. when instantiating model objects from client in a loop)
	 *	@param bool   $load_only      whether or not to just load the file and NOT instantiate, or load AND instantiate (default)
	 *	@return EE_Base_Class
	 */
	public function load_class ( $class_name, $arguments = array(), $from_db = FALSE, $cache = TRUE, $load_only = FALSE ) {
		$paths = apply_filters('FHEE__EE_Registry__load_class__paths',array(
			EE_CORE,
			EE_CLASSES,
			EE_BUSINESS
		));
		// retrieve instantiated class
		return $this->_load( $paths, 'EE_' , $class_name, 'class', $arguments, $from_db, $cache, $load_only );
	}




	/**
	 *    loads helper classes - must be singletons
	 *
	 * @param string $class_name - simple class name ie: price
	 * @param array  $arguments
	 * @param bool   $load_only
	 * @return EEH_Base
	 */
	public function load_helper ( $class_name, $arguments = array(), $load_only = TRUE ) {
		$helper_paths = apply_filters( 'FHEE__EE_Registry__load_helper__helper_paths', array(EE_HELPERS ) );
		// retrieve instantiated class
		return $this->_load( $helper_paths, 'EEH_', $class_name, 'helper', $arguments, FALSE, TRUE, $load_only );
	}



	/**
	 *    loads core classes - must be singletons
	 *
	 * @access    public
	 * @param string $class_name - simple class name ie: session
	 * @param array  $arguments
	 * @param bool   $load_only
	 * @return mixed
	 */
	public function load_lib ( $class_name, $arguments = array(), $load_only = FALSE ) {
		$paths = array(
			EE_LIBRARIES,
			EE_LIBRARIES . 'messages' . DS,
			EE_LIBRARIES . 'shortcodes' . DS,
			EE_LIBRARIES . 'qtips' . DS,
			EE_LIBRARIES . 'payment_methods' . DS,
		);
		// retrieve instantiated class
		return $this->_load( $paths, 'EE_' , $class_name, 'lib', $arguments, FALSE, TRUE, $load_only );
	}



	/**
	 *    loads model classes - must be singletons
	 *
	 * @param string $class_name - simple class name ie: price
	 * @param array  $arguments
	 * @param bool   $load_only
	 * @return EEM_Base
	 */
	public function load_model ( $class_name, $arguments = array(), $load_only = FALSE ) {
		$paths = apply_filters('FHEE__EE_Registry__load_model__paths',array(
			EE_MODELS,
			EE_CORE
		));
		// retrieve instantiated class
		return $this->_load( $paths, 'EEM_' , $class_name, 'model', $arguments, FALSE, TRUE, $load_only );
	}



	/**
	 *    loads model classes - must be singletons
	 *
	 * @param string $class_name - simple class name ie: price
	 * @param array  $arguments
	 * @param bool   $load_only
	 * @return mixed
	 */
	public function load_model_class ( $class_name, $arguments = array(), $load_only = TRUE ) {
		$paths = array(
			EE_MODELS . 'fields' . DS,
			EE_MODELS . 'helpers' . DS,
			EE_MODELS . 'relations' . DS,
			EE_MODELS . 'strategies' . DS
		);
		// retrieve instantiated class
		return $this->_load( $paths, 'EE_' , $class_name, '', $arguments, FALSE, TRUE, $load_only );
	}





	/**
	 * Determines if $model_name is the name of an actual EE model.
	 * @param string $model_name like Event, Attendee, Question_Group_Question, etc.
	 * @return boolean
	 */
	public function is_model_name( $model_name ){
		return isset( $this->models[ $model_name ] ) ? TRUE : FALSE;
	}



	/**
	 *    generic class loader
	 *
	 * @param string $path_to_file - directory path to file location, not including filename
	 * @param string $file_name   - file name  ie:  my_file.php, including extension
	 * @param string $type         - file type - core? class? helper? model?
	 * @param array  $arguments
	 * @param bool   $load_only
	 * @return mixed
	 */
	public function load_file ( $path_to_file, $file_name, $type = '', $arguments = array(), $load_only = TRUE ) {
		// retrieve instantiated class
		return $this->_load( $path_to_file, '', $file_name, $type, $arguments, FALSE, TRUE, $load_only );
	}



	/**
	 *    load_addon
	 *
	 * @param string $path_to_file - directory path to file location, not including filename
	 * @param string $class_name   - full class name  ie:  My_Class
	 * @param string $type         - file type - core? class? helper? model?
	 * @param array  $arguments
	 * @param bool   $load_only
	 * @return EE_Addon
	 */
	public function load_addon ( $path_to_file, $class_name, $type = 'class', $arguments = array(), $load_only = FALSE ) {
		// retrieve instantiated class
		return $this->_load( $path_to_file, 'addon', $class_name, $type, $arguments, FALSE, TRUE, $load_only );
	}


	/**
	 *    loads and tracks classes
	 *
	 * @param array       $file_paths
	 * @param string      $class_prefix - EE  or EEM or... ???
	 * @param bool|string $class_name   - $class name
	 * @param string      $type         - file type - core? class? helper? model?
	 * @param array  $arguments    - an argument or array of arguments to pass to the class upon instantiation
	 * @param bool        $from_db      - some classes are instantiated from the db and thus call a different method to instantiate
	 * @param bool        $cache
	 * @param bool        $load_only
	 * @internal param string $file_path - file path including file name
	 * @return bool | object
	 */
	private function _load(
		$file_paths = array(),
		$class_prefix = 'EE_',
		$class_name = false,
		$type = 'class',
		$arguments = array(),
		$from_db = false,
		$cache = true,
		$load_only = false
	) {
		// strip php file extension
		$class_name = str_replace( '.php', '', trim( $class_name ));
		// does the class have a prefix ?
		if ( ! empty( $class_prefix ) && $class_prefix !== 'addon' ) {
			// make sure $class_prefix is uppercase
			$class_prefix = strtoupper( trim( $class_prefix ));
			// add class prefix ONCE!!!
			$class_name = $class_prefix . str_replace( $class_prefix, '', $class_name );
		}

		$class_abbreviations = array(
			'EE_Cart' => 'CART',
			'EE_Config' => 'CFG',
			'EE_Network_Config' => 'NET_CFG',
			'EE_Request_Handler' => 'REQ',
			'EE_Session' => 'SSN',
			'EE_Capabilities' => 'CAP'
		);

		$class_abbreviation = isset( $class_abbreviations[ $class_name ] )
			? $class_abbreviations[ $class_name ]
			: '';
		// check if class has already been loaded, and return it if it has been
		if ( $class_abbreviation !== '' && $this->{$class_abbreviation} !== null
		) {
			return $this->{$class_abbreviation};
		} else if ( isset ( $this->{$class_name} ) ) {
			return $this->{$class_name};
		} else if ( isset ( $this->LIB->{$class_name} ) ) {
			return $this->LIB->{$class_name};
		} else if ( $class_prefix === 'addon' && isset ( $this->addons->{$class_name} ) ) {
			return $this->addons->{$class_name};
		}

		// assume all paths lead nowhere
		$path = FALSE;
		// make sure $file_paths is an array
		$file_paths = (array)$file_paths;
		// cycle thru paths
		foreach ( $file_paths as $key => $file_path ) {
			// convert all separators to proper DS, if no filepath, then use EE_CLASSES
			$file_path = $file_path ? str_replace( array( '/', '\\' ), DS, $file_path ) : EE_CLASSES;
			// prep file type
			$type = ! empty( $type ) ? trim( $type, '.' ) . '.' : '';
			// build full file path
			$file_paths[ $key ] = rtrim( $file_path, DS ) . DS . $class_name . '.' . $type . 'php';
			//does the file exist and can be read ?
			if ( is_readable( $file_paths[ $key ] )) {
				$path = $file_paths[ $key ];
				break;
			}
		}
		// don't give up! you gotta...
		try {
			//does the file exist and can it be read ?
			if ( ! $path ) {
				// so sorry, can't find the file
				throw new EE_Error (
					sprintf (
						__('The %1$s file %2$s could not be located or is not readable due to file permissions. Please ensure that the following filepath(s) are correct: %3$s','event_espresso'),
						trim( $type, '.' ),
						$class_name,
						'<br />' . implode( ',<br />', $file_paths )
					)
				);
			}
			// get the file
			require_once( $path );
			// if the class isn't already declared somewhere
			if ( class_exists( $class_name, FALSE ) === FALSE ) {
				// so sorry, not a class
				throw new EE_Error(
					sprintf(
						__('The %s file %s does not appear to contain the %s Class.','event_espresso'),
						$type,
						$path,
						$class_name
					)
				);
			}

		} catch ( EE_Error $e ) {
			$e->get_error();
		}


		// don't give up! you gotta...
		try {
			// create reflection
			$reflector = new ReflectionClass( $class_name );
			// instantiate the class and add to the LIB array for tracking
			// EE_Base_Classes are instantiated via new_instance by default (models call them via new_instance_from_db)
			if ( $load_only || $reflector->getConstructor() === NULL || $reflector->isAbstract() ) {
//				$instantiation_mode = 0;
				// no constructor = static methods only... nothing to instantiate, loading file was enough
				return TRUE;
			} else if ( $from_db && method_exists( $class_name, 'new_instance_from_db' ) ) {
//				$instantiation_mode = 1;
				$class_obj =  call_user_func_array( array( $class_name, 'new_instance_from_db' ), $arguments );
			} else if ( method_exists( $class_name, 'new_instance' ) ) {
//				$instantiation_mode = 2;
				$class_obj =  call_user_func_array( array( $class_name, 'new_instance' ), $arguments );
			} else if ( method_exists( $class_name, 'instance' )) {
//				$instantiation_mode = 3;
				$class_obj =  call_user_func_array( array( $class_name, 'instance' ), $arguments );
			} else if ( $reflector->isInstantiable() ) {
//				$instantiation_mode = 4;
				$class_obj =  $reflector->newInstanceArgs( $arguments );
			} else if ( ! $load_only ) {
				// heh ? something's not right !
//				$instantiation_mode = 5;
				throw new EE_Error(
					sprintf(
						__('The %s file %s could not be instantiated.','event_espresso'),
						$type,
						$class_name
					)
				);
			}

		} catch ( EE_Error $e ) {
			$e->get_error();
		}

//	echo '<h4>$class_name : ' . $class_name . '  <br /><span style="font-size:10px;font-weight:normal;">$instantiation_mode : ' . $instantiation_mode . '<br/>' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
//	echo '<h4>$from_db : ' . $from_db . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
//	echo '<h4>$cache : ' . $cache . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
//	echo '<h4>$load_only : ' . $load_only . '  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span></h4>';
//	EEH_Debug_Tools::printr( $arguments, '$arguments  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
//	EEH_Debug_Tools::printr( $class_obj, '$class_obj  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );


		if ( isset( $class_obj )) {
			// return newly instantiated class
			if ( $class_abbreviation !== '' ) {
				$this->{$class_abbreviation} = $class_obj;
			} else if ( EEH_Class_Tools::has_property( $this, $class_name )) {
				$this->{$class_name} = $class_obj;
			} else if ( $class_prefix === 'addon' && $cache  ) {
				$this->addons->{$class_name} = $class_obj;
			} else if ( !$from_db && $cache  ) {
				$this->LIB->{$class_name} = $class_obj;
			}
			return $class_obj;
		}

		return FALSE;

	}




	/**
	 *		@ override magic methods
	 *		@ return void
	 */
	final public function __destruct() {}



	/**
	 * @param $a
	 * @param $b
	 */
	final public function __call($a,$b) {}



	/**
	 * @param $a
	 */
	final public function __get($a) {}



	/**
	 * @param $a
	 * @param $b
	 */
	final public function __set($a,$b) {}



	/**
	 * @param $a
	 */
	final public function __isset($a) {}



	/**
	 * @param $a
	 */
	final public function __unset($a) {}



	/**
	 * @return array
	 */
	final public function __sleep() { return array(); }
	final public function __wakeup() {}



	/**
	 * @return string
	 */
	final public function __toString() { return ''; }
	final public function __invoke() {}
	final public function __set_state() {}
	final public function __clone() {}



	/**
	 * @param $a
	 * @param $b
	 */
	final public static function __callStatic($a,$b) {}

	/**
	 * Gets the addon by its name/slug (not classname. For that, just
	 * use the classname as the property name on EE_Config::instance()->addons)
	 * @param string $name
	 * @return EE_Addon
	 */
	public function get_addon_by_name( $name ){
		foreach($this->addons as $addon){
			if( $addon->name() === $name){
				return $addon;
			}
		}
		return NULL;
	}
	/**
	 * Gets an array of all the registered addons, where the keys are their names. (ie, what each returns for their name() function) They're already available on EE_Config::instance()->addons as properties, where each property's name is the addon's classname. So if you just want to get the addon by classname, use EE_Config::instance()->addons->{classname}
	 *
	 * @return EE_Addon[] where the KEYS are the addon's name()
	 */
	public function get_addons_by_name(){
		$addons = array();
		foreach($this->addons as $addon){
			$addons[ $addon->name() ] = $addon;
		}
		return $addons;
	}



	/**
	 * Resets the specified model's instance AND makes sure EE_Registry doesn't keep
	 * a stale copy of it around
	 *
	 * @param  string $model_name
	 * @return \EEM_Base
	 * @throws \EE_Error
	 */
	public function reset_model( $model_name ){
		$model = $this->load_model( $model_name );
		$model_class_name = get_class( $model );
		//get that model reset it and make sure we nuke the old reference to it
		if ( $model instanceof $model_class_name && is_callable( array( $model_class_name, 'reset' ))) {
			$this->LIB->{$model_class_name} = $model::reset();
		}else{
			throw new EE_Error( sprintf( __( 'Model %s does not have a method "reset"', 'event_espresso' ), $model_name ) );
		}
		return $this->LIB->{$model_class_name};
	}

	/**
	 * Resets the registry and everything in it (eventually, getting it to properly
	 * reset absolutely everything will probably be tricky. right now it just resets
	 * the config, data migration manager, and the models)
	 *
	 * @param boolean $hard          whether to reset data in the database too, or just refresh
	 *                               the Registry to its state at the beginning of the request
	 * @param boolean $reinstantiate whether to create new instances of EE_Registry's singletons too,
	 *                               or just reset without re-instantiating (handy to set to FALSE if you're not sure if you CAN
	 *                               currently reinstantiate the singletons at the moment)
	 *
	 * @return \EE_Registry
	 * @throws \EE_Error
	 */
	public static function reset( $hard = FALSE, $reinstantiate = TRUE ){
		$instance = self::instance();
		$instance->load_helper('Activation');
		EEH_Activation::reset();
		$instance->CFG = EE_Config::reset( $hard, $reinstantiate );
		$instance->LIB->EE_Data_Migration_Manager = EE_Data_Migration_Manager::reset();
		$instance->LIB = new stdClass();
		$model_names = array_keys( $instance->non_abstract_db_models );
		foreach( $model_names as $model_name ){
			$instance->reset_model( $model_name );
		}
		return $instance;
	}

	/**
	 * Gets all the custom post type models defined
	 * @return array keys are model "short names" (Eg "Event") and keys are classnames (eg "EEM_Event")
	 */
	public function cpt_models() {
		$cpt_models = array();
		foreach( $this->non_abstract_db_models as $short_name => $classname ) {
			if( is_subclass_of( $classname, 'EEM_CPT_Base' ) ) {
				$cpt_models[ $short_name ] = $classname;
			}
		}
		return $cpt_models;
	}


}
// End of file EE_Registry.core.php
// Location: ./core/EE_Registry.core.php
