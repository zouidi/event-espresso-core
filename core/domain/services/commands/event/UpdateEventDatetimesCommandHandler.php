<?php

namespace EventEspresso\core\services\commands\event;

use DomainException;
use EE_Datetime;
use EE_Error;
use EE_Event;
use EEH_DTT_Helper;
use EEM_Datetime;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\services\commands\CommandHandler;
use EventEspresso\core\services\commands\CommandInterface;
use InvalidArgumentException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class UpdateEventDatetimesCommandHandler
 * Adds/Updates/Deletes datetimes for an event
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class UpdateEventDatetimesCommandHandler extends CommandHandler
{


    /**
     * @var EEM_Datetime $datetime_model
     */
    protected $datetime_model;

    /**
     * @var EE_Event $event
     */
    protected $event;

    /**
     * @var array $updated
     */
    private $updated = array();

    /**
     * @var EE_Datetime[] $datetimes
     */
    private $datetimes = array();



    /**
     * EventDatetimeService constructor.
     *
     * @param EEM_Datetime $datetime_model
     */
    public function __construct(EEM_Datetime $datetime_model)
    {
        $this->datetime_model = $datetime_model;
    }



    /**
     * @param CommandInterface $command
     * @return mixed
     * @throws InvalidArgumentException
     * @throws EE_Error
     * @throws DomainException
     * @throws InvalidEntityException
     */
    public function handle(CommandInterface $command)
    {
        /** @var UpdateEventDatetimesCommand $command */
        if (! $command instanceof UpdateEventDatetimesCommand) {
            throw new InvalidEntityException(get_class($command), 'UpdateEventDatetimesCommand');
        }
        $this->event = $command->event();
        $datetime_data_array = $command->datetimeData();
        $timezone = $command->timezone();
        $date_and_time_formats = $command->dateAndTimeFormats();
        foreach ($datetime_data_array as $row => $datetime_data) {
            //if we have an id then let's get existing object first and then set the new values.  Otherwise we instantiate a new object for save.
            if (! empty($datetime_data['DTT_ID'])) {
                $datetime = $this->updateDatetime($datetime_data, $timezone, $date_and_time_formats);
                // make sure the $dtt_id here is saved the autosave replaces it
                // just in case after the add_relation_to().
                // We need to do this so we dont' TRASH the parent DTT.
                // save the ID for both key and value to avoid duplications
                $this->updated[$datetime->ID()] = $datetime;
            } else {
                $date_and_time_formats['DTT_order'] = $row;
                $datetime = $this->createDatetime($datetime_data, $timezone, $date_and_time_formats);
            }
            $DTT_ID = $datetime->ID();
            if ($DTT_ID > 0) {
                $this->updateEventAfterDatetimeSave($datetime);
            }
            // now we have to make sure we add the new DTT_ID to the $saved_datetime_ids array
            // because it is possible there was a new one created during an autosave or something.
            // (save the ID for both key and value to avoid duplications)
            $this->updated[$DTT_ID] = $datetime;
            $this->datetimes[$row] = $datetime;
        }
        if (! empty($datetime_data['datetime_IDs'])) {
            $this->deleteDatetimes($datetime_data['datetime_IDs']);
        }
        return $this->datetimes;
    }



    /**
     * @param array $datetime_data
     * @param null  $timezone
     * @param array $date_and_time_formats
     * @return EE_Datetime
     * @throws \DomainException
     * @throws EE_Error
     * @throws InvalidArgumentException
     */
    private function updateDatetime(
        array $datetime_data,
        $timezone = null,
        array $date_and_time_formats
    ) {
        $datetime_data = apply_filters(
            'AFEE__EventEspresso\core\domain\services\event\EventDatetimesService__updateDatetime__datetime_data',
            $this->validateDatetimeData($datetime_data),
            $timezone,
            $date_and_time_formats,
            $this
        );
        /** @var EE_Datetime $datetime */
        $datetime = $this->datetime_model->get_one_by_ID($datetime_data['DTT_ID']);
        //set date and time format according to what is set in this class.
        $datetime->set_date_format($date_and_time_formats['date']);
        $datetime->set_time_format($date_and_time_formats['time']);
        foreach ($datetime_data as $field => $value) {
            $datetime->set($field, $value);
        }
        $datetime->save();
        do_action(
            'AHEE__EventEspresso\core\domain\services\event\EventDatetimesService__updateDatetime__datetime_updated',
            $datetime,
            $datetime_data,
            $timezone,
            $date_and_time_formats,
            $this
        );
        return $datetime;
    }



    /**
     * @param array $datetime_data
     * @param null  $timezone
     * @param array $date_and_time_formats
     * @return EE_Datetime
     * @throws \DomainException
     * @throws EE_Error
     * @throws InvalidArgumentException
     */
    private function createDatetime(
        array $datetime_data,
        $timezone = null,
        array $date_and_time_formats
    ) {
        $datetime_data = apply_filters(
            'AFEE__EventEspresso\core\domain\services\event\EventDatetimesService__createDatetime__datetime_data',
            $this->validateDatetimeData($datetime_data),
            $timezone,
            $date_and_time_formats,
            $this
        );
        $datetime = EE_Datetime::new_instance(
            $datetime_data,
            $timezone,
            $date_and_time_formats
        );
        $this->validateStartDate($datetime);
        $datetime->save();
        do_action(
            'AHEE__EventEspresso\core\domain\services\event\EventDatetimesService__createDatetime__new_datetime',
            $datetime,
            $datetime_data,
            $timezone,
            $date_and_time_formats,
            $this
        );
        return $datetime;
    }



    /**
     * removes any datetimes that have been deleted, as determined by the passed array of IDs
     *
     * @param array $datetime_IDs
     * @throws EE_Error
     */
    private function deleteDatetimes(array $datetime_IDs)
    {
        // array_filter() with no callback removes empty entries
        $datetimes_to_remove = array_filter($datetime_IDs);
        if (! empty($datetimes_to_remove)) {
            $datetimes_to_remove = array_diff($datetimes_to_remove, array_keys($this->updated));
            foreach ($datetimes_to_remove as $datetime_ID) {
                $datetime_ID = absint($datetime_ID);
                if (empty($datetime_ID)) {
                    continue;
                }
                /** @var EE_Datetime $datetime_to_remove */
                $datetime_to_remove = isset($this->updated[$datetime_ID])
                    ? $this->updated[$datetime_ID]
                    : $this->datetime_model->get_one_by_ID($datetime_ID);
                $this->deleteDatetime($datetime_to_remove);
            }
        }
    }



    /**
     * removes all relations between the datetime event and tickets,
     * then deletes it, but only if the datetime's DTT_sold equals zero
     *
     * @param EE_Datetime $datetime_to_remove
     * @throws EE_Error
     */
    private function deleteDatetime(EE_Datetime $datetime_to_remove)
    {
        if ($datetime_to_remove->sold()) {
            return;
        }
        do_action(
            'AHEE__EventEspresso\core\domain\services\event\EventDatetimesService__deleteDatetime__datetime',
            $datetime_to_remove,
            $this
        );
        //remove tkt relationships.
        $related_tickets = $datetime_to_remove->get_many_related('Ticket');
        foreach ($related_tickets as $ticket) {
            $datetime_to_remove->_remove_relation_to($ticket, 'Ticket');
        }
        $this->event->_remove_relation_to($datetime_to_remove->ID(), 'Datetime');
        $datetime_to_remove->refresh_cache_of_related_objects();
        $datetime_to_remove->delete_permanently();
    }



    /**
     * simply ensures that all fields for the datetime model have values
     *
     * @param array $datetime_data
     * @return array
     * @throws InvalidArgumentException
     */
    private function validateDatetimeData(array $datetime_data)
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
     * @throws \EE_Error
     */
    private function validateStartDate(EE_Datetime $datetime)
    {
        if ($datetime->get_raw('DTT_EVT_start') > $datetime->get_raw('DTT_EVT_end')) {
            $datetime->set_start_date($datetime->get('DTT_EVT_start'));
            EEH_DTT_Helper::date_time_add($datetime, 'DTT_EVT_end', 'days');
        }
    }



    /**
     * Simply adds an intersection between the the supplied datetime and the event it was created for
     *
     * @param EE_Datetime $datetime
     * @throws \EE_Error
     */
    private function updateEventAfterDatetimeSave(EE_Datetime $datetime)
    {
        $this->event->_add_relation_to($datetime, 'Datetime');
        $this->event->save();
    }



}
// End of file UpdateEventDatetimesCommandHandler.php
// Location: core/services/commands/event/UpdateEventDatetimesCommandHandler.php