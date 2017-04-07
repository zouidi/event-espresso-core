<?php

namespace EventEspresso\core\domain\services\commands\datetime;

use DomainException;
use EE_Datetime;
use EE_Error;
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
     * @throws InvalidEntityException
     * @throws DomainException
     * @throws EE_Error
     */
    public function handle(CommandInterface $command)
    {
        /** @var UpdateDatetimeCommand $command */
        if (! $command instanceof UpdateDatetimeCommand) {
            throw new InvalidEntityException(get_class($command), 'UpdateDatetimeCommand');
        }
        return $this->updateEntity($command, array($this, 'validateDatetimeData'), $this->datetime_model);
    }



}
// End of file UpdateDatetimeCommandHandler.php
// Location: EventEspresso\core\domain\services\commands\datetime/UpdateDatetimeCommandHandler.php