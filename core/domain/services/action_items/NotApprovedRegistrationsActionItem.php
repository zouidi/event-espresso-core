<?php
namespace EventEspresso\core\domain\services\action_items;

use EventEspresso\core\domain\services\capabilities\CapCheck;
use EventEspresso\core\domain\services\capabilities\CapCheckInterface;
use EventEspresso\core\domain\services\capabilities\RequiresCapCheckInterface;
use EventEspresso\core\services\action_items\ActionItem;

defined( 'ABSPATH' ) || exit;



/**
 * Class NotApprovedRegistrationsActionItem
 * checks for any registrations with a status of Not Approved
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class NotApprovedRegistrationsActionItem extends ActionItem implements RequiresCapCheckInterface
{


    /**
     * @var int $rna_count
     */
    private $rna_count = 0;



    public function doConditionCheck()
    {
        $this->rna_count = absint(
            \EEM_Registration::instance()->count(
                array( array( 'STS_ID' => \EEM_Registration::status_id_not_approved ) ),
                'REG_ID'
            )
        );
    }



    /**
     * @return bool
     */
    public function conditionPassed()
    {
        return $this->rna_count > 0
            && ! (
                isset( $_REQUEST['page'], $_REQUEST['_reg_status'] )
                && (
                    $_REQUEST['page'] === 'espresso_registrations'
                    && $_REQUEST['_reg_status'] === \EEM_Registration::status_id_not_approved
                )
                || (
                    isset( $_REQUEST['action'] )
                    && $_REQUEST['action'] === 'view_registration'
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
                'There is currently "%1$d" registration with a status of "Not Approved" awaiting your decision. You can either set its status to "Approved" or "Declined".',
                'There are currently "%1$d" registrations with a status of "Not Approved" awaiting your decision. You can either set their status to "Approved" or "Declined".',
                $this->rna_count,
                'event_espresso'
            ),
            $this->rna_count
        );
    }



    /**
     * @return string
     */
    public function getActionItemUrl()
    {
        return \EE_Admin_Page::add_query_args_and_nonce(
            array( '_reg_status' => \EEM_Registration::status_id_not_approved, 'action' => '' ),
            REG_ADMIN_URL
        );
    }



    /**
     * @return string
     */
    public function getActionItemButtonText()
    {
        return esc_html__( 'View Registrations', 'event_espresso' );
    }



    /**
     * @return CapCheckInterface
     */
    public function getCapCheck()
    {
        if ( ! $this->cap_check instanceof CapCheckInterface ) {
            $this->setCapCheck( new CapCheck( 'ee_edit_registrations', 'approve_registrations' ) );
        }
        return $this->cap_check;
    }



}
// End of file NotApprovedRegistrationsActionItem.php
// Location: EventEspresso\core\domain\services\action_items/NotApprovedRegistrationsActionItem.php