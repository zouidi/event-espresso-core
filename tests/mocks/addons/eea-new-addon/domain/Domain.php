<?php

namespace EventEspresso\NewAddon\domain;

use DomainException;
use EventEspresso\core\domain\DomainBase;

defined('EVENT_ESPRESSO_VERSION') || exit;


/**
 * Domain Class
 *
 * @package     Event Espresso
 * @subpackage  New Addon
 * @author      Event Espresso
 */
class Domain extends DomainBase
{

    /**
     * EE Core Version Required for Add-on
     */
    const CORE_VERSION_REQUIRED = '4.9.44.rc.0000';



    /**
     * @return string
     * @throws DomainException
     */
    public static function entitiesPath()
    {
        return self::pluginPath() . 'domain' . DS . 'entities' . DS;
    }



    /**
     * @return string
     * @throws DomainException
     */
    public static function servicesPath()
    {
        return self::pluginPath() . 'domain' . DS . 'services' . DS;
    }



    /**
     * @return string
     * @throws DomainException
     */
    public static function adminPath()
    {
        return self::pluginPath() . 'domain' . DS . 'services' . DS . 'admin' . DS;
    }


}
