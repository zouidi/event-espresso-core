<?php use EventEspresso\modules\ForwardedModuleResponse;

defined('EVENT_ESPRESSO_VERSION') || exit();



/**
 * Class EE_Module_Request_Router
 * This class handles module instantiation, forward chaining,
 * and obtaining views for the Front Controller. Basically a Module Factory.
 *
 * @package     Event Espresso
 * @subpackage  /core/
 * @author      Brent Christensen
 */
final class EE_Module_Request_Router
{

    /**
     * @var    array $_previous_routes
     * @access    private
     */
    private static $_previous_routes = array();

    /**
     * @var ForwardedModuleResponse $forwarded_module_response
     */
    protected $forwarded_module_response;

    /**
     * @var    WP_Query $WP_Query
     * @access    public
     */
    public $WP_Query;



    /**
     * class constructor

     */
    public function __construct()
    {
    }



    /**
     * resolve_module_routes_and_return_view
     * cycles thru all registered routes trying to find a match for the current request,
     * in order to ultimately return a template path
     *
     * @access    public
     * @param WP_Query $WP_Query
     * @return    string
     * @throws \EE_Error
     */
    public function resolve_module_routes_and_return_view(WP_Query $WP_Query)
    {
        $this->WP_Query = $WP_Query;
        $status = ForwardedModuleResponse::NO_FORWARD;
        // cycle thru module routes
        while ($route = $this->get_route($status)) {
            // determine module and method for route
            $this->forwarded_module_response = $this->resolve_route($route[0], $route[1]);
            if ($this->forwarded_module_response instanceof ForwardedModuleResponse) {
                $status = $this->forwarded_module_response->status();
            } else {
                $status = ForwardedModuleResponse::NO_FORWARD;
                $this->forwarded_module_response = null;
            }
            // get registered view for route
            $template_path = $this->get_view($route, $status);
            if ( ! empty($template_path)) {
                return $template_path;
            }
        }
        return '';
    }



    /**
     * get_route
     * on the first call  to this method, it checks the EE_Request_Handler for a "route"
     * on subsequent calls to this method, instead of checking the EE_Request_Handler for a route,
     * it checks the previous routes array, and checks if the last called route has any forwarding routes registered
     * for it
     *
     * @access public
     * @param  int $status
     * @return NULL|string
     * @throws \EE_Error
     */
    public function get_route($status)
    {
        $current_route = null;
        // assume this if first route being called
        $previous_route = false;
        // but is it really ???
        if ( ! empty(self::$_previous_routes)) {
            // get last run route
            $previous_route = end(self::$_previous_routes);
        }
        //  has another route already been run ?
        if ($previous_route) {
            // check if  forwarding has been set
            $current_route = $this->get_forward($previous_route, $status);
            try {
                //check for recursive forwarding
                if (isset($current_route[1], self::$_previous_routes[$current_route[1]])) {
                    throw new EE_Error(
                        sprintf(
                            __(
                                'An error occurred. The %s route has already been called, and therefore can not be forwarded to, because an infinite loop would be created and break the interweb.',
                                'event_espresso'
                            ),
                            $current_route
                        )
                    );
                }
            } catch (EE_Error $e) {
                $e->get_error();
                return null;
            }
        } else {
            // grab all routes
            $routes = EE_Config::get_routes();
            //d( $routes );
            foreach ($routes as $key => $route) {
                // check request for module route
                if (EE_Registry::instance()->REQ->is_set($key)) {
                    $current_route = sanitize_text_field(EE_Registry::instance()->REQ->get($key));
                    if ($current_route) {
                        $current_route = array($key, $current_route);
                        break;
                    }
                }
            }
        }
        // sorry, but I can't read what you route !
        if (empty($current_route) || ! isset($current_route[0], $current_route[1])) {
            return null;
        }
        //add route to previous routes array
        self::$_previous_routes[] = $current_route;
        return $current_route;
    }



    /**
     *    resolve_route
     *    this method simply takes a valid route, and resolves what module class method the route points to
     *
     * @access    public
     * @param    string $key
     * @param    string $current_route
     * @return    mixed        EED_Module | boolean
     * @throws \EE_Error
     */
    public function resolve_route($key, $current_route)
    {
        // get module method that route has been mapped to
        $module_method = EE_Config::get_route($current_route, $key);
        // verify result was returned
        if (empty($module_method)) {
            $msg = sprintf(__('The requested route %s could not be mapped to any registered modules.',
                'event_espresso'), $current_route);
            EE_Error::add_error($msg, __FILE__, __FUNCTION__, __LINE__);
            return false;
        }
        // verify that result is an array
        if ( ! is_array($module_method)) {
            $msg = sprintf(__('The %s  route has not been properly registered.', 'event_espresso'), $current_route);
            EE_Error::add_error($msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__);
            return false;
        }
        // grab module name
        $module_name = $module_method[0];
        // verify that a class method was registered properly
        if ( ! isset($module_method[1])) {
            $msg = sprintf(__('A class method for the %s  route has not been properly registered.', 'event_espresso'),
                $current_route);
            EE_Error::add_error($msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__);
            return false;
        }
        // grab method
        $method = $module_method[1];
        // verify that class exists
        if ( ! class_exists($module_name)) {
            $msg = sprintf(__('The requested %s class could not be found.', 'event_espresso'), $module_name);
            EE_Error::add_error($msg, __FILE__, __FUNCTION__, __LINE__);
            return false;
        }
        // verify that method exists
        if ( ! method_exists($module_name, $method)) {
            $msg = sprintf(__('The class method %s for the %s route is in invalid.', 'event_espresso'), $method,
                $current_route);
            EE_Error::add_error($msg . '||' . $msg, __FILE__, __FUNCTION__, __LINE__);
            return false;
        }
        // instantiate module and call route method
        return $this->_module_router($module_name, $method);
    }



    /**
     *    module_factory
     *    this method instantiates modules and calls the method that was defined when the route was registered
     *
     * @access    public
     * @param   string $module_name
     * @return    EED_Module | NULL
     */
    public static function module_factory($module_name)
    {
        if ($module_name === 'EED_Module') {
            EE_Error::add_error(
                sprintf(
                    __(
                        'EED_Module is an abstract parent class an can not be instantiated. Please provide a proper module name.',
                        'event_espresso'
                    ),
                    $module_name
                ),
                __FILE__, __FUNCTION__, __LINE__
            );
            return null;
        }
        return EE_Registry::instance()->load_module($module_name);
    }



    /**
     * _module_router
     * this method instantiates modules and calls the method that was defined when the route was registered
     *
     * @access private
     * @param  string $module_name
     * @param  string $method
     * @return mixed
     * @throws \EE_Error
     */
    private function _module_router($module_name, $method)
    {
        // instantiate module class
        $module = EE_Module_Request_Router::module_factory($module_name);
        // ensure that class is actually a module
        if ( ! $module instanceof EED_Module) {
            EE_Error::add_error(
                sprintf(
                    __('The requested %s module is not of the class EED_Module.', 'event_espresso'),
                    $module_name
                ),
                __FILE__,
                __FUNCTION__,
                __LINE__
            );
            return null;
        }
        // grab module name
        $module_name = $module->module_name();
        // map the module to the module objects
        EE_Registry::instance()->modules->{$module_name} = $module;
        // and call whatever action the route was for
        try {
            if ($method === 'run') {
                global $wp;
                $module->run($wp);
                return null;
            } else {
                return $module->{$method}($this->forwarded_module_response);
            }
        } catch (EE_Error $e) {
            $e->get_error();
            return null;
        }
    }



    /**
     * get_forward
     *
     * @access public
     * @param  array $current_route
     * @param  int   $status
     * @return string
     */
    public function get_forward(array $current_route, $status)
    {
        $route = null;
        $key = 'ee';
        if (is_array($current_route)) {
            $key = isset($current_route[0]) ? $current_route[0] : $key;
            $route = isset($current_route[1]) ? $current_route[1] : $route;
        }
        return EE_Config::get_forward($route, $status, $key);
    }



    /**
     * get_view
     *
     * @param  array|string $current_route
     * @param  int          $status
     * @return string
     */
    public function get_view($current_route, $status)
    {
        $route = null;
        $key = 'ee';
        if (is_array($current_route)) {
            $key = isset($current_route[0]) ? $current_route[0] : $key;
            $route = isset($current_route[1]) ? $current_route[1] : $route;
        }
        return EE_Config::get_view($route, $status, $key);
    }



    /**
     * @param $a
     * @param $b
     * @return bool
     */
    public function __set($a, $b)
    {
        return false;
    }



    /**
     * @param $a
     * @return bool
     */
    public function __get($a)
    {
        return false;
    }



    /**
     * @param $a
     * @return bool
     */
    public function __isset($a)
    {
        return false;
    }



    /**
     * @param $a
     * @return bool
     */
    public function __unset($a)
    {
        return false;
    }



    /**
     * @return void
     */
    public function __clone()
    {
    }



    /**
     * @return void
     */
    public function __wakeup()
    {
    }



    /**
     *
     */
    public function __destruct()
    {
    }

}
// End of file EE_Module_Request_Router.core.php
// Location: /core/EE_Module_Request_Router.core.php
