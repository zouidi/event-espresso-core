<?php

namespace EventEspresso\Core\Libraries\Repositories;

use core\interfaces\EEI_Collection;

if ( ! defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}
/**
 * Class ObjectInfoSingleKeyStrategy
 *
 * setObjectInfo
 * Sets a single datum for the info associated with an object in the SplObjectStorage
 * if no $info is supplied, then the spl_object_hash() is used
 *
 * getObjectByInfo
 * finds and returns an object in the repository based on the info that was set using addObject()
 *
 * @package 			Event Espresso
 * @subpackage 	core
 * @author 				Brent Christensen
 * @since 				$VID:$
 *
 */
class ObjectInfoSingleKeyStrategy implements ObjectInfoStrategyInterface {


	/**
	* @type ObjectRepository $object_collection
	*/
	protected $object_collection;



	/**
	* @param EEI_Collection $object_collection
	*/
	public function setCollection( EEI_Collection $object_collection ) {
		$this->object_collection = $object_collection;
	}



	/**
	* setObjectInfo
	*
	* Sets the data associated with an object in the SplObjectStorage
	* if no $info is supplied, then the spl_object_hash() is used
	*
	* @access protected
	* @param object $object
	* @param mixed  $info
	* @return bool
	*/
	public function setObjectInfo( $object, $info = null ) {
		$info = ! empty( $info ) ? $info : spl_object_hash( $object );
		$this->object_collection->rewind();
		while ( $this->object_collection->valid() ) {
			if ( $object == $this->object_collection->current() ) {
				$this->object_collection->setInfo( $info );
				$this->object_collection->rewind();
				return true;
			}
			$this->object_collection->next();
		}
		return false;
	}



	/**
	* getObjectByInfo
	*
	* finds and returns an object in the repository based on the info that was set using addObject()
	*
	* @access protected
	* @param mixed $info
	* @return null | object
	*/
	public function getObjectByInfo( $info ) {
		$this->object_collection->rewind();
		while ( $this->object_collection->valid() ) {
			if ( $info === $this->object_collection->getInfo() ) {
				$object = $this->object_collection->current();
				$this->object_collection->rewind();
				return $object;
			}
			$this->object_collection->next();
		}
		return null;
	}



	/**
	 * setCurrentByInfo
	 *
	 * advances pointer to the object whose info matches that which was provided
	 *
	 * @access public
	 * @param $info
	 * @return void
	 */
	public function setCurrentByInfo( $info ) {
		$this->object_collection->rewind();
		while ( $this->object_collection->valid() ) {
			if ( $info === $this->object_collection->getInfo() ) {
				break;
			}
			$this->object_collection->next();
		}
	}



}
// End of file ObjectInfoSingleKeyStrategy.php
// Location: /ObjectInfoSingleKeyStrategy.php