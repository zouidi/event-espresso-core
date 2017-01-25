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

    const RECURRENCE = 'hourly';



    /**
     * @return string
     */
    public function getRecurrence()
    {
        return TriggerStrategyHourly::RECURRENCE;
    }



}
// End of file TriggerStrategyHourly.php
// Location: EventEspresso\core\services\automated_actions/TriggerStrategyHourly.php