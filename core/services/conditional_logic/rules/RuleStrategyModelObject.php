<?php
namespace EventEspresso\core\services\conditional_logic\rules;

defined('ABSPATH') || exit;



/**
 * Class RuleStrategyModelObject
 * abstract RuleStrategy class for translating EE model object related Rules into query params
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class RuleStrategyModelObject extends RuleStrategy
{

    protected $object;

}
// End of file RuleStrategyModelObject.php
// Location: EventEspresso\core\services\conditional_logic\rules/RuleStrategyModelObject.php