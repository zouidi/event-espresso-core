<?php
namespace EventEspresso\core\domain\services\conditional_logic\rules;

use EventEspresso\core\services\conditional_logic\rules\RuleStrategyForQuery;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class EventQuery
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class EventQuery extends RuleStrategyForQuery
{



    public function category()
    {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        \EEH_Debug_Tools::printr($this->comparison, '$this->comparison', __FILE__, __LINE__);
        \EEH_Debug_Tools::printr($this->value, '$this->value', __FILE__, __LINE__);
        \EEM_Event::instance();
    }

}
// End of file EventQuery.php
// Location: EventEspresso\core\domain\services\conditional_logic\rules/EventQuery.php