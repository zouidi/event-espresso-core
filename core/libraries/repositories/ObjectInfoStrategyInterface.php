<?php

namespace EventEspresso\Core\Libraries\Repositories;


if ( ! defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}

interface ObjectInfoStrategyInterface {

	/**
	 * @param EE_Object_Repository $object_repository
	 */
	function setRepository( EE_Object_Repository $object_repository );



	/**
	 * @param $object
	 * @param $info
	 * @return bool
	 */
	function setObjectInfo( $object, $info );



	/**
	 * @param $info
	 * @return null|object
	 */
	function getObjectByInfo( $info );

}
// End of file ObjectInfoStrategyInterface.php
// Location: /ObjectInfoStrategyInterface.php