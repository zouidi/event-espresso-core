<?php
namespace EventEspresso\core\services\automated_actions;

use EventEspresso\core\domain\services\capabilities\CapabilitiesChecker;
use EventEspresso\core\domain\services\capabilities\RequiresCapCheckInterface;
use EventEspresso\core\exceptions\InsufficientPermissionsException;
use EventEspresso\core\exceptions\InvalidClassException;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\Benchmark;
use EventEspresso\core\services\collections\Collection;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class AutomatedActionManager
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class AutomatedActionManager
{


    /**
     * @var Collection $automated_actions
     */
    private $automated_actions;

    /**
     * @var AutomatedActionHandlerInterface $automated_action_handler
     */
    private $automated_action_handler;

    /**
     * @var AutomatedActionFactory $automated_action_factory
     */
    private $automated_action_factory;

    /**
     * @type CapabilitiesChecker $capabilities_checker
     */
    private $capabilities_checker;



    /**
     * AutomatedActionManager constructor
     *
     * @param AutomatedActionHandlerInterface $automated_action_handler
     * @param AutomatedActionFactory          $automated_action_factory
     * @param CapabilitiesChecker             $capabilities_checker
     * @throws InvalidInterfaceException
     */
    public function __construct(
        AutomatedActionHandlerInterface $automated_action_handler,
        AutomatedActionFactory $automated_action_factory,
        CapabilitiesChecker $capabilities_checker
    ) {
        Benchmark::startTimer(__METHOD__);
        $this->automated_actions = new Collection(
            'EventEspresso\core\services\automated_actions\AutomatedActionInterface'
        );
        $this->automated_action_handler = $automated_action_handler;
        $this->automated_action_factory = $automated_action_factory;
        $this->capabilities_checker = $capabilities_checker;
        add_action('AHEE__EE_System__initialize', array($this, 'initialize'));
        Benchmark::stopTimer(__METHOD__);
    }



    /**
     * @throws \DomainException
     * @throws InvalidEntityException
     * @throws InsufficientPermissionsException
     * @throws InvalidClassException
     */
    public function initialize()
    {
        $results = $this->getActiveActions();
        $this->generateActionObjects($results);
        $this->setTriggers();
        add_action('shutdown', array($this, 'processActions'));
    }



    /**
     * @return array
     * @throws \DomainException
     */
    protected function getActiveActions()
    {
        Benchmark::startTimer(__METHOD__);
        global $wpdb;
        $results = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}esp_automated_action WHERE AMA_is_active = 1;"
        );
        if ($results instanceof \WP_Error) {
            throw new \DomainException(
                $results->get_error_message()
            );
        }
        $results = is_array($results) ? $results : array($results);
        Benchmark::stopTimer(__METHOD__);
        return $results;
    }



    /**
     * @param array $results
     * @throws InvalidEntityException
     * @throws \DomainException
     */
    protected function generateActionObjects(array $results)
    {
        Benchmark::startTimer(__METHOD__);
        foreach ($results as $result) {
            $this->automated_actions->add(
                $this->automated_action_factory->create($result),
                $result->AMA_name
            );
        }
        Benchmark::stopTimer(__METHOD__);
    }



    /**
     * @throws InsufficientPermissionsException
     * @throws InvalidClassException
     */
    public function setTriggers()
    {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        Benchmark::startTimer(__METHOD__);
        foreach ($this->automated_actions as $automated_action) {
            /** @var AutomatedActionInterface $automated_action */
            if ($automated_action instanceof RequiresCapCheckInterface) {
                try {
                    $this->capabilities_checker->processCapCheck(
                        $automated_action->getCapCheck()
                    );
                } catch (\Exception $e) {
                    // just eat the exception for now and skip to the next action
                    continue;
                }
            }
            $automated_action->setTrigger();
        }
        Benchmark::stopTimer(__METHOD__);
    }



    /**
     *
     */
    public function processActions()
    {
        \EEH_Debug_Tools::printr('', 'SHUTDOWN HOOK', __FILE__, __LINE__, 2);
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        Benchmark::startTimer(__METHOD__);
        foreach ($this->automated_actions as $automated_action) {
            /** @var AutomatedActionInterface $automated_action */
            if ( ! $automated_action->triggerPulled() || $automated_action->hasRun()) {
                continue;
            }
            $this->automated_action_handler->processAction($automated_action);
        }
        Benchmark::stopTimer(__METHOD__);
        Benchmark::displayResults();
    }


}
// End of file AutomatedActionManager.php
// Location: /core/services/automated_actions/AutomatedActionManager.php