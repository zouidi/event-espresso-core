<?php
namespace EventEspresso\core\services\automated_actions;

use EventEspresso\core\exceptions\InvalidEntityException;

defined('EVENT_ESPRESSO_VERSION') || exit;



interface JobSchedulerInterface
{

    /**
     * @param string $identifier
     * @return bool
     * @throws InvalidEntityException
     */
    public function hasJob($identifier);

    /**
     * @param JobInterface $job
     * @param string $identifier
     * @return bool
     * @throws InvalidEntityException
     */
    public function addJob(JobInterface $job, $identifier);

    /**
     * @param array $crons
     * @throws InvalidEntityException
     */
    public function addJobs($crons = array());

    /**
     * @param string $identifier
     * @return JobInterface|null
     */
    public function getJob($identifier);

    /**
     * @param JobInterface $job
     * @return void
     */
    public function scheduleJob(JobInterface $job);

    /**
     * @param JobInterface $job
     * @return int
     */
    public function nextScheduledJob(JobInterface $job);

    /**
     * @param JobInterface $job
     * @return void
     */
    public function clearScheduledJob(JobInterface $job);

    /**
     * @param JobInterface $job
     * @return void
     */
    public function unscheduleJob(JobInterface $job);

}
// End of file JobSchedulerInterface.php
// Location: EventEspresso\core\services\automated_actions/JobSchedulerInterface.php