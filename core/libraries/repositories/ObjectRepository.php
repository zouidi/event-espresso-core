<?php
namespace EventEspresso\Core\Libraries\Repositories;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class ObjectRepository
 *
 * abstract storage entity for unique objects
 * extends SplObjectStorage so therefore implements the
 * Countable, Iterator, Serializable, and ArrayAccess interfaces
 *
 * @package               Event Espresso
 * @subpackage            core
 * @author                Brent Christensen
 * @since                 4.6.31
 *
 */
class ObjectRepository extends \SplObjectStorage {


	/**
	 * how to set, get, and utilize object info when retrieving objects
	 *
	 * @type ObjectInfoStrategyInterface $object_info_strategy
	 */
	protected $object_info_strategy;



	/**
	 * @param ObjectInfoStrategyInterface $object_info_strategy
	 */
	function __construct( ObjectInfoStrategyInterface $object_info_strategy = null ) {
		// so that object info strategy can access objects, rewind, etc
		$object_info_strategy->setRepository( $this );
		$this->object_info_strategy = $object_info_strategy instanceof ObjectInfoStrategyInterface
			? $object_info_strategy : new ObjectInfoSingleKeyStrategy();
	}



	/**
	 * addObject
	 *
	 * attaches an object to the SplObjectStorage
	 * and sets any supplied data associated with the current iterator entry
	 * by calling ObjectRepository::setObjectInfo()
	 *
	 * @access protected
	 * @param object $object
	 * @param mixed  $info
	 * @return bool
	 */
	protected function addObject( $object, $info = null ) {
		$this->attach( $object );
		$this->setObjectInfo( $object, $info );
		return $this->contains( $object );
	}



	/**
	 * hasObject
	 *
	 * returns TRUE or FALSE depending on whether the supplied object is within the repository
	 *
	 * @access protected
	 * @param object $object
	 * @return bool
	 */
	protected function hasObject( $object ) {
		return $this->contains( $object );
	}



	/**
	 * setObjectInfo
	 *
	 * Sets the data associated with an object in the SplObjectStorage
	 * according to the ObjectInfoStrategy
	 *
	 * @access protected
	 * @param object $object
	 * @param mixed  $info
	 * @return bool
	 */
	public function setObjectInfo( $object, $info = null ) {
		return call_user_func_array( array( $this->object_info_strategy, 'setObjectInfo' ), array( $object, $info ) );
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
		return call_user_func_array( array( $this->object_info_strategy, 'getObjectByInfo' ), array( $info ) );
	}



	/**
	 * persistObject
	 *
	 * primarily used for saving EE_Base_Class classes to the database,
	 * but can be supplied with a "persistence callback" that can be used for classes that are not instances of EE_Base_Class,
	 * or for providing alternate ways to persist an object such as session caching, etc...
	 * an array of arguments can also be supplied that will be passed along to the object's persistence method
	 *
	 * @access protected
	 * @param object $object
	 * @param string $persistence_callback name of method found on object that can be used for persisting the object
	 * @param array  $persistence_arguments arrays of arguments that will be passed to the object's persistence method
	 * @return bool | int
	 * @throws \EE_Error
	 */
	protected function persistObject( $object, $persistence_callback = '', $persistence_arguments = array() ) {
		if ( $this->contains( $object ) ) {
			$this->rewind();
			while ( $this->valid() ) {
				if ( $object === $this->current() ) {
					$success = false;
					if ( method_exists( $object, $persistence_callback ) ) {
						$success = call_user_func_array( array( $object, $persistence_callback ),
														 $persistence_arguments );
					} else if ( $object instanceof \EE_Base_Class ) {
						$success = $object->save( $persistence_arguments );
					}
					$this->rewind();
					return $success;
				}
				$this->next();
			}
		}
		return false;
	}



	/**
	 * removeObject
	 *
	 * detaches an object from the SplObjectStorage
	 *
	 * @access protected
	 * @param $object
	 * @return void
	 */
	protected function removeObject( $object ) {
		$this->detach( $object );
	}



}
// End of file EE_Object_Repository.php
// Location: /core/libraries/repositories/EE_Object_Repository.php