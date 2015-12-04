<?php
namespace EventEspresso\core\interfaces;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



interface CartInterface {


	function ID();



	/**
	 * @return boolean
	 */
	function open();



	/**
	 * @param boolean $open
	 */
	function setOpen( $open );



	/**
	 * sets the cart open status to false
	 */
	function closeCart();



	/**
	 * @return \DateTime
	 */
	function getCreated();



	/**
	 * @return \DateTime
	 */
	function getUpdated();



}
// End of file CartInterface.php
// Location: /CartInterface.php