<?php
namespace EventEspresso\core\services\cart;

use EventEspresso\core\interfaces\cart\DiscountInterface;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class DiscountCartItem
 *
 * Description
 *
 * @package 	Event Espresso
 * @subpackage  core\services\cart
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
class DiscountCartItem extends CartItem {


	/**
	 * TicketCartItem constructor.
	 *
	 * @param DiscountInterface $discount
	 * @param Cart 			$cart
	 */
	public function __construct( DiscountInterface $discount, Cart $cart ) {
		$this->item = $discount;
		parent::__construct( $cart );
	}



	/**
	 * @return string
	 */
	public function generateSKU() {
		return md5( $this->item->ID() . $this->item->description() );
	}



	/**
	 * @param $item
	 * @return object
	 * @throws \EE_Error
	 */
	public function validateItem( $item ) {
		if ( ! $item instanceof DiscountInterface ) {
			throw new \EE_Error( 'Invalid or missing discount in cart.' );
		}
		return $item;
	}



	/**
	 * @return float
	 */
	public function calculatePrice() {
		return (float)( $this->getItem()->amount() * $this->quantity );
	}



	/**
	 * @return bool
	 */
	public function isTaxable() {
		return $this->getItem()->taxable() ? true : false;
	}


}
// End of file DiscountCartItem.php
// Location: /DiscountCartItem.php