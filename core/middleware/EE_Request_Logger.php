<?php
if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class EE_Request_Logger
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class EE_Request_Logger extends EE_Middleware {

	const file = 'espresso_debug_request.log';


	/**
	 * converts a Request to a Response
	 *
	 * @param    EE_Request  $request
	 * @param    EE_Response $response
	 * @return \EE_Response
	 * @throws \EE_Error
	 */
	public function handle_request( EE_Request $request, EE_Response $response ) {
		$this->request = $request;
		$this->response = $response;
		// disable logging by adding the following to your functions.php file:
		// add_filter( 'FHEE__EE_Request_Logger__handle_request__enable_debug_request_log', '__return_false' );
		if (
			apply_filters(
				'FHEE__EE_Request_Logger__handle_request__enable_debug_request_log',
				in_array(
					$_SERVER['REMOTE_ADDR'],
					// servers that we'll enable logging on
					array(
						'127.0.0.1',
						'::1'
					)
				)
			)
		) {
			$path_to_log_file = str_replace(
				array( '\\', '/' ),
				DS,
				EVENT_ESPRESSO_UPLOAD_DIR . 'logs' . DS . EE_Request_Logger::file
			);
			if ( WP_DEBUG ) {
				$this->write_to_log( $path_to_log_file );
			} else {
				$this->delete_log( $path_to_log_file );
			}
		}
		$this->response = $this->process_request_stack( $this->request, $this->response );
		return $this->response;
	}



	/**
	 * @param $path_to_log_file
	 * @throws \EE_Error
	 */
	protected function write_to_log( $path_to_log_file ) {
		if (
			isset( $_GET['doing_wp_cron'] )
			|| ( isset( $_POST['action'] ) && $_POST['action'] === 'heartbeat' )
		) {
			return;
		}
		if ( ! file_exists( $path_to_log_file ) && ! touch( $path_to_log_file ) ) {
			throw new EE_Error(
				sprintf(
					__( 'The Espresso debug request log file %1$s can not be created', 'event_espresso' ),
					$path_to_log_file
				)
			);
		}
		if ( ! is_writable( $path_to_log_file ) ) {
			throw new EE_Error(
				sprintf(
					__( 'The Espresso debug request log file %1$s is not writable', 'event_espresso' ),
					$path_to_log_file
				)
			);
		}
		$entry = '[' . date( 'Y-m-d H:i:s' ) . '] ' . PHP_EOL;
		$entry .= 'http' . ( isset( $_SERVER['HTTPS'] ) ? 's' : '' ) . '://';
		$entry .= "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" . PHP_EOL;
		$entry .= is_admin() ? 'ADMIN REQUEST' . PHP_EOL : '';
		$entry .= defined( 'DOING_AJAX' ) && DOING_AJAX ? 'AJAX REQUEST' . PHP_EOL : '';
		if ( ! empty( $_GET ) ) {
			$entry .= '$_GET ' . var_export( $_GET, true ) . PHP_EOL;
		}
		if ( ! empty( $_POST ) ) {
			$entry .= '$_POST ' . var_export( $_POST, true ) . PHP_EOL;
		}
		$entry .= PHP_EOL;
		file_put_contents( $path_to_log_file, $entry, FILE_APPEND | LOCK_EX );
	}



	/**
	 * @param $path_to_log_file
	 * @throws \EE_Error
	 */
	protected function delete_log( $path_to_log_file ) {
		if ( file_exists( $path_to_log_file ) && is_writable( $path_to_log_file ) ) {
			unlink( $path_to_log_file );
		}
	}

}
// End of file EE_Request_Logger.php
// Location: /EE_Request_Logger.php