<?php

namespace EventEspresso\core\domain\services\commands\price;

use DomainException;
use EE_Error;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\services\commands\CommandInterface;
use InvalidArgumentException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class UpdatePriceCommandHandler
 * Service class for updating an EE_Price object
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class UpdatePriceCommandHandler extends PriceCommandHandler
{



    /**
     * @param CommandInterface $command
     * @return mixed
     * @throws DomainException
     * @throws EE_Error
     * @throws InvalidArgumentException
     * @throws InvalidEntityException
     */
    public function handle(CommandInterface $command)
    {
        /** @var UpdatePriceCommand $command */
        if (! $command instanceof UpdatePriceCommand) {
            throw new InvalidEntityException(get_class($command), 'UpdatePriceCommand');
        }
        return $this->updateEntity($command, array($this, 'validatePriceData'), $this->price_model);
    }


}
// End of file UpdatePriceCommandHandler.php
// Location: EventEspresso\core\domain\services\commands\price/UpdatePriceCommandHandler.php