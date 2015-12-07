<?php
namespace EventEspresso\core\services\cart;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class CartTotal
 *
 * A DTO (Data Transfer Object) for all of the totals and subtotals of a cart's prices
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
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