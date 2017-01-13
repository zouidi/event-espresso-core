<?php
namespace EventEspresso\core\services\conditional_logic\rules;

use EEM_Base;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class RuleStrategyForQuery
 * abstract RuleStrategy class for translating EE model related Rules into query params
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class RuleStrategyForQuery extends RuleStrategy
{


    /**
     * @var EEM_Base $model
     */
    protected $model;


}
// End of file RuleStrategyForQuery.php
// Location: EventEspresso\core\services\conditional_logic\rules/RuleStrategyForQuery.php