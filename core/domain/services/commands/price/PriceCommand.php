<?php

namespace EventEspresso\core\domain\services\commands\price;

use EventEspresso\core\domain\services\commands\EntityCommand;
use EventEspresso\core\entities\datetime\DatetimeFormat;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class PriceCommand
 * abstract parent DTO class for passing data to CommandHandlers that operate on Price objects
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class PriceCommand extends EntityCommand
{



    /**
     * PriceCommand constructor.
     *
     * @param array          $entity_data
     * @param DatetimeFormat $datetime_format
     */
    public function __construct(array $entity_data, DatetimeFormat $datetime_format)
    {
        parent::__construct('EE_Price', $entity_data, $datetime_format);
    }


}
// End of file CreatePriceCommand.php
// Location: core/domain/services/commands/price/PriceCommand.php