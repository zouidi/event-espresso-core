<?php
namespace EventEspresso\core\services\automated_actions;

use DomainException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\conditional_logic\rules\QueryGenerator;

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
     * @var CronManager $cron_manager
     */
    protected static $cron_manager;

    /**
     * @var QueryGenerator $query_generator
     */
    protected static $query_generator;



    /**
     * @return CronManager
     * @throws InvalidInterfaceException
     */
    public static function getCronManager()
    {
        if (! AutomatedActionFactory::$cron_manager instanceof CronManager) {
            AutomatedActionFactory::$cron_manager = new CronManager();
        }
        return AutomatedActionFactory::$cron_manager;
    }



    /**
     * @return QueryGenerator
     * @throws InvalidInterfaceException
     */
    public static function getQueryGenerator()
    {
        if (! AutomatedActionFactory::$query_generator instanceof QueryGenerator) {
            AutomatedActionFactory::$query_generator = new QueryGenerator();
        }
        return AutomatedActionFactory::$query_generator;
    }



    /**
     * @param \stdClass $args
     * @return AutomatedAction
     * @throws \DomainException
     */
    public static function create(\stdClass $args)
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
                    AutomatedActionFactory::getQueryGenerator()
                );
                break;
            case 'daily' :
                $trigger = new TriggerStrategyDaily(
                    AutomatedActionFactory::getQueryGenerator(),
                    AutomatedActionFactory::getCronManager()
                );
                break;
            case 'hourly' :
                $trigger = new TriggerStrategyHourly(
                    AutomatedActionFactory::getQueryGenerator(),
                    AutomatedActionFactory::getCronManager()
                );
                break;
            case 'date' :
                $trigger = new TriggerStrategyDate(
                    AutomatedActionFactory::getQueryGenerator(),
                    AutomatedActionFactory::getCronManager()
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