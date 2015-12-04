<?php

namespace EventEspresso\Core\Services\Cart;

use EventEspresso\Core;
use EventEspresso\Core\Entities\Product;
use EventEspresso\Core\Libraries\Repositories\EE_Object_Repository;

if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * Class ProductRepository
 *
 * Storage entity for Products that implements the
 * Countable, Iterator, Serializable, and ArrayAccess interfaces
 *
 * @package 	Event Espresso
 * @subpackage 	core
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
class ProductRepository extends EE_Object_Repository {



	/**
	 * @param Product $product
	 * @return bool
	 */
	public function add_product( Product $product ) {
		return $this->add( $product, $product->ID() );
	}



	/**
	 * @param mixed $ID
	 * @return Product
	 */
	public function get_product( $ID ) {
		$product = $this->get_by_info( $ID );
		if ( ! $product instanceof Product ) {
			// ??? exception ???
		}
		return $product;
	}



	/**
	 * @param mixed $ID
	 * @return Product
	 */
	public function get_product_by_id( $ID ) {
		return $this->get_by_info( $ID );
	}



	/**
	 * @param Product $product
	 * @return bool
	 */
	public function has_product( Product $product ) {
		return $this->has( $product );
	}



	/**
	 * @param mixed $ID
	 * @return bool
	 */
	public function has_product_by_id( $ID ) {
		$product = $this->get_by_info( $ID );
		return $this->has( $product );
	}



	/**
	 * @param Product $product
	 * @return bool | int
	 */
	public function save_product( Product $product ) {
		return $this->persist( $product, 'save' );
	}



	/**
	 * @param mixed $ID
	 * @return bool | int
	 */
	public function save_product_by_id( $ID ) {
		$product = $this->get_by_info( $ID );
		return $this->persist( $product, 'save' );
	}



	/**
	 * @param Product $product
	 * @return void
	 */
	public function remove_product( Product $product ) {
		$this->remove( $product );
	}



	/**
	 * @param mixed $ID
	 * @return void
	 */
	public function remove_product_by_id( $ID ) {
		$product = $this->get_by_info( $ID );
		$this->remove( $product );
	}



}
// End of file ProductRepository.class.php
// Location: /core/services/cart/ProductRepository.php