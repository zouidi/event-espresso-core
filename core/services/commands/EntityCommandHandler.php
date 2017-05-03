<?php
namespace EventEspresso\core\services\commands;

use DomainException;
use EE_Error;
use EEM_Base;
use EventEspresso\core\exceptions\InvalidEntityException;

if ( ! defined('EVENT_ESPRESSO_VERSION')) {
    exit('No direct script access allowed');
}



/**
 * Class EntityCommandHandler
 * abstract parent class for CommandHandlers that operate on a single entity
 * these would usually be used for providing basic CRUD functionality
 * but with some extra data validation, filtering, and action events built in
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         4.9.1
 */
abstract class EntityCommandHandler extends CommandHandler
{



    /**
     * @param EntityCommand $command
     * @param Callable      $data_validation_callback
     * @return mixed
     * @throws EE_Error
     * @throws InvalidEntityException
     */
    protected function createEntity(EntityCommand $command, Callable $data_validation_callback)
    {
        /** @var EntityCommand $command */
        if (! $command instanceof EntityCommand) {
            throw new InvalidEntityException(get_class($command), 'EntityCommand');
        }
        $data = apply_filters(
            'AFEE__EventEspresso\core\services\commands\EntityCommandHandler__createEntity__data',
            $data_validation_callback($command->getEntityData()),
            $command->getEntityClass(),
            $command->getDateTimeFormat(),
            $this
        );
        /** @var \EE_Attendee $entity_class - might not be this exact class, but just using this for the phpdoc */
        $entity_class = $command->getEntityClass();
        /** @var \EE_Base_Class $entity */
        $entity = $entity_class::new_instance(
            $data,
            $command->getDateTimeFormat()->getTimezoneString(),
            $command->getDateTimeFormat()->getDateAndTimeFormatArray()
        );
        $entity->save();
        do_action(
            'AHEE__EventEspresso\core\services\commands\EntityCommandHandler__createEntity__new_entity',
            $entity,
            $command->getEntityData(),
            $command->getEntityClass(),
            $command->getDateTimeFormat(),
            $this
        );
        return $entity;
    }



    /**
     * Retrieves the entity from the DB based on the passed ID
     *
     * @param EntityCommand $command
     * @param Callable      $data_validation_callback
     * @param EEM_Base      $model
     * @return mixed
     * @throws DomainException
     * @throws EE_Error
     * @throws InvalidEntityException
     */
    protected function updateEntity(EntityCommand $command, Callable $data_validation_callback, EEM_Base $model) {
        /** @var EntityCommand $command */
        if (! $command instanceof EntityCommand) {
            throw new InvalidEntityException(get_class($command), 'EntityCommand');
        }
        $data = $command->getEntityData();
        $primary_key_name = $model->primary_key_name();
        $entity_class = $command->getEntityClass();
        if (empty($data[$primary_key_name])) {
            throw new DomainException(
                sprintf(
                    esc_html__(
                        '"%1$s" is not a valid key in the supplied entity data array or it\'s value is missing. In order to perform an update, the EntityCommandHandler class calling updateEntity() must provide a valid value for the "%2$s" class primary key.',
                        'event_espresso'
                    ),
                    $model->primary_key_name(),
                    $entity_class
                )
            );
        }
        /** @var \EE_Attendee $entity - might not be this exact class, but just using this for the phpdoc */
        $entity = $model->get_one_by_ID($data[$primary_key_name]);
        if (! $entity instanceof $entity_class) {
            throw new DomainException(
                sprintf(
                    esc_html__(
                        'Could not update the "%1$s" entity where "%2$s = %3$s" because a valid record could not be located.',
                        'event_espresso'
                    ),
                    $entity_class,
                    $model->primary_key_name(),
                    $data[$primary_key_name]
                )
            );
        }
        //set date and time format according to what is set in this class.
        $entity->set_date_format($command->getDateTimeFormat()->getDateFormat());
        $entity->set_time_format($command->getDateTimeFormat()->getTimeFormat());
        $data = apply_filters(
            'AFEE__EventEspresso\core\domain\services\commands\EntityCommandHandler__updateEntity__data',
            $data_validation_callback($data, $entity),
            $entity_class,
            $command->getDateTimeFormat(),
            $this
        );
        foreach ($data as $field => $value) {
            $entity->set($field, $value);
        }
        $entity->save();
        do_action(
            'AHEE__EventEspresso\core\domain\services\commands\EntityCommandHandler__updateEntity__entity_updated',
            $entity,
            $command->getEntityData(),
            $command->getEntityClass(),
            $command->getDateTimeFormat(),
            $this
        );
        return $entity;
    }


}
// End of file EntityCommandHandler.php
// Location: core/services/commands/EntityCommandHandler.php