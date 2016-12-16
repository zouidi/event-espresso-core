<?php
namespace EventEspresso\core\domain\services\conditional_logic\rules;

use EE_Registration;
use EventEspresso\core\services\conditional_logic\rules\modelObject;

defined('ABSPATH') || exit;



/**
 * Class Registration
 * RuleStrategy class for translating EE_Registration related Rules into query params
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class Registration extends modelObject
{



    /**
     * Registration constructor.
     *
     * @param $object
     */
    public function __construct( EE_Registration $object)
    {
        $this->object = $object;
    }



    public function status()
    {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
    }



    public function totalPaid()
    {
        \EEH_Debug_Tools::printr(__FUNCTION__, __CLASS__, __FILE__, __LINE__, 2);
    }

}
// End of file Registration.php
// Location: core/domain/services/conditional_logic/rules/Registration.php