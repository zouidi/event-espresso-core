<?php

namespace EventEspresso\core\domain\services\commands\price;

use EE_Error;
use EE_Ticket;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\services\commands\CommandHandler;
use EventEspresso\core\services\commands\CommandInterface;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class DeletePriceCommandHandler
 * Service class for deleting an EE_Price object
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class DeletePriceCommandHandler extends CommandHandler
{



    /**
     * @param CommandInterface $command
     * @return void
     * @throws EE_Error
     * @throws InvalidEntityException
     */
    public function handle(CommandInterface $command)
    {
        /** @var DeletePriceCommand $command */
        if (! $command instanceof DeletePriceCommand) {
            throw new InvalidEntityException(get_class($command), 'DeletePriceCommand');
        }
        $price = $command->getPrice();
        $ticket = $command->getTicket();
        do_action(
            'AHEE__EventEspresso\core\domain\services\commands\price\DeletePriceCommandHandler__before_delete_price',
            $price,
            $this
        );
        // remove ticket relationships.
        $related_tickets = $ticket instanceof EE_Ticket ? array($ticket) : $price->tickets();
        foreach ($related_tickets as $related_ticket) {
            $related_ticket->_remove_relation_to($price, 'Price');
        }
        if ($ticket instanceof EE_Ticket) {
            // permanently delete the price
            $price->delete_permanently();
        } else {
            $price->delete();
        }
    }
}
// End of file DeletePriceCommandHandler.php
// Location: EventEspresso\core\domain\services\commands\price/DeletePriceCommandHandler.php