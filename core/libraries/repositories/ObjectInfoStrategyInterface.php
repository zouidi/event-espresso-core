<?php

namespace EventEspresso\Core\Libraries\Repositories;

use EventEspresso\core\interfaces\EEI_Collection;


if ( ! defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}

interface ObjectInfoStrategyInterface {

	/**
	 * @param EEI_Collection $object_collection
	 */
	function setCollection( EEI_Collection $object_collection );



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



	/**
	 * @param mixed $array_of_key_value_pairings
	 * @return void
	 */
	function setCurrentByInfo( $array_of_key_value_pairings );

}
// End of file ObjectInfoStrategyInterface.php
// Location: /ObjectInfoStrategyInterface.php