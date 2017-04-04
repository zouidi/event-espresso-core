<?php
namespace EventEspresso\core\services\commands;

use InvalidArgumentException;

if ( ! defined('EVENT_ESPRESSO_VERSION')) {
    exit('No direct script access allowed');
}



/**
 * Class CommandHandler
 * abstract parent class for CommandHandlers
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         4.9.1
 */
abstract class CommandHandler implements CommandHandlerInterface
{



    /**
     * ensures an array element has an appropriate value
     *
     * @param array      $data
     * @param int|string $key
     * @param null       $default
     * @param bool       $required
     * @return mixed|null
     * @throws InvalidArgumentException
     */
    protected function validateArrayElement(array $data, $key, $default = null, $required = false)
    {
        if (empty($data[$key])) {
            if ($required) {
                throw new InvalidArgumentException(
                    sprintf(
                        esc_html__('"%1$s" is a required value and must be set.', 'event_espresso'),
                        $key
                    )
                );
            }
            return $default;
        }
        return $data[$key];
    }



}
// End of file CommandHandler.php
// Location: /CommandHandler.php