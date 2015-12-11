<?php
namespace EventEspresso\core\interfaces\cart;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



interface SurchargeInterface {



	/**
	 * gets ID
	 *
	 * @return int
	 */
	function ID();



	/**
	 * monetary value of surcharge.
	 *
	 * @return float
	 */
	function amount();



	/**
	 * Gets the surcharge name.
	 *
	 * @return string
	 */
	function name();



	/**
	 * Gets a string which describes the surcharge.
	 *
	 * @return string
	 */
	function description();

}
// End of file SurchargeInterface.php
// Location: /SurchargeInterface.php