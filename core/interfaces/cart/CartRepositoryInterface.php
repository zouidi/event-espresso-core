<?php
namespace EventEspresso\core\interfaces\cart;

use EventEspresso\core\interfaces\EEI_Repository;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Cart Repository Interface
 *
 * @package    Event Espresso
 * @subpackage interfaces
 * @since      4.8.0
 * @author     Brent Christensen
 */
interface CartRepositoryInterface extends EEI_Repository {



	/**
	 * @param CartInterface $cart
	 * @return bool
	 */
	function addCart( CartInterface $cart );



	/**
	 * @param mixed $ID
	 * @return null | object
	 */
	function getCart( $ID );



	/**
	 * @param CartInterface $cart
	 * @return bool
	 */
	function hasCart( CartInterface $cart );



	/**
	 * @param mixed $ID
	 * @return bool
	 */
	function hasCartByID( $ID );



	/**
	 * @param CartInterface $cart
	 * @return bool | int
	 */
	function saveCart( CartInterface $cart );



	/**
	 * @param mixed $ID
	 * @return bool | int
	 */
	function saveCartByID( $ID );



	/**
	 * @param CartInterface $cart
	 * @return void
	 */
	function removeCart( CartInterface $cart );



	/**
	 * @param mixed $ID
	 * @return void
	 */
	function removeCartByID( $ID );




}
// End of file CartRepositoryInterface.php
// Location: /core/interfaces/CartRepositoryInterface.php