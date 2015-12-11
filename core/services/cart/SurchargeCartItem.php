<?php
namespace EventEspresso\core\services\cart;

use EventEspresso\core\interfaces\cart\SurchargeInterface;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class SurchargeCartItem
 *
 * Description
 *
 * @package 	Event Espresso
 * @subpackage  core\services\cart
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
class SurchargeCartItem extends CartItem {


	/**
	 * TicketCartItem constructor.
	 *
	 * @param SurchargeInterface $surcharge
	 * @param Cart 			$cart
	 */
	public function __construct( SurchargeInterface $surcharge, Cart $cart ) {
		$this->item = $surcharge;
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
		if ( ! $item instanceof SurchargeInterface ) {
			throw new \EE_Error( 'Invalid or missing surcharge in cart.' );
		}
		return $item;
	}



	/**
	 * @return float
	 */
	public function calculatePrice() {
		return (float)( $this->getItem()->amount() * $this->quantity );
	}


}
// End of file SurchargeCartItem.php
// Location: /SurchargeCartItem.php