<?php

namespace EventEspresso\core\domain\services\commands\event;

use EE_Event;
use EventEspresso\core\domain\services\capabilities\CapCheck;
use EventEspresso\core\domain\services\capabilities\CapCheckInterface;
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
     * @var string $timezone
     */
    private $timezone;

    /**
     * @var array $date_and_time_formats
     */
    private $date_and_time_formats;



    /**
     * UpdateEventDatetimesCommand constructor.
     *
     * @param EE_Event $event
     * @param array    $datetime_data
     * @param string   $timezone
     * @param array    $date_and_time_formats
     */
    public function __construct(EE_Event $event, array $datetime_data, $timezone, array $date_and_time_formats)
    {
        $this->event = $event;
        $this->datetime_data = $datetime_data;
        $this->timezone = $timezone;
        $this->date_and_time_formats = $date_and_time_formats;
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
     * @return string
     */
    public function timezone()
    {
        return $this->timezone;
    }



    /**
     * @return array
     */
    public function dateAndTimeFormats()
    {
        return $this->date_and_time_formats;
    }



}
// End of file UpdateEventDatetimesCommand.php
// Location: core/services/commands/event/UpdateEventDatetimesCommand.php