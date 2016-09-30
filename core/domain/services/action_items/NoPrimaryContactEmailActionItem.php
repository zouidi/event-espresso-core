<?php
namespace EventEspresso\core\domain\services\action_items;

use EventEspresso\core\domain\services\capabilities\CapCheck;
use EventEspresso\core\domain\services\capabilities\CapCheckInterface;
use EventEspresso\core\domain\services\capabilities\RequiresCapCheckInterface;
use EventEspresso\core\services\action_items\ActionItem;

defined( 'ABSPATH' ) || exit;



/**
 * Class NoPrimaryContactEmailActionItem
 * Checks that the Primary Contact Email field in the organization settings is not empty
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class NoPrimaryContactEmailActionItem extends ActionItem implements RequiresCapCheckInterface
{


    /**
     * @var boolean $email_not_set
     */
    private $email_not_set = false;



    public function doConditionCheck()
    {
        $this->email_not_set = empty( \EE_Config::instance()->organization->email )
        && ! (
                isset( $_REQUEST['page'] )
                && $_REQUEST['page'] === 'espresso_general_settings'
            );
    }



    /**
     * @return bool
     */
    public function conditionPassed()
    {
        return $this->email_not_set;
    }



    /**
     * @return string
     */
    public function getActionItemNotice()
    {
        return esc_html__(
            'The "Primary Contact Email Address" for your organization is currently not set, which will negatively affect your ability to send and receive event related emails.',
            'event_espresso'
        );
    }



    /**
     * @return string
     */
    public function getActionItemUrl()
    {
        return \EE_Admin_Page::add_query_args_and_nonce(
            array(),
            GEN_SET_ADMIN_URL
        ) . '#organization_country';
    }



    /**
     * @return string
     */
    public function getActionItemButtonText()
    {
        return esc_html__( 'Add Email Address', 'event_espresso' );
    }



    /**
     * @return CapCheckInterface
     */
    public function getCapCheck()
    {
        if ( ! $this->cap_check instanceof CapCheckInterface ) {
            $this->setCapCheck( new CapCheck( 'manage_options', 'Add Primary Contact Email Address' ) );
        }
        return $this->cap_check;
    }


}
// End of file NoPrimaryContactEmailActionItem.php
// Location: EventEspresso\core\domain\services\action_items/NoPrimaryContactEmailActionItem.php