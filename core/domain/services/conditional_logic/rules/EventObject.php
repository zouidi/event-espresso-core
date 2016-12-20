<?php
namespace EventEspresso\core\domain\services\conditional_logic\rules;

use EE_Event;
use EventEspresso\core\services\conditional_logic\rules\RuleStrategyModelObject;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class Event
 * RuleStrategy class for translating EE_Event related Rules into query params
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class EventObject extends RuleStrategyModelObject
{



    /**
     * Event constructor.
     *
     * @param $object
     */
    public function __construct(EE_Event $object)
    {
        $this->object = $object;
    }




}
// End of file Event.php
// Location: /core/domain/services/conditional_logic/rules/EventObject.php