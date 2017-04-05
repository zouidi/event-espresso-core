<?php

namespace EventEspresso\core\domain\services\commands\event;

use DomainException;
use EE_Datetime;
use EE_Error;
use EE_Event;
use EEM_Datetime;
use EventEspresso\core\domain\services\commands\datetime\CreateDatetimeCommand;
use EventEspresso\core\domain\services\commands\datetime\DeleteDatetimeCommand;
use EventEspresso\core\domain\services\commands\datetime\UpdateDatetimeCommand;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\services\commands\CommandInterface;
use EventEspresso\core\services\commands\CompositeCommandHandler;
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
class UpdateEventDatetimesCommandHandler extends CompositeCommandHandler
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
     * @throws \EventEspresso\core\services\commands\middleware\InvalidCommandBusMiddlewareException
     * @throws \EventEspresso\core\exceptions\InvalidDataTypeException
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
                $datetime = $this->commandBus()->execute(
                    new UpdateDatetimeCommand($datetime_data, $timezone, $date_and_time_formats)
                );
                // make sure the $DTT_ID here is saved in case the autosave replaces it after the add_relation_to().
                // We need to do this so we dont' TRASH the parent DTT.
                // save the ID for both key and value to avoid duplications
                $this->updated[$datetime->ID()] = $datetime;
            } else {
                $date_and_time_formats['DTT_order'] = $row;
                $datetime = $this->commandBus()->execute(
                    new CreateDatetimeCommand($datetime_data, $timezone, $date_and_time_formats)
                );
            }
            $DTT_ID = $datetime->ID();
            if ($DTT_ID > 0) {
                $this->event->_add_relation_to($datetime, 'Datetime');
                $this->event->save();
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
     * removes any datetimes that have been deleted, as determined by the passed array of IDs
     *
     * @param array $datetime_IDs
     * @throws EE_Error
     * @throws \EventEspresso\core\exceptions\InvalidDataTypeException
     * @throws \EventEspresso\core\services\commands\middleware\InvalidCommandBusMiddlewareException
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
                $this->commandBus()->execute(
                    new DeleteDatetimeCommand($datetime_to_remove)
                );
            }
        }
    }



}
// End of file UpdateEventDatetimesCommandHandler.php
// Location: core/services/commands/event/UpdateEventDatetimesCommandHandler.php