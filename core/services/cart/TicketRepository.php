<?php

namespace EventEspresso\Core\Services\Cart;

use EventEspresso\Core;
use EventEspresso\Core\Libraries\Repositories\EE_Base_Class_Repository;

if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * Class TicketRepository
 *
 * Storage entity for Tickets that implements the
 * Countable, Iterator, Serializable, and ArrayAccess interfaces
 *
 * @package 	Event Espresso
 * @subpackage 	core
 * @author 		Brent Christensen
 * @since 		$VID:$
 *
 */
class TicketRepository extends EE_Base_Class_Repository {



	/**
	 * @param \EE_Ticket $ticket
	 * @return bool
	 */
	public function addTicket( \EE_Ticket $ticket ) {
		return $this->add( $ticket, array( $ticket->ID(), $ticket->get_event_ID() ) );
	}



	/**
	 * @param mixed $ID
	 * @return null | object
	 */
	public function getTicket( $ID ) {
		$ticket = $this->get_by_info( $ID );
		if ( ! $ticket instanceof \EE_Ticket ) {
			// ??? exception ???
		}
		return $ticket;
	}



	/**
	 * @param mixed $ID
	 * @return null | object
	 */
	public function getTicketByID( $ID ) {
		return $this->get_by_info( $ID );
	}



	/**
	 * @param \EE_Ticket $ticket
	 * @return bool
	 */
	public function hasTicket( \EE_Ticket $ticket ) {
		return $this->has( $ticket );
	}



	/**
	 * @param mixed $ID
	 * @return bool
	 */
	public function hasTicketByID( $ID ) {
		$ticket = $this->get_by_info( $ID );
		return $this->has( $ticket );
	}



	/**
	 * @param \EE_Ticket $ticket
	 * @return bool | int
	 */
	public function saveTicket( \EE_Ticket $ticket ) {
		return $this->persist( $ticket, 'save' );
	}



	/**
	 * @param mixed $ID
	 * @return bool | int
	 */
	public function saveTicketByID( $ID ) {
		$ticket = $this->get_by_info( $ID );
		return $this->persist( $ticket, 'save' );
	}



	/**
	 * @param \EE_Ticket $ticket
	 * @return void
	 */
	public function removeTicket( \EE_Ticket $ticket ) {
		$this->remove( $ticket );
	}



	/**
	 * @param mixed $ID
	 * @return void
	 */
	public function removeTicketByID( $ID ) {
		$ticket = $this->get_by_info( $ID );
		$this->remove( $ticket );
	}



}
// End of file TicketRepository.class.php
// Location: /core/services/cart/TicketRepository.php