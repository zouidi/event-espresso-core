<?php
namespace EventEspresso\core\services\cart;

use EventEspresso\core\interfaces\CartInterface;
use EventEspresso\core\interfaces\CartCalculatorInterface;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class CartCalculatorForTickets
 *
 * Description
 *
 * @package 	Event Espresso
 * @subpackage 	core
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
class CartCalculatorForTickets implements CartCalculatorInterface {


	/**
	 * @type Cart $cart
	 */
	protected $cart;


	/**
	 * @type CartTotal $cartTotal
	 */
	protected $cartTotal;



	/**
	 * @return string
	 */
	public function name() {
		return 'TicketTotal';
	}



	/**
	 * @param CartInterface $cart
	 * @param CartTotal     $cartTotal
	 * @return CartTotal
	 */
	public function calculateTotal( CartInterface $cart, CartTotal $cartTotal ) {
		$this->cart = $cart;
		$this->cartTotal = $cartTotal;
		$this->calculateTicketTotal();
	}



	/**
	 * calculateTicketTotal
	 */
	protected function calculateTicketTotal() {
		$tickets = $this->cart->getTickets();
		$tickets->rewind();
		while ( $tickets->valid() ) {
			$ticket_price = $tickets->current()->ticket_price();
			$this->cartTotal->ticketCount++;
			$this->cartTotal->totalTicketAmount += $ticket_price;
			$this->cartTotal->preTaxSubtotal += $ticket_price;
			$tickets->next();
		}
		$tickets->rewind();
	}




}



// End of file CartCalculatorForTickets.php
// Location: /CartCalculatorForTickets.php