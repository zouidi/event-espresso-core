<?php if ( !defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}
/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package 		Event Espresso
 * @ author 		Event Espresso
 * @ copyright 	(c) 2008-2011 Event Espresso  All Rights Reserved.
 * @ license 		{@link http://eventespresso.com/support/terms-conditions/}   * see Plugin Licensing *
 * @ link 				{@link http://www.eventespresso.com}
 * @ since 			4.0
 *
 */



/**
 * EE_WP_User_Meta class
 *
 * @package 			Event Espresso
 * @subpackage 	includes/classes/EE_Answer.class.php
 * @author 				Mike Nelson
 */
class EE_WP_User_Meta extends EE_Base_Class {

	/**
	 * @param array $props_n_values
	 * @return EE_WP_User_Meta|mixed
	 */
	public static function new_instance( $props_n_values = array() ) {
		$has_object = parent::_check_for_object( $props_n_values, __CLASS__ );
		return $has_object ? $has_object : new self( $props_n_values );
	}



	/**
	 * @param array $props_n_values
	 * @return EE_WP_User_Meta
	 */
	public static function new_instance_from_db( $props_n_values = array() ) {
		return new self( $props_n_values, TRUE );
	}


	/**
	 * Gets key
	 * @return string
	 */
	function key() {
		return $this->get( 'meta_key' );
	}



	/**
	 * Sets key
	 * @param string $key
	 * @return boolean
	 */
	function set_key( $key ) {
		$this->set( 'meta_key', $key );
	}



	/**
	 * Gets value
	 * @return string
	 */
	function value() {
		return $this->get( 'meta_value' );
	}



	/**
	 * Sets value
	 * @param string $value
	 * @return boolean
	 */
	function set_value( $value ) {
		$this->set( 'meta_value', $value );
	}
	
	/**
	 * Gets value
	 * @return string
	 */
	function wp_user_ID() {
		return $this->get( 'user_id' );
	}



	/**
	 * Sets value
	 * @param string $value
	 * @return boolean
	 */
	function set_wp_user_ID( $value ) {
		$this->set( 'user_id', $value );
	}

	


}
/* End of file EE_WP_User_Meta.class.php */
/* Location: /includes/classes/EE_Answer.class.php */