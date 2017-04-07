<?php

namespace EventEspresso\core\domain\services\commands\ticket;

use EE_Error;
use EE_Price;
use EE_Ticket;
use EventEspresso\core\domain\services\commands\price\CreatePriceCommand;
use EventEspresso\core\domain\services\commands\price\DeletePriceCommand;
use EventEspresso\core\domain\services\commands\price\UpdatePriceCommand;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\services\commands\CommandInterface;
use EventEspresso\core\services\commands\CompositeCommandHandler;
use EventEspresso\core\services\commands\middleware\InvalidCommandBusMiddlewareException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class addTicketPricesCommandHandler
 * Service class for adding/updating/deleting relations between Prices and a Ticket
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class addTicketPricesCommandHandler extends CompositeCommandHandler
{



    /**
     * @param CommandInterface $command
     * @return EE_Ticket
     * @throws EE_Error
     * @throws InvalidCommandBusMiddlewareException
     * @throws InvalidDataTypeException
     * @throws InvalidEntityException
     */
    public function handle(CommandInterface $command)
    {
        /** @var addTicketPricesCommand $command */
        if (! $command instanceof addTicketPricesCommand) {
            throw new InvalidEntityException(get_class($command), 'addTicketPricesCommand');
        }
        $ticket = $command->getTicket();
        $prices = $command->getPrices();
        $new_prices = $command->getNewPrices();
        $base_price_id = $command->getBasePriceId();
        $base_price = $command->getBasePrice();
        $timezone = $command->getTimezone();
        $date_and_time_formats = $command->getDateAndTimeFormats();
        $updated_prices = array();
        // if $base_price ! FALSE then updating a base price.
        if ($base_price !== false) {
            $prices[1] = array(
                'PRC_ID'     => ! $new_prices && $base_price_id > 1 ? $base_price_id : 0,
                'PRT_ID'     => 1,
                'PRC_amount' => $base_price,
                'PRC_name'   => $ticket->get('TKT_name'),
                'PRC_desc'   => $ticket->get('TKT_description')
            );
        }
        //possibly need to save tkt
        if (! $ticket->ID()) {
            $ticket->save();
        }
        foreach ($prices as $row => $price_data) {
            $price = $new_prices || $price_data['PRC_ID'] === 0
                ? $this->commandBus()->execute(
                    new CreatePriceCommand($price_data, $timezone, $date_and_time_formats)
                )
                : $this->commandBus()->execute(
                    new UpdatePriceCommand($price_data, $timezone, $date_and_time_formats)
                );
            $PRC_ID = $price->ID();
            if ($PRC_ID > 0) {
                $updated_prices[$PRC_ID] = $price;
                $ticket->_add_relation_to($price, 'Price');
            }
        }
        $this->deleteTicketPrices($ticket, $base_price, $updated_prices);
        return $ticket;
    }



    /**
     * @param EE_Ticket $ticket
     * @param           $base_price
     * @param array     $updated_prices
     * @throws EE_Error
     * @throws InvalidCommandBusMiddlewareException
     * @throws InvalidDataTypeException
     */
    protected function deleteTicketPrices(EE_Ticket $ticket, $base_price, array $updated_prices) {
        // retrieve any current prices that may exist on the ticket
        // so we can remove any prices that got trashed in this session.
        $current_prices = $base_price !== false
            ? $ticket->base_price(true)
            : $ticket->price_modifiers();
        if (! empty ($current_prices)) {
            $current = array_keys($current_prices);
            $updated = array_keys($updated_prices);
            $prices_to_remove = array_diff($current, $updated);
            if (! empty($prices_to_remove)) {
                foreach ($prices_to_remove as $price_id) {
                    $price = isset($current_prices[$price_id]);
                    if (! $price instanceof EE_Price){
                        continue;
                    }
                    $this->commandBus()->execute(
                        new DeletePriceCommand($price, $ticket)
                    );
                }
            }
        }
    }
}
// End of file addTicketPricesCommandHandler.php
// Location: EventEspresso\core\domain\services\commands\ticket/addTicketPricesCommandHandler.php