<?php

namespace EventEspresso\Core\Services\Cart;

use EventEspresso\Core\Libraries\Repositories\EE_Object_Repository;

if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * Class EE_Cart_Repository
 *
 * Storage entity for Carts that implements the
 * Countable, Iterator, Serializable, and ArrayAccess interfaces
 *
 * @package 			Event Espresso
 * @subpackage 	core
 * @author 				Brent Christensen
 * @since 				$VID:$
 *
 */
class CartRepository extends EE_Object_Repository {

	/**
	 * @type \EE_Session $_session
	 */
	protected $_session;



	/**
	 * @param \EE_Session $session
	 */
	function __construct( \EE_Session $session ) {
		$this->_session = $session;
		parent::__construct();
	}



	/**
	 * @return \EE_Session
	 */
	public function session() {
		return $this->_session;
	}



	/**
	 * @return Cart
	 */
	public function create_cart() {
		return $this->add_cart( new Cart() );
	}



	/**
	 * @param Cart $cart
	 *
*@return bool
	 */
	public function add_cart( Cart $cart ) {
		return $this->addObject( $cart, $cart->ID() );
	}



	/**
	 * @param mixed $ID
	 * @return null | object
	 */
	public function get_cart( $ID ) {
		$cart = $this->getObjectByInfo( $ID );
		if ( ! $cart instanceof Cart ) {
			$cart = $this->create_cart();
		}
		return $cart;
	}



	/**
	 * @param mixed $ID
	 * @return null | object
	 */
	public function get_cart_by_id( $ID ) {
		return $this->getObjectByInfo( $ID );
	}



	/**
	 * @param mixed $ID
	 * @return null | object
	 */
	public function get_cart_from_session( $ID ) {
		//try getting the cart out of the session
		$cart = $this->session()->cart( $ID );
		if ( $cart instanceof Cart && $ID == $cart->ID() ) {
			if ( $this->addObject( $cart, $ID ) ) {
				return $cart;
			}
		}
		return null;
	}



	/**
	 * @param Cart $cart
	 *
*@return bool
	 */
	public function has_cart( Cart $cart ) {
		return $this->hasObject( $cart );
	}



	/**
	 * @param mixed $ID
	 * @return bool
	 */
	public function has_cart_by_id( $ID ) {
		$cart = $this->getObjectByInfo( $ID );
		return $this->hasObject( $cart );
	}



	/**
	 * @param Cart $cart
	 *
*@return bool | int
	 */
	public function save_cart( Cart $cart ) {
		return $this->persistObject( $cart, 'save_cart' );
	}



	/**
	 * @param mixed $ID
	 * @return bool | int
	 */
	public function save_cart_by_id( $ID ) {
		$cart = $this->getObjectByInfo( $ID );
		return $this->persistObject( $cart, 'save_cart' );
	}



	/**
	 * @param Cart $cart
	 *
*@return void
	 */
	public function remove_cart( Cart $cart ) {
		$this->removeObject( $cart );
	}



	/**
	 * @param mixed $ID
	 * @return void
	 */
	public function remove_cart_by_id( $ID ) {
		$cart = $this->getObjectByInfo( $ID );
		$this->removeObject( $cart );
	}



}
// End of file CartRepository.class.php
// Location: /core/services/cart/CartRepository.php