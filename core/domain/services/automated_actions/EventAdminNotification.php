<?php
namespace EventEspresso\core\domain\services\automated_actions;

use EventEspresso\core\services\automated_actions\AutomatedActionStrategy;

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
     */
    public function execute()
    {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        \EEH_Debug_Tools::printr($this->getParams(), 'getParams()', __FILE__, __LINE__);
        $this->setHasRun();
        exit();
    }



}
// End of file EventAdminNotification.php
// Location: EventEspresso\core\domain\services\automated_actions/EventAdminNotification.php