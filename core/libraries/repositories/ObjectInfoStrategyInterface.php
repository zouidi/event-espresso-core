<?php

namespace EventEspresso\Core\Libraries\Repositories;


if ( ! defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}
abstract class ObjectInfoStrategy {

	/**
	 * @param ObjectRepository $object_repository
	 */
	abstract function setRepository( ObjectRepository $object_repository );



	/**
	 * @param $object
	 * @param $info
	 * @return bool
	 */
	abstract function setObjectInfo( $object, $info );



	/**
	 * @param $info
	 * @return null|object
	 */
	abstract function getObjectByInfo( $info );

}
// End of file ObjectInfoStrategyInterface.php
// Location: /ObjectInfoStrategyInterface.php