<?php
namespace EventEspresso\core\services\cart;

use EventEspresso\Core;
use EventEspresso\Core\Libraries\Repositories\EE_Object_Collection;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class CartItem
 *
 * @package 	Event Espresso
 * @subpackage 	core
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
abstract class CartItem {

	/**
	 * date and time the cart was first created in UTC+0
	 *
	 * @type \DateTime $created
	 */
	protected $created = null;

	/** @var Cart */
	protected $cart = null;

	/** @var string */
	protected $SKU = '';

	/** @var object */
	protected $item = null;

	/** @var int */
	protected $quantity = 0;

	/** @var EE_Object_Collection $cartItemOptions */
	protected $cartItemOptions;



	public function __construct( Cart $cart ) {
		$this->setCreated();
		$this->cart = $cart;
		$this->cartItemOptions = new EE_Object_Collection( null, 'CartItemOption' );
	}



	abstract public function generateSKU();

	abstract public function validateItem( $item );

	abstract public function calculatePrice();

	abstract public function isTaxable();



	/**
	 * @access protected
	 */
	protected function setCreated() {
		$this->created = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
	}



	/**
	 * @return string
	 */
	public function SKU() {
		if ( empty( $this->SKU ) ) {
			$this->SKU = $this->generateFullSKU();
		}
		return $this->SKU;
	}



	/**
	 * @return object
	 */
	final public function getItem() {
		return $this->validateItem( $this->item );
	}



	/**
	 * @return Cart
	 */
	public function getCart() {
		return $this->cart;
	}



	/**
	 * @return int
	 */
	public function quantity() {
		return $this->quantity;
	}



	/**
	 * @param int $quantity
	 */
	public function setQuantity( $quantity = 1 ) {
		$this->quantity = absint( $quantity );
	}



	/**
	 * @return EE_Object_Collection
	 */
	public function getCartItemOptions() {
		return $this->cartItemOptions;
	}



	/**
	 * @param CartItemOption $cartItemOption
	 */
	public function addCartItemOption ( CartItemOption $cartItemOption ) {
		$this->cartItemOptions->add( $cartItemOption, $cartItemOption->SKU() );
	}



	/**
	 * generateFullSKU
	 *
	 * adds SKUs for options to item SKU to differentiate between customized items
	 *
	 * @return string
	 */
	protected function generateFullSKU() {
		$SKU = array();
		$SKU[] = $this->generateSKU();
		foreach ( $this->cartItemOptions as $cartItemOption ) {
			$SKU[] = $cartItemOption->SKU();
		}
		return md5( implode( '.', $SKU ));
	}





}
// End of file CartItem.php
// Location: /CartItem.php