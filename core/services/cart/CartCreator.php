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
	 * @type TicketRepository $ticketRepository
	 */
	protected $ticketRepository;

	/**
	 * @type ProductRepository $productRepository
	 */
	protected $productRepository;

	/**
	 * @type PromotionRepository $promotionRepository
	 */
	protected $promotionRepository;



	/**
	 * @param \EventEspresso\core\services\cart\TicketRepository    $ticketRepository
	 * @param \EventEspresso\core\services\cart\ProductRepository   $productRepository
	 * @param \EventEspresso\core\services\cart\PromotionRepository $promotionRepository
	 */
	function __construct(
		TicketRepository $ticketRepository,
		ProductRepository $productRepository,
		PromotionRepository $promotionRepository
	) {
		$this->ticketRepository 	= $ticketRepository;
		$this->productRepository 	= $productRepository;
		$this->promotionRepository 	= $promotionRepository;
	}



	protected function generateID() {
		$admin = is_admin() && ! EE_FRONT_AJAX ? 'admin-' : '';
		return uniqid( $admin );
	}



	/**
	 * createCart
	 *
	 * @return CartInterface
	 */
	protected function newCart() {
		return new Cart(
			$this->generateID(),
			$this->ticketRepository,
			$this->productRepository,
			$this->promotionRepository,
			new \DateTime( 'now', new \DateTimeZone( 'UTC' ) )
		);
	}

}
// End of file CartCreator.php
// Location: /CartCreator.php