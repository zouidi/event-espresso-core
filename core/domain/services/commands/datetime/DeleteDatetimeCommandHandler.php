<?php

namespace EventEspresso\core\domain\services\commands\datetime;

use EE_Error;
use EventEspresso\core\exceptions\InvalidEntityException;
use EventEspresso\core\services\commands\CommandHandler;
use EventEspresso\core\services\commands\CommandInterface;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class DeleteDatetimeCommandHandler
 * Service class for deleting a datetime
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class DeleteDatetimeCommandHandler extends CommandHandler
{


    /**
     * removes all relations between the datetime event and tickets,
     * then deletes it, but only if the datetime's DTT_sold equals zero
     *
     * @param CommandInterface $command
     * @return void
     * @throws EE_Error
     * @throws InvalidEntityException
     */
    public function handle(CommandInterface $command)
    {
        /** @var DeleteDatetimeCommand $command */
        if (! $command instanceof DeleteDatetimeCommand) {
            throw new InvalidEntityException(get_class($command), 'DeleteDatetimeCommand');
        }
        $datetime = $command->getDatetime();
        if ($datetime->sold()) {
            return;
        }
        do_action(
            'AHEE__EventEspresso\core\domain\services\event\EventDatetimesService__deleteDatetime__datetime',
            $datetime,
            $this
        );
        //remove tkt relationships.
        $related_tickets = $datetime->get_many_related('Ticket');
        foreach ($related_tickets as $ticket) {
            $datetime->_remove_relation_to($ticket, 'Ticket');
        }
        $event = $datetime->event();
        $event->_remove_relation_to($datetime->ID(), 'Datetime');
        $datetime->refresh_cache_of_related_objects();
        $datetime->delete_permanently();
    }



}
// End of file DeleteDatetimeCommandHandler.php
// Location: core/domain/services/commands/datetime/DeleteDatetimeCommandHandler.php