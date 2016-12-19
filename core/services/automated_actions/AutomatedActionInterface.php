<?php
namespace EventEspresso\core\services\automated_actions;

defined('ABSPATH') || exit;



interface AutomatedActionInterface
{

    /**
     * @return int
     */
    public function getID();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getTriggerValue();

    /**
     * @return \stdClass
     */
    public function getData();

    /**
     * @return void
     */
    public function setTrigger();

    /**
     * @return bool
     */
    public function triggerPulled();

    /**
     * @return bool
     */
    public function hasRun();

    /**
     * @return void
     */
    public function process();

}
// End of file AutomatedActionInterface.php
// Location: EventEspresso\core\services\automated_actions/AutomatedActionInterface.php