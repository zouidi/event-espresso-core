<?php

namespace EventEspresso\Core\Services\Cart;

use EventEspresso\Core;
use EventEspresso\core\interfaces\CartInterface;
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
	  * @type TicketRepository $tickets
	  */
	 protected $tickets;
	 /**
	  * @type ProductRepository $items
	  */
	 protected $products;

	 /**
	  * @type PromotionRepository $promos
	  */
	 protected $promotions;



	 function __construct(
		 $ID,
		 TicketRepository $TicketRepository,
		 ProductRepository $ProductRepository,
		 PromotionRepository $PromoRepository,
		 \DateTime $created = null
	 ) {
		 $this->ID 		 	= $ID;
		 $this->tickets  	= $TicketRepository;
		 $this->products 	= $ProductRepository;
		 $this->promotions 	= $PromoRepository;
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
	  * @return TicketRepository
	  */
	 public function getTickets() {
		 return $this->tickets;
	 }



 }



// End of file Cart.php
// Location: /Cart.php