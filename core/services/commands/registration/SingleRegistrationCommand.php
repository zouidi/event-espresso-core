<?php
namespace EventEspresso\core\services\commands\registration;

use EventEspresso\core\services\commands\Command;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class SingleRegistrationCommand
 * DTO for passing data a single EE_Registration object to a CommandHandler
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         4.9.0
 * @deprecated    4.9.35
 */
abstract class SingleRegistrationCommand extends Command
{


	/**
	 * @var \EE_Registration $registration
	 */
	private $registration;



	/**
	 * CancelRegistrationAndTicketLineItemCommand constructor.
	 *
	 * @param \EE_Registration    $registration
     * @deprecated 4.9.35
	 */
	public function __construct(
		\EE_Registration $registration
	) {
		$this->registration = $registration;
        // commands have moved to different directory so this is deprecated
        // can't use $this in Closures, so make a copy to pass in
        $this_command = $this;
        add_filter(
            'FHEE__EventEspresso\core\services\commands\CommandHandlerManager__getCommandHandler__command_handler',
            function ($command_name, Command $command) use ($this_command) {
                if ($command === $this_command) {
                    $command_name = basename(get_class($this_command));
                    $command_name = 'EventEspresso\core\services\commands\registration\\' . $command_name . 'Handler';
                }
                return $command_name;
            },
            10, 2
        );
        \EE_Error::doing_it_wrong(
            get_class($this),
            esc_html__(
                'All Commands found in "/core/services/commands/registration/" have been moved to "/core/domain/services/commands/registration/"',
                'event_espresso'
            ),
            '4.9.35',
            '5.0.0'
        );
    }



	/**
	 * @return \EE_Registration
	 */
	public function registration()
	{
		return $this->registration;
	}

}
// End of file SingleRegistrationCommand.php
// Location: /SingleRegistrationCommand.php