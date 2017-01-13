<?php
namespace EventEspresso\core\services\automated_actions;

use EventEspresso\core\domain\services\capabilities\CapCheckInterface;

defined('ABSPATH') || exit;



/**
 * Class AutomatedAction
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class AutomatedAction implements AutomatedActionInterface
{

    /**
     * @var int $ID
     */
    protected $ID;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $description
     */
    protected $description;

    /**
     * @var AutomatedActionStrategy $strategy
     */
    protected $strategy;


    /**
     * @var TriggerStrategy $trigger
     */
    protected $trigger;

    /**
     * @var string $trigger_value
     */
    protected $trigger_value;

    /**
     * @var string $data
     */
    protected $data;


    /*
     * @var CapCheckInterface $cap_check
     */
    protected $cap_check;




    /**
     * AutomatedAction constructor.
     *
     * @param \stdClass               $args
     * @param AutomatedActionStrategy $strategy
     * @param TriggerStrategy         $trigger
     * @throws \DomainException
     */
    public function __construct(\stdClass $args, AutomatedActionStrategy $strategy, TriggerStrategy $trigger)
    {
        $this->setID($args);
        $this->setName($args);
        $this->setDescription($args);
        $this->strategy = $strategy;
        $this->trigger = $trigger;
        $this->setTriggerValue($args);
        $this->setData($args);
    }



    /**
     * @return int
     */
    public function getID()
    {
        return $this->ID;
    }



    /**
     * @param \stdClass $args
     * @throws \DomainException
     */
    protected function setID(\stdClass $args)
    {
        $this->ID = isset($args->AMA_ID) ? absint($args->AMA_ID) : 0;
        if (! $this->ID){
            throw new \DomainException();
        }

    }



    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }



    /**
     * @param \stdClass $args
     * @throws \DomainException
     */
    protected function setName(\stdClass $args)
    {
        $this->name = isset($args->AMA_name) ? sanitize_text_field($args->AMA_name) : '';
        if (empty($this->name)) {
            throw new \DomainException();
        }
    }



    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }



    /**
     * @param \stdClass $args
     */
    protected function setDescription(\stdClass $args)
    {
        $this->description = isset($args->AMA_description) ? sanitize_text_field($args->AMA_description) : '';
    }



    /**
     * @return AutomatedActionStrategy
     */
    public function getStrategy()
    {
        return $this->strategy;
    }




    /**
     * @return TriggerStrategy
     */
    public function getTrigger()
    {
        return $this->trigger;
    }



    /**
     * @return string
     */
    public function getTriggerValue()
    {
        return $this->trigger_value;
    }


    /**
     * @param \stdClass $args
     */
    protected function setTriggerValue(\stdClass $args)
    {
        $this->trigger_value = isset($args->AMA_trigger_value) ? sanitize_text_field($args->AMA_trigger_value) : '';
    }



    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }



    /**
     * @param \stdClass $args
     * @throws \DomainException
     */
    protected function setData(\stdClass $args)
    {
        $this->data = isset($args->AMA_data) ? sanitize_text_field($args->AMA_data) : '';
        if ($this->data !== null) {
            $this->data = json_decode($this->data);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \DomainException(json_last_error_msg());
            }
        }
    }



    /**
     * @param CapCheckInterface $cap_check
     */
    public function setCapCheck(CapCheckInterface $cap_check)
    {
        $this->cap_check = $cap_check;
    }



    /**
     * not to be confused with being a setter for the $trigger property,
     * but instead informs the trigger to set hooks or similar initialization
     *
     * @return void
     */
    public function setTrigger()
    {
        $this->trigger->set($this);
    }



    /**
     * @return bool
     */
    public function triggerPulled()
    {
        // \EEH_Debug_Tools::printr($this->trigger->pulled(), '$this->trigger->pulled()', __FILE__, __LINE__);
        return $this->trigger->pulled();
    }



    /**
     * @return bool
     */
    public function hasRun()
    {
        // \EEH_Debug_Tools::printr($this->strategy->hasRun(), '$this->strategy->hasRun()', __FILE__, __LINE__);
        return $this->strategy->hasRun();
    }



    /**
     * by default this is called during the "shutdown"
     * in AutomatedActionManager::processActions(),
     * but individual AutomatedActionStrategy classes
     * can run their callback() method whenever it is best,
     * as long as they call setHasRun() to avoid duplication
     */
    public function process()
    {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        // \EEH_Debug_Tools::printr($this->description, $this->name, __FILE__, __LINE__);
        $this->strategy->callback(
            $this->trigger->getCallbackArgs()
        );
    }

}
// End of file AutomatedAction.php
// Location: EventEspresso\core\services\automated_actions/AutomatedAction.php