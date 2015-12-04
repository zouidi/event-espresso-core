<?php

namespace EventEspresso\Core\Services\Cart;

use EventEspresso\Core;
use EventEspresso\Core\Libraries\Repositories\EE_Base_Class_Repository;

if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * Class PromotionRepository
 *
 * Storage entity for Carts that implements the
 * Countable, Iterator, Serializable, and ArrayAccess interfaces
 *
 * @package 	Event Espresso
 * @subpackage 	core
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
class PromotionRepository extends EE_Base_Class_Repository {



	/**
	 * @param \EE_Promotion $promotion
	 * @return bool
	 */
	public function add_promotion( \EE_Promotion $promotion ) {
		return $this->add( $promotion, $promotion->ID() );
	}



	/**
	 * @param mixed $ID
	 * @return null | object
	 */
	public function get_promotion( $ID ) {
		$promotion = $this->get_by_info( $ID );
		if ( ! $promotion instanceof \EE_Ticket ) {
			// ??? exception ???
		}
		return $promotion;
	}



	/**
	 * @param mixed $ID
	 * @return null | object
	 */
	public function get_promotion_by_id( $ID ) {
		return $this->get_by_info( $ID );
	}



	/**
	 * @param \EE_Promotion $promotion
	 * @return bool
	 */
	public function has_promotion( \EE_Promotion $promotion ) {
		return $this->has( $promotion );
	}



	/**
	 * @param mixed $ID
	 * @return bool
	 */
	public function has_promotion_by_id( $ID ) {
		$promotion = $this->get_by_info( $ID );
		return $this->has( $promotion );
	}



	/**
	 * @param \EE_Promotion $promotion
	 * @return bool | int
	 */
	public function save_promotion( \EE_Promotion $promotion ) {
		return $this->persist( $promotion, 'save' );
	}



	/**
	 * @param mixed $ID
	 * @return bool | int
	 */
	public function save_promotion_by_id( $ID ) {
		$promotion = $this->get_by_info( $ID );
		return $this->persist( $promotion, 'save' );
	}



	/**
	 * @param \EE_Promotion $promotion
	 * @return void
	 */
	public function remove_promotion( \EE_Promotion $promotion ) {
		$this->remove( $promotion );
	}



	/**
	 * @param mixed $ID
	 * @return void
	 */
	public function remove_promotion_by_id( $ID ) {
		$promotion = $this->get_by_info( $ID );
		$this->remove( $promotion );
	}



}
// End of file PromotionRepository.class.php
// Location: /core/services/cart/PromotionRepository.php