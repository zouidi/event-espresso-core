<?php

namespace EventEspresso\core\domain\services\commands\price;

use EventEspresso\core\domain\services\capabilities\CapCheck;
use EventEspresso\core\domain\services\capabilities\CapCheckInterface;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class CreatePriceCommand
 * DTO for passing data to CreatePriceCommandHandler
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class CreatePriceCommand extends PriceCommand
{



    /**
     * @return CapCheckInterface
     */
    public function getCapCheck()
    {
        if (! $this->cap_check instanceof CapCheckInterface) {
            return new CapCheck('ee_edit_events', 'create_ticket_price');
        }
        return $this->cap_check;
    }



}
// End of file CreatePriceCommand.php
// Location: EventEspresso\core\domain\services\commands\price/CreatePriceCommand.php