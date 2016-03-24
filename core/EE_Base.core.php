<?php
if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}
/**
 *
 * EE_BASE
 *
 * @package			Event Espresso
 * @subpackage	core/
 * @author				Brent Christensen
 */
class EE_Base {

	/**
	 * @override magic methods
	 * @param $a
	 * @param $b
	 * @return bool
	 */
	public function __set( $a, $b ) {
		return false;
	}



	/**
	 * @param $a
	 * @return bool
	 */
	public function __get( $a ) {
		return false;
	}



	/**
	 * @param $a
	 * @return bool
	 */
	public function __isset( $a ) {
		return false;
	}



	/**
	 * @param $a
	 * @return bool
	 */
	public function __unset( $a ) {
		return false;
	}



	public function __clone() {
	}



	public function __wakeup() {
	}



	public function __destruct() {
	}

}
// End of file EE_BASE.core.php
// Location: /core/EE_BASE.core.php