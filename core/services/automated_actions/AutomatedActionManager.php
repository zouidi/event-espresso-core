<?php
namespace EventEspresso\core\services\automated_actions;

use EventEspresso\core\domain\services\capabilities\CapabilitiesChecker;
// use EventEspresso\core\domain\services\capabilities\RequiresCapCheckInterface;
use EventEspresso\core\services\collections\Collection;
use EventEspresso\core\services\collections\CollectionDetails;
use EventEspresso\core\services\collections\CollectionLoader;

defined('ABSPATH') || exit;



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
     * CapChecker constructor
     *
     * @param CapabilitiesChecker $capabilities_checker
     */
    public function __construct(CapabilitiesChecker $capabilities_checker)
    {
        $this->capabilities_checker = $capabilities_checker;
    }



    /**
     * @return Collection
     * @throws \EventEspresso\core\exceptions\InvalidIdentifierException
     * @throws \EventEspresso\core\exceptions\InvalidInterfaceException
     * @throws \EventEspresso\core\exceptions\InvalidFilePathException
     * @throws \EventEspresso\core\exceptions\InvalidEntityException
     * @throws \EventEspresso\core\exceptions\InvalidDataTypeException
     * @throws \EventEspresso\core\exceptions\InvalidClassException
     */
    protected function loadAutomatedActionCollection()
    {
        if ( ! $this->automated_actions instanceof Collection) {
            $loader = new CollectionLoader(
                new CollectionDetails(
                // collection name
                    'automated_actions',
                    // collection interface
                    '\EventEspresso\core\services\automated_actions\AutomatedActionInterface',
                    // FQCNs for classes to add (all classes within that namespace will be loaded)
                    array('EventEspresso\core\domain\services\automated_actions'),
                    // filepaths to classes to add
                    array(),
                    // filemask to use if parsing folder for files to add
                    '',
                    // what to use as identifier for collection entities
                    // using CLASS NAME prevents duplicates (works like a singleton)
                    CollectionDetails::ID_CLASS_NAME
                )
            );
            $this->automated_actions = $loader->getCollection();
        }
        return $this->automated_actions;
    }



    public function setConditionCheckHooks()
    {
        foreach ($this->loadAutomatedActionCollection() as $action_item) {
            /** @var AutomatedActionInterface $action_item */
            if ($action_item instanceof RequiresCapCheckInterface) {
                $this->capabilities_checker->processCapCheck(
                    $action_item->getCapCheck()
                );
            }
            $action_item->setConditionCheckHooks();
        }
    }



    public function processConditionChecks()
    {
        foreach ($this->loadAutomatedActionCollection() as $action_item) {
            /** @var AutomatedActionInterface $action_item */
            if ( ! $action_item->conditionPassed()) {
                continue;
            }

        }
    }


}
// End of file AutomatedActionManager.php
// Location: /core/services/automated_actions/AutomatedActionManager.php