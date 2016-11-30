<?php
namespace EventEspresso\core\services\action_items;

use EventEspresso\core\domain\services\capabilities\CapabilitiesChecker;
use EventEspresso\core\domain\services\capabilities\RequiresCapCheckInterface;
use EventEspresso\core\services\collections\Collection;
use EventEspresso\core\services\collections\CollectionDetails;
use EventEspresso\core\services\collections\CollectionLoader;

defined( 'ABSPATH' ) || exit;



/**
 * Class ActionItemManager
 * Loads and processes a Collection of ActionItem classes
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class ActionItemManager
{

    /**
     * @var Collection $action_items
     */
    private $action_items;

    /**
     * @type CapabilitiesChecker $capabilities_checker
     */
    private $capabilities_checker;



    /**
     * CapChecker constructor
     *
     * @param CapabilitiesChecker $capabilities_checker
     */
    public function __construct( CapabilitiesChecker $capabilities_checker )
    {
        $this->capabilities_checker = $capabilities_checker;
        add_action(
            'admin_notices',
            // 'in_admin_header',
            array( $this,'processConditionChecks' )
        );
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
    protected function loadActionItemCollection()
    {
        if ( ! $this->action_items instanceof Collection ) {
            $loader = new CollectionLoader(
                new CollectionDetails(
                    // collection name
                    'action_items',
                    // collection interface
                    '\EventEspresso\core\services\action_items\ActionItemInterface',
                    // FQCNs for classes to add (all classes within that namespace will be loaded)
                    array('EventEspresso\core\domain\services\action_items'),
                    // filepaths to classes to add
                    array(),
                    // filemask to use if parsing folder for files to add
                    '',
                    // what to use as identifier for collection entities
                // using CLASS NAME prevents duplicates (works like a singleton)
                    CollectionDetails::ID_CLASS_NAME
                )
            );
            $this->action_items = $loader->getCollection();
        }
        return $this->action_items;
    }



    public function setConditionCheckHooks()
    {
        foreach ( $this->loadActionItemCollection() as $action_item ) {
            /** @var ActionItemInterface $action_item */
            if ( $action_item instanceof RequiresCapCheckInterface ) {
                $this->capabilities_checker->processCapCheck(
                    $action_item->getCapCheck()
                );
            }
            $action_item->setConditionCheckHooks();
        }
    }



    public function processConditionChecks()
    {
        foreach ( $this->loadActionItemCollection() as $action_item ) {
            /** @var ActionItemInterface $action_item */
            if ( ! $action_item->conditionPassed() ) {
                continue;
            }
            $notice = \EEH_HTML::div(
                \EEH_HTML::link(
                    $action_item->getActionItemUrl(),
                    $action_item->getActionItemButtonText(),
                    '', '', 'button button-primary', 'float:right; margin:.25em 0 .25em 2em;'
                ) .
                \EEH_HTML::p(
                    $action_item->getActionItemNotice()
                ),
                'message',
                'ee-action-item-notice-dv updated notice',
                'border-left-color:gold;'
            );
            echo $notice;
        }
    }

}
// End of file ActionItemManager.php
// Location: EventEspresso\core\services\action_items/ActionItemManager.php