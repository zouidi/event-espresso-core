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
use stdClass;

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
     * @type CapabilitiesChecker $capabilities_checker
     */
    private $capabilities_checker;



    /**
     * AutomatedActionManager constructor
     *
     * @param CapabilitiesChecker $capabilities_checker
     * @param Collection          $automated_actions
     * @throws InvalidInterfaceException
     */
    public function __construct(CapabilitiesChecker $capabilities_checker, Collection $automated_actions = null)
    {
        Benchmark::startTimer(__METHOD__);
        $this->capabilities_checker = $capabilities_checker;
        $this->automated_actions    = $automated_actions instanceof Collection
            ? $automated_actions
            : new Collection('EventEspresso\core\services\automated_actions\AutomatedActionInterface');
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
                AutomatedActionFactory::create($result),
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
                $this->capabilities_checker->processCapCheck(
                    $automated_action->getCapCheck()
                );
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
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
        Benchmark::startTimer(__METHOD__);
        foreach ($this->automated_actions as $automated_action) {
            /** @var AutomatedActionInterface $automated_action */
            if ( ! $automated_action->triggerPulled() || $automated_action->hasRun()) {
                continue;
            }
            $automated_action->process();
        }
        Benchmark::stopTimer(__METHOD__);
        Benchmark::displayResults();
    }


}
// End of file AutomatedActionManager.php
// Location: /core/services/automated_actions/AutomatedActionManager.php