<?php
namespace EventEspresso\core\services\automated_actions;

use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\conditional_logic\rules\QueryParamGenerator;
use EventEspresso\core\services\conditional_logic\rules\RuleManager;

defined('ABSPATH') || exit;



/**
 * Class TriggerStrategyCron
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class TriggerStrategyCron extends TriggerStrategy
{

    /**
     * @var JobSchedulerInterface $job_scheduler
     */
    protected $job_scheduler;



    /**
     * TriggerStrategyCron constructor
     *
     * @param RuleManager $rule_manager
     * @param JobSchedulerInterface $job_scheduler
     * @throws InvalidInterfaceException
     */
    public function __construct(RuleManager $rule_manager, JobSchedulerInterface $job_scheduler)
    {
        $this->job_scheduler = $job_scheduler;
        parent::__construct($rule_manager);
    }



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
    }



}
// End of file TriggerStrategyCron.php
// Location: /core/services/automated_actions/TriggerStrategyCron.php