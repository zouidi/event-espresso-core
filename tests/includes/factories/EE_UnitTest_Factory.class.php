<?php
/**
 * This is a factory for more quickly setting up objects/items needed for EE Unit Tests.
 *
 * Examples of things we might setup using the factory are events, registrations, tickets etc.
 *
 * @since 		4.3.0
 * @package 	Event Espresso
 * @subpackage 	tests
 */
class EE_UnitTest_Factory extends WP_UnitTest_Factory {


	/**
	 * EE_Test_Factories extend the EE_UnitTest_Factory_for_Model_Object class,
	 * which extends the WP_UnitTest_Factory_for_Thing abstract class
	 *
	 * @see wp tests/includes/EE_UnitTest_Factory.class.php
	 * @var EE_UnitTest_Factory_for_Model_Object[]
	 */
	public $repo = array();





	public function __construct() {
		parent::__construct();

		// simple factories
		// setup any properties containing various test factory objects.

		$factories = array(
			'event',
			'venue',
			'datetime',
			'ticket',
			'price',
			'price_type',
			'registration',
			'transaction',
			'attendee',
			'status',
			'payment',
			'state',
			'country',
			'wp_user',
			'line_item',
		);
		foreach ( $factories as $factory ) {
			$class = 'EE_UnitTest_Factory_For_' . str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $factory ) ) );
			if ( class_exists( $class )) {
				$this->repo[ $factory ]              = new $class( $this );
				$this->repo[ $factory . '_chained' ] = new $class( $this, array() );
			}
		}

	}



	/**
	 * get_factory_for_model
	 *
	 * @param string $model_name
	 * @return \EE_UnitTest_Factory_for_Model_Object
	 */
	public function get_factory_for_model( $model_name ) {
		$model_name = strtolower( rtrim( $model_name, '*' ) );
		if ( isset( $this->repo[ $model_name ] ) ) {
			return $this->repo[ $model_name ];
		}
		return null;
	}




	/**
	 * construct_generic_factory_for_model
	 *
	 * @param string $model_name
	 * @return \EE_UnitTest_Factory_for_Model_Object
	 */
	public function construct_generic_factory_for_model( $model_name ) {
		$model_name = strtolower( rtrim( $model_name, '*' ) );
		$this->repo[ $model_name ] = new EE_UnitTest_Factory_For_Generic_Model( $model_name, $this );
		$this->repo[ $model_name . '_chained' ] = new EE_UnitTest_Factory_For_Generic_Model( $model_name, $this, array() );
		return $this->get_factory_for_model( $model_name );
	}



	/**
	 * @param string $factory
	 * @return \EE_UnitTest_Factory_for_Model_Object
	 */
	public function __get( $factory ) {
		if ( isset( $this->repo[ $factory ] ) ) {
			return $this->repo[ $factory ];
		}
		return null;
	}



}


