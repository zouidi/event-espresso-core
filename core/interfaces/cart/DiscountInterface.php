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
	 * monetary value of discount.
	 *
	 * @return float
	 */
	function amount();



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

}
// End of file DiscountInterface.php
// Location: /DiscountInterface.php