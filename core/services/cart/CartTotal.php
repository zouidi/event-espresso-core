<?php
namespace EventEspresso\core\services\cart;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



class CartTotal {

	/** @var float */
	public $preTaxSubtotal = 0;

	/** @var float */
	public $totalTicketAmount = 0;

	/** @var int */
	public $ticketCount = 0;

	/** @var float */
	public $totalDiscount = 0;

	/** @var float */
	public $taxSubtotal = 0;

	/** @var float */
	public $Subtotal = 0;

	/** @var float */
	public $grandTotal = 0;


}
// End of file CartTotal.php
// Location: /core/services/cart/CartTotal.php