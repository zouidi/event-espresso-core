<?php

namespace EventEspresso\core\domain\services\commands\price;

use EE_Error;
use EE_Price;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\services\commands\CommandInterface;
use InvalidArgumentException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class CreatePriceCommandHandler
 * abstract parent Service class for creating a new EE_Price object
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class CreatePriceCommandHandler extends PriceCommandHandler
{



    /**
     * @param CommandInterface $command
     * @return EE_Price
     * @throws InvalidEntityException
     * @throws EE_Error
     * @throws InvalidArgumentException
     */
    public function handle(CommandInterface $command)
    {
        /** @var CreatePriceCommand $command */
        if (! $command instanceof CreatePriceCommand) {
            throw new InvalidEntityException(get_class($command), 'CreatePriceCommand');
        }
        return $this->createEntity($command, array($this, 'validatePriceData'));
    }
}
// End of file CreatePriceCommandHandler.php
// Location: core/domain/services/commands/price/CreatePriceCommandHandler.php