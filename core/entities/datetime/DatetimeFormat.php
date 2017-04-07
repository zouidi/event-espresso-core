<?php

namespace EventEspresso\core\entities\datetime;

use EventEspresso\core\exceptions\InvalidDataTypeException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class DatetimeFormat
 * DTO for passing around a timezone string plus date and time formats for later use in generating a DateTime
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class DatetimeFormat
{


    /**
     * @var string $timezone_string
     */
    private $timezone_string;

    /**
     * @var string $date_format
     */
    private $date_format;

    /**
     * @var string $time_format
     */
    private $time_format;



    /**
     * DatetimeFormat constructor.
     *
     * @param string $timezone_string
     * @param string $date_format
     * @param string $time_format
     * @throws InvalidDataTypeException
     */
    public function __construct($timezone_string = 'UTC', $date_format = 'Y-m-d', $time_format = 'H:i:s')
    {
        $this->setTimezoneString($timezone_string);
        $this->setDateFormat($date_format);
        $this->setTimeFormat($time_format);
    }



    /**
     * @return string
     */
    public function getTimezoneString()
    {
        return $this->timezone_string;
    }



    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->date_format;
    }



    /**
     * @return string
     */
    public function getTimeFormat()
    {
        return $this->time_format;
    }



    /**
     * @param string $separator
     * @return string
     */
    public function getDateAndTimeFormat($separator = ' ')
    {
        return $this->date_format . $separator . $this->time_format;
    }



    /**
     * @return array
     */
    public function getDateAndTimeFormatArray()
    {
        return array($this->date_format, $this->time_format);
    }



    /**
     * @param string $timezone_string
     * @throws InvalidDataTypeException
     */
    public function setTimezoneString($timezone_string)
    {
        if (! is_string($timezone_string)) {
            throw new InvalidDataTypeException('$timezone_string', $timezone_string, 'string');
        }
        $this->timezone_string = $timezone_string;
    }



    /**
     * @param string $date_format
     * @throws InvalidDataTypeException
     */
    public function setDateFormat($date_format)
    {
        if (! is_string($date_format)) {
            throw new InvalidDataTypeException('$date_format', $date_format, 'string');
        }
        $this->date_format = $date_format;
    }



    /**
     * @param string $time_format
     * @throws InvalidDataTypeException
     */
    public function setTimeFormat($time_format)
    {
        if (! is_string($time_format)) {
            throw new InvalidDataTypeException('$time_format', $time_format, 'string');
        }
        $this->time_format = $time_format;
    }



}
// End of file DatetimeFormat.php
// Location: core/entities/datetime/DatetimeFormat.php