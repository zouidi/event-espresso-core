<?php

namespace EventEspresso\core\domain\services\commands\datetime;

use EE_Datetime;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\services\commands\CommandInterface;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class UpdateDatetimeCommandHandler
 * Service class for updating a datetime
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class UpdateDatetimeCommandHandler extends DatetimeCommandHandler
{



    /**
     * @param CommandInterface $command
     * @return EE_Datetime
     * @throws \EE_Error
     * @throws \InvalidArgumentException
     * @throws InvalidEntityException
     */
    public function handle(CommandInterface $command)
    {
        /** @var UpdateDatetimeCommand $command */
        if (! $command instanceof UpdateDatetimeCommand) {
            throw new InvalidEntityException(get_class($command), 'UpdateDatetimeCommand');
        }
        $datetime_data = $command->getDatetimeData();
        $timezone = $command->getTimezone();
        $date_and_time_formats = $command->getDateAndTimeFormats();
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



}
// End of file UpdateDatetimeCommandHandler.php
// Location: EventEspresso\core\domain\services\commands\datetime/UpdateDatetimeCommandHandler.php