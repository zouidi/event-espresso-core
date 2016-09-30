<?php
namespace EventEspresso\core\services\action_items;

defined( 'ABSPATH' ) || exit;

/**
 * Class ActionItem
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
interface ActionItemInterface
{

    public function setConditionCheckHooks();


    public function doConditionCheck();

    /**
     * @return bool
     */
    public function conditionPassed();

    /**
     * @return string
     */
    public function getActionItemNotice();

    /**
     * @return string
     */
    public function getActionItemUrl();

    /**
     * @return string
     */
    public function getActionItemButtonText();


}
// End of file ActionItemInterface.php
// Location: EventEspresso\core\services\action_items;
