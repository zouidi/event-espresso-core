<?php
namespace EventEspresso\core\services\cart;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class cartItemOption
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
abstract class cartItemOption {


	/** @var string */
	protected $SKU = '';



	abstract public function generateSKU();



	/**
	 * @return string
	 */
	public function SKU() {
		if ( empty( $this->SKU ) ) {
			$this->SKU = $this->generateSKU();
		}
		return $this->SKU;
	}


}
// End of file cartItemOption.php
// Location: /cartItemOption.php