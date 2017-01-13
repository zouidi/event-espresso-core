<?php
namespace EventEspresso\core\services\conditional_logic\rules;

defined('EVENT_ESPRESSO_VERSION') || exit;



abstract class RuleStrategy
{

    /**
     * @var string $target
     */
    protected $target = '';

    /**
     * @var string $comparison
     */
    protected $comparison = '';

    /**
     * @var string $value
     */
    protected $value = '';


    /**
     * @param \EventEspresso\core\services\conditional_logic\rules\Rule $rule
     * @return mixed
     */
    public function getQueryParamForRule(Rule $rule) {
        $this->comparison = $rule->getComparison();
        $this->value      = $rule->getValue();
        $target           = $rule->getTarget();
        // \EEH_Debug_Tools::printr($target, '$target', __FILE__, __LINE__);
        return method_exists($this, $target)
            ? $this->{$target}()
            : "{$target} {$this->comparison} {$this->value}";
    }

}
// End of file RuleStrategy.php
// Location: EventEspresso\core\services\conditional_logic\rules/RuleStrategy.php