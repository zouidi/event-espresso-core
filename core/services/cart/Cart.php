<?php

namespace EventEspresso\Core\Services\Cart;

use EventEspresso\Core;
use EventEspresso\core\interfaces\cart\CartInterface;
use EventEspresso\core\interfaces\cart\DiscountInterface;
use EventEspresso\core\interfaces\EEI_Ticket;
//use EventEspresso\Core\Libraries\Repositories\EE_Object_Repository;
//use EventEspresso\Core\Libraries\Repositories\ObjectInfoArrayKeyStrategy;

if ( ! defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}
/**
 * Class Cart
 *
 * Description
 *
 * @package 	Event Espresso
 * @subpackage 	core
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
 class Cart implements CartInterface {

	 /**
	  * unique identifier for cart
	  * @type string $ID
	  */
	 protected $ID;

	 /**
	  * whether the cart is open for modification
	  * @type boolean $open
	  */
	 protected $open;

	 /**
	  * date and time the cart was first created in UTC+0
	  * @type \DateTime $created
	  */
	 protected $created;

	 /**
	  * date and time of last update in UTC+0
	  * @type \DateTime $updated
	  */
	 protected $updated;

	 /**
	  * @type CartItemRepository $items
	  */
	 protected $items;

	 /**
	  * @type CartTotal $cartTotal
	  */
	 protected $cartTotal;

	 /**
	  * @type CartCalculatorRepository $cartCalculatorRepository
	  */
	 protected $cartCalculators;



	 function __construct(
		 $ID,
		 CartItemRepository $cartItemRepository,
		 CartCalculatorRepository $cartCalculatorRepository,
		 CartTotal $cartTotal,
		 \DateTime $created = null
	 ) {
		 $this->ID 		 		= $ID;
		 $this->items  			= $cartItemRepository;
		 $this->cartCalculators = $cartCalculatorRepository;
		 $this->cartTotal 		= $cartTotal;
		 $this->setCreated( $created );
	 }



	 public function ID() {
		 return $this->ID;
	 }



	 /**
	  * @return boolean
	  */
	 public function open() {
		 return $this->open;
	 }



	 /**
	  * @param boolean $open
	  */
	 public function setOpen( $open ) {
		 $this->open = filter_var( $open, FILTER_VALIDATE_BOOLEAN );
	 }



	 /**
	  * sets the cart open status to false
	  */
	 public function closeCart() {
		 $this->setOpen( false );
	 }



	 /**
	  * @access protected
	  * @param \DateTime $created
	  */
	 protected function setCreated( \DateTime $created = null ) {
		 if ( ! $created instanceof \DateTime ) {
			 $created = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
		 }
		 $this->created = $created;
	 }



	 /**
	  * @return \DateTime
	  */
	 public function getCreated() {
		 return $this->created;
	 }



	 /**
	  * @access protected
	  * @param \DateTime $updated
	  */
	 protected function setUpdated( \DateTime $updated = null ) {
		 if ( ! $updated instanceof \DateTime ) {
			 $updated = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
		 }
		 $this->updated = $updated;
	 }



	 /**
	  * @return \DateTime
	  */
	 public function getUpdated() {
		 return $this->updated;
	 }



	 /**
	  * @param EEI_Ticket $ticket
	  * @return bool
	  */
	 public function addTicket( EEI_Ticket $ticket ) {
		 return $this->items->addItem(
			 new TicketCartItem( $ticket, $this )
		 );
	 }



	 /**
	  * @return EEI_Ticket[]
	  */
	 public function getTickets() {
		 $tickets = array();
		 foreach ( $this->items as $item ) {
			 if ( $item instanceof TicketCartItem ) {
				 $tickets[] = $item->getItem();
			 }
		 }
		 return $tickets;
	 }



	 /**
	  * @return TicketCartItem[]
	  */
	 public function getTicketCartItems() {
		 $ticketCartItems = array();
		 foreach ( $this->items as $item ) {
			 if ( $item instanceof TicketCartItem ) {
				 $ticketCartItems[] = $item;
			 }
		 }
		 return $ticketCartItems;
	 }



	 /**
	  * @param DiscountInterface $discount
	  * @return bool
	  */
	 public function addDiscount( DiscountInterface $discount ) {
		 return $this->items->addItem(
			 new DiscountCartItem( $discount, $this )
		 );
	 }



	 /**
	  * @return DiscountInterface[]
	  */
	 public function getDiscounts() {
		 $tickets = array();
		 foreach ( $this->items as $item ) {
			 if ( $item instanceof DiscountCartItem ) {
				 $tickets[] = $item->getItem();
			 }
		 }
		 return $tickets;
	 }



	 /**
	  * @return DiscountCartItem[]
	  */
	 public function getDiscountCartItems() {
		 $ticketCartItems = array();
		 foreach ( $this->items as $item ) {
			 if ( $item instanceof DiscountCartItem ) {
				 $ticketCartItems[] = $item;
			 }
		 }
		 return $ticketCartItems;
	 }



	 /**
	  * calculateCartTotal
	  *
	  * @return CartTotal
	  */
	 protected function calculateCartTotal() {
		 // allow each cart calculator to modify the subtotals
		 $this->cartCalculators->rewind();
		 while ( $this->cartCalculators->valid() ) {
			 $this->cartCalculators->current()->calculateTotal( $this, $this->cartTotal );
			 $this->cartCalculators->next();
		 }
		 // ya gotta ADD IT UP! ADD IT UP!
		 $this->cartTotal->grandTotal =
			 $this->cartTotal->preTaxSubtotal
			 - $this->cartTotal->totalDiscount
			 + $this->cartTotal->taxSubtotal;
		 // no negative values please
		 $this->cartTotal->grandTotal = max( 0, $this->cartTotal->grandTotal );
		 return $this->cartTotal;
	 }


 }



// End of file Cart.php
// Location: /Cart.php