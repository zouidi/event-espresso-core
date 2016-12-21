<?php
namespace EventEspresso\core\services\conditional_logic\rules;

use DomainException;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\Benchmark;
use EventEspresso\core\services\collections\Collection;
use InvalidArgumentException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class RuleManager
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class RuleManager
{

    const RULE_TYPE_QUERY = 'query';
    const RULE_TYPE_OBJECT = 'object';

    /**
     * @var QueryParamGenerator $query_generator
     */
    private $query_generator;



    /**
     * RuleManager constructor
     *
     * @param QueryParamGenerator $query_generator
     * @throws InvalidInterfaceException
     */
    public function __construct(QueryParamGenerator $query_generator)
    {
        $this->query_generator = $query_generator;
    }



    /**
     * @param \stdClass[] $results
     * @return Collection
     * @throws InvalidInterfaceException
     * @throws InvalidArgumentException
     * @throws InvalidEntityException
     */
    protected function getRulesCollection(array $results)
    {
        Benchmark::startTimer(__METHOD__);
        $rules = new Collection('EventEspresso\core\services\conditional_logic\rules\Rule');
        foreach ($results as $result) {
            $rules->add(new Rule($result));
        }
        Benchmark::stopTimer(__METHOD__);
        return $rules;
    }



    /**
     * @param string $OBJ_name
     * @param mixed  $OBJ_ID
     * @return array
     * @throws DomainException
     */
    public function retrieveRules($OBJ_name, $OBJ_ID)
    {
        Benchmark::startTimer(__METHOD__);
        global $wpdb;
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}esp_object_rule AS object_rule
                 LEFT JOIN {$wpdb->prefix}esp_rule AS rule
                 ON object_rule.RUL_ID = rule.RUL_ID
                 WHERE object_rule.ORL_OBJ_name = %s
                 AND object_rule.ORL_OBJ_ID = %d
                 ORDER BY object_rule.ORL_order",
                $OBJ_name,
                $OBJ_ID
            )
        );
        \EEH_Debug_Tools::printr($results, '$results', __FILE__, __LINE__);
        if ($results instanceof \WP_Error) {
            throw new DomainException(
                $results->get_error_message()
            );
        }
        $results = is_array($results) ? $results : array($results);
        Benchmark::stopTimer(__METHOD__);
        return $results;
    }



    /**
     * @param string $OBJ_name
     * @param mixed  $OBJ_ID
     * @return Collection
     * @throws InvalidArgumentException
     * @throws InvalidInterfaceException
     * @throws InvalidEntityException
     * @throws DomainException
     */
    public function retrieveRulesForObject( $OBJ_name = '', $OBJ_ID )
    {
        return $this->getRulesCollection(
            $this->retrieveRules($OBJ_name, $OBJ_ID)
        );
    }



    /**
     * @param string $OBJ_name
     * @param mixed  $OBJ_ID
     * @return array
     * @throws InvalidArgumentException
     * @throws InvalidInterfaceException
     * @throws InvalidEntityException
     * @throws DomainException
     */
    public function getQueryParamsForObjectRules( $OBJ_name = '', $OBJ_ID )
    {
        return $this->getQueryParamsForRules(
            $this->getRulesCollection(
                $this->retrieveRules($OBJ_name, $OBJ_ID)
            )
        );
    }



    /**
     * @param Collection $rules
     * @return array
     */
    public function getQueryParamsForRules(Collection $rules)
    {
        $this->query_generator->addRules($rules);
        $SQL = $this->query_generator->generateQueryParams(RuleManager::RULE_TYPE_QUERY);
        \EEH_Debug_Tools::printr($SQL, '$SQL', __FILE__, __LINE__);
        return $SQL;
    }


}
// End of file RuleManager.php
// Location: EventEspresso\core\services\conditional_logic\rules/RuleManager.php