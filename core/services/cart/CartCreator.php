<?php
namespace EventEspresso\core\services\cart;

use EventEspresso\core\interfaces\CartInterface;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class CartCreator
 *
 * Description
 *
 * @package 	Event Espresso
 * @subpackage 	core
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
class CartCreator {

	/**
	 * @type CartItemRepository $cartItemRepository
	 */
	protected $cartItemRepository;



	/**
	 * @param CartItemRepository $cartItemRepository
	 */
	function __construct( CartItemRepository $cartItemRepository ) {
		$this->cartItemRepository = $cartItemRepository;
	}



	protected function generateID() {
		$admin = is_admin() && ! EE_FRONT_AJAX ? 'admin-' : '';
		return uniqid( $admin );
	}



	/**
	 * createCart
	 *
	 * @param 	CartCalculatorRepository $cartCalculatorRepository
	 * @return 	CartInterface
	 */
	protected function newCart( CartCalculatorRepository $cartCalculatorRepository ) {
		return new Cart(
			$this->generateID(),
			$this->cartItemRepository,
			$cartCalculatorRepository,
			new CartTotal(),
			new \DateTime( 'now', new \DateTimeZone( 'UTC' ) )
		);
	}

}
// End of file CartCreator.php
// Location: /CartCreator.php