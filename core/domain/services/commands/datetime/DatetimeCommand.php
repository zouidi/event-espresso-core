<?php

namespace EventEspresso\core\domain\services\commands\datetime;

use EventEspresso\core\services\commands\Command;
use EventEspresso\core\services\commands\CommandRequiresCapCheckInterface;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class DatetimeCommand
 * parent class DTO for passing data to UpdateDatetimeCommandHandler
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class DatetimeCommand extends Command implements CommandRequiresCapCheckInterface
{

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
     * UpdateDatetimeCommand constructor.
     *
     * @param array  $datetime_data
     * @param string $timezone
     * @param array  $date_and_time_formats
     */
    public function __construct(array $datetime_data, $timezone, array $date_and_time_formats)
    {
        $this->datetime_data = $datetime_data;
        $this->timezone = $timezone;
        $this->date_and_time_formats = $date_and_time_formats;
    }



    /**
     * @return array
     */
    public function getDatetimeData()
    {
        return $this->datetime_data;
    }



    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }



    /**
     * @return array
     */
    public function getDateAndTimeFormats()
    {
        return $this->date_and_time_formats;
    }



}
// End of file UpdateDatetimeCommand.php
// Location: core/domain/services/commands/datetime/UpdateDatetimeCommand.php