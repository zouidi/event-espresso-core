<?php
namespace EventEspresso\core\services\cart;

use EventEspresso\core\interfaces\cart\CartInterface;
use EventEspresso\core\interfaces\cart\CartCreatorInterface;
use EventEspresso\core\interfaces\cart\CartCalculatorRepositoryInterface;

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
class CartCreator implements CartCreatorInterface{

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
	 * @param    CartCalculatorRepositoryInterface $cartCalculatorRepository
	 * @return 	CartInterface
	 */
	public function getNewCart( CartCalculatorRepositoryInterface $cartCalculatorRepository ) {
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