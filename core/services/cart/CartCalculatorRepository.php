<?php

namespace EventEspresso\Core\Services\Cart;

use EventEspresso\core\interfaces\CartCalculatorInterface;
use EventEspresso\core\interfaces\CartCalculatorRepositoryInterface;
use EventEspresso\Core\Libraries\Repositories\EE_Object_Collection;

if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * Class CartCalculatorRepository
 *
 * Storage entity for CartCalculators that implements the
 * Countable, Iterator, Serializable, and ArrayAccess interfaces
 *
 * @package 	Event Espresso
 * @subpackage 	core
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
class CartCalculatorRepository extends EE_Object_Collection implements CartCalculatorRepositoryInterface {




	/**
	 * @param CartCalculatorInterface $cartCalculator
	 * @return bool
	 */
	public function addCartCalculator( CartCalculatorInterface $cartCalculator ) {
		return $this->add( $cartCalculator, $cartCalculator->name() );
	}



	/**
	 * @param mixed $name
	 * @return CartCalculatorInterface
	 */
	public function getCartCalculator( $name ) {
		$cartCalculator = $this->get_by_info( $name );
		if ( ! $cartCalculator instanceof CartCalculatorInterface ) {
			// exception ??
		}
		return $cartCalculator;
	}



	/**
	 * @param CartCalculatorInterface $cartCalculator
	 * @return bool
	 */
	public function hasCartCalculator( CartCalculatorInterface $cartCalculator ) {
		return $this->contains( $cartCalculator );
	}



	/**
	 * @param mixed $name
	 * @return bool
	 */
	public function hasCartCalculatorByName( $name ) {
		$cartCalculator = $this->get_by_info( $name );
		return $this->contains( $cartCalculator );
	}



	/**
	 * @param CartCalculatorInterface $cartCalculator
	 * @return void
	 */
	public function removeCartCalculator( CartCalculatorInterface $cartCalculator ) {
		$this->remove( $cartCalculator );
	}



	/**
	 * @param mixed $name
	 * @return void
	 */
	public function removeCartCalculatorByName( $name ) {
		$cartCalculator = $this->get_by_info( $name );
		$this->remove( $cartCalculator );
	}



}
// End of file CartCalculatorRepository.class.php
// Location: /core/services/cart/CartCalculatorRepository.php