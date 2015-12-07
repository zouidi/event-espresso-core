<?php

namespace EventEspresso\Core\Services\Cart;

use EventEspresso\Core;
use EventEspresso\core\interfaces\CartInterface;
use EventEspresso\core\interfaces\CartCreatorInterface;
use EventEspresso\core\interfaces\CartRepositoryInterface;
use EventEspresso\core\interfaces\CartCalculatorRepositoryInterface;

if ( ! defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}
/**
 * Class CartManager
 *
 * the CartManager class oversees the creation, storage, and retrieval of carts,
 * as well as the storage of cart calculators
 *
 * @package 	Event Espresso
 * @subpackage 	core
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
 class CartManager {

	 /**
	  * @type \EE_Session $session
	  */
	 protected $session;

	 /**
	  * @type CartRepository $cartRepository
	  */
	 protected $cartRepository;

	 /**
	  * @type CartCreator $cartCreator
	  */
	 protected $cartCreator;

	 /**
	  * @type CartCalculatorRepository $cartCalculatorRepository
	  */
	 protected $cartCalculators;



	 /**
	  * @param \EE_Session 							$session
	  * @param CartCreatorInterface     			$CartCreator
	  * @param CartRepositoryInterface 				$CartRepository
	  * @param CartCalculatorRepositoryInterface 	$cartCalculatorRepository
	  */
	 function __construct(
		 \EE_Session $session,
		 CartCreatorInterface $CartCreator,
		 CartRepositoryInterface $CartRepository,
		 CartCalculatorRepositoryInterface $cartCalculatorRepository
	 ) {
		 $this->session 		= $session;
		 $this->cartCreator 	= $CartCreator;
		 $this->cartRepository 	= $CartRepository;
		 $this->cartCalculators = $cartCalculatorRepository;
		 do_action( 'AHEE__CartManager__construct__complete', $this );
	 }



	 /**
	  * @param mixed $ID
	  * @param bool  $createNew
	  * @return CartInterface
	  */
	 public function getCart( $ID, $createNew = false ) {
		 $cart = $this->cartRepository->getCart( $ID );
		 if ( ! $cart instanceof Cart ) {
			 $cart = $this->getCartFromSession( $ID );
		 }
		 if ( ! $cart instanceof Cart && $createNew ) {
			 $cart = $this->createCart();
		 }
		 if ( ! $cart instanceof Cart ) {
			 // exception ?
		 }
		 return $cart;
	 }



	 /**
	  * @param mixed $ID
	  * @return CartInterface
	  */
	 public function getCartFromSession( $ID ) {
		 // try getting the cart out of the session
		 $cart = $this->session->cart( $ID );
		 if ( $cart instanceof Cart && $ID == $cart->ID() ) {
			 if ( $this->cartRepository->addCart( $cart ) ) {
				 return $cart;
			 }
		 }
		 return null;
	 }



	 /**
	  * createCart
	  *
	  * @return CartInterface
	  */
	 protected function createCart() {
		 $cart = $this->cartCreator->newCart( $this->cartCalculators );
		 if ( $this->cartRepository->addCart( $cart ) ) {
			 return $cart;
		 }
		 return null;
	 }



 }



// End of file CartManager.php
// Location: /CartManager.php