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
        /** @var CreateDatetimeCommand $command */
        if (! $command instanceof CreateDatetimeCommand) {
            throw new InvalidEntityException(get_class($command), 'CreateDatetimeCommand');
        }
        return $this->createEntity(
            $command,
            array($this, 'validateDatetimeData')
        );
    }



}
// End of file CreateDatetimeCommandHandler.php
// Location: core/domain/services/commands/datetime/CreateDatetimeCommandHandler.php