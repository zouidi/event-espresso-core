<?php

namespace EventEspresso\core\services\commands;

use EventEspresso\core\entities\datetime\DatetimeFormat;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class EntityCommand
 * abstract parent DTO class for passing data to EntityCommandHandlers that operate on a single entity
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class EntityCommand extends Command implements CommandRequiresCapCheckInterface
{


    /**
     * @var string $entity_class
     */
    private $entity_class;

    /**
     * @var array $entity_data
     */
    private $entity_data;

    /**
     * @var DatetimeFormat $datetime_format
     */
    private $datetime_format;



    /**
     * EntityCommand constructor.
     *
     * @param string         $entity_class
     * @param array          $entity_data
     * @param DatetimeFormat $datetime_format
     */
    public function __construct($entity_class, array $entity_data, DatetimeFormat $datetime_format)
    {
        $this->entity_class = $entity_class;
        $this->entity_data = $entity_data;
        $this->datetime_format = $datetime_format;
    }



    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entity_class;
    }



    /**
     * @return array
     */
    public function getEntityData()
    {
        return $this->entity_data;
    }



    /**
     * @return DatetimeFormat
     */
    public function getDateTimeFormat()
    {
        return $this->datetime_format;
    }



}
// End of file EntityCommand
// Location: core/services/commands/EntityCommand.php