<?php
namespace EventEspresso\core\services\automated_actions;

defined('EVENT_ESPRESSO_VERSION') || exit;



interface AutomatedActionHandlerInterface
{

    /**
     * @param AutomatedActionInterface $automated_action
     * @return void
     */
    public function processAction(AutomatedActionInterface $automated_action);

}
// End of file AutomatedActionHandlerInterface.php
// Location: core/services/automated_actions/AutomatedActionHandlerInterface.php