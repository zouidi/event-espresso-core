<?php
namespace EventEspresso\core\services\automated_actions;

defined('ABSPATH') || exit;



/**
 * Class TriggerStrategyHook
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class TriggerStrategyHook extends TriggerStrategy
{

    /**
     * This allows strategies to set up whatever hooks are necessary to trigger their logic.
     * All overriding methods should call: $this->setAutomatedAction($automated_action)
     *
     * @param AutomatedActionInterface $automated_action
     */
    public function set(AutomatedActionInterface $automated_action)
    {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        $this->setAutomatedAction($automated_action);
        $extra_data = $automated_action->getData();
        // todo: might want to change where these parameters are saved
        // todo: maybe make them part of the AMA_trigger_value field ?
        $priority = isset($extra_data->hook, $extra_data->hook->priority) ? $extra_data->hook->priority : 10;
        $arguments = isset($extra_data->hook, $extra_data->hook->arguments) ? $extra_data->hook->arguments : 1;
        // \EEH_Debug_Tools::printr($priority, '$priority', __FILE__, __LINE__);
        // \EEH_Debug_Tools::printr($arguments, '$arguments', __FILE__, __LINE__);
        add_action($automated_action->getTriggerValue(), array($this, 'triggerCallback'), $priority, $arguments);
    }





}
// End of file TriggerStrategyHook.php
// Location: EventEspresso\core\services\automated_actions/TriggerStrategyHook.php