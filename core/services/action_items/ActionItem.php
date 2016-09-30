<?php
namespace EventEspresso\core\services\action_items;

use EventEspresso\core\domain\services\capabilities\CapCheckInterface;

defined( 'ABSPATH' ) || exit;



/**
 * Class ActionItem
 * abstract base class for ActionItem classes
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class ActionItem implements ActionItemInterface
{


    /*
     * @var CapCheckInterface $cap_check
     */
    protected $cap_check;




    /**
     * @param CapCheckInterface $cap_check
     */
    public function setCapCheck( CapCheckInterface $cap_check )
    {
        $this->cap_check = $cap_check;
    }



    /**
     * default method for setting up condition checks
     *
     * @return void
     */
    public function setConditionCheckHooks()
    {
        add_action(
            'AHEE__EE_System__core_loaded_and_ready',
            array( $this, 'doConditionCheck' )
        );
    }



    /**
     * @return string
     */
    public function getActionItemButtonText() {
        return esc_html__( 'Click here to take action', 'event_espresso' );
    }



}
// End of file ActionItem.php
// Location: EventEspresso\core\services\action_items/ActionItem.php