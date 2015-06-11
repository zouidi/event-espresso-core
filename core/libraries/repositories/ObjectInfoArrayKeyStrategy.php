<?php

namespace EventEspresso\Core\Libraries\Repositories;

if ( ! defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}
/**
 * Class ObjectInfoArrayKeyStrategy
 *
 * setObjectInfo
 * Sets an array for the info associated with an object in the SplObjectStorage
 * if no $info array is supplied, then the spl_object_hash() is used with a key of "ID"
 *
 * getObjectByInfo
 * finds and returns an object in the repository based on passed key value pairings
 * where all key value pairs must match an object in the repository, else null is returned
 * ie: array( 'ID' => 7 ) or array( 'ticket' => 7, 'event' => 15 )
 *
 * @package 			Event Espresso
 * @subpackage 	core
 * @author 				Brent Christensen
 * @since 				$VID:$
 *
 */
 class ObjectInfoArrayKeyStrategy implements ObjectInfoStrategyInterface {


	 /**
	  * @type ObjectRepository $object_repository
	  */
	 protected $object_repository;



	 /**
	  * @param ObjectRepository $object_repository
	  */
	 public function setRepository( ObjectRepository $object_repository ) {
		 $this->object_repository = $object_repository;
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
		 $info = is_array( $info ) ? $info : array( 'ID' => spl_object_hash( $object ) );
		 $this->object_repository->rewind();
		 while ( $this->object_repository->valid() ) {
			 if ( $object == $this->object_repository->current() ) {
				 $this->object_repository->setInfo( $info );
				 $this->object_repository->rewind();
				 return;
			 }
			 $this->object_repository->next();
		 }
	 }



	 /**
	  * getObjectByInfo
	  *
	  * finds and returns an object in the repository based on the info that was set using addObject()
	  *
	  * @access protected
	  * @param array $array_of_key_value_pairings
	  * @return null|object
	  */
	 public function getObjectByInfo( $array_of_key_value_pairings ) {
		 $this->object_repository->rewind();
		 while ( $this->object_repository->valid() ) {
			 $currentInfo = $this->object_repository->getInfo();
			 if ( ! is_array( $currentInfo ) ) {
				 $this->object_repository->next();
				 continue;
			 }
			 $found = true;
			 foreach ( $array_of_key_value_pairings as $key => $value ) {
				 if ( ! isset( $currentInfo[ $key ] ) || $currentInfo[ $key ] !== $value ) {
					 $found = false;
					 break;
				 }
			 }
			 if ( ! $found ) {
				 continue;
			 }
			 $object = $this->object_repository->current();
			 $this->object_repository->rewind();
			 return $object;
		 }
		 return null;
	 }


}
// End of file ObjectInfoArrayKeyStrategy.php
// Location: /core/libraries/repositories/ObjectInfoArrayKeyStrategy.php