<?php

if (!defined('EVENT_ESPRESSO_VERSION'))
	exit('No direct script access allowed');

/**
 * Event Espresso
 *
 * Event Registration and Management Plugin for WordPress
 *
 * @ package			Event Espresso
 * @ author				Seth Shoultes
 * @ copyright		(c) 2008-2011 Event Espresso  All Rights Reserved.
 * @ license				http://eventespresso.com/support/terms-conditions/   * see Plugin Licensing *
 * @ link					http://www.eventespresso.com
 * @ version		 	3.1.P.7
 *
 * ------------------------------------------------------------------------
 *
 * Payment Model
 *
 * @package			Event Espresso
 * @subpackage	includes/models/
 * @author				Sidney Harrell
 *
 * ------------------------------------------------------------------------
 */
Class EEM_Gateways {

	private static $_instance = NULL;
	private $_active_gateways = array();
	private $_all_gateways = array();
	private $_gateway_instances = array();
	private $_payment_settings = array();
	private $_selected_gateway = NULL;
	private $_hide_other_gateways = FALSE;
	private $_session_gateway_data = NULL;
	private $_off_site_form = NULL;
	private $_ajax = TRUE;



	/**
	 * 		@singleton method used to instantiate class object
	 * 		@access public
	 * 		@return class instance
	 */
	public static function instance() {
		// check if class object is instantiated
		if (self::$_instance === NULL or !is_object(self::$_instance) or !is_a(self::$_instance, __CLASS__)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}



	/**
	 * 		class constructor
	 * 		@access private
	 * 		@return void
	 */
	private function __construct() {
		//so client code can check for instatiation b4 including
		define('ESPRESSO_GATEWAYS', TRUE);
		require_once(EVENT_ESPRESSO_INCLUDES_DIR . 'classes/EE_Gateway.class.php');
		require_once(EVENT_ESPRESSO_INCLUDES_DIR . 'classes/EE_Offline_Gateway.class.php');
		require_once(EVENT_ESPRESSO_INCLUDES_DIR . 'classes/EE_Offsite_Gateway.class.php');
		require_once(EVENT_ESPRESSO_INCLUDES_DIR . 'classes/EE_Onsite_Gateway.class.php');

		$this->_load_session_gateway_data();
		$this->_set_active_gateways();
		$this->_load_payment_settings();
		$this->_scan_and_load_all_gateways();
	}



	/**
	 * 		load_session_gateway_data
	 * 		try to pull data from session b4 hitting db
	 * 		@access private
	 * 		@return void
	 */
	private function _load_session_gateway_data() {
		global $EE_Session;
		$this->_session_gateway_data = $EE_Session->get_session_data(FALSE, 'gateway_data');
		if (!empty($this->_session_gateway_data['selected_gateway'])) {
			$this->_selected_gateway = $this->_session_gateway_data['selected_gateway'];
		}
		if (!empty($this->_session_gateway_data['hide_other_gateways'])) {
			$this->_hide_other_gateways = $this->_session_gateway_data['hide_other_gateways'];
		}
		if (!empty($this->_session_gateway_data['off_site_form'])) {
			$this->_off_site_form = $this->_session_gateway_data['off_site_form'];
		}
		if (!empty($this->_session_gateway_data['ajax'])) {
			$this->_ajax = $this->_session_gateway_data['ajax'];
		}
	}



	/**
	 * 		_set_active_gateways
	 * 		@access private
	 * 		@return void
	 */
	private function _set_active_gateways() {
		if (!empty($this->_session_gateway_data['active_gateways'])) {
			$this->_active_gateways = $this->_session_gateway_data['active_gateways'];
		} else {
			global $espresso_wp_user, $EE_Session;
			$this->_active_gateways = get_user_meta($espresso_wp_user, 'active_gateways', TRUE);
			if (!is_array($this->_active_gateways)) {
				$this->_active_gateways = array();
			}
			$EE_Session->set_session_data(array('active_gateways' => $this->_active_gateways), 'gateway_data');
		}
	}



	/**
	 * 		_load_payment_settings
	 * 		@access private
	 * 		@return void
	 */
	private function _load_payment_settings() {

		if (!empty($this->_session_gateway_data['payment_settings'])) {
			$this->_payment_settings = $this->_session_gateway_data['payment_settings'];
		} else {
			global $espresso_wp_user, $EE_Session;
			$this->_payment_settings = get_user_meta($espresso_wp_user, 'payment_settings', TRUE);
			if (!is_array($this->_payment_settings)) {
				$this->_payment_settings = array();
			}
			$EE_Session->set_session_data(array('payment_settings' => $this->_payment_settings), 'gateway_data');
		}

		//echo printr( $this->_payment_settings, __CLASS__ . ' ->' . __FUNCTION__ . ' ( line #' .  __LINE__ . ' )' );
	}




	/**
	 * 		_scan_and_load_all_gateways
	 * 		@access private
	 * 		@return void
	 */
	private function _scan_and_load_all_gateways() {
		// on the settings page, scan and load all the gateways
		if (is_admin() && !empty($_GET['page']) && $_GET['page'] == 'payment_gateways') {
			$this->_load_all_gateway_files();
		} else {
			if (!is_array($this->_active_gateways)) {	// if something went wrong, fail gracefully
				//global $espresso_notices;
				//$espresso_notices['errors'][] = 'No Active Event Espresso Payment Gateways';
				return;
			}
			foreach ($this->_active_gateways as $gateway => $in_uploads) {
				$classname = 'EE_' . $gateway;
				$filename = $classname . '.class.php';
				if ($in_uploads) {
					if (file_exists(EVENT_ESPRESSO_GATEWAY_DIR . $gateway . DS . $filename)) {
						require_once(EVENT_ESPRESSO_GATEWAY_DIR . $gateway . DS . $filename);
					} else {
						$this->unset_active($gateway);	// if it can't find a gateway, delete it from the active_gateways
					}
				} else {
					if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'gateways' . DS . $gateway . DS . $filename)) {
						require_once(EVENT_ESPRESSO_PLUGINFULLPATH . 'gateways' . DS . $gateway . DS . $filename);
					} else {
						$this->unset_active($gateway);	// if it can't find a gateway, delete it from the active_gateways
					}
				}
				if (class_exists($classname)) {
					//$gateway = $classname::instance($this);
					$gateway = call_user_func(array($classname, 'instance'), &$this);
					$this->_gateway_instances[$gateway->gateway()] = $gateway;
				}
			}
		}
	}




	/**
	 * 		_load_all_gateway_files
	 * 		@access private
	 * 		@return void
	 */
	private function _load_all_gateway_files() {
		global $espresso_notices;
		$gateways = array();
		$upload_gateways = array();
		$gateways_glob = glob(EVENT_ESPRESSO_PLUGINFULLPATH . "gateways" . DS . "*", GLOB_ONLYDIR);
		$upload_gateways_glob = glob(EVENT_ESPRESSO_GATEWAY_DIR . '*', GLOB_ONLYDIR);
		if (!is_array($gateways_glob))
			$gateways_glob = array();
		foreach ($gateways_glob as $gateway) {
			$pos = strrpos($gateway, DS);
			$sub = substr($gateway, $pos + 1);
			$gateways[$sub] = FALSE;
		}
		if (!is_array($upload_gateways_glob))
			$upload_gateways_glob = array();
		foreach ($upload_gateways_glob as $upload_gateway) {
			$pos = strrpos($upload_gateway, DS);
			$sub = substr($upload_gateway, $pos + 1);
			$upload_gateways[$sub] = TRUE;
		}
		$this->_all_gateways = array_merge($upload_gateways, $gateways);

		foreach ($this->_all_gateways as $gateway => $in_uploads) {
			$classname = 'EE_' . $gateway;
			$filename = $classname . '.class.php';
			if ($in_uploads) {
				if (file_exists(EVENT_ESPRESSO_GATEWAY_DIR . $gateway . DS . $filename)) {
					require_once(EVENT_ESPRESSO_GATEWAY_DIR . $gateway . DS . $filename);
				} else {
					$espresso_notices['errors'][] = 'The file : <b>' . $filename . '</b> could not be located in either : <b>' . EVENT_ESPRESSO_GATEWAY_DIR . $gateway . DS . '</b>';
				}
			} else if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'gateways' . DS . $gateway . DS . $filename)) {
				require_once(EVENT_ESPRESSO_PLUGINFULLPATH . 'gateways' . DS . $gateway . DS . $filename);
			} else {
				$espresso_notices['errors'][] = 'The file : <b>' . $filename . '</b> could not be located in either : <b>' . EVENT_ESPRESSO_GATEWAY_DIR . $gateway . DS . '</b> or <b>' . EVENT_ESPRESSO_PLUGINFULLPATH . 'gateways' . DS . $gateway . DS . '</b>';
			}

			if (class_exists($classname)) {
				$gateway = call_user_func(array($classname, 'instance'), &$this);
				$this->_gateway_instances[$gateway->gateway()] = $gateway;
			}
		}
	}



	/**
	 * 		get payment settings for specific gateway
	 * 		@access public
	* 		@param	string	$gateway
	 * 		@return 	mixed	array on success FALSE on fail
	 */
	public function payment_settings($gateway = FALSE) {
		if (!empty($this->_payment_settings[$gateway])) {
			return $this->_payment_settings[$gateway];
		} else {
			return FALSE;
		}
	}



	/**
	 * 		update payment settings for specific gateway
	 * 		@access public
	* 		@param	string	$gateway
	* 		@param	array	$settings
	 * 		@return 	boolean	TRUE on success FALSE on fail
	 */
	public function update_payment_settings($gateway = FALSE, $settings = FALSE) {
		if (!$gateway || !$settings) {
			return FALSE;
		}
		$this->_payment_settings[$gateway] = $settings;

		global $espresso_wp_user, $espresso_notices;

		if (update_user_meta($espresso_wp_user, 'payment_settings', $this->_payment_settings)) {
			$espresso_notices['success'][] = __('Payment Settings Updated!', 'event_espresso');
			return TRUE;
		} else {
			$espresso_notices['errors'][] = __('Payment Settings were not saved! ', 'event_espresso');
			return FALSE;
		}
	}



	/**
	 * 		is gateway active ?
	 * 		@access public
	* 		@param	string	$gateway
	 * 		@return 	mixed	array on success FALSE on fail
	 */
	public function is_active($gateway = FALSE) {
		if (!$gateway) {
			return FALSE;
		}
		if (array_key_exists($gateway, $this->_active_gateways)) {
			return array($gateway => $this->_active_gateways[$gateway]);
		} else {
			return FALSE;
		}
	}



	/**
	 * 		is gateway in the uploads dir ?
	 * 		@access public
	* 		@param	string	$gateway
	 * 		@return 	mixed	array on success FALSE on fail
	 */
	public function is_in_uploads($gateway = FALSE) {
		if (!$gateway) {
			return FALSE;
		}
		if (array_key_exists($gateway, $this->_all_gateways)) {
			return array($gateway => $this->_all_gateways[$gateway]);
		} else {
			return FALSE;
		}
	}



	/**
	 * 		set gateway as active and available during registration
	 * 		@access public
	* 		@param	string	$gateway
	 * 		@return 	boolean	TRUE on success FALSE on fail
	 */
	public function set_active($gateway = FALSE) {
		if (!$gateway) {
			return FALSE;
		}
		if (array_key_exists($gateway, $this->_all_gateways)) {
			$this->_active_gateways[$gateway] = $this->_all_gateways[$gateway];
			global $espresso_wp_user, $espresso_notices;
			if (update_user_meta($espresso_wp_user, 'active_gateways', $this->_active_gateways)) {
				$espresso_notices['success'][] = $gateway . __(' Gateway Activated!', 'event_espresso');
				return TRUE;
			} else {
				$espresso_notices['errors'][] = $gateway . __(' Gateway Not Activated! ', 'event_espresso');
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}



	/**
	 * 		unset gateway as active and available during registration
	 * 		@access public
	* 		@param	string	$gateway
	 * 		@return 	boolean	TRUE on success FALSE on fail
	 */
	public function unset_active($gateway = FALSE) {
		if (!$gateway) {
			return FALSE;
		}
		if (array_key_exists($gateway, $this->_active_gateways)) {
			unset($this->_active_gateways[$gateway]);
			global $espresso_wp_user, $espresso_notices;
			if (update_user_meta($espresso_wp_user, 'active_gateways', $this->_active_gateways)) {
				$espresso_notices['success'][] =$gateway .  __('Gateway Deactivated!', 'event_espresso');
				return TRUE;
			} else {
				$espresso_notices['errors'][] = $gateway . __('Gateway Not Deactivated! ', 'event_espresso');
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}



	/**
	 * 		set gateway as selected
	 * 		@access public
	* 		@param	string	$gateway
	 * 		@return 	boolean	TRUE on success FALSE on fail
	 */
	public function set_selected_gateway($gateway = FALSE) {
	
		if (!$gateway || !array_key_exists($gateway, $this->_active_gateways)) {
			return FALSE;
		} 
		
		$this->_selected_gateway = $gateway;
		$this->_hide_other_gateways = TRUE;
		$this->_set_session_data();
		foreach ($this->_active_gateways as $gateway => $in_uploads) {
			if ($this->_selected_gateway == $gateway) {
				$this->_gateway_instances[$gateway]->set_selected();
			} else {
				$this->_gateway_instances[$gateway]->set_hidden();
			}
		}
		return TRUE;

	}



	/**
	 * 		unset gateway as selected
	 * 		@access public
	 * 		@return 	boolean	TRUE 
	 */
	public function unset_selected_gateway() {
		$this->_selected_gateway = NULL;
		$this->_hide_other_gateways = FALSE;
		$this->_set_session_data();
		foreach ($this->_active_gateways as $gateway => $in_uploads) {
			$this->_gateway_instances[$gateway]->unset_selected();
		}
		return TRUE;
	}



	/**
	 * 		get selected gateway
	 * 		@access public
	 * 		@return 	string
	 */
	public function selected_gateway() {
		return $this->_selected_gateway;
	}



	/**
	 * 		set_form_url
	 * 		@access public
	* 		@param	string	$base_url
	 * 		@return 	boolean	TRUE on success FALSE on fail
	 */
	public function set_form_url($base_url = FALSE) {
		if (!$base_url) {
			return FALSE;
		}
		foreach ($this->_active_gateways as $gateway => $in_uploads) {
			if (!empty($this->_gateway_instances[$gateway])) {
				$this->_gateway_instances[$gateway]->set_form_url($base_url);
			} else {
				return FALSE;
			}
		}
		return TRUE;
	}



	/**
	 * 		get gateway type
	 * 		@access public
	 * 		@return 	mixed	string on success FALSE on fail
	 */
	public function type() {
		if (!empty($this->_payment_settings[$this->_selected_gateway]['type'])) {
			return $this->_payment_settings[$this->_selected_gateway]['type'];
		} else {
			return FALSE;
		}
	}



	/**
	 * 		get gateway display name
	 * 		@access public
	 * 		@return 	mixed	string on success FALSE on fail
	 */
	public function display_name() {
		if (!empty($this->_payment_settings[$this->_selected_gateway]['display_name'])) {
			return $this->_payment_settings[$this->_selected_gateway]['display_name'];
		} else {
			return FALSE;
		}
	}



	/**
	 * 		get gateway off_site_form
	 * 		@access public
	 * 		@return 	mixed	string on success FALSE on fail
	 */
	public function off_site_form() {
		if (!empty($this->_off_site_form)) {
			return $this->_off_site_form;
		} else {
			return FALSE;
		}
	}



	/**
	 * 		set_off_site_form
	 * 		@access public
	* 		@param	array	$form_data
	 * 		@return 	boolean	TRUE on success FALSE on fail
	 */
	public function set_off_site_form($form_data = FALSE) {
		if (!$form_data) {
			return FALSE;
		}
		$this->_off_site_form = $form_data;
		$this->_set_session_data();
		return TRUE;
	}



	/**
	 * 		set_session_data
	 * 		@access public
	 * 		@return 	boolean	TRUE on success FALSE on fail
	 */
	private function _set_session_data() {
		global $EE_Session;
		
		$session_data = array(
						'selected_gateway' => $this->_selected_gateway,
						'hide_other_gateways' => $this->_hide_other_gateways,
						'off_site_form' => $this->_off_site_form,
						'ajax' => $this->_ajax
				);
		// returns TRUE or FALSE
		return $EE_Session->set_session_data( $session_data, 'gateway_data' );		

	}



	/**
	 * 		set_ajax
	 * 		@access public
	* 		@param	boolean	$on_or_off
	 * 		@return 	boolean	TRUE on success FALSE on fail
	 */
	public function set_ajax( $on_or_off ) {
		if ( ! is_bool( $on_or_off )) {
			$this->_notices['errors'][] = __( 'An error occured. Set Ajax requires a boolean paramater.', 'event_espresso' );
			return FALSE;
		}
		$this->_ajax = $on_or_off;
		return $this->_set_session_data();
	}




	/**
	 * 		get ajax - whether request is regular HTML or via ajax
	 * 		@access public
	 * 		@return 	boolean
	 */
	public function ajax() {
		return $this->_ajax;
	}



	/**
	 * 		reset_session_data
	 * 		@access public
	 * 		@return 	boolean	TRUE on success FALSE on fail
	 */
	public function reset_session_data() {
		foreach ($this->_active_gateways as $gateway => $in_uploads) {
			if (!empty($this->_gateway_instances[$gateway])) {
				$this->_gateway_instances[$gateway]->reset_session_data();
			} else {
				return FALSE;
			}
		}
		$this->_selected_gateway = NULL;
		$this->_hide_other_gateways = FALSE;
		$this->_off_site_form = NULL;
		$this->_ajax = TRUE;
		$this->_payment_settings = array();
		$this->_active_gateways = array();
		global $EE_Session;
		$EE_Session->set_session_data(array(
				'selected_gateway' => $this->_selected_gateway,
				'hide_other_gateways' => $this->_hide_other_gateways,
				'off_site_form' => $this->_off_site_form,
				'ajax' => $this->_ajax,
				'payment_settings' => $this->_payment_settings,
				'active_gateways' => $this->_active_gateways
						), 'gateway_data');
		return TRUE;
	}



	/**
	 * 		send_invoice
	 * 		@access public
	* 		@param	int	$id
	 * 		@return 	boolean	TRUE on success FALSE on fail
	 */
	public function send_invoice($id) {
		$gateway = 'Invoice';
		$classname = 'EE_' . $gateway;
		$filename = $classname . '.class.php';
		if (file_exists(EVENT_ESPRESSO_GATEWAY_DIR . $gateway . DS . $filename)) {
			require_once(EVENT_ESPRESSO_GATEWAY_DIR . $gateway . DS . $filename);
		} else if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'gateways' . DS . $gateway . DS . $filename)) {
			require_once(EVENT_ESPRESSO_PLUGINFULLPATH . 'gateways' . DS . $gateway . DS . $filename);
		} else {
			global $espresso_notices;
			$espresso_notices['errors'][] = 'The file : <b>' . $filename . '</b> could not be located in either : <b>' . EVENT_ESPRESSO_GATEWAY_DIR . $gateway . DS . '</b> or <b>' . EVENT_ESPRESSO_PLUGINFULLPATH . 'gateways' . DS . $gateway . DS . '</b>';
		}

		if (class_exists($classname)) {
			$invoice = call_user_func(array($classname, 'instance'), &$this);
			$invoice->send_invoice($id);
			return TRUE;
		} else {
			return FALSE;
		}
	}



	/**
	 * 		get_country_ISO2_codes
	 * 		@access public
	 * 		@return 	array
	 */
	public function get_country_ISO2_codes() {
		
		return array(
					'US' => 'United States',
					'AU' => 'Australia',
					'CA' => 'Canada', 
					'GB' => 'United Kingdom',
					'FR' => 'France', 
					'IT' => 'Italy', 
					'ES' => 'Spain', 
					'DE' => 'Germany', 
					'CH' => 'Switzerland', 
					'NL' => 'The Netherlands', 
					'SE' => 'Sweden', 
					'AF' => 'Afghanistan', 
					'AL' => 'Albania', 
					'CY' => 'Akrotiri and Dhekelia', 
					'AD' => 'Andorra', 
					'AO' => 'Angola', 
					'AI' => 'Anguilla', 
					'AQ' => 'Antarctica', 
					'AG' => 'Antigua and Barbuda', 
					'SA' => 'Saudi Arabia', 
					'DZ' => 'Argelia', 
					'AR' => 'Argentina', 
					'AM' => 'Armenia', 
					'AW' => 'Aruba', 
					'AT' => 'Austria', 
					'AZ' => 'Azerbaijan', 
					'BS' => 'Bahamas', 
					'BH' => 'Bahrein', 
					'BD' => 'Bangladesh', 
					'BB' => 'Barbados', 
					'BE' => 'Belgium ', 
					'BZ' => 'Belize', 
					'BJ' => 'Benin', 
					'BM' => 'Bermudas', 
					'BY' => 'Belarus', 
					'BO' => 'Bolivia', 
					'BA' => 'Bosnia and Herzegovina', 
					'BW' => 'Botswana', 
					'BV' => 'Bouvet Island', 
					'BR' => 'Brazil', 
					'BN' => 'Brunei', 
					'BG' => 'Bulgaria', 
					'BF' => 'Burkina Faso', 
					'BI' => 'Burundi', 
					'BT' => 'Bhutan', 
					'CV' => 'Cape Verde', 
					'KH' => 'Cambodia', 
					'CM' => 'Cameroon', 
					'KY' => 'Cayman Islands', 
					'CF' => 'Central African Republic', 
					'TD' => 'Chad', 
					'CL' => 'Chile', 
					'CN' => 'China', 
					'CX' => 'Christmas Island', 
					'CY' => 'Cyprus', 
					'CC' => 'Cocos Island', 
					'CK' => 'Cook Islands', 
					'CO' => 'Colombia', 
					'KM' => 'Comoros', 
					'CG' => 'Congo', 
					'KP' => 'Corea del Norte', 
					'CR' => 'Costa Rica', 
					'HR' => 'Croatia', 
					'CU' => 'Cuba', 
					'CZ' => 'Czech Republic', 
					'DK' => 'Danmark', 
					'DJ' => 'Djibouti', 
					'DM' => 'Dominica', 
					'DO' => 'Dominican Republic', 
					'EC' => 'Ecuador', 
					'EG' => 'Egypt', 
					'SV' => 'El Salvador', 
					'ER' => 'Eritrea', 
					'SK' => 'Eslovakia', 
					'SI' => 'Eslovenia', 
					'EE' => 'Estonia', 
					'ET' => 'Ethiopia', 
					'FO' => 'Faroe islands', 
					'FK' => 'Falkland Islands', 
					'FJ' => 'Fiji', 
					'FI' => 'Finland', 
					'GA' => 'Gabon', 
					'GM' => 'Gambia', 
					'GE' => 'Georgia', 
					'GH' => 'Ghana', 
					'GI' => 'Gibraltar', 
					'GR' => 'Greece', 
					'GD' => 'Grenada', 
					'GL' => 'Greenland', 
					'GP' => 'Guadeloupe', 
					'GU' => 'Guam', 
					'GT' => 'Guatemala', 
					'GN' => 'Guinea', 
					'GQ' => 'Equatorial Guinea',
					'GW' => 'Guinea-Bissau', 
					'GY' => 'Guyana', 
					'HT' => 'Haiti', 
					'HN' => 'Honduras', 
					'HK' => 'Hong Kong', 
					'HU' => 'Hungary', 
					'IN' => 'India', 
					'IO' => 'British Indian Ocean Territory', 
					'ID' => 'Indonesia', 
					'IQ' => 'Iraq', 
					'IR' => 'Iran', 
					'IE' => 'Ireland', 
					'IS' => 'Iceland', 
					'IL' => 'Israel', 
					'CI' => 'Ivory Coast ', 
					'JM' => 'Jamaica', 
					'JP' => 'Japan', 
					'JO' => 'Jordan', 
					'KZ' => 'Kazakhstan', 
					'KE' => 'Kenya', 
					'KG' => 'Kirguistan', 
					'KI' => 'Kiribati', 
					'KR' => 'South Korea', 
					'XK' => 'Kosovo', 
					'KW' => 'Kuwait', 
					'LA' => 'Laos', 
					'LV' => 'Latvia', 
					'LS' => 'Lesotho', 
					'LB' => 'Lebanon', 
					'LR' => 'Liberia', 
					'LY' => 'Libya', 
					'LI' => 'Liechtenstein', 
					'LT' => 'Lithuania', 
					'LU' => 'Luxemburg', 
					'MO' => 'Macao', 
					'MK' => 'Macedonia', 
					'MG' => 'Madagascar', 
					'MY' => 'Malaysia', 
					'MW' => 'Malawi', 
					'MV' => 'Maldivas', 
					'ML' => 'Mali', 
					'MT' => 'Malta', 
					'MP' => 'Northern Marianas', 
					'MA' => 'Marruecos', 
					'MH' => 'Marshall islands', 
					'MQ' => 'Martinica', 
					'MU' => 'Mauricio', 
					'MR' => 'Mauritania', 
					'YT' => 'Mayote', 
					'MX' => 'Mexico', 
					'FM' => 'Micronesia', 
					'MD' => 'Moldova', 
					'MC' => 'Monaco', 
					'MN' => 'Mongolia', 
					'MS' => 'Montserrat', 
					'ME' => 'Montenegro', 
					'MZ' => 'Mozambique', 
					'MM' => 'Myanmar', 
					'NA' => 'Namibia', 
					'NR' => 'Nauru', 
					'NP' => 'Nepal', 
					'AN' => 'Netherlands Antilles', 
					'NI' => 'Nicaragua', 
					'NE' => 'Niger', 
					'NG' => 'Nigeria', 
					'NU' => 'Niue', 
					'NO' => 'Norway', 
					'NC' => 'New Caledonia', 
					'NZ' => 'New Zealand', 
					'OM' => 'Oman', 
					'PK' => 'Pakistan', 
					'PW' => 'Palau', 
					'PA' => 'Panama', 
					'PG' => 'Papua New Guinea', 
					'PY' => 'Paraguay', 
					'PE' => 'Peru', 
					'PH' => 'Philippines', 
					'PL' => 'Poland', 
					'PT' => 'Portugal', 
					'PR' => 'Puerto Rico', 
					'QA' => 'Qatar', 
					'RW' => 'Rowanda', 
					'RO' => 'Romania', 
					'RU' => 'Russia', 
					'PM' => 'Saint Pierre and Miquelon', 
					'WS' => 'Samoa', 
					'AS' => 'American Samoa', 
					'SM' => 'San Marino', 
					'VC' => 'San Vincente y las Granadinas', 
					'SH' => 'Santa Helena', 
					'LC' => 'Santa Lucia', 
					'SN'  => 'Senegal', 
					'SC' => 'Seychelles', 
					'SL' => 'Sierra Leona', 
					'SG' => 'Singapore', 
					'SY' => 'Syria', 
					'SO' => 'Somalia', 
					'LK' => 'Sri Lanka', 
					'ZA' => 'South Africa', 
					'SD' => 'Sudan', 
					'SR' => 'Suriname', 
					'SZ' => 'Swaziland', 
					'TH' => 'Thailand', 
					'TW' => 'Taiwan', 
					'TZ' => 'Tanzania', 
					'TJ' => 'Tajikistan', 
					'TP' => 'Timor Oriental', 
					'TG' => 'Togo', 
					'TK' => 'Tokelau', 
					'TO' => 'Tonga', 
					'TT' => 'Trinidad and Tobago', 
					'TN' => 'Tunisia', 
					'TM' => 'Turkmenistan', 
					'TR' => 'Turkey', 
					'TV' => 'Tuvalu', 
					'UA' => 'Ukraine', 
					'UG' => 'Uganda', 
					'AE' => 'United Arab Emirates', 
					'UY' => 'Uruguay', 
					'UZ' => 'Uzbekistan', 
					'VU' => 'Vanuatu', 
					'VA' => 'Vatican City', 
					'VE' => 'Venezuela', 
					'VN' => 'Vietnam', 
					'VI' => 'Virgin Islands',
					'YE' => 'Yemen', 
					'YU' => 'Yugoslavia', 
					'ZM' => 'Zambia', 
					'ZW' => 'Zimbabwe'					
				);
	
	}



	/**
	 * 		process_gateway_selection()
	 * 		@access public
	 * 		@return 	mixed	array on success or FALSE on fail
	 */
	public function process_gateway_selection() {	

			global $espresso_notices;
			// check for off site payment
			if ( isset( $_POST['selected_gateway'] ) && ! empty( $_POST['selected_gateway'] )) {
				$this->set_selected_gateway(sanitize_text_field( $_POST['selected_gateway'] ));
			} else {
				$espresso_notices['errors'][] =  __( 'An error occured. No gateway has been selected.', 'event_espresso' );
			}
			$this->_gateway_instances[ $this->_selected_gateway ]->process_gateway_selection();		
	}



	/**
	 * 		set_billing_info_for_confirmation
	 * 		@access public
	* 		@param	array	$billing_info
	 * 		@return 	mixed	array on success or FALSE on fail
	 */
	public function set_billing_info_for_confirmation( $billing_info = FALSE ) {
		if( ! is_array( $billing_info )) {
			return FALSE;
		}
		$confirm_info = $this->_gateway_instances[ $this->_selected_gateway ]->set_billing_info_for_confirmation( $billing_info );
		$confirm_info['gateway'] = $this->display_name();
		return $confirm_info;
	}



	/**
	 * 		process_reg_step_3
	 * 		@access public
	* 		@param	int	$id
	 * 		@return 	mixed	void or FALSE on fail
	 */
	public function process_reg_step_3() {
		$return_page_url = $this->_set_return_page_url();
		// free event?
		if (isset($_POST['reg-page-no-payment-required']) && absint($_POST['reg-page-no-payment-required']) == 1) {
			// becuz this was a free event we need to generate some pseudo gateway results
			$txn_results = array(
					'approved' => TRUE,
					'response_msg' => __('You\'re registration has been completed successfully.', 'event_espresso'),
					'status' => 'Approved',
					'details' => 'free event',
					'amount' => 0.00,
					'method' => 'none'
			);
			$EE_Session->set_session_data(array('txn_results' => $txn_results), 'session_data');

		} else {
			$this->_gateway_instances[ $this->_selected_gateway ]->process_reg_step_3($return_page_url);
		}
		return $return_page_url;
	}	

		/**
	 * 		set_return_page_url
	 *
	 * 		@access 		public
	 * 		@return 		void
	 */
	private function _set_return_page_url() {
		global $org_options;
		$return_page_id = $org_options['return_url'];
		// get permalink for thank you page
		// to ensure that it ends with a trailing slash, first we remove it (in case it is there) then add it again
		$this->_return_page_url = rtrim( get_permalink( $return_page_id ), '/' );
	}


	/**
	 * Replaces all but the last for digits with x's in the given credit card number
	 * @param int|string $cc The credit card number to mask
	 * @return string The masked credit card number
	 */
	function MaskCreditCard($cc){
		// Get the cc Length
		$cc_length = strlen($cc);
		// Replace all characters of credit card except the last four and dashes
		for($i=0; $i<$cc_length-4; $i++){
			if($cc[$i] == '-'){continue;}
			$cc[$i] = 'X';
		}
		// Return the masked Credit Card #
		return $cc;
	}
	
	
	
	/**
	 * Add dashes to a credit card number.
	 * @param int|string $cc The credit card number to format with dashes.
	 * @return string The credit card with dashes.
	 */
	function FormatCreditCard($cc)
	{
		// Clean out extra data that might be in the cc
		$cc = str_replace(array('-',' '),'',$cc);
		// Get the CC Length
		$cc_length = strlen($cc);
		// Initialize the new credit card to contian the last four digits
		$newCreditCard = substr($cc,-4);
		// Walk backwards through the credit card number and add a dash after every fourth digit
		for($i=$cc_length-5;$i>=0;$i--){
			// If on the fourth character add a dash
			if((($i+1)-$cc_length)%4 == 0){
				$newCreditCard = '&nbsp;'.$newCreditCard;
			}
			// Add the current character to the new credit card
			$newCreditCard = $cc[$i].$newCreditCard;
		}
		// Return the formatted credit card number
		return $newCreditCard;
	}
		
	public function thank_you_page() {
	if ($type == 'off-site' || ($type == 'onsite' && !$EEM_Gateways->ajax())) {
		$SPCO = EE_Single_Page_Checkout::instance();
		$SPCO->process_registration_payment(FALSE);
	} elseif ($type == 'off-line') {
		$SPCO = EE_Single_Page_Checkout::instance();
		$SPCO->process_off_line_gateway();
	}
	}
	
	/**
	 * 		process registration payment
	 *
	 * 		@access 		private
	 * 		@param 		boolean 		$perform_redirect  - whether to send JSON response or redirect
	 * 		@return 		JSON			or redirect
	 */
	public function process_registration_payment() {

		global $EE_Session;
		require_once ( EVENT_ESPRESSO_INCLUDES_DIR . 'models/EEM_Transaction.model.php' );

		$success_msg = FALSE;
		$error_msg = FALSE;

		// grab session data
		$session = $EE_Session->get_session_data();
		//printr( $session, 'session data ( ' . __FUNCTION__ . ' on line: ' .  __LINE__ . ' )' ); die();

		$transaction = $session['transaction'];
		$txn_results = $session['txn_results'];
		// $txn_results['txn_results'] = $session;

		$txn_results['amount'] = isset($txn_results['amount']) ? $txn_results['amount'] : 0.00;
		$txn_results['method'] = isset($txn_results['method']) ? $txn_results['method'] : '';

		switch ($txn_results['status']) {

			case 'Approved' :
				$pay_status = 'PAP';
				$success_msg = $txn_results['response_msg'];
				do_action('action_hook_espresso_reg_approved');
				break;
			
			case 'Declined' :
				$pay_status = 'PDC';
				$error_msg = __('We\'re sorry, but the transaction was declined for the following reasons: <br />', 'event_espresso') . '<b>' . $txn_results['response_msg'] . '</b>';
				do_action('action_hook_espresso_reg_declined');
				break;

			case 'Cancelled' :
				$pay_status = 'PCN';
				$error_msg = __('The Transaction was cancelled.', 'event_espresso');
				do_action('action_hook_espresso_reg_cancelled');
				break;

			case 'FAILED' :
				$pay_status = 'PFL';
				$error_msg = __('We\'re sorry, but an error occured and the transaction could not be completed. Please try again. If problems persist, contact the site administrator.', 'event_espresso');
				do_action('action_hook_espresso_reg_incomplete');
				break;
		}
		
		$txn_status = 'TOP';
		
		// did transaction require payment now ? later ? or was it free ?
		if ( $transaction->total() > 0 && $gateway_type != 'off-line' ) {
		
			require_once(EVENT_ESPRESSO_INCLUDES_DIR . 'models/EEM_Payment.model.php');
			EEM_Payment::instance();
			$payment = new EE_Payment( 
																	$transaction->ID(), 
																	$pay_status,
																	$transaction->datetime(), 
																	$txn_results['method'], 
																	$txn_results['amount'],
																	$this->display_name(),
																	$txn_results['response_msg'],
																	$txn_results['transaction_id'],
																	NULL,
																	$session['primary_attendee']['registration_id'],
																	FALSE,
																	maybe_serialize( $txn_results )
																);
			$results = $payment->insert();
			if (!$results) {
				$error_msg = __('There was a problem inserting your payment into our records. Do not attempt the transaction again. Please contact support.', 'event_espresso');
			}
		
//printr( $payment, '$payment  <br /><span style="font-size:10px;font-weight:normal;">( file: '. __FILE__ . ' - line no: ' . __LINE__ . ' )</span>', 'auto' );
//printr( $transaction, '$transaction  <br /><span style="font-size:10px;font-weight:normal;">( file: '. __FILE__ . ' - line no: ' . __LINE__ . ' )</span>', 'auto' );

		
			if ( $payment->amount() >= $transaction->total() ) {
				$txn_status = 'TCM';
			} 
			
		//TODO: 	set $txn_status in gateway type classes
		} elseif ( $gateway_type == 'off-line' ) {
			// payments using 'off-line' gateways stay at OPEN status
			$txn_status = 'TOP';
		} else {
			// but free events get set as completed !
			$txn_status = 'TCM';
		} 
		
		$transaction->set_paid($txn_results['amount']);
		$transaction->set_status($txn_status);
		$transaction->set_details( $txn_results );
		unset( $session['transaction'] );
		$transaction->set_session_data( $session );

		if (isset($txn_results['md5_hash'])) {
			$transaction->set_hash_salt($txn_results['md5_hash']);
		}

		if (isset($session['taxes'])) {
			$tax_data = array('taxes' => $session['taxes'], 'tax_totals' => $session['tax_totals']);
			$transaction->set_tax_data($tax_data);
		}

		$transaction->update();

//printr( $transaction, '$transaction  <br /><span style="font-size:10px;font-weight:normal;">( file: '. __FILE__ . ' - line no: ' . __LINE__ . ' )</span>', 'auto' );
//die();


	}


}
