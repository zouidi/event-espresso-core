<?php

if (!defined('EVENT_ESPRESSO_VERSION'))
	exit('No direct script access allowed');

/**
 *
 * EE_Mock
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Mike Nelson
 *
 */
class EE_New_Addon_Thing extends EE_Base_Class{



	/**
	 * overrides parent constructor
	 *
	 * @param array   $fieldValues  where each key is a field
	 *                              (ie, array key in the 2nd layer of the model's _fields array,
	 *                              (eg, EVT_ID, TXN_amount, QST_name, etc) and values are their values
	 * @param boolean $bydb         a flag for setting if the class is instantiated
	 *                              by the corresponding db model or not.
	 * @param string  $timezone     indicate what timezone you want any datetime fields
	 *                              to be in when instantiating a EE_Base_Class object.
	 * @param array   $date_formats An array of date formats to set on construct where first
	 *                              value is the date_format and second value is the time
	 *                              format.
	 * @throws \EE_Error
	 * @throws \Exception
	 */
	public function __construct( array $fieldValues, $bydb, $timezone, array $date_formats ) {
		parent::__construct( $fieldValues, $bydb, $timezone, $date_formats );
	}



}
// End of file EE_Base_Class_Mock.class.php