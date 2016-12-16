<?php
namespace EventEspresso\core\services\automated_actions;

use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\Benchmark;
use EventEspresso\core\services\collections\Collection;
use EventEspresso\core\services\conditional_logic\rules\QueryParamGenerator;
use EventEspresso\core\services\conditional_logic\rules\Rule;

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
     * @var QueryParamGenerator $query_generator
     */
    private $query_generator;

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
     * TriggerStrategy constructor

     *
*@param QueryParamGenerator $query_generator
     * @throws InvalidInterfaceException
     */
    public function __construct(QueryParamGenerator $query_generator)
    {
        Benchmark::startTimer(__METHOD__);
        $this->query_generator = $query_generator;
        $this->rules = new Collection('EventEspresso\core\services\conditional_logic\rules\Rule');
        Benchmark::stopTimer(__METHOD__);
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
     */
    public function getRules()
    {
        return $this->rules;
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
        // \EEH_Debug_Tools::printr($this->automated_action, '$this->automated_action', __FILE__, __LINE__);
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
     * @throws InvalidEntityException
     * @throws \InvalidArgumentException
     */
    public function triggerCallback() {
        $this->setCallbackArgs(func_get_args());
        $this->generateRuleObjects(
            $this->retrieveRules()
        );
        $this->generateRulesQuery();
        $this->execute();
    }



    protected function retrieveRules() {
        Benchmark::startTimer(__METHOD__);
        global $wpdb;
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}esp_object_rule AS object_rule
                 JOIN {$wpdb->prefix}esp_rule AS rule
                 ON object_rule.RUL_ID = rule.RUL_ID
                 WHERE object_rule.ORL_OBJ_name = 'Automated Action'
                 AND object_rule.ORL_OBJ_ID = %d
                 ORDER BY object_rule.ORL_order",
                $this->automated_action->getID()
            )
        );
        if ($results instanceof \WP_Error) {
            throw new \DomainException(
                $results->get_error_message()
            );
        }
        $results = is_array($results) ? $results : array($results);
        Benchmark::stopTimer(__METHOD__);
        return $results;
    }



    /**
     * @param \stdClass[] $results
     * @return void
     * @throws \InvalidArgumentException
     * @throws InvalidEntityException
     */
    protected function generateRuleObjects(array $results)
    {
        Benchmark::startTimer(__METHOD__);
        foreach ($results as $result) {
            $this->rules->add(new Rule($result));
        }
        Benchmark::stopTimer(__METHOD__);
    }



    protected function generateRulesQuery()
    {
        $this->query_generator->addRules($this->rules);
        $SQL = $this->query_generator->getSql();
        \EEH_Debug_Tools::printr($SQL, '$SQL', __FILE__, __LINE__);
    }

    /**
     * when a trigger has been "pulled",
     * either by a do_action or scheduled cron,
     * this is the TriggerStrategy method that will run
     * to continue processing the trigger.
     * This will typically mean retrieving objects from the db
     */
    abstract public function execute();


}
// End of file TriggerStrategy.php
// Location: /core/services/automated_actions/TriggerStrategy.php