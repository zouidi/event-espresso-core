<?php
namespace EventEspresso\core\services\automated_actions;

use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\conditional_logic\rules\QueryGenerator;

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
     * @param QueryGenerator $query_generator
     * @param CronManager    $cron_manager
     * @throws InvalidInterfaceException
     */
    public function __construct(QueryGenerator $query_generator, CronManager $cron_manager)
    {
        $this->cron_manager = $cron_manager;
        parent::__construct($query_generator);
    }


}
// End of file TriggerStrategyCron.php
// Location: /core/services/automated_actions/TriggerStrategyCron.php