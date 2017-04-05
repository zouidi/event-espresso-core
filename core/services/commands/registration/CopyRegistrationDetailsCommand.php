<?php
namespace EventEspresso\core\services\commands\registration;

use EventEspresso\core\services\commands\Command;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class CopyRegistrationDetailsCommand
 * DTO for passing data to a CopyRegistrationDetailsCommandHandler
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         4.9.0
 * @deprecated    4.9.35
 */
class CopyRegistrationDetailsCommand extends Command
{


	/**
	 * @var \EE_Registration $target_registration
	 */
	private $target_registration;


	/**
	 * @var \EE_Registration $registration_to_copy
	 */
	private $registration_to_copy;



	/**
	 * CopyRegistrationDetailsCommand constructor.
	 *
	 * @param \EE_Registration    $target_registration
	 * @param \EE_Registration    $registration_to_copy
	v
	 */
	public function __construct(
		\EE_Registration $target_registration,
		\EE_Registration $registration_to_copy
	) {
		$this->target_registration = $target_registration;
		$this->registration_to_copy = $registration_to_copy;
		// commands have moved to different directory so this is deprecated
        // can't use $this in Closures, so make a copy to pass in
		$this_command = $this;
		add_filter(
            'FHEE__EventEspresso\core\services\commands\CommandHandlerManager__getCommandHandler__command_handler',
            function($command_name, Command $command) use ($this_command) {
                if ($command === $this_command) {
                    $command_name = 'EventEspresso\core\services\commands\registration\CopyRegistrationDetailsCommandHandler';
                }
                return $command_name;
            },
            10, 2
        );
		\EE_Error::doing_it_wrong(
		    'EventEspresso\core\services\commands\registration\CopyRegistrationDetailsCommand',
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
	public function targetRegistration() {
		return $this->target_registration;
	}



	/**
	 * @return \EE_Registration
	 */
	public function registrationToCopy() {
		return $this->registration_to_copy;
	}



}
// End of file CopyRegistrationDetailsCommand.php
// Location: /CopyRegistrationDetailsCommand.php