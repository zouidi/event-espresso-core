<?php

namespace EventEspresso\core\domain\services\commands\datetime;

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


}
// End of file DatetimeCommandHandler.php
// Location: core/domain/services/commands/datetime/DatetimeCommandHandler.php