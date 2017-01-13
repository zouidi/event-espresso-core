<?php
namespace EventEspresso\core\services\automated_actions;

use DomainException;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\collections\Collection;
use EventEspresso\core\services\conditional_logic\rules\RuleManager;
use InvalidArgumentException;

defined('ABSPATH') || exit;



/**
 * Class TriggerStrategy
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class TriggerStrategy
{

    /**
     * @var RuleManager $rule_manager
     */
    private $rule_manager;

    /**
     * @var AutomatedActionInterface $automated_action
     */
    private $automated_action;

    /**
     * @var array $callback_args
     */
    private $callback_args = array();

    /**
     * @var boolean $pulled
     */
    private $pulled = false;

    /**
     * @var Collection $rules
     */
    private $rules;



    /**
     * TriggerStrategy constructor.
     *
     * @param RuleManager $rule_manager
     */
    public function __construct(RuleManager $rule_manager)
    {
        $this->rule_manager = $rule_manager;
    }



    /**
     * @return AutomatedActionInterface
     */
    public function getAutomatedAction()
    {
        return $this->automated_action;
    }



    /**
     * @param \EventEspresso\core\services\automated_actions\AutomatedActionInterface $automated_action
     */
    public function setAutomatedAction(AutomatedActionInterface $automated_action) {
        $this->automated_action = $automated_action;
    }




    /**
     * @return array
     */
    public function getCallbackArgs()
    {
        return $this->callback_args;
    }



    /**
     * @param array $callback_args
     */
    public function setCallbackArgs(array $callback_args = array())
    {
        $this->callback_args = $callback_args;
    }



    /**
     * @return bool
     */
    public function pulled()
    {
        return $this->pulled;
    }



    /**
     * @param bool $pulled
     */
    public function setPulled($pulled = true)
    {
        $this->pulled = filter_var($pulled, FILTER_VALIDATE_BOOLEAN);
    }



    /**
     * @return Collection
     * @throws InvalidArgumentException
     * @throws InvalidInterfaceException
     * @throws InvalidEntityException
     * @throws DomainException
     */
    public function getRules()
    {
        if ($this->rules === null) {
            $this->setRules();
        }
        return $this->rules;
    }



    /**
     * @return void
     * @throws InvalidArgumentException
     * @throws InvalidInterfaceException
     * @throws InvalidEntityException
     * @throws DomainException
     */
    private function setRules()
    {
        $this->rules = $this->rule_manager->retrieveRulesForObject(
            'Automated Action',
            $this->automated_action->getID()
        );
    }



    /**
     * This is required and should be overridden by all non-cron type triggers,
     * as it allows strategies to set up whatever hooks are necessary to trigger their logic.
     * Cron related strategies do not need to implement this method
     * as this one will be used and do nothing but capture the incoming data
     *
     * @param AutomatedActionInterface $automated_action
     */
    public function set(AutomatedActionInterface $automated_action)
    {
        $this->automated_action = $automated_action;
    }



    /**
     * callback method for all strategies (regardless of type),
     * that runs when the trigger is "pulled".
     * At this point strategies should ONLY proceed to collect data
     * required for the action, store it for later processing,
     * and call setPulled() to indicate that the trigger was successful
     * (meaning data results for the action were retrieved).
     * This allows control over when that happens
     * because the moment the trigger is pulled
     * may not be the most optimum time for data processing.
     * By default, logic processing will occur during "shutdown",
     * but can be overridden by an AutomatedActionStrategy class
     *
     * @throws InvalidArgumentException
     * @throws InvalidInterfaceException
     * @throws InvalidEntityException
     * @throws DomainException
     */
    public function triggerCallback() {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        $this->setCallbackArgs(func_get_args());
        $this->execute(
            $this->getQueryParamsForRules()
        );
    }



    /**
     * @return array
     * @throws InvalidArgumentException
     * @throws InvalidInterfaceException
     * @throws InvalidEntityException
     * @throws DomainException
     */
    public function getQueryParamsForRules()
    {
        return $this->rule_manager->getQueryParamsForRules(
            $this->getRules()
        );
    }



    /**
     * when a trigger has been "pulled",
     * either by a do_action or scheduled cron,
     * this is the TriggerStrategy method that will run
     * to continue processing the trigger.
     * This will typically mean retrieving objects from the db.
     * By default, this method does nothing but set callback args
     * to be used for querying during the 'shutdown' hook when
     * AutomatedActionManager::processActions() runs,
     * but can be overridden to perform querying now.
     *
     * @param array $query_params
     * @return void
     */
    public function execute(array $query_params)
    {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        $this->setCallbackArgs(
            array_merge($this->callback_args, array('query_params' => $query_params))
        );
        // \EEH_Debug_Tools::printr($this->callback_args, '$this->callback_args', __FILE__, __LINE__);
        $this->setPulled();
    }


}
// End of file TriggerStrategy.php
// Location: /core/services/automated_actions/TriggerStrategy.php