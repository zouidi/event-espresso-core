<?php

namespace EventEspresso\core\domain\services\commands\datetime;

use EE_Datetime;
use EventEspresso\core\domain\services\capabilities\CapCheck;
use EventEspresso\core\domain\services\capabilities\CapCheckInterface;
use EventEspresso\core\services\commands\Command;
use EventEspresso\core\services\commands\CommandRequiresCapCheckInterface;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class DeleteDatetimeCommand
 * parent class DTO for passing data to DeleteDatetimeCommandHandler
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class DeleteDatetimeCommand extends Command implements CommandRequiresCapCheckInterface
{

    /**
     * @var EE_Datetime $datetime
     */
    private $datetime;



    /**
     * DeleteDatetimeCommand constructor.
     *
     * @param EE_Datetime $datetime
     */
    public function __construct(EE_Datetime $datetime)
    {
        $this->datetime = $datetime;
    }



    /**
     * @return EE_Datetime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }



    /**
     * @return CapCheckInterface
     */
    public function getCapCheck()
    {
        if (! $this->cap_check instanceof CapCheckInterface) {
            return new CapCheck('ee_delete_events', 'delete_datetime');
        }
        return $this->cap_check;
    }

}
// End of file DeleteDatetimeCommand.php
// Location: EventEspresso\core\domain\services\commands\datetime/DeleteDatetimeCommand.php