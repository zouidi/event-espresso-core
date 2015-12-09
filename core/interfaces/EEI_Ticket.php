<?php

namespace EventEspresso\core\interfaces;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}
/**
 * EEI_Ticket class
 *
 * @package 	Event Espresso
 * @subpackage  /core/interfaces/EEI_Ticket.php
 * @author 		Brent Christensen
 */
interface EEI_Ticket {



	/**
	 * @return bool
	 */
	public function parent();



	/**
	 * return if a ticket has quantities available for purchase
	 *
	 * @param  int $DTT_ID the primary key for a particular datetime
	 * @return boolean
	 */
	public function available( $DTT_ID = 0 );



	/**
	 * Using the start date and end date this method calculates whether the ticket is On Sale, Pending, or Expired
	 *
	 * @param bool $display true = we'll return a localized string, otherwise we just return the value of the relevant
	 *                      status const
	 * @return mixed(int|string) status int if the display string isn't requested
	 */
	public function ticket_status( $display = false );



	/**
	 * The purpose of this method is to simply return a boolean for whether there are any tickets remaining for sale
	 * considering ALL the factors used for figuring that out.
	 *
	 * @access public
	 * @param  int $DTT_ID if an int above 0 is included here then we get a specific dtt.
	 * @return boolean         true = tickets remaining, false not.
	 */
	public function is_remaining( $DTT_ID = 0 );



	/**
	 * return the total number of tickets available for purchase
	 *
	 * @param  int $DTT_ID the primary key for a particular datetime. set to null for
	 *                     all related datetimes
	 * @return int
	 */
	public function remaining( $DTT_ID = 0 );



	/**
	 * Gets min
	 *
	 * @return int
	 */
	function min();



	/**
	 * return if a ticket is no longer available cause its available dates have expired.
	 *
	 * @return boolean
	 */
	public function is_expired();



	/**
	 * Return if a ticket is yet to go on sale or not
	 *
	 * @return boolean
	 */
	public function is_pending();



	/**
	 * Return if a ticket is on sale or not
	 *
	 * @return boolean
	 */
	public function is_on_sale();



	/**
	 * This returns the chronologically last datetime that this ticket is associated with
	 *
	 * @param string $dt_frmt
	 * @param string $conjunction - conjunction junction what's your function ? this string joins the start date with
	 *                            the end date ie: Jan 01 "to" Dec 31
	 * @return array
	 */
	public function date_range( $dt_frmt = '', $conjunction = ' - ' );



	/**
	 * This returns the chronologically first datetime that this ticket is associated with
	 *
	 * @return \EE_Datetime
	 */
	public function first_datetime();



	/**
	 * Gets all the datetimes this ticket can be used for attending.
	 * Unless otherwise specified, orders datetimes by start date.
	 *
	 * @param array $query_params see EEM_Base::get_all()
	 * @return \EE_Datetime[]
	 */
	public function datetimes( $query_params = array() );



	/**
	 * This returns the chronologically last datetime that this ticket is associated with
	 *
	 * @return \EE_Datetime
	 */
	public function last_datetime();



	/**
	 * This returns the total tickets sold depending on the given parameters.
	 *
	 * @param  string $what   Can be one of two options: 'ticket', 'datetime'.
	 *                        'ticket' = total ticket sales for all datetimes this ticket is related to
	 *                        'datetime' = total ticket sales for a specified datetime (required $dtt_id)
	 *                        'datetime' = total ticket sales in the datetime_ticket table. If $dtt_id is not given
	 *                        then we return an array of sales indexed by datetime.  If $dtt_id IS given then we return
	 *                        the tickets sold for that given datetime.
	 * @param  int    $dtt_id [optional] include the dtt_id with $what = 'datetime'.
	 * @return mixed (array|int)          how many tickets have sold
	 */
	public function tickets_sold( $what = 'ticket', $dtt_id = null );



	/**
	 * This returns the base price object for the ticket.
	 *
	 * @access public
	 * @param  bool $return_array whether to return as an array indexed by price id or just the object.
	 * @return \EE_Price
	 */
	public function base_price( $return_array = false );



	/**
	 * This returns ONLY the price modifiers for the ticket (i.e. no taxes or base price)
	 *
	 * @access public
	 * @return \EE_Price[]
	 */
	public function price_modifiers();



	/**
	 * Gets all the prices that combine to form the final price of this ticket
	 *
	 * @param array $query_params like EEM_Base::get_all
	 * @return \EE_Price[]
	 */
	public function prices( $query_params = array() );



	/**
	 * Gets all the ticket applicabilities (ie, relations between datetimes and tickets)
	 *
	 * @param array $query_params see EEM_Base::get_all()
	 * @return \EE_Datetime_Ticket
	 */
	public function datetime_tickets( $query_params = array() );



	/**
	 * Gets all the datetimes from the db ordered by DTT_order
	 *
	 * @param boolean $show_expired
	 * @param boolean $show_deleted
	 * @return \EE_Datetime[]
	 */
	public function datetimes_ordered( $show_expired = true, $show_deleted = false );



	/**
	 * Gets ID
	 *
	 * @return string
	 */
	function ID();



	/**
	 * get the author of the ticket.
	 *
	 * @since 4.5.0
	 *
	 * @return int
	 */
	public function wp_user();



	/**
	 * Gets the template for the ticket
	 *
	 * @return \EE_Ticket_Template
	 */
	public function template();



	/**
	 * Simply returns an array of \EE_Price objects that are taxes.
	 *
	 * @return \EE_Price[]
	 */
	public function get_ticket_taxes_for_admin();



	/**
	 * @return float
	 */
	public function ticket_price();



	/**
	 * @return mixed
	 */
	public function pretty_price();



	/**
	 * @return bool
	 */
	public function is_free();



	/**
	 * get_ticket_total_with_taxes
	 *
	 * @param bool $no_cache
	 * @return float
	 */
	public function get_ticket_total_with_taxes( $no_cache = false );



	public function ensure_TKT_Price_correct();



	/**
	 * @return float
	 */
	public function get_ticket_subtotal();



	/**
	 * Returns the total taxes applied to this ticket
	 *
	 * @return float
	 */
	public function get_ticket_taxes_total_for_admin();



	/**
	 * Sets name
	 *
	 * @param string $name
	 * @return boolean
	 */
	function set_name( $name );



	/**
	 * Gets description
	 *
	 * @return string
	 */
	function description();



	/**
	 * Sets description
	 *
	 * @param string $description
	 * @return boolean
	 */
	function set_description( $description );



	/**
	 * Gets start_date
	 *
	 * @param string $dt_frmt
	 * @param string $tm_frmt
	 * @return string
	 */
	function start_date( $dt_frmt = '', $tm_frmt = '' );



	/**
	 * Sets start_date
	 *
	 * @param string $start_date
	 * @return void
	 */
	function set_start_date( $start_date );



	/**
	 * Gets end_date
	 *
	 * @param string $dt_frmt
	 * @param string $tm_frmt
	 * @return string
	 */
	function end_date( $dt_frmt = '', $tm_frmt = '' );



	/**
	 * Sets end_date
	 *
	 * @param string $end_date
	 * @return void
	 */
	function set_end_date( $end_date );



	/**
	 * Sets sell until time
	 *
	 * @since 4.5.0
	 *
	 * @param string $time a string representation of the sell until time (ex 9am or 7:30pm)
	 */
	function set_end_time( $time );



	/**
	 * Sets min
	 *
	 * @param int $min
	 * @return boolean
	 */
	function set_min( $min );



	/**
	 * Gets max
	 *
	 * @return int
	 */
	function max();



	/**
	 * Sets max
	 *
	 * @param int $max
	 * @return boolean
	 */
	function set_max( $max );



	/**
	 * Sets price
	 *
	 * @param float $price
	 * @return boolean
	 */
	function set_price( $price );



	/**
	 * Gets sold
	 *
	 * @return int
	 */
	function sold();



	/**
	 * increments sold by amount passed by $qty
	 *
	 * @param int $qty
	 * @return boolean
	 */
	function increase_sold( $qty = 1 );



	/**
	 * Sets sold
	 *
	 * @param int $sold
	 * @return boolean
	 */
	function set_sold( $sold );



	/**
	 * decrements (subtracts) sold by amount passed by $qty
	 *
	 * @param int $qty
	 * @return boolean
	 */
	function decrease_sold( $qty = 1 );



	/**
	 * Gets ticket quantity
	 *
	 * @param string $context     ticket quantity is somewhat subjective depending on the exact information sought
	 *                            therefore $context can be one of three values: '', 'reg_limit', or 'saleable'
	 *                            '' (default) quantity is the actual db value for TKT_qty, unaffected by other objects
	 *                            REG LIMIT: caps qty based on DTT_reg_limit for ALL related datetimes
	 *                            SALEABLE: also considers datetime sold and returns zero if ANY DTT is sold out, and
	 *                            is therefore the truest measure of tickets that can be purchased at the moment
	 *
	 * @return int
	 */
	function qty( $context = '' );



	/**
	 * Gets ticket quantity
	 *
	 * @param string $context     ticket quantity is somewhat subjective depending on the exact information sought
	 *                            therefore $context can be one of two values: 'reg_limit', or 'saleable'
	 *                            REG LIMIT: caps qty based on DTT_reg_limit for ALL related datetimes
	 *                            SALEABLE: also considers datetime sold and returns zero if ANY DTT is sold out, and
	 *                            is therefore the truest measure of tickets that can be purchased at the moment
	 *
	 * @return int
	 */
	function real_quantity_on_ticket( $context = 'reg_limit' );



	/**
	 * Sets qty - IMPORTANT!!! Does NOT allow QTY to be set higher than the lowest reg limit of any related datetimes
	 *
	 * @param int $qty
	 * @return bool
	 * @throws \EE_Error
	 */
	function set_qty( $qty );



	/**
	 * Gets uses
	 *
	 * @return int
	 */
	function uses();



	/**
	 * Sets uses
	 *
	 * @param int $uses
	 * @return boolean
	 */
	function set_uses( $uses );



	/**
	 * returns whether ticket is required or not.
	 *
	 * @return boolean
	 */
	public function required();



	/**
	 * sets the TKT_required property
	 *
	 * @param boolean $required
	 * @return boolean
	 */
	public function set_required( $required );



	/**
	 * Gets taxable
	 *
	 * @return boolean
	 */
	function taxable();



	/**
	 * Sets taxable
	 *
	 * @param boolean $taxable
	 * @return boolean
	 */
	function set_taxable( $taxable );



	/**
	 * Gets is_default
	 *
	 * @return boolean
	 */
	function is_default();



	/**
	 * Sets is_default
	 *
	 * @param boolean $is_default
	 * @return boolean
	 */
	function set_is_default( $is_default );



	/**
	 * Gets order
	 *
	 * @return int
	 */
	function order();



	/**
	 * Sets order
	 *
	 * @param int $order
	 * @return boolean
	 */
	function set_order( $order );



	/**
	 * Gets row
	 *
	 * @return int
	 */
	function row();



	/**
	 * Sets row
	 *
	 * @param int $row
	 * @return boolean
	 */
	function set_row( $row );



	/**
	 * Gets deleted
	 *
	 * @return boolean
	 */
	function deleted();



	/**
	 * Sets deleted
	 *
	 * @param boolean $deleted
	 * @return boolean
	 */
	function set_deleted( $deleted );



	/**
	 * Gets parent
	 *
	 * @return int
	 */
	function parent_ID();



	/**
	 * Sets parent
	 *
	 * @param int $parent
	 * @return boolean
	 */
	function set_parent_ID( $parent );



	/**
	 * Gets a string which is handy for showing in gateways etc that describes the ticket.
	 *
	 * @return string
	 */
	function name_and_info();



	/**
	 * Gets name
	 *
	 * @return string
	 */
	function name();



	/**
	 * Gets price
	 *
	 * @return float
	 */
	function price();



	/**
	 * Gets all the registrations for this ticket
	 *
	 * @param array $query_params like EEM_Base::get_all's
	 * @return \EE_Registration[]
	 */
	public function registrations( $query_params = array() );



	/**
	 * Updates the TKT_sold attribute (and saves) based on the number of APPROVED registrations for this ticket.
	 * into account
	 *
	 * @return int
	 */
	public function update_tickets_sold();



	/**
	 * Counts the registrations for this ticket
	 *
	 * @param array $query_params like EEM_Base::get_all's
	 * @return int
	 */
	public function count_registrations( $query_params = array() );



	/**
	 * Implementation for EEI_Has_Icon interface method.
	 *
	 * @see EEI_Visual_Representation for comments
	 * @return string
	 */
	public function get_icon();



	/**
	 * Implementation of the EEI_Event_Relation interface method
	 *
	 * @see EEI_Event_Relation for comments
	 * @return \EE_Event|null
	 */
	public function get_related_event();



	/**
	 * Implementation of the EEI_Event_Relation interface method
	 *
	 * @see EEI_Event_Relation for comments
	 * @return string
	 */
	public function get_event_name();



	/**
	 * Implementation of the EEI_Event_Relation interface method
	 *
	 * @see EEI_Event_Relation for comments
	 * @return int
	 */
	public function get_event_ID();


}
// End of file EEI_Ticket.php
// Location: /EEI_Ticket.php
