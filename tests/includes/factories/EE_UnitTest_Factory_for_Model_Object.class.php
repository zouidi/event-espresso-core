<?php
if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class EE_UnitTest_Factory_for_Model_Object
 *
 * Description
 *
 * @package               Event Espresso
 * @subpackage            core
 * @author                Brent Christensen
 * @since                 $VID:$
 *
 */
abstract class EE_UnitTest_Factory_for_Model_Object extends WP_UnitTest_Factory_For_Thing {

	/**
	 * the full classname for the EEM_ model class (ie: EEM_Event)
	 * @var string
	 */
	protected $_model_name = '';

	/**
	 * the full classname for the EE_ class (ie: EE_Event)
	 * @var string
	 */
	protected $_object_class = '';

	/**
	 * @var string
	 */
	protected $_timezone = '';

	/**
	 * @var string
	 */
	protected $_date_time_formats = array();

	/**
	 * array of default object properties for the primary object being instantiated where keys are field names
	 *
	 * @var array
	 */
	protected $_default_properties = null;

	/**
	 * array of default object properties for related objects where keys are the model names
	 *
	 * @var array
	 */
	protected $_default_relations = null;

	/**
	 * array of object properties for any possibly related model objects required for the tests,
	 * AFTER incoming custom arguments have been merged with the defaults above.
	 * Keys are either the field names for the object properties or the model names of related objects.
	 *
	 * @var array
	 */
	protected $_related_model_object_properties = array();

	/**
	 * @var EE_UnitTest_Factory
	 */
	protected $_factory = null;

	/**
	 * EEM model object for the primary object being instantiated
	 *
	 * @var EEM_Base
	 */
	protected $_model = null;



	/**
	 * _set_default_properties_and_relations
	 *
	 * ALL child classes are required to set their _default_properties and _default_relations
	 *
	 * @access protected
	 * @return void
	 */
	abstract protected function _set_default_properties_and_relations();


	/**
	 * @param EE_UnitTest_Factory $factory
	 * @param array | null        $properties_and_relations see: _set_properties_and_relations()
	 */
	public function __construct( EE_UnitTest_Factory $factory, $properties_and_relations ) {
		$this->_model 	= EE_Registry::instance()->load_model( $this->model_name() );
		$this->_factory = $factory;
		parent::__construct( $factory );
		$this->set_properties_and_relations( $properties_and_relations );
	}



	/**
	 * @param string $model_object_name
	 */
	protected function set_model_object_name( $model_object_name ) {
		$model_object_name = $this->_prep_model_or_class_name( $model_object_name );
		$this->set_model_name( $model_object_name );
		$this->set_object_class( $model_object_name );
	}



	/**
	 * _prep_model_or_class_name
	 *
	 * convert underscore to spaces, capitalize words, then convert spaces back to underscore
	 * and remove any asterisks used solely for preventing array keys from being overwritten
	 *
	 * @param string $model_name
	 * @return string.
	 */
	public function _prep_model_or_class_name( $model_name ) {
		return str_replace( ' ', '_', ucwords( str_replace( '_', ' ', trim( $model_name, '*' ) ) ) );
	}



	/**
	 * _prefix_model_name
	 *
	 * ensures model name starts with "EEM_"
	 *
	 * @param string $model_name
	 * @return string.
	 */
	public function _prefix_model_name( $model_name ) {
		return strpos( $model_name, 'EEM_' ) === 0 ? $model_name : 'EEM_' . $model_name;
	}



	/**
	 * _prefix_class_name
	 *
	 * ensures class name starts with "EE_"
	 *
	 * @param string $class_name
	 * @return string.
	 */
	public function _prefix_class_name( $class_name ) {
		return $class_name = strpos( $class_name, 'EE_' ) === 0 ? $class_name : 'EE_' . $class_name;
	}



	/**
	 * @param string $model_name
	 */
	protected function set_model_name( $model_name ) {
		$this->_model_name = $this->_prefix_model_name( $model_name );
	}



	/**
	 * @return string
	 * @throws \Exception
	 */
	protected function model_name() {
		if ( empty( $this->_model_name ) ) {
			throw new Exception(
				sprintf( 'The Model Name was not set on the %s EE_UnitTest_Factory' ),
				get_called_class( $this )
			);
		}
		return $this->_model_name;
	}



	/**
	 * @param string $class_name
	 */
	protected function set_object_class( $class_name ) {
		$this->_object_class = $this->_prefix_class_name( $class_name );;
	}



	/**
	 * @return string
	 * @throws \Exception
	 */
	protected function object_class() {
		if ( empty( $this->_object_class ) ) {
			throw new Exception(
				sprintf( 'The Class Name was not set on the %s EE_UnitTest_Factory' ),
				get_called_class( $this )
			);
		}
		return $this->_object_class;
	}



	/**
	 * @param string $timezone
	 */
	public function set_timezone( $timezone ) {
		$this->_timezone = $timezone;
	}



	/**
	 * @return string
	 */
	public function timezone() {
		return $this->_timezone;
	}



	/**
	 * @param string $date_time_formats
	 */
	public function set_date_time_formats( $date_time_formats ) {
		$this->_date_time_formats = $date_time_formats;
	}



	/**
	 * @return string
	 */
	public function date_time_formats() {
		return $this->_date_time_formats;
	}



	/**
	 * @return array
	 */
	public function default_properties() {
		return $this->_default_properties;
	}



	/**
	 * @return array
	 */
	public function default_relations() {
		return $this->_default_relations;
	}



	/**
	 * _set_properties_and_relations
	 *
	 * @param array | null $properties_and_relations
	 *          pass null (or nothing) to just get the default properties with NO relations
	 *          or pass empty array for default properties AND relations
	 *          or non-empty array to override default properties and manually set related objects and their properties,
	 * @return void
	 */
	public function set_properties_and_relations( $properties_and_relations ) {
		// If incoming $properties_and_relations is an empty array,
		// then it means we want ALL default properties AND relations,
		// so we merge the default related object properties with the default relations.
		// Otherwise, merge incoming properties with the default properties,
		// and get all relation settings from the incoming $properties_and_relations,
		// which could be an array which actually does define all properties and relations,
		// or NOTHING (null)... which means we'll just end up with the default properties
		$merged_properties_and_relations = empty( $properties_and_relations ) && is_array( $properties_and_relations )
			? array_merge( $this->_default_properties, $this->_default_relations )
			: array_merge( $this->_default_properties, (array)$properties_and_relations );

		// default args for creating model objects
		// NOTE: WP_UnitTest_Factory_For_Thing can only handle scalar property values,
		// so we need to remove all of the related object property arrays,
		// array_diff_key() works great for this purpose
		$this->default_generation_definitions = array_diff_key(
			$merged_properties_and_relations,
			$this->_model->relation_settings()
		);

		// and now we do the exact opposite of above
		// and generate an array with JUST the related object properties
		// and NONE of the root level fields and values
		// array_intersect_key() is what we want this time
		$this->_related_model_object_properties = array_intersect_key(
			$merged_properties_and_relations,
			$this->_model->relation_settings()
		);
	}



	/**
	 * used by WP factory to create model object
	 *
	 * @since 4.3.0
	 * @param array $model_fields_and_values Incoming field values to set on the new model object
	 * @return \EE_Base_Class
	 */
	public function create_object( $model_fields_and_values = array() ) {
		$model_fields_and_values = ! empty( $model_fields_and_values )
			? $model_fields_and_values
			: $this->default_generation_definitions;
		return $this->create_object_and_relations( $model_fields_and_values, $this->_related_model_object_properties );
	}



	/**
	 * used to generate model object and all related objects as defined in $related_model_objects array
	 *
	 * @param array $model_fields_and_values Incoming field values to set on the new model object
	 * @param array $related_model_objects array of object properties for any possibly related model objects required,
	 *                                     Keys are either the field names for the object properties
	 *                                     or the model names of related objects and their properties.
	 * @return \EE_Base_Class
	 */
	public function create_object_and_relations( $model_fields_and_values, $related_model_objects ) {
		$object = null;
		$object_class = $this->object_class();
		//timezone?
		if ( isset( $args[ 'timezone' ] ) ) {
			$this->set_timezone( $args[ 'timezone' ] );
			unset( $args[ 'timezone' ] );
		}
		//date formats?
		if ( isset( $args[ 'formats' ] ) && is_array( $args[ 'formats' ] ) ) {
			$this->set_date_time_formats( $args[ 'formats' ] );
			unset( $args[ 'formats' ] );
		}
		$primary_key = $this->find_primary_key_in_model_fields( $this->_model, $model_fields_and_values );
		if ( ! empty( $primary_key ) && ! empty( $model_fields_and_values[ $primary_key ] ) ) {
			$object = $this->get_object_by_id( $model_fields_and_values[ $primary_key ] );
		}
		if ( ! $object instanceof $object_class ) {
			$object = call_user_func_array(
				array( $object_class, 'new_instance' ),
				array( $model_fields_and_values, $this->_timezone, $this->_date_time_formats )
			);
			$object->save();
		}
		return $this->generate_related_objects( $object, $related_model_objects );
	}



	/**
	 * find_primary_key_in_model_fields
	 *
	 * @param EEM_Base $model
	 * @param array $model_fields_and_values
	 * @return string
	 */
	public function find_primary_key_in_model_fields( EEM_Base $model, $model_fields_and_values ) {
		foreach ( $model_fields_and_values as $field => $values ) {
			if ( $model->has_field( $field ) ) {
				if ( $field === $model->primary_key_name() ) {
					return $field;
				}
			}
		}
		return '';
	}



	/**
	 * generate_related_objects
	 *
	 * @param EE_Base_Class $object
	 * @param array $properties_and_relations
	 * @return \EE_Base_Class
	 * @throws \Exception
	 */
	public function generate_related_objects( EE_Base_Class $object, $properties_and_relations ) {
		foreach ( $properties_and_relations as $model_name => $model_properties_and_relations ) {
			$model_fields 	= array();
			$related_models = array();
			$model_name = $this->_prep_model_or_class_name( $model_name );
			$model = EE_Registry::instance()->load_model( $model_name );
			foreach ( $model_properties_and_relations as $field_or_model => $value_or_properties ) {
				if ( $model->has_field( $field_or_model ) ) {
					$model_fields[ $field_or_model ] = $value_or_properties;
				} else {
					$related_models[ $field_or_model ] = $value_or_properties;
				}
			}
			$related_object = $this->_generate_related_object( $model_name, $model_fields, $related_models );
			$object->_add_relation_to( $related_object, $model_name );
		}
		return $object;
	}



	/**
	 * generate_related_objects
	 *
	 * @param string $model_name
	 * @param array $model_fields
	 * @param array $related_models
	 * @return \EE_Base_Class
	 * @throws \Exception
	 */
	protected function _generate_related_object( $model_name, $model_fields, $related_models ) {
		$factory_model = $this->_get_model_factory( $model_name );
		$related_object = $factory_model->create_object_and_relations( $model_fields, $related_models );
		$related_object_class = $this->_prefix_class_name( $model_name );
		if ( ! $related_object instanceof $related_object_class ) {
			throw new Exception(
				sprintf( 'An invalid "%1$s" object was generated.', $related_object_class )

			);
		}
		return $related_object;
	}



	/**
	 * gets
	 *
	 * @param string $model_name
	 * @return EE_UnitTest_Factory_for_Model_Object
	 * @throws \Exception
	 */
	protected function _get_model_factory( $model_name ) {
		$factory_model = strtolower( $model_name );
		if ( ! property_exists( $this->_factory, $factory_model ) ) {
			throw new Exception(
				sprintf(
					'Unable to construct object because "%1$s" is not a property on the EE_UnitTest_Factory class.',
					$factory_model
				)
			);
		}
		if ( ! $this->_factory->$factory_model instanceof EE_UnitTest_Factory_for_Model_Object ) {
			throw new Exception(
				sprintf(
					'"EE_UnitTest_Factory->%1$s" is not a valid EE_UnitTest_Factory_for_Model_Object class.',
					$factory_model
				)
			);
		}
		return $this->_factory->$factory_model;
	}



	/**
	 * Update datetime object for given datetime
	 *
	 * @since 4.3.0
	 *
	 * @param int $ID 	ID for the model object to update
	 * @param array 	$cols_n_data columns and values to change/update
	 *
	 * @return EE_Base_Class.
	 */
	public function update_object( $ID, $cols_n_data ) {
		//all the stuff for updating an datetime.
		$object = $this->get_object_by_id( $ID );
		if ( ! $object instanceof EE_Base_Class ) {
			return null;
		}
		foreach ( $cols_n_data as $key => $val ) {
			$object->set( $key, $val );
		}
		return $object->save() ? $object : null;
	}



	/**
	 * return the model object object for a given ID
	 *
	 * @since 4.3.0
	 *
	 * @param int $ID the ID for the model object to attempt to retrieve
	 *
	 * @return EE_Base_Class
	 */
	public function get_object_by_id( $ID ) {
		return $this->_model->get_one_by_ID( $ID );
	}



}
// End of file EE_UnitTest_Factory_for_Model_Object.class.php
// Location: /EE_UnitTest_Factory_for_Model_Object.class.php