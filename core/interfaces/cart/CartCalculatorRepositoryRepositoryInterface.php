<?php
namespace EventEspresso\core\interfaces;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Cart Calculator Repository Interface
 *
 * @package    Event Espresso
 * @subpackage interfaces
 * @since      4.8.0
 * @author     Brent Christensen
 */
interface CartCalculatorRepositoryInterface extends EEI_Collection {



	/**
	 * @param CartCalculatorInterface $cartCalculator
	 * @return bool
	 */
	function addCartCalculator( CartCalculatorInterface $cartCalculator );



	/**
	 * @param string $name
	 * @return null | object
	 */
	function getCartCalculator( $name );



	/**
	 * @param CartCalculatorInterface $cartCalculator
	 * @return bool
	 */
	function hasCartCalculator( CartCalculatorInterface $cartCalculator );



	/**
	 * @param string $name
	 * @return bool
	 */
	function hasCartCalculatorByName( $name );



	/**
	 * @param CartCalculatorInterface $cartCalculator
	 * @return void
	 */
	function removeCartCalculator( CartCalculatorInterface $cartCalculator );



	/**
	 * @param string $name
	 * @return void
	 */
	function removeCartCalculatorByName( $name );




}
// End of file CartCalculatorRepositoryInterface.php
// Location: /core/interfaces/CartCalculatorRepositoryInterface.php