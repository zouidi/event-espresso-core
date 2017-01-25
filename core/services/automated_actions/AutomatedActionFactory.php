<?php
namespace EventEspresso\core\services\automated_actions;

use DomainException;
use EventEspresso\core\services\conditional_logic\rules\RuleManager;

defined('ABSPATH') || exit;



/**
 * Class AutomatedActionFactory
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class AutomatedActionFactory
{

    /**
     * @var JobSchedulerInterface $job_scheduler
     */
    protected $job_scheduler;

    /**
     * @var RuleManager $rule_manager
     */
    protected $rule_manager;



    /**
     * AutomatedActionFactory constructor
     *
     * @param JobSchedulerInterface $job_scheduler
     * @param RuleManager $rule_manager
     */
    public function __construct(JobSchedulerInterface $job_scheduler, RuleManager $rule_manager) {
        $this->job_scheduler = $job_scheduler;
        $this->rule_manager = $rule_manager;
    }



    /**
     * @param \stdClass $args
     * @return AutomatedAction
     * @throws \DomainException
     */
    public function create(\stdClass $args)
    {
        if (! isset($args->AMA_strategy, $args->AMA_trigger)) {
            throw new DomainException(
                esc_html__('Can not create AutomatedAction without specifying a strategy or trigger.', 'event_espresso')
            );
        }
        if (! class_exists($args->AMA_strategy)) {
            throw new DomainException(
                sprintf(
                    esc_html__('The "%1$s" class can not be located.', 'event_espresso'),
                    $args->AMA_strategy
                )
            );
        }
        $strategy = $args->AMA_strategy;
        $strategy = new $strategy();
        if ( ! $strategy instanceof AutomatedActionStrategy) {
            throw new DomainException(
                sprintf(
                    esc_html__('"%1$s" is not a valid AutomatedActionStrategy class.', 'event_espresso'),
                    $args->AMA_strategy
                )
            );
        }
        // todo: triggers need to go into their own factory so we can remove some dependencies here
        switch ($args->AMA_trigger) {
            case 'hook' :
                $trigger = new TriggerStrategyHook(
                    $this->rule_manager
                );
                break;
            case 'daily' :
                $trigger = new TriggerStrategyDaily(
                    $this->rule_manager,
                    $this->job_scheduler
                );
                break;
            case 'hourly' :
                $trigger = new TriggerStrategyHourly(
                    $this->rule_manager,
                    $this->job_scheduler
                );
                break;
            case 'date' :
                $trigger = new TriggerStrategyDate(
                    $this->rule_manager,
                    $this->job_scheduler
                );
                break;
            default :
                throw new DomainException(
                    sprintf(
                        esc_html__('"%1$s" is either missing or an invalid TriggerStrategy class',
                            'event_espresso'),
                        $args->AMA_trigger
                    )
                );
        }

        return new AutomatedAction($args, $strategy, $trigger);
    }

}
// End of file AutomatedActionFactory.php
// Location: EventEspresso\core\services\automated_actions/AutomatedActionFactory.php