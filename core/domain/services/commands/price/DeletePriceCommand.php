<?php

namespace EventEspresso\core\domain\services\commands\price;

use EE_Price;
use EE_Ticket;
use EventEspresso\core\domain\services\capabilities\CapCheck;
use EventEspresso\core\domain\services\capabilities\CapCheckInterface;
use EventEspresso\core\services\commands\Command;
use EventEspresso\core\services\commands\CommandRequiresCapCheckInterface;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class DeletePriceCommand
 * DTO for supplying data to DeletePriceCommandHandler
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class DeletePriceCommand extends Command implements CommandRequiresCapCheckInterface
{


    /**
     * @var EE_Price $price
     */
    private $price;


    /**
     * @var EE_Ticket $ticket
     */
    private $ticket;



    /**
     * DeletePriceCommand constructor.
     *
     * @param EE_Price  $price
     * @param EE_Ticket $ticket if supplied, then price will be removed from this ticket only,
     *                          else price will be removed from ALL related tickets before deletion
     */
    public function __construct(EE_Price $price, EE_Ticket $ticket)
    {
        $this->price = $price;
        $this->ticket = $ticket;
    }



    /**
     * @return EE_Price
     */
    public function getPrice()
    {
        return $this->price;
    }



    /**
     * @return EE_Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }



    /**
     * @return CapCheckInterface
     */
    public function getCapCheck()
    {
        if (! $this->cap_check instanceof CapCheckInterface) {
            return new CapCheck('ee_edit_events', 'delete_ticket_price');
        }
        return $this->cap_check;
    }



}
// End of file DeletePriceCommand.php
// Location: EventEspresso\core\domain\services\commands\price/DeletePriceCommand.php