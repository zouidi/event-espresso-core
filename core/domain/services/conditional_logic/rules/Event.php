<?php
namespace EventEspresso\core\domain\services\conditional_logic\rules;

use EE_Event;
use EventEspresso\core\services\conditional_logic\rules\modelObject;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class Event
 * RuleStrategy class for translating EE_Event related Rules into query params
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class Event extends modelObject
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



    public function category()
    {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
    }

}
// End of file Event.php
// Location: EventEspresso\core\domain\services\conditional_logic\rules/Event.php