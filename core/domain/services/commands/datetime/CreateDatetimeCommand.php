<?php

namespace EventEspresso\core\domain\services\commands\datetime;

use EventEspresso\core\domain\services\capabilities\CapCheck;
use EventEspresso\core\domain\services\capabilities\CapCheckInterface;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class CreateDatetimeCommand
 * DTO for passing data to CreateDatetimeCommandHandler
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class CreateDatetimeCommand extends DatetimeCommand
{


    /**
     * @return CapCheckInterface
     */
    public function getCapCheck()
    {
        if (! $this->cap_check instanceof CapCheckInterface) {
            return new CapCheck('ee_edit_events', 'create_datetime');
        }
        return $this->cap_check;
    }

}
// End of file CreateDatetimeCommand.php
// Location: core/domain/services/commands/datetime/CreateDatetimeCommand.php