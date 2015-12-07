<?php
namespace EventEspresso\core\services\cart;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class TicketCartItem
 *
 * Description
 *
 * @package 	Event Espresso
 * @subpackage 	core
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
class TicketCartItem extends CartItem {


	/**
	 * TicketCartItem constructor.
	 *
	 * @param \EE_Ticket 	$ticket
	 * @param Cart 			$cart
	 */
	public function __construct( \EE_Ticket $ticket, Cart $cart ) {
		$this->item = $ticket;
		parent::__construct( $cart );
	}



	/**
	 * @return string
	 */
	public function generateSKU() {
		return md5( $this->item->ID() . $this->item->name_and_info() );
	}



	/**
	 * @param $item
	 * @return object
	 * @throws \EE_Error
	 */
	public function validateItem( $item ) {
		if ( ! $item instanceof \EE_Ticket ) {
			throw new \EE_Error( 'Invalid or missing ticket in cart.' );
		}
		return $item;
	}



	/**
	 * @return float
	 */
	public function calculatePrice() {
		return (float)( $this->getItem()->ticket_price() * $this->quantity );
	}


}
// End of file TicketCartItem.php
// Location: /TicketCartItem.php