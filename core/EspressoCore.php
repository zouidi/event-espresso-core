<?php

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class EspressoCore
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class EspressoCore {

	/**
	 * @access protected
	 * @type   \EE_Request $request
	 */
	protected $request;

	/**
	 * @access protected
	 * @type   \EE_Response $response
	 */
	protected $response;

	/**
	 * @access protected
	 * @type   \EE_Registry $registry
	 */
	protected $registry;

	/**
	 * @access protected
	 * @type   \EE_Cron_Tasks $cron_tasks
	 */
	protected $cron_tasks;

	/**
	 * @access protected
	 * @type   \EE_Request_Handler $request_handler
	 */
	protected $request_handler;

	/**
	 * @access protected
	 * @type   \EE_System $system
	 */
	protected $system;

	/**
	 * @access protected
	 * @type   array $db_version_history
	 */
	protected $db_version_history;



	/**
	 * @access    public
	 */
	public function __construct() {
	}



	/**
	 * @return EE_Request
	 */
	public function request() {
		return $this->request;
	}



	/**
	 * @param \EE_Request $request
	 */
	public function set_request( \EE_Request $request ) {
		$this->request = $request;
	}



	/**
	 * @return EE_Response
	 */
	public function response() {
		return $this->response;
	}



	/**
	 * @param \EE_Response $response
	 */
	public function set_response( \EE_Response $response ) {
		$this->response = $response;
	}



	/**
	 * @return EE_Registry
	 */
	public function registry() {
		return $this->registry;
	}



	/**
	 * @param \EE_Registry $registry
	 */
	public function set_registry( \EE_Registry $registry ) {
		$this->registry = $registry;
	}



	/**
	 * @return \EE_Cron_Tasks
	 */
	public function cron_tasks() {
		return $this->cron_tasks;
	}



	/**
	 * @param \EE_Cron_Tasks $cron_tasks
	 */
	public function set_cron_tasks( \EE_Cron_Tasks $cron_tasks ) {
		$this->cron_tasks = $cron_tasks;
	}



	/**
	 * @return \EE_Request_Handler
	 */
	public function request_handler() {
		return $this->request_handler;
	}



	/**
	 * @param \EE_Request_Handler $request_handler
	 */
	public function set_request_handler( \EE_Request_Handler $request_handler ) {
		$this->request_handler = $request_handler;
	}



	/**
	 * @return \EE_System
	 */
	public function system() {
		return $this->system;
	}



	/**
	 * @param \EE_System $system
	 */
	public function set_system( \EE_System $system ) {
		$this->system = $system;
	}



	/**
	 * @return array
	 */
	public function db_version_history() {
		return $this->db_version_history;
	}



	/**
	 * @param array $db_version_history
	 */
	public function set_db_version_history( $db_version_history ) {
		$this->db_version_history = $db_version_history;
	}






}
// End of file EspressoCore.php
// Location: /EspressoCore.php