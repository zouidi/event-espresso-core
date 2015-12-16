<?php

namespace EventEspresso\Core\Services\Cart;

use EventEspresso\Core;
use EventEspresso\core\interfaces\cart\CartInterface;
use EventEspresso\core\interfaces\cart\DiscountInterface;
use EventEspresso\core\interfaces\cart\SurchargeInterface;
use EventEspresso\core\interfaces\cart\CartCalculatorRepositoryInterface;
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
		 CartCalculatorRepositoryInterface $cartCalculatorRepository,
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
	 public function setOpen( $open = true ) {
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
	  * @param EEI_Ticket 		$ticket
	  * @param int        		$quantity
	  * @param CartItemOption[] $options
	  * @return bool
	  */
	 public function addTicket( EEI_Ticket $ticket, $quantity = 1, $options = array() ) {
		 return $this->addItem( $ticket, $quantity, $options, 'Ticket' );
	 }



	 /**
	  * @return EEI_Ticket[]
	  */
	 public function getTickets() {
		 return $this->getItems( 'Ticket' );
	 }



	 /**
	  * @return TicketCartItem[]
	  */
	 public function getTicketCartItems() {
		 return $this->getCartItems( 'Ticket' );
	 }



	 /**
	  * @param DiscountInterface $discount
	  * @param int               $quantity
	  * @param CartItemOption[]  $options
	  * @return bool
	  */
	 public function addDiscount( DiscountInterface $discount, $quantity = 1, $options = array() ) {
		 return $this->addItem( $discount, $quantity, $options, 'Discount' );
	 }



	 /**
	  * @return DiscountInterface[]
	  */
	 public function getDiscounts() {
		 return $this->getItems( 'Discount' );
	 }



	 /**
	  * @return DiscountCartItem[]
	  */
	 public function getDiscountCartItems() {
		 return $this->getCartItems( 'Discount' );
	 }



	 /**
	  * @param SurchargeInterface $surcharge
	  * @param int                $quantity
	  * @param CartItemOption[]   $options
	  * @return bool
	  */
	 public function addSurcharge( SurchargeInterface $surcharge, $quantity = 1, $options = array() ) {
		 return $this->addItem( $surcharge, $quantity, $options, 'Surcharge' );
 	}



	 /**
	  * @return SurchargeInterface[]
	  */
	 public function getSurcharges() {
		 return $this->getItems( 'Surcharge' );
	 }



	 /**
	  * @return SurchargeCartItem[]
	  */
	 public function getSurchargeCartItems() {
		 return $this->getCartItems( 'Surcharge' );
	 }



	 /**
	  * @access protected
	  * @param  string 			$item
	  * @param int     			$quantity
	  * @param CartItemOption[] $options
	  * @param  string 			$type
	  * @return bool
	  * @throws \EE_Error
	  */
	 protected function addItem( $item, $quantity, $options, $type = 'Ticket' ) {
		 $cartItemClass = $this->getCartItemClass( $type );
		 $cartItem = new $cartItemClass( $item, $this );
		 if ( ! $cartItem instanceof CartItem ) {
			 throw new \EE_Error(
				 sprintf(
					 __( '"%1$s" is not a valid CartItem class.', 'event_espresso' ),
					 $cartItemClass
				 )
			 );
		 }
		 foreach ( $options as $option ) {
			 if ( $option instanceof CartItemOption ) {
				 $cartItem->addCartItemOption( $option );
			 }
		 }
		 return $this->items->addItem( $cartItem, $quantity );
	 }



	 /**
	  * @access protected
	  * @param  string $type
	  * @return array
	  * @throws \EE_Error
	  */
	 protected function getItems( $type = 'Ticket' ) {
		 $cartItemClass = $this->getCartItemClass( $type );
		 $items = array();
		 foreach ( $this->items as $item ) {
			 if ( $item instanceof $cartItemClass ) {
				 $items[] = $item->getItem();
			 }
		 }
		 return $items;
	 }



	 /**
	  * @access protected
	  * @param  string $type
	  * @return CartItem[]
	  * @throws \EE_Error
	  */
	 protected function getCartItems( $type = 'Ticket' ) {
		 $cartItemClass = $this->getCartItemClass( $type );
		 $cartItems = array();
		 foreach ( $this->items as $item ) {
			 if ( $item instanceof $cartItemClass ) {
				 $cartItems[] = $item;
			 }
		 }
		 return $cartItems;
	 }



	 /**
	  * @param  string $type
	  * @return string
	  * @throws \EE_Error
	  */
	 public function getCartItemClass( $type = 'Ticket' ) {
		 $itemClass = $type . 'CartItem';
		 if ( ! class_exists( $itemClass ) || ! is_subclass_of( $itemClass, 'CartItem' ) ) {
			 throw new \EE_Error(
				 sprintf(
					 __( 'The "%1$s" class is either missing or not a valid CartItem class.', 'event_espresso' ),
					 $itemClass
				 )
			 );
		 }
		 return $itemClass;
	 }



	 /**
	  * calculateCartTotal
	  *
	  * @access protected
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
			 + $this->cartTotal->surchargeTotal
			 - $this->cartTotal->discountTotal
			 + $this->cartTotal->taxSubtotal;
		 // no negative values please
		 $this->cartTotal->grandTotal = max( 0, $this->cartTotal->grandTotal );
		 return $this->cartTotal;
	 }


 }



// End of file Cart.php
// Location: /Cart.php