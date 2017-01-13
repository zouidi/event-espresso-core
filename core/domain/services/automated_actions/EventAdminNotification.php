<?php
namespace EventEspresso\core\domain\services\automated_actions;

use EE_Registration;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\services\automated_actions\AutomatedActionStrategy;
use InvalidArgumentException;

defined('ABSPATH') || exit;



/**
 * Class EventAdminNotification
 * AutomatedActionStrategy class for sending notifications to Event Admins
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class EventAdminNotification extends AutomatedActionStrategy
{



    /**
     * when a trigger has been "pulled",
     * either by a do_action or scheduled cron,
     * this is the callback method that will run
     * to actually execute the logic
     * for the automated action
     *
     * @throws InvalidEntityException
     * @throws InvalidArgumentException
     */
    public function execute()
    {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        $params = $this->getParams();
        if ( ! isset($params[0], $params[1], $params[2], $params['query_params']) ) {
            throw new InvalidArgumentException();
        }
        if ( ! $params[0] instanceof EE_Registration) {
            throw new InvalidEntityException($params[0], 'EE_Registration');
        }
        /** @var EE_Registration $registration */
        list($registration, $old_STS_ID, $new_STS_ID) = $params;
        // $registration = $params[0];
        // $old_STS_ID = $params[1];
        // $new_STS_ID = $params[2];
        $query_params = $params['query_params'];
        \EEH_Debug_Tools::printr($registration, '$registration', __FILE__, __LINE__);
        \EEH_Debug_Tools::printr($old_STS_ID, '$old_STS_ID', __FILE__, __LINE__);
        \EEH_Debug_Tools::printr($new_STS_ID, '$new_STS_ID', __FILE__, __LINE__);
        \EEH_Debug_Tools::printr($query_params, 'Trigger Rules query_params', __FILE__, __LINE__);
        $where = array();
        foreach ($query_params as $key => $query_param) {
            \EEH_Debug_Tools::printr($key, '$key', __FILE__, __LINE__);
            \EEH_Debug_Tools::printr($query_param, '$query_param', __FILE__, __LINE__);
            if($key === 0 ) {
                $where[0] = isset($where[0]) ? array_merge($where[0], $query_param) : $query_param;
            } else if( is_int($key) ) {
                $where["AND*{$key}"]= $query_param;
            } else {
                $where[$key] = $query_param;
            }
            \EEH_Debug_Tools::printr($where, '$where', __FILE__, __LINE__);
        }
        $this->setHasRun();
        exit();
    }



}
// End of file EventAdminNotification.php
// Location: EventEspresso\core\domain\services\automated_actions/EventAdminNotification.php