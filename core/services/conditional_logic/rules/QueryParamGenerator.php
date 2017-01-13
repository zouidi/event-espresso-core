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

    // /**
    //  * @var array $query_params
    //  */
    // private $query_params;



    public function addRules(Collection $rules)
    {
        $this->rules = $rules;
    }



    public function resetRules()
    {
        $this->rules = null;
    }



    public function generateQueryParams($type = '')
    {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        $rules = $this->getRulesAsArray($type);
        // \EEH_Debug_Tools::printr($rules, 'Rules As Array', __FILE__, __LINE__);
        $query_params = $this->addQueryParamForRule($rules, array());
        // \EEH_Debug_Tools::printr($query_params, '>>> $QUERY_PARAMS', __FILE__, __LINE__);
        return $query_params;
    }



    private function getRulesAsArray($type = '')
    {
        // \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        $rules_array = array();
        foreach ($this->rules as $rule) {
            /** @var Rule $rule */
            if ($type && $rule->getType() !== $type){
                continue;
            }
            $rules_array = $this->addRuleToArray($rules_array, $rule);
        }
        return $rules_array;
    }



    private function addRuleToArray(array $rules_array, Rule $rule)
    {
        // static $level = 0;
        // \EEH_Debug_Tools::printr(__FUNCTION__, $level . ') ' .__CLASS__, __FILE__, __LINE__, 2);
        // \EEH_Debug_Tools::printr((string)$rule->getID(), 'ID', __FILE__, __LINE__);
        $parent = $rule->getParent();
        // \EEH_Debug_Tools::printr((string)$parent, 'Parent', __FILE__, __LINE__);
        if ($rule->getOperator() === ')') {
            // $level--;
            // \EEH_Debug_Tools::printr('SUBGROUP', 'CLOSE', __FILE__, __LINE__);
            return $rules_array;
        } else if ( isset($rules_array[$parent])) {
            // $level++;
            // \EEH_Debug_Tools::printr('PARENT', 'FOUND', __FILE__, __LINE__);
            $rules_array[$parent] = $this->addRuleToArray($rules_array[$parent], $rule);
            return $rules_array;
        } else if (! empty($rules_array)){
            // \EEH_Debug_Tools::printr('SUB ARRAY', 'FOUND', __FILE__, __LINE__);
            // \EEH_Debug_Tools::printr($rules_array, '$rules_array', __FILE__, __LINE__);
            foreach($rules_array as $ID => $rules) {
                // \EEH_Debug_Tools::printr($ID, '$rules_array $ID', __FILE__, __LINE__);
                if($ID === $parent) {
                    // \EEH_Debug_Tools::printr($ID === $parent, '$ID === $parent', __FILE__, __LINE__);
                    $rules_array[$ID] = $this->addRuleToArray($rules[$ID], $rule);
                    return $rules_array;
                }
            }
        }
        // \EEH_Debug_Tools::printr(
        //     $rule->getOperator()
        //     . ' '
        //     . $rule->getTarget()
        //     . ' '
        //     . $rule->getComparison()
        //     . ' '
        //     . $rule->getValue(), 'ADD RULE',
        //     __FILE__, __LINE__
        // );
        $rules_array[$rule->getID()] = array();
        $rules_array[$rule->getID()]['rule'] = $rule;
        return $rules_array;
    }



    public function addQueryParamForRule($rules, array $query_params, $operator = ' AND ')
    {
        // \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        // \EEH_Debug_Tools::printr($rule, '$rule', __FILE__, __LINE__);
        foreach ($rules as $key => $rule) {
            // \EEH_Debug_Tools::printr((string)$key, '2) $key', __FILE__, __LINE__);
            if (is_array($rule)) {
                // \EEH_Debug_Tools::printr(is_array($rule), 'is_array($rule)', __FILE__, __LINE__);
                $query_params = $this->addQueryParamForRule($rule, $query_params, $operator);
            } else {
                /** @var Rule $rule */
                // \EEH_Debug_Tools::printr($rule, 'getQueryParamForRule', __FILE__, __LINE__);
                $operator = $rule->getOperator();
                $parent = $rule->getParent();
                $parent = $parent ? $parent : 0;
                if ( ! isset($query_params[$parent])) {
                    $query_params[$parent] = array();
                }
                \EEH_Debug_Tools::printr($operator, '$operator', __FILE__, __LINE__);
                \EEH_Debug_Tools::printr((string)$parent, '$parent', __FILE__, __LINE__);
                switch ($operator) {
                    default :
                    case '  ';
                    case ' AND ';
                        $query_params[$parent] = array_merge(
                            $query_params[$parent],
                            $this->getQueryParamForRule($rule)
                        );
                        break;
                    case ' OR ';
                        $query_params[$parent] = array_merge(
                            $query_params[$parent],
                            array('OR' => $this->getQueryParamForRule($rule))
                        );
                        break;
                    case ' AND ( ';
                        $query_params[$parent] = array_merge(
                            $query_params[$parent],
                            array( $this->getQueryParamForRule($rule) )
                        );
                        break;
                    case ' OR ( ';
                        $query_params['OR'][$parent] = array_merge(
                            $query_params['OR'][$parent],
                            $this->getQueryParamForRule($rule)
                        );
                        break;
                    case ' ) ';
                        break;
                }
            }
        }
        // \EEH_Debug_Tools::printr($query_params, '$query_params', __FILE__, __LINE__);
        return $query_params;
    }



    protected function getQueryParamForRule(Rule $rule)
    {
        // \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        $strategy = $rule->getStrategy();
        if (
            ! class_exists($strategy)
            || ! is_subclass_of($strategy, '\EventEspresso\core\services\conditional_logic\rules\RuleStrategy')
        ){
            throw new \DomainException(
                sprintf(
                    esc_html__('The "%1$s" class is either missing or not a valid Rule Strategy.', 'event_espresso'),
                    $strategy
                )
            );
        }
        /** @var RuleStrategy $strategy */
        $strategy = new $strategy();
        $query_param = $strategy->getQueryParamForRule($rule);
        // \EEH_Debug_Tools::printr($query_param, '$query_param', __FILE__, __LINE__);
        return $query_param;
    }

}
// End of file QueryParamGenerator.php
// Location: EventEspresso\core\services\conditional_logic\rules/QueryParamGenerator.php