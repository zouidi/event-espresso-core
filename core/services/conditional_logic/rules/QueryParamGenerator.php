<?php
namespace EventEspresso\core\services\conditional_logic\rules;

use EventEspresso\core\services\collections\Collection;

defined('ABSPATH') || exit;



/**
 * Class QueryParamGenerator
 * transforms business logic stored in Rules into query parameters
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class QueryParamGenerator
{

    /**
     * @var Collection $rules
     */
    private $rules;



    public function addRules(Collection $rules)
    {
        $this->rules = $rules;
    }



    public function getSql($type = '')
    {
        $SQL = '';
        foreach($this->rules as $rule){
            /** @var Rule $rule */
            if ($type && $rule->getType() !== $type) {
                continue;
            }
            $SQL .= $rule->getOperator();
            $SQL .= $rule->getStrategy();
            $SQL .= '.';
            $SQL .= $rule->getTarget();
            $SQL .= $rule->getComparison();
            $SQL .= $rule->getValue();
        }
        return $SQL;
    }



}
// End of file QueryParamGenerator.php
// Location: EventEspresso\core\services\conditional_logic\rules/QueryParamGenerator.php