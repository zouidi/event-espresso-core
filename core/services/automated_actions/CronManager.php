<?php
namespace EventEspresso\core\services\automated_actions;

use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\collections\Collection;

defined('ABSPATH') || exit;



/**
 * Class CronManager
 * maintains a collection of cron objects and manages scheduling
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class CronManager
{

    /**
     * @var $crons Collection
     */
    private $crons;



    /**
     * CronManager constructor.
     *
     * @param Collection $crons
     * @throws InvalidInterfaceException
     */
    public function __construct(Collection $crons = null)
    {
        $this->crons = $crons instanceof Collection
            ? $crons
            : new Collection('EventEspresso\core\services\automated_actions\Cron');
    }



    /**
     * @param Cron $cron
     * @throws InvalidEntityException
     */
    public function addCron(Cron $cron) {
        // todo add identifier so that crons are unique
        $this->crons->add($cron);
    }



    /**
     * @param array $crons
     * @throws InvalidEntityException
     */
    public function addCrons($crons = array()) {
        $crons = is_array($crons) ? $crons : array($crons);
        foreach ($crons as $cron) {
            $this->crons->add($cron);
        }
    }



    /**
     * @param Cron $cron
     * @return void
     */
    public function scheduleEvent(Cron $cron)
    {
        if ( ! $this->nextScheduledEvent($cron)) {
            if ($cron->getRecurrence() === '') {
                wp_schedule_single_event(
                    $cron->getTimestamp(),
                    $cron->getActionHook(),
                    $cron->getData()
                );
            } else {
                wp_schedule_event(
                    $cron->getTimestamp(),
                    $cron->getRecurrence(),
                    $cron->getActionHook(),
                    $cron->getData()
                );
            }
        }
    }



    /**
     * @param Cron $cron
     * @return int
     */
    public function nextScheduledEvent(Cron $cron)
    {
        return wp_next_scheduled($cron->getActionHook(), $cron->getData());
    }



    /**
     * @param Cron $cron
     * @return void
     */
    public function clearScheduledEvent(Cron $cron)
    {
        wp_clear_scheduled_hook($cron->getActionHook(), $cron->getData());
    }



    /**
     * @param Cron $cron
     * @return void
     */
    public function unscheduleEvent(Cron $cron)
    {
        wp_unschedule_event($cron->getTimestamp(), $cron->getActionHook(), $cron->getData());
    }



}
// End of file CronManager.php
// Location: EventEspresso\core\services\automated_actions/CronManager.php