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
		$ticketCartItems = $this->cart->getTicketCartItems();
		foreach ( $ticketCartItems as $ticketCartItem ) {
			$this->cartTotal->ticketCount += $ticketCartItem->quantity();
			$ticket_price = $ticketCartItem->calculatePrice();
			$this->cartTotal->totalTicketAmount += $ticket_price;
			$this->cartTotal->preTaxSubtotal += $ticket_price;
		}
	}




}



// End of file CartCalculatorForTickets.php
// Location: /CartCalculatorForTickets.php