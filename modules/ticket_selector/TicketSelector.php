<?php
namespace EventEspresso\modules\ticket_selector;

defined('ABSPATH') || exit;



/**
 * Class TicketSelector
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class TicketSelector
{

    /**
     * @var \EE_Event $event
     */
    protected $event;

    /**
     * @var \EE_Ticket[] $tickets
     */
    protected $tickets;

    /**
     * @var int max_attendees
     */
    protected $max_attendees;

    /**
     * @var array $template_args
     */
    protected $template_args;



    /**
     * TicketSelectorSimple constructor.
     *
     * @param \EE_Event    $event
     * @param \EE_Ticket[] $tickets
     * @param int          $max_attendees
     * @param array        $template_args
     */
    public function __construct(\EE_Event $event, array $tickets, $max_attendees, array $template_args)
    {
        $this->event         = $event;
        $this->tickets       = $tickets;
        $this->max_attendees = $max_attendees;
        $this->template_args = $template_args;
        $this->addTemplateArgs();
    }



    /**
     * sets any and all template args that are required for this Ticket Selector
     *
     * @return void
     */
    abstract protected function addTemplateArgs();



    /**
     * loadTicketSelectorTemplate
     *
     * @return string
     */
    protected function loadTicketSelectorTemplate()
    {
        try {
            return \EEH_Template::locate_template(
                apply_filters(
                    'FHEE__EE_Ticket_Selector__display_ticket_selector__template_path',
                    $this->template_args['template_path'],
                    $this->event
                ),
                $this->template_args
            );
        } catch (\Exception $e) {
            \EE_Error::add_error($e->getMessage(), __FILE__, __FUNCTION__, __LINE__);
        }
        return '';
    }



    /**
     * The __toString method allows a class to decide how it will react when it is converted to a string.
     *
     * @return string
     * @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
     */
    public function __toString()
    {
        return $this->loadTicketSelectorTemplate();
    }



}
// End of file TicketSelector.php
// Location: EventEspresso\modules\ticket_selector/TicketSelector.php