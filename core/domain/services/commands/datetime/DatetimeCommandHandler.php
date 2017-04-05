<?php

namespace EventEspresso\core\domain\services\commands\datetime;

use EE_Datetime;
use EE_Error;
use EEH_DTT_Helper;
use EEM_Datetime;
use EventEspresso\core\services\commands\CommandHandler;
use InvalidArgumentException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class DatetimeCommandHandler
 * abstract parent class for CommandHandlers that operate on a Datetime
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class DatetimeCommandHandler extends CommandHandler
{



    /**
     * @var EEM_Datetime $datetime_model
     */
    protected $datetime_model;



    /**
     * DatetimeCommandHandler constructor.
     *
     * @param EEM_Datetime $datetime_model
     */
    public function __construct(EEM_Datetime $datetime_model)
    {
        $this->datetime_model = $datetime_model;
    }



    /**
     * simply ensures that all fields for the datetime model have values
     *
     * @param array $datetime_data
     * @return array
     * @throws InvalidArgumentException
     */
    protected function validateDatetimeData(array $datetime_data)
    {
        //trim all values to ensure any excess whitespace is removed.
        $datetime_data = array_map(
            function ($datetime_data) {
                return is_array($datetime_data) ? $datetime_data : trim($datetime_data);
            },
            $datetime_data
        );
        if (empty($datetime_data['DTT_EVT_start'])) {
            $datetime_data['DTT_EVT_start'] = date('Y-m-d g:i a');
        }
        if (empty($datetime_data['DTT_order'])) {
            throw new InvalidArgumentException(
                esc_html__('DTT_order is a required value and must be set.', 'event_espresso')
            );
        }
        return array(
            'DTT_ID'          => $this->validateArrayElement($datetime_data, 'DTT_ID'),
            'DTT_name'        => $this->validateArrayElement($datetime_data, 'DTT_name', ''),
            'DTT_description' => $this->validateArrayElement($datetime_data, 'DTT_description', ''),
            'DTT_EVT_start'   => $this->validateArrayElement(
                $datetime_data,
                'DTT_EVT_start',
                null,
                true
            ),
            'DTT_EVT_end'     => $this->validateArrayElement(
                $datetime_data,
                'DTT_EVT_end',
                $datetime_data['DTT_EVT_start']
            ),
            'DTT_reg_limit'   => $this->validateArrayElement($datetime_data, 'DTT_reg_limit', EE_INF),
            'DTT_sold'        => $this->validateArrayElement($datetime_data, 'DTT_sold', 0),
            'DTT_reserved'    => $this->validateArrayElement($datetime_data, 'DTT_reserved', 0),
            'DTT_is_primary'  => $this->validateArrayElement($datetime_data, 'DTT_is_primary', false),
            'DTT_order'       => $this->validateArrayElement(
                $datetime_data,
                'DTT_order',
                null,
                true
            ),
            'DTT_parent'      => $this->validateArrayElement($datetime_data, 'DTT_parent', 0),
            'DTT_deleted'     => $this->validateArrayElement($datetime_data, 'DTT_deleted', false),
        );
    }



    /**
     * ensures our dates are setup correctly
     * so that the end date is always equal or greater than the start date
     *
     * @param EE_Datetime $datetime
     * @throws EE_Error
     */
    protected function validateStartDate(EE_Datetime $datetime)
    {
        if ($datetime->get_raw('DTT_EVT_start') > $datetime->get_raw('DTT_EVT_end')) {
            $datetime->set_start_date($datetime->get('DTT_EVT_start'));
            EEH_DTT_Helper::date_time_add($datetime, 'DTT_EVT_end', 'days');
        }
    }

}
// End of file DatetimeCommandHandler.php
// Location: core/domain/services/commands/datetime/DatetimeCommandHandler.php