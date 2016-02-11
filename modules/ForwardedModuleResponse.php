<?php
namespace EventEspresso\modules;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class ForwardedModuleResponse
 *
 * Simple container to be used as a DTO (Data Transfer Object)
 * Uses an internal array for holding data added/retrieved by get() and set()
 * Uses a corresponding dataMap array for defining the keys to be used in the data array,
 * as well as
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class ForwardedModuleResponse {

	/**
	 * @var int $status
	 */
	protected $status = \EED_Module::no_forward;

	/**
	 * @var array $data
	 */
	protected $data = array();

	/**
	 * keys = name of data parameter
	 * values = array of validators applied during data setting,
	 * where validator values are the validation callbacks OR a specific value to be tested against,
	 * and the keys can be used to define specific validation types like 'instanceof'
	 * but are usually empty for primitive data types that use simple validation methods
	 *      for example:
	 *          array(
	 *              'an-integer'  => array( 'absint' ), // no key
	 *              'some-string' => array( 'sanitize_text_field' ), // no key
	 *              'my-object'   => array( 'instanceof' => 'class_name' ),
	 *          )
 *
	 * @var array $dataMap
	 */
	protected $dataMap = array();



	/**
	 * ForwardedModuleResponse constructor.
	 */
	public function __construct() {
		if ( empty( $this->dataMap ) ) {
			throw new InvalidModuleResponseDataException(
				__( 'The data map can not be empty.', 'event_espresso' )
			);
		}
	}



	/**
	 * INTENDED TO BE OVERRIDDEN FOR MORE CUSTOMIZED MODULE RESPONSES
	 * @return bool
	 */
	public function valid() {
		return ! empty( $this->data ) ? true : false;
	}



	/**
	 * @param array $data_map
	 */
	public function setDataMap( array $data_map ) {
		$this->dataMap = $data_map;
	}



	/**
	 * @param int $status
	 * @throws \EE_Error
	 */
	public function setStatus( $status = \EED_Module::no_forward ) {
		$forward_class_constants = \EED_Module::get_forward_class_constants();
		if ( ! in_array( $status, $forward_class_constants, true ) ) {
			throw new InvalidModuleResponseDataException(
				sprintf(
					__( '"%1$s" is an invalid module forwarding status.', 'event_espresso' ),
					$status
				)
			);
		}
		$this->status = $status;
	}



	/**
	 * @return int
	 */
	public function status() {
		return $this->status;
	}



	/**
	 * whether or not the data parameter key exists in the dataMap
	 * does NOT report whether a value has been set for that data parameter
	 *
	 * @param  string $key
	 * @return bool
	 */
	public function has( $key ) {
		return isset( $this->dataMap[ $key ] ) ? true : false;
	}



	/**
	 * @param  string $key
	 * @return mixed
	 */
	public function get( $key ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
	}



	/**
	 * @param  string $key
	 * @param  null   $value
	 */
	public function set( $key, $value ) {
		$this->data[ $key ] = $this->applySetterValidation( $key, $value );
	}



	/**
	 * @return array
	 */
	public function getAll() {
		return $this->data;
	}



	/**
	 * @param array $data
	 */
	public function setAll( array $data ) {
		foreach ( $data as $key => $value ) {
			$this->set( $key, $value );
		}
	}



	/**
	 * @param string $key
	 * @param mixed  $value
	 * @return mixed|null
	 * @throws \EE_Error
	 */
	protected function applySetterValidation( $key, $value ) {
		if ( ! $this->has( $key ) ) {
			throw new InvalidModuleResponseDataException(
				sprintf(
					__( '"%1$s" is not a valid data parameter for the "%2$s" class', 'event_espresso' ),
					$key,
					__CLASS__
				)
			);
		}
		foreach ( $this->dataMap[ $key ] as $type => $validator ) {
			switch ( (string)$type ) {

				case 'instanceof' :
					if ( ! $value instanceof $validator ) {
						throw new InvalidModuleResponseDataException(
							sprintf(
								__( '"%1$s" is not a valid instance of "%2$s"', 'event_espresso' ),
								$key,
								$validator
							)
						);
					}
					break;

				default :
					$value = $validator( $value );

			}
		}
		return $value;
	}




}
// End of file ForwardedModuleResponse.php
// Location: /ForwardedModuleResponse.php