<?php
namespace EventEspresso\core\interfaces\cart;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



interface DiscountInterface {



	/**
	 * gets ID
	 *
	 * @return int
	 */
	function ID();



	/**
	 * Gets the discount name.
	 *
	 * @return string
	 */
	function name();



	/**
	 * Gets a string which describes the discount.
	 *
	 * @return string
	 */
	function description();



	/**
	 * monetary value of discount.
	 *
	 * @return float
	 */
	function amount();



	/**
	 * whether discount is applied before or after taxes are calculated
	 *
	 * @return float
	 */
	function isTaxable();

}
// End of file DiscountInterface.php
// Location: /DiscountInterface.php