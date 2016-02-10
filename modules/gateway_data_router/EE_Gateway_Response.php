<?php
namespace EventEspresso\modules\gateway_data_router;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class EE_Gateway_Response
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class EE_Gateway_Response {

	/**
	 * @var string $selected_method_of_payment
	 */
	protected $selected_method_of_payment = '';

	/**
	 * @var string $session_id
	 */
	protected $session_id = '';

	/**
	 * @var int $transaction_id
	 */
	protected $transaction_id = 0;

	/**
	 * @var \EE_Transaction $transaction
	 */
	protected $transaction = null;



	/**
	 * EE_Gateway_Response constructor.
	 *
	 * @param string          $session_id
	 * @param string          $transaction_id
	 * @param \EE_Transaction $transaction
	 * @param string          $selected_method_of_payment
	 */
	public function __construct(
		$session_id = '',
		$transaction_id = '',
		\EE_Transaction $transaction = null,
		$selected_method_of_payment = ''
	) {
		$this->set_session_id( $session_id );
		$this->set_transaction_id( $transaction_id );
		$this->set_transaction( $transaction );
		$this->set_selected_method_of_payment( $selected_method_of_payment );
	}



	/**
	 * @return mixed
	 */
	public function selected_method_of_payment() {
		return $this->selected_method_of_payment;
	}



	/**
	 * @param mixed $selected_method_of_payment
	 */
	public function set_selected_method_of_payment( $selected_method_of_payment ) {
		$this->selected_method_of_payment = sanitize_text_field( $selected_method_of_payment );
	}



	/**
	 * @return string
	 */
	public function session_id() {
		return $this->session_id;
	}



	/**
	 * @param string $session_id
	 */
	public function set_session_id( $session_id ) {
		$this->session_id = sanitize_text_field( $session_id );
	}



	/**
	 * @return int
	 */
	public function transaction_id() {
		return $this->transaction_id;
	}



	/**
	 * @param int $transaction_id
	 */
	public function set_transaction_id( $transaction_id ) {
		$this->transaction_id = absint( $transaction_id );
	}



	/**
	 * @return \EE_Transaction
	 */
	public function transaction() {
		return $this->transaction;
	}



	/**
	 * @param \EE_Transaction $transaction
	 */
	public function set_transaction( \EE_Transaction $transaction = null ) {
		$this->transaction = $transaction;
	}



}
// End of file EE_Gateway_Response.php
// Location: /EE_Gateway_Response.php