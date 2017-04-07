<?php

namespace EventEspresso\core\domain\services\commands\price;

use EventEspresso\core\domain\services\capabilities\CapCheck;
use EventEspresso\core\domain\services\capabilities\CapCheckInterface;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class UpdatePriceCommand
 * DTO for passing data to UpdatePriceCommandHandler
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class UpdatePriceCommand extends PriceCommand
{



    /**
     * @return CapCheckInterface
     */
    public function getCapCheck()
    {
        if (! $this->cap_check instanceof CapCheckInterface) {
            return new CapCheck('ee_edit_events', 'update_ticket_price');
        }
        return $this->cap_check;
    }


}
// End of file UpdatePriceCommand.php
// Location: EventEspresso\core\domain\services\commands\price/UpdatePriceCommand.php