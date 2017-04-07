<?php

namespace EventEspresso\core\domain\services\commands\datetime;

use EE_Datetime;
use EEM_Datetime;
use EventEspresso\core\domain\services\commands\EntityCommandHandler;
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
abstract class DatetimeCommandHandler extends EntityCommandHandler
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
     * @param array       $datetime_data
     * @param EE_Datetime $datetime
     * @return array
     * @throws InvalidArgumentException
     */
    protected function validateDatetimeData(array $datetime_data, EE_Datetime $datetime = null)
    {
        //trim all values to ensure any excess whitespace is removed.
        $datetime_data = array_map(
            function ($datetime_data) {
                return is_array($datetime_data) ? $datetime_data : trim($datetime_data);
            },
            $datetime_data
        );
        // ensure start date is set. default is now
        $datetime_data['DTT_EVT_start'] = $this->validateArrayElement(
            $datetime_data,
            'DTT_EVT_start',
            $datetime instanceof EE_Datetime ? $datetime->start_date_and_time() : date('Y-m-d g:i a'),
            true
        );
        // ensure end date is set. default = start date
        $datetime_data['DTT_EVT_end'] = $this->validateArrayElement(
            $datetime_data,
            'DTT_EVT_end',
            $datetime instanceof EE_Datetime ? $datetime->end_date_and_time() : $datetime_data['DTT_EVT_start'],
            true
        );
        //ensure start date is earlier than end date
        $start = strtotime($datetime_data['DTT_EVT_start']);
        $end = strtotime($datetime_data['DTT_EVT_end']);
        if ($end <= $start) {
            $end += DAY_IN_SECONDS;
            $datetime_data['DTT_EVT_end'] = date('Y-m-d g:i a', $end);
        }
        if (empty($datetime_data['DTT_order'])) {
            throw new InvalidArgumentException(
                esc_html__('DTT_order is a required value and must be set.', 'event_espresso')
            );
        }

        return array(
            'DTT_ID'          => $this->validateArrayElement($datetime_data, 'DTT_ID'),
            'DTT_name'        => $this->validateArrayElement(
                $datetime_data,
                'DTT_name',
                $datetime instanceof EE_Datetime ? $datetime->name() : ''
            ),
            'DTT_description' => $this->validateArrayElement(
                $datetime_data,
                'DTT_description',
                $datetime instanceof EE_Datetime ? $datetime->description() : ''
            ),
            'DTT_EVT_start'   => $datetime_data['DTT_EVT_start'],
            'DTT_EVT_end'     => $datetime_data['DTT_EVT_end'],
            'DTT_reg_limit'   => $this->validateArrayElement(
                $datetime_data,
                'DTT_reg_limit',
                $datetime instanceof EE_Datetime ? $datetime->reg_limit() : EE_INF
            ),
            'DTT_sold'        => $this->validateArrayElement(
                $datetime_data,
                'DTT_sold',
                $datetime instanceof EE_Datetime ? $datetime->sold() : 0
            ),
            'DTT_reserved'    => $this->validateArrayElement(
                $datetime_data,
                'DTT_reserved',
                $datetime instanceof EE_Datetime ? $datetime->reserved() : 0
            ),
            'DTT_is_primary'  => $this->validateArrayElement(
                $datetime_data,
                'DTT_is_primary',
                $datetime instanceof EE_Datetime ? $datetime->is_primary() : false
            ),
            'DTT_order'       => $this->validateArrayElement(
                $datetime_data,
                'DTT_order',
                $datetime instanceof EE_Datetime ? $datetime->order() : null,
                true
            ),
            'DTT_parent'      => $this->validateArrayElement(
                $datetime_data,
                'DTT_parent',
                $datetime instanceof EE_Datetime ? $datetime->parent() : 0
            ),
            'DTT_deleted'     => $this->validateArrayElement(
                $datetime_data,
                'DTT_deleted',
                $datetime instanceof EE_Datetime ? $datetime->deleted() : false
            ),
        );
    }


}
// End of file DatetimeCommandHandler.php
// Location: core/domain/services/commands/datetime/DatetimeCommandHandler.php