<?php
namespace EventEspresso\core\services\cart;

use EventEspresso\core\interfaces\cart\CartInterface;
use EventEspresso\core\interfaces\cart\CartCalculatorInterface;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class DiscountCartCalculator
 *
 * Description
 *
 * @package 	Event Espresso
 * @subpackage 	core
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
class DiscountCartCalculator implements CartCalculatorInterface {


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
		return 'DiscountTotal';
	}



	/**
	 * @param CartInterface $cart
	 * @param CartTotal     $cartTotal
	 * @return CartTotal
	 */
	public function calculateTotal( CartInterface $cart, CartTotal $cartTotal ) {
		$this->cart = $cart;
		$this->cartTotal = $cartTotal;
		$this->calculateDiscountTotal();
	}



	/**
	 * calculateTicketTotal
	 */
	protected function calculateDiscountTotal() {
		$discountCartItems = $this->cart->getDiscountCartItems();
		foreach ( $discountCartItems as $discountCartItem ) {
			$discountTotal = $discountCartItem->calculatePrice();
			$this->cartTotal->discountTotal -= $discountTotal;
			$this->cartTotal->preTaxSubtotal -= $discountTotal;
		}
	}




}



// End of file DiscountCartCalculator.php
// Location: /DiscountCartCalculator.php