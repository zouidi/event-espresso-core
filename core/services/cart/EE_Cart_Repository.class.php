<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
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
class EE_Cart_Repository extends EE_Object_Repository {

	/**
	 * @type \EE_Session $_session
	 */
	protected $_session;



	/**
	 * @param \EE_Session $session
	 */
	function __construct( EE_Session $session ) {
		$this->_session = $session;
	}



	/**
	 * @return \EE_Session
	 */
	public function session() {
		return $this->_session;
	}



	/**
	 * @param \EE_Cart $cart
	 * @return bool
	 */
	public function add_cart( EE_Cart $cart ) {
		return $this->addObject( $cart, $cart->cart_id() );
	}



	/**
	 * @param mixed $cart_id
	 * @return null | object
	 */
	public function get_cart( $cart_id ) {
		return $this->getObjectByInfo( $cart_id );
	}



	/**
	 * @param mixed $cart_id
	 * @return null | object
	 */
	public function get_cart_by_id( $cart_id ) {
		return $this->getObjectByInfo( $cart_id );
	}



	/**
	 * @param mixed $cart_id
	 * @return null | object
	 */
	public function get_cart_from_session( $cart_id ) {
		//try getting the cart out of the session
		$cart = $this->session()->cart( $cart_id );
		if ( $cart instanceof EE_Cart && $cart_id == $cart->cart_id() ) {
			if ( $this->addObject( $cart, $cart_id ) ) {
				return $cart;
			}
		}
		return null;
	}



	/**
	 * @param \EE_Cart $cart
	 * @return bool
	 */
	public function has_cart( EE_Cart $cart ) {
		return $this->hasObject( $cart );
	}



	/**
	 * @param mixed $cart_id
	 * @return bool
	 */
	public function has_cart_by_id( $cart_id ) {
		$cart = $this->getObjectByInfo( $cart_id );
		return $this->hasObject( $cart );
	}



	/**
	 * @param \EE_Cart $cart
	 * @return bool | int
	 */
	public function save_cart( EE_Cart $cart ) {
		return $this->persistObject( $cart, 'save_cart' );
	}



	/**
	 * @param mixed $cart_id
	 * @return bool | int
	 */
	public function save_cart_by_id( $cart_id ) {
		$cart = $this->getObjectByInfo( $cart_id );
		return $this->persistObject( $cart, 'save_cart' );
	}



	/**
	 * @param \EE_Cart $cart
	 * @return void
	 */
	public function remove_cart( EE_Cart $cart ) {
		$this->removeObject( $cart );
	}



	/**
	 * @param mixed $cart_id
	 * @return void
	 */
	public function remove_cart_by_id( $cart_id ) {
		$cart = $this->getObjectByInfo( $cart_id );
		$this->removeObject( $cart );
	}



}
// End of file EE_Cart_Repository.class.php
// Location: /core/services/EE_Cart_Repository.class.php:18