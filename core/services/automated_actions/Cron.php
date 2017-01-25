<?php
namespace EventEspresso\core\services\automated_actions;

defined('ABSPATH') || exit;



/**
 * Class Cron
 * DTO for passing around cron details
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class Cron implements JobInterface
{

    /**
     * @var $timestamp integer
     */
    private $timestamp;

    /**
     * @var $recurrence string
     */
    private $recurrence;

    /**
     * @var $action_hook string
     */
    private $action_hook;

    /**
     * @var $data array
     */
    private $data;



    /**
     * Cron constructor.
     *
     * @param $timestamp
     * @param $recurrence
     * @param $action_hook
     * @param $data
     */
    public function __construct($timestamp = 0, $recurrence = '', $action_hook = '', $data = array())
    {
        $this->setTimestamp($timestamp);
        $this->recurrence = $recurrence;
        $this->action_hook = $action_hook;
        $this->data = $data;
    }



    /**
     * @param int $timestamp
     */
    protected function setTimestamp($timestamp)
    {
        $timestamp = absint($timestamp);
        $this->timestamp = $timestamp ? $timestamp : time();
    }


    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }



    /**
     * @return string
     */
    public function getRecurrence()
    {
        return $this->recurrence;
    }



    /**
     * @return string
     */
    public function getActionHook()
    {
        return $this->action_hook;
    }



    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }



}
// End of file Cron.php
// Location: /core/services/automated_actions/Cron.php