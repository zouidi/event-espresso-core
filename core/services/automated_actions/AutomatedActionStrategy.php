<?php
namespace EventEspresso\core\services\automated_actions;

defined('ABSPATH') || exit;



/**
 * Class AutomatedActionStrategy
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class AutomatedActionStrategy
{

    /**
     * @var $has_run boolean
     */
    private $has_run = false;

    /**
     * @var $params array
     */
    private $params = array();



    /**
     * @return bool
     */
    public function hasRun() {
        return $this->has_run;
    }



    /**
     * @param bool $has_run
     */
    protected function setHasRun($has_run = true)
    {
        $this->has_run = filter_var($has_run, FILTER_VALIDATE_BOOLEAN);;
    }



    /**
     * @return array
     */
    protected function getParams()
    {
        return $this->params;
    }



    /**
     * when a trigger has been "pulled",
     * either by a do_action or scheduled cron,
     * this is the callback method that will
     * then execute the the automated action
     * for this strategy.
     * by default this is called during "shutdown"
     * in AutomatedActionManager::processActions(),
     * but individual AutomatedActionStrategy classes
     * can call their execute method whenever is best,
     * as long as they call setHasRun() afterwards
     * to avoid this being called again
     *
     * @param array $params
     */
    public function callback(array $params)
    {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        $this->params = $params;
        $this->execute();
    }



    /**
     * when a trigger has been "pulled",
     * either by a do_action or scheduled cron,
     * this is the callback method that will run
     * to actually execute the logic
     * for the automated action
     */
    abstract public function execute();

}
// End of file AutomatedActionStrategy.php
// Location: EventEspresso\core\services\automated_actions/AutomatedActionStrategy.php