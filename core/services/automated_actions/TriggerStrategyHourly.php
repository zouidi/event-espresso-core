<?php
namespace EventEspresso\core\services\automated_actions;

defined('ABSPATH') || exit;



/**
 * Class TriggerStrategyHourly
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class TriggerStrategyHourly extends TriggerStrategyCron
{



    /**
     * when a trigger has been "pulled",
     * either by a do_action or scheduled cron,
     * this is the callback method that will run
     * to actually execute the logic
     * for the automated action
     */
    public function execute(array $query_params)
    {
        // TODO: Implement execute() method.
    }
}
// End of file TriggerStrategyHourly.php
// Location: EventEspresso\core\services\automated_actions/TriggerStrategyHourly.php