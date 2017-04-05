<?php

namespace EventEspresso\core\domain\services\commands\datetime;

use EE_Datetime;
use EE_Error;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\services\commands\CommandInterface;
use InvalidArgumentException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class CreateDatetimeCommandHandler
 * Service class for creating a datetime
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class CreateDatetimeCommandHandler extends DatetimeCommandHandler
{



    /**
     * @param CommandInterface $command
     * @return EE_Datetime
     * @throws InvalidArgumentException
     * @throws EE_Error
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
            'AFEE__EventEspresso\core\domain\services\commands\datetime\CreateDatetimeCommandHandler__handle__datetime_data',
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
            'AHEE__EventEspresso\core\domain\services\commands\datetime\CreateDatetimeCommandHandler__handle__new_datetime',
            $datetime,
            $datetime_data,
            $timezone,
            $date_and_time_formats,
            $this
        );
        return $datetime;
    }



}
// End of file CreateDatetimeCommandHandler.php
// Location: core/domain/services/commands/datetime/CreateDatetimeCommandHandler.php