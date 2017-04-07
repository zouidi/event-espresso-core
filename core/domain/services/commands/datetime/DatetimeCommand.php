<?php

namespace EventEspresso\core\domain\services\commands\datetime;

use EventEspresso\core\domain\services\commands\EntityCommand;
use EventEspresso\core\entities\datetime\DatetimeFormat;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class DatetimeCommand
 * parent class DTO for passing data to DatetimeCommandHandler classes
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class DatetimeCommand extends EntityCommand
{


    /**
     * UpdateDatetimeCommand constructor.
     *
     * @param array          $entity_data
     * @param DatetimeFormat $datetime_format
     */
    public function __construct(array $entity_data, DatetimeFormat $datetime_format)
    {
        parent::__construct('EE_Datetime', $entity_data, $datetime_format);
    }




}
// End of file UpdateDatetimeCommand.php
// Location: core/domain/services/commands/datetime/UpdateDatetimeCommand.php