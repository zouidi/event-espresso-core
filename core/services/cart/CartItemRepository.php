<?php
namespace EventEspresso\core\services\cart;

use EventEspresso\Core;
use EventEspresso\Core\Libraries\Repositories\EE_Object_Collection;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class CartItemRepository
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class CartItemRepository extends EE_Object_Collection {



	/**
	 * @param CartItem $item
	 * @return bool
	 */
	public function addItem( CartItem $item ) {
		if ( $this->has( $item ) ) {
			$item->setQuantity( $item->quantity() + 1 );
			return true;
		}
		return $this->add( $item, $item->SKU() );
	}



	/**
	 * @param mixed $SKU
	 * @return CartItem
	 * @throws \EE_Error
	 */
	public function getItem( $SKU ) {
		$item = $this->get_by_info( $SKU );
		if ( ! $item instanceof CartItem ) {
			throw new \EE_Error( 'Invalid or missing Item in cart.' );
		}
		return $item->validateItem( $item );
	}



	/**
	 * @param mixed $SKU
	 * @return CartItem
	 */
	public function getItemBySKU( $SKU ) {
		return $this->getItem( $SKU );
	}



	/**
	 * @param CartItem $item
	 * @return bool
	 */
	public function hasItem( CartItem $item ) {
		return $this->has( $item );
	}



	/**
	 * @param mixed $SKU
	 * @return bool
	 */
	public function hasItemBySKU( $SKU ) {
		$item = $this->get_by_info( $SKU );
		return $this->has( $item );
	}



	/**
	 * @param CartItem $item
	 * @return void
	 */
	public function removeItem( CartItem $item ) {
		$this->remove( $item );
	}



	/**
	 * @param mixed $SKU
	 * @return void
	 */
	public function removeItemBySKU( $SKU ) {
		$item = $this->get_by_info( $SKU );
		$this->remove( $item );
	}



}
// End of file CartItemRepository.php
// Location: /CartItemRepository.php