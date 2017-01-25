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
class CronManager implements JobSchedulerInterface
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
     * @param string $identifier
     * @return bool
     * @throws InvalidEntityException
     */
    public function hasJob($identifier) {
        return $this->crons->has($identifier);
    }



    /**
     * @param JobInterface $cron
     * @param string $identifier
     * @return bool
     * @throws InvalidEntityException
     */
    public function addJob(JobInterface $cron, $identifier) {
        if ($this->crons->has($identifier)) {
            return false;
        }
        return $this->crons->add($cron, $identifier);
    }



    /**
     * @param array $crons
     * @throws InvalidEntityException
     */
    public function addJobs($crons = array()) {
        $crons = is_array($crons) ? $crons : array($crons);
        foreach ($crons as $identifier => $cron) {
            $this->addJob($cron, $identifier);
        }
    }



    /**
     * @param string $identifier
     * @return Cron|null
     */
    public function getJob($identifier)
    {
        if ($this->crons->has($identifier)) {
            return $this->crons->get($identifier);
        }
        return null;
    }



    /**
     * @param JobInterface|Cron $cron
     * @return void
     */
    public function scheduleJob(JobInterface $cron)
    {
        if ( ! $this->nextScheduledJob($cron)) {
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
     * @param JobInterface|Cron $cron
     * @return int
     */
    public function nextScheduledJob(JobInterface $cron)
    {
        return wp_next_scheduled($cron->getActionHook(), $cron->getData());
    }



    /**
     * @param JobInterface|Cron $cron
     * @return void
     */
    public function clearScheduledJob(JobInterface $cron)
    {
        wp_clear_scheduled_hook($cron->getActionHook(), $cron->getData());
    }



    /**
     * @param JobInterface|Cron $cron
     * @return void
     */
    public function unscheduleJob(JobInterface $cron)
    {
        wp_unschedule_event($cron->getTimestamp(), $cron->getActionHook(), $cron->getData());
    }



}
// End of file CronManager.php
// Location: EventEspresso\core\services\automated_actions/CronManager.php