<?php

namespace EventEspresso\core\domain\services\commands\ticket;

use EE_Ticket;
use EventEspresso\core\domain\services\capabilities\CapCheck;
use EventEspresso\core\domain\services\capabilities\CapCheckInterface;
use EventEspresso\core\services\commands\Command;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class addTicketPricesCommand
 * DTO for passing data to addTicketPricesCommandHandler
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class addTicketPricesCommand extends Command
{


    /**
     * @var EE_Ticket $ticket
     */
    private $ticket;

    /**
     * @var array $prices
     */
    private $prices;

    /**
     * @var boolean $new_prices
     */
    private $new_prices;

    /**
     * @var string $timezone
     */
    private $timezone;

    /**
     * @var int $base_price_id
     */
    private $base_price_id;

    /**
     * @var float $base_price
     */
    private $base_price;

    /**
     * @var array $date_and_time_formats
     */
    private $date_and_time_formats;



    /**
     * addTicketPricesCommand constructor.
     *
     * @param EE_Ticket $ticket
     * @param array     $prices
     * @param boolean   $new_prices
     * @param string    $timezone
     * @param int       $base_price_id
     * @param float     $base_price
     * @param array     $date_and_time_formats
     */
    public function __construct(
        EE_Ticket $ticket,
        array $prices,
        $new_prices = false,
        $base_price_id = 0,
        $base_price,
        $timezone = '',
        $date_and_time_formats = array()
    ) {
        $this->ticket = $ticket;
        $this->prices = $prices;
        $this->new_prices = filter_var($new_prices, FILTER_VALIDATE_BOOLEAN);
        $this->base_price_id = filter_var($base_price_id, FILTER_SANITIZE_NUMBER_INT);
        $this->base_price = $base_price !== false
            ? filter_var($base_price, FILTER_SANITIZE_NUMBER_FLOAT)
            : false;
        $this->timezone = $timezone;
        $this->date_and_time_formats = $date_and_time_formats;
    }



    /**
     * @return EE_Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }



    /**
     * @return array
     */
    public function getPrices()
    {
        return $this->prices;
    }



    /**
     * @return boolean
     */
    public function getNewPrices()
    {
        return $this->new_prices;
    }



    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }



    /**
     * @return int
     */
    public function getBasePriceId()
    {
        return $this->base_price_id;
    }



    /**
     * @return float
     */
    public function getBasePrice()
    {
        return $this->base_price;
    }



    /**
     * @return array
     */
    public function getDateAndTimeFormats()
    {
        return $this->date_and_time_formats;
    }




    /**
     * @return CapCheckInterface
     */
    public function getCapCheck()
    {
        if (! $this->cap_check instanceof CapCheckInterface) {
            return new CapCheck('ee_edit_events', 'create_ticket_price');
        }
        return $this->cap_check;
    }


}
// End of file addTicketPricesCommand.php
// Location: core/domain/services/commands/ticket/addTicketPricesCommand.php