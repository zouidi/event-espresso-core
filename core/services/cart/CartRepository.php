<?php

namespace EventEspresso\Core\Services\Cart;

use EventEspresso\core\interfaces\cart\CartInterface;
use EventEspresso\core\interfaces\cart\CartRepositoryInterface;
use EventEspresso\Core\Libraries\Repositories\EE_Object_Repository;

if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * Class EE_Cart_Repository
 *
 * Storage entity for Carts that implements the
 * Countable, Iterator, Serializable, and ArrayAccess interfaces
 *
 * @package 	Event Espresso
 * @subpackage 	core
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
class CartRepository extends EE_Object_Repository implements CartRepositoryInterface {




	/**
	 * @param CartInterface $cart
	 * @return bool
	 */
	public function addCart( CartInterface $cart ) {
		return $this->add( $cart, $cart->ID() );
	}



	/**
	 * @param mixed $ID
	 * @return null | object
	 */
	public function getCart( $ID ) {
		$cart = $this->get_by_info( $ID );
		if ( ! $cart instanceof Cart ) {
			// exception ??
		}
		return $cart;
	}



	/**
	 * @param CartInterface $cart
	 * @return bool
	 */
	public function hasCart( CartInterface $cart ) {
		return $this->contains( $cart );
	}



	/**
	 * @param mixed $ID
	 * @return bool
	 */
	public function hasCartByID( $ID ) {
		$cart = $this->get_by_info( $ID );
		return $this->contains( $cart );
	}



	/**
	 * @param CartInterface $cart
	 * @return bool | int
	 */
	public function saveCart( CartInterface $cart ) {
		return $this->persist( $cart, 'save_cart' );
	}



	/**
	 * @param mixed $ID
	 * @return bool | int
	 */
	public function saveCartByID( $ID ) {
		$cart = $this->get_by_info( $ID );
		return $this->persist( $cart, 'save_cart' );
	}



	/**
	 * @param CartInterface $cart
	 * @return void
	 */
	public function removeCart( CartInterface $cart ) {
		$this->remove( $cart );
	}



	/**
	 * @param mixed $ID
	 * @return void
	 */
	public function removeCartByID( $ID ) {
		$cart = $this->get_by_info( $ID );
		$this->remove( $cart );
	}



}
// End of file CartRepository.class.php
// Location: /core/services/cart/CartRepository.php