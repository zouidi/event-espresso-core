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
     * @var $cron_manager CronManager
     */
    protected $cron_manager;



    /**
     * TriggerStrategyCron constructor.
     *
     * @param RuleManager $rule_manager
     * @param CronManager   $cron_manager
     * @throws InvalidInterfaceException
     */
    public function __construct(RuleManager $rule_manager, CronManager $cron_manager)
    {
        $this->cron_manager = $cron_manager;
        parent::__construct($rule_manager);
    }


}
// End of file TriggerStrategyCron.php
// Location: /core/services/automated_actions/TriggerStrategyCron.php