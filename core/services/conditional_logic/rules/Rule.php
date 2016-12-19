<?php
namespace EventEspresso\core\services\conditional_logic\rules;

use InvalidArgumentException;

defined('ABSPATH') || exit;



/**
 * Class Rule
 * DTO for storing data related to Rules
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class Rule
{

    /**
     * @var int $ID
     */
    private $ID = 0;

    /**
     * @var int $order
     */
    private $order = 0;

    /**
     * @var int $parent
     */
    private $parent = 0;

    /**
     * @var string $operator
     */
    private $operator = '';

    /**
     * @var string $type
     */
    private $type = '';

    /**
     * @var string $strategy
     */
    private $strategy = '';

    /**
     * @var string $target
     */
    private $target = '';

    /**
     * @var string $comparison
     */
    private $comparison = '';

    /**
     * @var string $value
     */
    private $value = '';

    /**
     * @var string $extra
     */
    private $extra = '';



    /**
     * Rule constructor.
     *
     * @param \stdClass $result
     * @throws \InvalidArgumentException
     */
    public function __construct(\stdClass $result)
    {
        $this->setID($result);
        $this->setOrder($result);
        $this->setParent($result);
        $this->setOperator($result);
        $this->setType($result);
        $this->setStrategy($result);
        $this->setTarget($result);
        $this->setComparison($result);
        $this->setValue($result);
        $this->setExtra($result);
    }



    /**
     * @return int
     */
    public function getID()
    {
        return $this->ID;
    }



    /**
     * @param \stdClass $result
     * @throws \InvalidArgumentException
     */
    protected function setID(\stdClass $result)
    {
        $this->ID = isset($result->ORL_ID) ? absint($result->ORL_ID) : 0;
        if(! $this->ID) {
            throw new InvalidArgumentException();
        }
    }



    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }



    /**
     * @param \stdClass $result
     * @throws \InvalidArgumentException
     */
    protected function setOrder(\stdClass $result)
    {
        $this->order = isset($result->ORL_order) ? absint($result->ORL_order) : 1;
        if ( ! $this->order) {
            throw new InvalidArgumentException();
        }
    }



    /**
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }



    /**
     * @param \stdClass $result
     */
    protected function setParent(\stdClass $result)
    {
        $this->parent = isset($result->ORL_parent) ? sanitize_text_field($result->ORL_parent) : 0;
    }



    /**
     * @param bool $spaces
     * @return string
     */
    public function getOperator($spaces = true)
    {
        return $spaces ? " {$this->operator} " : $this->operator;
    }



    /**
     * @param \stdClass $result
     */
    protected function setOperator(\stdClass $result)
    {
        $this->operator = isset($result->ORL_operator) ? sanitize_text_field($result->ORL_operator) : '';
    }



    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }



    /**
     * @param \stdClass $result
     * @throws \InvalidArgumentException
     */
    protected function setType(\stdClass $result)
    {
        $this->type = isset($result->RUL_type) ? sanitize_text_field($result->RUL_type) : '';
        // if ( ! $this->type) {
        //     throw new InvalidArgumentException();
        // }
    }



    /**
     * @return string
     */
    public function getStrategy()
    {
        return $this->strategy;
    }



    /**
     * @param \stdClass $result
     * @throws \InvalidArgumentException
     */
    protected function setStrategy(\stdClass $result)
    {
        $this->strategy = isset($result->RUL_strategy) ? sanitize_text_field($result->RUL_strategy) : '';
        // if ( ! $this->strategy) {
        //     throw new InvalidArgumentException();
        // }
    }



    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }



    /**
     * @param \stdClass $result
     * @throws \InvalidArgumentException
     */
    protected function setTarget(\stdClass $result)
    {
        $this->target = isset($result->RUL_target) ? sanitize_text_field($result->RUL_target) : '';
        // if ( ! $this->target) {
        //     throw new InvalidArgumentException();
        // }
    }



    /**
     * @param bool $spaces
     * @return string
     */
    public function getComparison($spaces = true)
    {
        return $spaces ? " {$this->comparison} " : $this->comparison;
    }



    /**
     * @param \stdClass $result
     * @throws \InvalidArgumentException
     */
    protected function setComparison(\stdClass $result)
    {
        $this->comparison = isset($result->RUL_comparison) ? sanitize_text_field($result->RUL_comparison) : '';
        // if ( ! $this->comparison) {
        //     throw new InvalidArgumentException();
        // }
    }



    /**
     * @param bool $spaces
     * @return string
     */
    public function getValue($spaces = true)
    {
        return $spaces ? " {$this->value} " : $this->value;
    }



    /**
     * @param \stdClass $result
     * @throws \InvalidArgumentException
     */
    protected function setValue(\stdClass $result)
    {
        $this->value = isset($result->RUL_value) ? sanitize_text_field($result->RUL_value) : '';
        // if ( ! $this->value) {
        //     throw new InvalidArgumentException();
        // }
    }



    /**
     * @return string
     */
    public function getExtra()
    {
        return $this->extra;
    }



    /**
     * @param \stdClass $result
     */
    protected function setExtra(\stdClass $result)
    {
        $this->extra = isset($result->RUL_extra) ? sanitize_text_field($result->RUL_extra) : '';
    }


}
// End of file Rule.php
// Location: EventEspresso\core\services\conditional_logic\rules/Rule.php