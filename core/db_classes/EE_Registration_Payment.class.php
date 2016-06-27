<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }
/**
 * Class EE_Registration_Payment
 *
 * Description
 *
 * @package 			Event Espresso
 * @subpackage 	core
 * @author 				Brent Christensen
 * @since 				4.7.0
 *
 */

class EE_Registration_Payment extends EE_Base_Class {



	/**
	 * registration_ID
	 * @access 	public
	 * @return 	int
	 */
	public function registration_ID() {
		return $this->get( 'REG_ID' );
	}



	/**
	 * payment_ID
	 * @access 	public
	 * @return    int
	 */
	public function payment_ID() {
		return $this->get( 'PAY_ID' );
	}



	/**
	 * amount
	 * @access 	public
	 * @return 	float
	 */
	public function amount() {
		return $this->get( 'RPY_amount' );
	}



	/**
	 * amount
	 * @access    public
	 * @param float $amount
	 */
	public function set_amount( $amount = 0.000 ) {
		$this->set( 'RPY_amount', $amount );
	}



	/**
	 * registration
	 * @access    public
	 * @return \EE_Registration
	 */
	public function registration() {
		return $this->get_first_related( 'Registration' );
	}



	/**
	 * payment
	 * @access    public
	 * @return \EE_Payment
	 */
	public function payment() {
		return $this->get_first_related( 'Payment' );
	}



}
// End of file EE_Registration_Payment.class.php
// Location: /EE_Registration_Payment.class.php