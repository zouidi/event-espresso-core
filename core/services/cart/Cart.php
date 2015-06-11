<?php

namespace EventEspresso\Core\Services\Cart;

use EventEspresso\Core;
use EventEspresso\Core\Libraries\Repositories\ObjectRepository;
use EventEspresso\Core\Libraries\Repositories\ObjectInfoArrayKeyStrategy;

if ( ! defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}
/**
 * Class Cart
 *
 * Description
 *
 * @package 			Event Espresso
 * @subpackage 	core
 * @author 				Brent Christensen
 * @since 				$VID:$
 *
 */
 class Cart {

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
	  * @type ObjectRepository $tickets
	  */
	 protected $tickets;
	 /**
	  * @type ObjectRepository $items
	  */
	 protected $items;

	 /**
	  * @type ObjectRepository $promos
	  */
	 protected $promos;



	 function __construct() {
		 $this->ID 			= $this->generateID();
		 $this->tickets 	= new ObjectRepository( new ObjectInfoArrayKeyStrategy() );
		 $this->items 		= new ObjectRepository( new ObjectInfoArrayKeyStrategy() );
		 $this->promos 	= new ObjectRepository( new ObjectInfoArrayKeyStrategy() );
		 $this->setCreated();
	 }



	 protected function generateID() {
		 $admin = is_admin() && ! EE_FRONT_AJAX ? 'admin-' : '';
		 return uniqid( $admin );
	 }



	 public function setID( $ID ) {
		 $this->ID = $ID;
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
	  * @param \DateTime $created
	  */
	 public function setCreated( \DateTime $created = null ) {
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
	  * @param \DateTime $updated
	  */
	 public function setUpdated( \DateTime $updated = null ) {
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



 }



// End of file Cart.php
// Location: /Cart.php