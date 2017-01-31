<?php
namespace EventEspresso\core\services\automated_actions;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class AutomatedActionHandler
 * Simply calls AutomatedActionInterface::process() on the received Action
 * so that its logic can be executed immediately.
 * Implements AutomatedActionHandlerInterface so that this class can be replaced,
 * allowing for alternate forms of action processing such as job queuing
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class AutomatedActionHandler implements AutomatedActionHandlerInterface
{



    /**
     * @param AutomatedActionInterface $automated_action
     * @return void
     */
    public function processAction(AutomatedActionInterface $automated_action)
    {
        $automated_action->process();
    }



}
// End of file AutomatedActionHandler.php
// Location: core/services/automated_actions/AutomatedActionHandler.php