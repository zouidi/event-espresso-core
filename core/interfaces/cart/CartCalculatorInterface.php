<?php
namespace EventEspresso\core\interfaces\cart;

use EventEspresso\core\services\cart\CartTotal;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}


interface CartCalculatorInterface {

	/**
	 * @return string
	 */
	public function name();



	/**
	 * @param CartInterface $cart
	 * @param CartTotal 	$cartTotal
	 * @return CartTotal
	 */
	public function calculateTotal( CartInterface $cart, CartTotal $cartTotal );



}
// End of file CartCalculatorInterface.php
// Location: /CartCalculatorInterface.php