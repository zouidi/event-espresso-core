<?php

namespace EventEspresso\Core\Libraries\Repositories;


if ( ! defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}

interface ObjectInfoStrategyInterface {

	/**
	 * @param ObjectRepository $object_repository
	 */
	function setRepository( ObjectRepository $object_repository );



	/**
	 * @param $object
	 * @param $info
	 * @return bool
	 */
	function setObjectInfo( $object, $info );



	/**
	 * @param mixed $array_of_key_value_pairings
	 * @return null|object
	 */
	function getObjectByInfo( $array_of_key_value_pairings );

}
// End of file ObjectInfoStrategyInterface.php
// Location: /ObjectInfoStrategyInterface.php