<?php
namespace EventEspresso\core\domain\services\conditional_logic\rules;

use EEM_Event;
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



    /**
     * @return \EEM_Event
     */
    protected function get_model()
    {
        if (!$this->model instanceof EEM_Event) {
            return EEM_Event::instance();
        }
        return $this->model;
    }



    public function eventCategory()
    {
        return array(
            'Term_Taxonomy.taxonomy' => array($this->comparison, 'espresso_event_categories' ),
            'Term_Taxonomy.term_id' => array($this->comparison, $this->value),
        );
    }



    public function eventStart()
    {
        return array(
            'Datetime.DTT_EVT_start' => array($this->comparison, $this->value),
        );
    }



    public function eventEnd()
    {
        return array(
            'Datetime.DTT_EVT_end' => array($this->comparison, $this->value),
        );
    }

}
// End of file EventQuery.php
// Location: EventEspresso\core\domain\services\conditional_logic\rules/EventQuery.php