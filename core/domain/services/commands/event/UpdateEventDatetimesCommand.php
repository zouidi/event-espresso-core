<?php

namespace EventEspresso\core\domain\services\commands\event;

use EE_Event;
use EventEspresso\core\domain\services\capabilities\CapCheck;
use EventEspresso\core\domain\services\capabilities\CapCheckInterface;
use EventEspresso\core\entities\datetime\DatetimeFormat;
use EventEspresso\core\services\commands\Command;
use EventEspresso\core\services\commands\CommandRequiresCapCheckInterface;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class UpdateEventDatetimesCommand
 * DTO for supplying datetime data for an event to UpdateEventDatetimesCommandHandler
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class UpdateEventDatetimesCommand extends Command implements CommandRequiresCapCheckInterface
{

    /**
     * @var EE_Event $event
     */
    private $event;

    /**
     * @var array $datetime_data
     */
    private $datetime_data;

    /**
     * @var DatetimeFormat $datetime_format
     */
    private $datetime_format;



    /**
     * UpdateEventDatetimesCommand constructor.
     *
     * @param EE_Event $event
     * @param array    $datetime_data
     * @param DatetimeFormat $datetime_format
     */
    public function __construct(EE_Event $event, array $datetime_data, DatetimeFormat $datetime_format)
    {
        $this->event = $event;
        $this->datetime_data = $datetime_data;
        $this->datetime_format = $datetime_format;
    }



    /**
     * @return \EventEspresso\core\domain\services\capabilities\CapCheckInterface
     */
    public function getCapCheck()
    {
        if (! $this->cap_check instanceof CapCheckInterface) {
            return new CapCheck('ee_edit_events', 'update_event_datetimes');
        }
        return $this->cap_check;
    }



    /**
     * @return mixed
     */
    public function event()
    {
        return $this->event;
    }



    /**
     * @return array
     */
    public function datetimeData()
    {
        return $this->datetime_data;
    }



    /**
     * @return DateTimeFormat
     */
    public function getDateTimeFormat()
    {
        return $this->datetime_format;
    }



}
// End of file UpdateEventDatetimesCommand.php
// Location: core/services/commands/event/UpdateEventDatetimesCommand.php