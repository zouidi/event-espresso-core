<?php
namespace EventEspresso\core\domain\services\action_items;

use EventEspresso\core\domain\services\capabilities\CapCheck;
use EventEspresso\core\domain\services\capabilities\CapCheckInterface;
use EventEspresso\core\domain\services\capabilities\RequiresCapCheckInterface;
use EventEspresso\core\services\action_items\ActionItem;

defined( 'ABSPATH' ) || exit;



/**
 * Class RefundsRequiredActionItem
 * checks for any transactions with a status of Overpaid
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class RefundsRequiredActionItem extends ActionItem implements RequiresCapCheckInterface
{



    /**
     * @var int $transactions_requiring_refund
     */
    private $transactions_requiring_refund = 0;



    public function doConditionCheck()
    {
        $this->transactions_requiring_refund = absint(
            \EEM_Transaction::instance()->count(
                array( array( 'STS_ID' => \EEM_Transaction::overpaid_status_code ) ),
                'TXN_ID'
            )
        );
    }



    /**
     * @return bool
     */
    public function conditionPassed()
    {
        return $this->transactions_requiring_refund > 0
            && ! (
                isset( $_REQUEST['page'], $_REQUEST['status'] )
                && (
                    $_REQUEST['page'] === 'espresso_transactions'
                    && $_REQUEST['status'] === 'overpaid'
                )
                || (
                    isset( $_REQUEST['action'] )
                    && $_REQUEST['action'] === 'view_transaction'
                )
            );
    }



    /**
     * @return string
     */
    public function getActionItemNotice()
    {
        return sprintf(
            _n(
                'There is currently "%1$d" transaction with a status of "Overpaid" that may require a refund.',
                'There are currently "%1$d" transactions with a status of "Overpaid" that may require a refund.',
                $this->transactions_requiring_refund,
                'event_espresso'
            ),
            $this->transactions_requiring_refund
        );
    }



    /**
     * @return string
     */
    public function getActionItemUrl()
    {
        return \EE_Admin_Page::add_query_args_and_nonce(
            array( 'status' => 'overpaid', 'action' => '' ),
            TXN_ADMIN_URL
        );
    }



    /**
     * @return string
     */
    public function getActionItemButtonText()
    {
        return esc_html__( 'View Transactions', 'event_espresso' );
    }



    /**
     * @return CapCheckInterface
     */
    public function getCapCheck()
    {
        if ( ! $this->cap_check instanceof CapCheckInterface ) {
            $this->setCapCheck( new CapCheck( 'ee_edit_payments', 'Issue Refund' ) );
        }
        return $this->cap_check;
    }
}
// End of file RefundsRequiredActionItem.php
// Location: EventEspresso\core\domain\services\action_items/RefundsRequiredActionItem.php