<?php
namespace EventEspresso\core\services\cart;

use EventEspresso\core\interfaces\cart\CartInterface;
use EventEspresso\core\interfaces\cart\CartCalculatorInterface;

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
			$ticketTotal = $ticketCartItem->calculatePrice();
			$this->cartTotal->ticketTotal += $ticketTotal;
			$this->cartTotal->preTaxSubtotal += $ticketTotal;
		}
	}




}



// End of file CartCalculatorForTickets.php
// Location: /CartCalculatorForTickets.php