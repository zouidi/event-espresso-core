<?php

namespace EventEspresso\Core\Libraries\Repositories;

if ( ! defined('EVENT_ESPRESSO_VERSION')) {
	exit('No direct script access allowed');
}
/**
 * Class ObjectInfoSingleKeyStrategy
 *
 * Description
 *
 * @package 			Event Espresso
 * @subpackage 	core
 * @author 				Brent Christensen
 * @since 				$VID:$
 *
 */
 class ObjectInfoSingleKeyStrategy implements ObjectInfoStrategyInterface {


	 /**
	  * @type EE_Object_Repository $object_repository
	  */
	 protected $object_repository;



	 /**
	  * @param EE_Object_Repository $object_repository
	  */
	 public function setRepository( EE_Object_Repository $object_repository ) {
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
		 $info = ! empty( $info ) ? $info : spl_object_hash( $object );
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
	  * @param mixed
	  * @return null | object
	  */
	 public function getObjectByInfo( $info ) {
		 $this->object_repository->rewind();
		 while ( $this->object_repository->valid() ) {
			 if ( $info === $this->object_repository->getInfo() ) {
				 $object = $this->object_repository->current();
				 $this->object_repository->rewind();
				 return $object;
			 }
			 $this->object_repository->next();
		 }
		 return null;
	 }

 }



// End of file ObjectInfoSingleKeyStrategy.php
// Location: /ObjectInfoSingleKeyStrategy.php