<?php
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\loaders\Loader;
use EventEspresso\core\services\loaders\LoaderInterface;
use EventEspresso\NewAddon\domain\Domain;

defined('EVENT_ESPRESSO_VERSION') || exit();


/**
 * Class  EE_New_Addon
 *
 * @package     Event Espresso
 * @subpackage  eea-new-addon
 * @author      Brent Christensen
 */
Class  EE_New_Addon extends EE_Addon
{

    /**
     * @var LoaderInterface $loader ;
     */
    private static $loader;



    /**
     * EE_New_Addon constructor.
     * !!! IMPORTANT !!!
     * you should NOT run any additional logic in the constructor for addons
     * because addon construction should NOT result in code execution.
     * Successfully registering the addon via the EE_Register_Addon API
     * should be the ONLY way that code should execute.
     * This prevents errors happening due to incompatibilities between addons and core.
     * If you run code here, but core deems it necessary to NOT activate this addon,
     * then fatal errors could happen if this code attempts to reference
     * other classes that do not exist because they have not been loaded.
     * That said, it's a better idea to add any extra code required
     * in the after_registration() method below.
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader = null)
    {
        EE_New_Addon::$loader = $loader;
        parent::__construct();
    }



    /**
     * !!! IMPORTANT !!!
     * this is not the place to perform any logic or add any other filter or action callbacks
     * this is just to bootstrap your addon; and keep in mind the addon might be DE-registered
     * in which case your callbacks should probably not be executed.
     * EED_New_Addon is typically the best place for most filter and action callbacks
     * to be placed (relating to the primary business logic of your addon)
     * IF however for some reason, a module does not work because you have some logic
     * that needs to run earlier than when the modules load,
     * then please see the after_registration() method below.
     *
     * @throws EE_Error
     * @throws \DomainException
     */
    public static function register_addon()
    {
        // register addon via Plugin API
        EE_Register_Addon::register(
            'New_Addon',
            array(
                'version'               => Domain::version(),
                'plugin_slug'           => 'espresso_new_addon',
                'min_core_version'      => Domain::CORE_VERSION_REQUIRED,
                'main_file_path'        => Domain::pluginFile(),
                'namespace'             => array(
                    'FQNS' => 'EventEspresso\NewAddon',
                    'DIR'  => __DIR__,
                ),
                'admin_path'            => Domain::adminPath(),
                'admin_callback'        => '',
                'config_class'          => 'EE_New_Addon_Config',
                'config_name'           => 'EE_New_Addon',
                'autoloader_paths'      => array(
                    'EE_New_Addon_Config'       => Domain::servicesPath()
                                                   . 'config' . DS . 'EE_New_Addon_Config.php',
                    'New_Addon_Admin_Page'      => Domain::adminPath() . 'New_Addon_Admin_Page.core.php',
                    'New_Addon_Admin_Page_Init' => Domain::adminPath() . 'New_Addon_Admin_Page_Init.core.php',
                ),
                'dms_paths'             => array(
                    Domain::servicesPath() . 'database' . DS . 'data_migration_scripts' . DS,
                ),
                'module_paths'          => array(
                    Domain::servicesPath() . 'modules' . DS . 'EED_New_Addon.module.php',
                ),
                'shortcode_paths'       => array(
                    Domain::servicesPath() . 'shortcodes' . DS . 'EES_New_Addon.shortcode.php',
                ),
                'widget_paths'          => array(
                    Domain::servicesPath() . 'widgets' . DS . 'EEW_New_Addon.widget.php',
                ),
                // if plugin update engine is being used for auto-updates. not needed if PUE is not being used.
                'pue_options'           => array(
                    'pue_plugin_slug' => 'eea-new-addon',
                    'plugin_basename' => Domain::pluginBasename(),
                    'checkPeriod'     => '24',
                    'use_wp_update'   => false,
                ),
                'capabilities'          => array(
                    'administrator' => array(
                        'edit_thing',
                        'edit_things',
                        'edit_others_things',
                        'edit_private_things',
                    ),
                ),
                'capability_maps'       => array(
                    'EE_Meta_Capability_Map_Edit' => array(
                        'edit_thing',
                        array('New_Addon_Thing', 'edit_things', 'edit_others_things', 'edit_private_things'),
                    ),
                ),
                'class_paths'           => array(
                    Domain::entitiesPath() . 'thing',
                ),
                'model_paths'           => array(
                    Domain::entitiesPath() . 'thing' . DS . 'model',
                ),
                'class_extension_paths' => array(
                    Domain::entitiesPath() . 'attendee' . DS . 'class_extension',
                ),
                'model_extension_paths' => array(
                    Domain::entitiesPath() . 'attendee' . DS . 'model_extension',
                ),
                //note for the mock we're not actually adding any custom cpt stuff yet.
                'custom_post_types'     => array(),
                'custom_taxonomies'     => array(),
                'default_terms'         => array(),
            )
        );
    }



    /**
     * @return LoaderInterface
     * @throws InvalidArgumentException
     * @throws InvalidInterfaceException
     * @throws InvalidDataTypeException
     */
    public static function loader()
    {
        if (! EE_New_Addon::$loader instanceof LoaderInterface) {
            EE_New_Addon::$loader = new Loader;
        }
        return EE_New_Addon::$loader;
    }



    /**
     * uncomment this method and use it as
     * a safe space to add additional logic like setting hooks
     * that will run immediately after addon registration
     * making this a great place for code that needs to be "omnipresent"
     *
     * @since 4.9.26
     */
    public function after_registration()
    {
        // your logic here
    }



}
// End of file EE_New_Addon.class.php
// Location: wp-content/plugins/eea-new-addon/EE_New_Addon.class.php
