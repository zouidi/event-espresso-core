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
	 * the model object classname with NO prefix (ie: Event)
	 *
*@var string
	 */
	protected $_factory_type = '';

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
	 * array of object properties and relations for overriding default values
	 *
	 * @var array
	 */
	protected $_custom_properties_and_relations = null;

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
	 * whether generated objects should be saved to the db
	 *
	 * @var bool
	 */
	protected $_save_to_db = true;

	/**
	 * primary key field for the current object being generated
	 *
	 * @var string
	 */
	protected $_primary_key = '';

	/**
	 * temporary generated objects storage
	 *
	 * @var array
	 */
	protected $_object_cache = array();

	/**
	 * basically a counter for the number of objects being created for this factory
	 * for example: if an objects relation settings included references to:
	 *    'Ticket' => array(),
	 *    'Ticket*' => array(),
	 *    'Ticket**' => array(),
	 * then this counts the number of related items of the same class
	 *
	 * @var int
	 */
	protected $_cache_key = 1;


	/**
	 * _set_default_properties_and_relations
	 *
	 * ALL child classes are required to set their _default_properties and _default_relations
	 *
	 * @param string $called_class in order to avoid recursive application of relations,
	 *                             we need to know which class is making this request
	 * @access protected
	 */
	abstract protected function _set_default_properties_and_relations( $called_class );


	/**
	 * @param EE_UnitTest_Factory $factory
	 * @param array | null 		  $custom_properties_and_relations
	 *          pass null (or nothing) to just get the default properties with NO relations
	 *          or pass empty array for default properties AND relations
	 *          or non-empty array to override default properties and manually set related objects and their properties,
	 */
	public function __construct( EE_UnitTest_Factory $factory, $custom_properties_and_relations ) {
		$this->_model 		= EE_Registry::instance()->load_model( $this->model_name() );
		$this->_factory 	= $factory;
		$this->set_save_to_db( true );
		$this->_custom_properties_and_relations = $custom_properties_and_relations;
		parent::__construct( $factory );
	}



	/**
	 * @param string $model_object_name
	 */
	protected function set_model_object_name( $model_object_name ) {
		$model_object_name = $this->_prep_model_or_class_name( $model_object_name );
		$this->set_factory_type( $model_object_name );
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
	protected function _prep_model_or_class_name( $model_name ) {
		return str_replace(
			array( ' ', 'Wp_User' ), // find spaces or malformed class names
			array( '_', 'WP_User' ), // replace with underscores or correct class names
			ucwords(
			   str_replace(
				   '_', // find underscores
				   ' ', // replace with spaces so that ucwords() will work
				   trim( $model_name, '*' ) // remove asterisks from model name
			   )
		   )
		);
	}



	/**
	 * _prefix_model_name
	 *
	 * ensures model name starts with "EEM_"
	 *
	 * @param string $model_name
	 * @return string.
	 */
	protected function _prefix_model_name( $model_name ) {
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
	protected function _prefix_class_name( $class_name ) {
		return $class_name = strpos( $class_name, 'EE_' ) === 0 ? $class_name : 'EE_' . $class_name;
	}



	/**
	 * @return string
	 */
	public function factory_type() {
		return $this->_factory_type;
	}



	/**
	 * @param string $factory_type
	 */
	public function set_factory_type( $factory_type ) {
		$this->_factory_type = $factory_type;
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
		$this->_object_class = $this->_prefix_class_name( $class_name );
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
	 * @param string $called_class in order to avoid recursive application of relations,
	 *                             we need to know which class is making this request
	 * @return array
	 */
	public function default_properties( $called_class ) {
		if ( empty( $this->_default_properties ) ) {
			$this->_set_default_properties_and_relations( $called_class );
		}
		return $this->_default_properties;
	}



	/**
	 * @return array
	 */
	public function default_relations() {
		if ( empty( $this->_default_relations ) ) {
			$this->_set_default_properties_and_relations( $this->factory_type() );
		}
		return $this->_default_relations;
	}



	/**
	 * @return array
	 */
	public function default_generation_definitions() {
		if ( empty( $this->default_generation_definitions ) ) {
			$this->_set_default_properties_and_relations( $this->factory_type() );
			$this->default_generation_definitions = $this->_default_properties;
		}
		return $this->default_generation_definitions;
	}



	/**
	 * _resolve_default_relations
	 *
	 * @param string $called_class in order to avoid recursive application of relations,
	 *                             we need to know which class is making this request
	 * @return array
	 */
	protected function _resolve_default_relations( $called_class ) {
		if ( ! is_null( $this->_custom_properties_and_relations ) ) {
			foreach ( $this->_default_relations as $relation => $properties ) {
				if ( $relation !== $called_class && empty( $properties ) ) {
					$factory_model = $this->_get_model_factory( $relation );
					$default_properties = $factory_model->default_properties( $this->factory_type() );
					$this->_default_relations[ $relation ] = $default_properties;
				}
			}
			if ( ! is_null( $this->_default_relations )) {
				$this->_default_properties = array_merge( $this->_default_properties, $this->_default_relations );
			}
		}
	}



	/**
	 * _set_save_to_db
	 *
	 * @param bool         $save_to_db
	 */
	public function set_save_to_db( $save_to_db = true ) {
		// don't save these models
		$model_save = array(
			'EE_Country'  => false,
			'EE_Currency' => false,
			'EE_Status'   => false,
		);
		$save_to_db = isset( $model_save[ $this->object_class() ] ) ? $model_save[ $this->object_class() ] : $save_to_db;
		$this->_save_to_db = filter_var( $save_to_db, FILTER_VALIDATE_BOOLEAN );
	}



	/**
	 * _set_properties_and_relations
	 *
	 * @param array | null $custom_properties_and_relations
	 *          pass null (or nothing) to just get the default properties with NO relations
	 *          or pass empty array for default properties AND relations
	 *          or non-empty array to override default properties and manually set related objects and their properties,
	 * @param bool         $save_to_db
	 */
	public function set_properties_and_relations( $custom_properties_and_relations = null, $save_to_db = true ) {
		$this->set_save_to_db( $save_to_db );
		$this->_custom_properties_and_relations = ! is_null( $custom_properties_and_relations )
			? $custom_properties_and_relations
			: $this->_custom_properties_and_relations;

		// If incoming $properties_and_relations is an empty array,
		// then it means we want ALL default properties AND relations,
		// so we merge the default related object properties with the default relations.
		// Otherwise, merge incoming properties with the default properties,
		// and get all relation settings from the incoming $properties_and_relations,
		// which could be an array which actually does define all properties and relations,
		// or NOTHING (null)... which means we'll just end up with the default properties
		if ( empty( $this->_custom_properties_and_relations ) && is_array( $this->_custom_properties_and_relations ) ) {
			$merged_properties_and_relations = $this->_object_safe_array_merge_recursive(
				$this->default_properties( $this->factory_type() ),
				$this->default_relations()
			);
		} else {
			$merged_properties_and_relations = $this->_object_safe_array_merge_recursive(
				$this->default_properties( $this->factory_type() ),
				(array)$this->_custom_properties_and_relations
			);
		}

		// default args for creating model objects
		// NOTE: WP_UnitTest_Factory_For_Thing can only handle scalar property values,
		// so we need to remove all of the related object property arrays,
		// array_diff_key() works great for this purpose
		$this->default_generation_definitions = array_diff_key(
			$merged_properties_and_relations,
			$this->_model->relation_settings()
		);
		// BUT !!!! We also need to remove any many relations using an asterisk in the relation name
		foreach ( $this->default_generation_definitions as $key => $value ) {
			if ( strpos( $key, '*' ) !== false ) {
				unset( $this->default_generation_definitions[ $key ] );
			}
		}

		// and now we do the exact opposite of above
		// and generate an array with JUST the related object properties
		// and NONE of the root level fields and values
		// array_intersect_key() is what we want this time
		$this->_related_model_object_properties = array_intersect_key(
			$merged_properties_and_relations,
			$this->_model->relation_settings()
		);
		// BUT !!!! We also need to include any many relations using an asterisk in the relation name
		foreach ( $merged_properties_and_relations as $key => $value ) {
			if ( ! isset( $this->_related_model_object_properties[ $key ] ) && strpos( $key, '*' ) !== false ) {
				$this->_related_model_object_properties[ $key ] = $value;
			}
		}
	}



	/**
	 * recursive array merging that doesn't try to convert objects to arrays
	 * borrowed from: http://php.net/manual/en/function.array-merge-recursive.php#73843
	 *
	 *
	 * @param $array1
	 * @param $array2
	 * @return array
	 */
	protected function _object_safe_array_merge_recursive( $array1, $array2 ) {
		$arrays = func_get_args();
		$remains = $arrays;
		// We walk through each arrays and put value in the results (without
		// considering previous value).
		$result = array();
		// loop available array
		foreach ( $arrays as $array ) {
			// The first remaining array is $array. We are processing it. So
			// we remove it from remaining arrays.
			array_shift( $remains );
			// We don't care non array param, like array_merge since PHP 5.0.
			if ( is_array( $array ) ) {
				// Loop values
				foreach ( $array as $key => $value ) {
					if ( is_array( $value ) ) {
						// we gather all remaining arrays that have such key available
						$args = array();
						foreach ( $remains as $remain ) {
							if ( array_key_exists( $key, $remain ) ) {
								array_push( $args, $remain[ $key ] );
							}
						}
						if ( count( $args ) > 2 ) {
							// put the recursion
							$result[ $key ] = call_user_func_array( __FUNCTION__, $args );
						} else {
							foreach ( $value as $value_key => $value_value ) {
								$result[ $key ][ $value_key ] = $value_value;
							}
						}
					} else {
						// simply put the value
						$result[ $key ] = $value;
					}
				}
			}
		}
		return $result;
	}



	/**
	 * used by WP factory to create model object
	 *
	 * @since 4.3.0
	 * @param array $model_fields_and_values Incoming field values to set on the new model object
	 * @return \EE_Base_Class
	 */
	public function create_object( $model_fields_and_values = array() ) {
		if ( $model_fields_and_values instanceof WP_Error ) {
			echo $model_fields_and_values->get_error_message();
			echo "\n FULL WP_Error: \n";
			var_dump( $model_fields_and_values );
			return null;
		}
		if ( ! empty( $model_fields_and_values ) ) {
			$this->set_properties_and_relations( $model_fields_and_values );
		}

		$object = $this->create_object_and_relations( $this->default_generation_definitions(), $this->_related_model_object_properties );
		if ( ! $object instanceof EE_Base_Class ) {
			echo "\n\n " . __LINE__ . ") " . __METHOD__ . "() ";
			echo "\n FINAL OBJECT CLASS: " . get_class( $object );
			var_dump( $object );
		}
		if ( $this->_save_to_db ) {
			$object->save();
		}
		return $object;
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
		$model_fields_and_values = $this->_parse_timezones_formats_and_sequences_in_model_fields(
			$model_fields_and_values
		);
		$primary_key = $this->_find_primary_key_in_model_fields( $this->_model, $model_fields_and_values );
		if ( ! empty( $primary_key ) && ! empty( $model_fields_and_values[ $primary_key ] ) ) {
			// check for primary key alias in cached objects
			if ( isset( $this->_object_cache[ $model_fields_and_values[ $primary_key ] ] )) {
				$object = $this->_object_cache[ $model_fields_and_values[ $primary_key ] ];
			} else {
				$object = $this->get_object_by_id( $model_fields_and_values[ $primary_key ] );
			}
		}
		if ( ! $object instanceof $object_class ) {
			$object = call_user_func_array(
				array( $object_class, 'new_instance' ),
				array( $model_fields_and_values, $this->_timezone, $this->_date_time_formats )
			);
			if ( $this->_save_to_db ) {
				$object->save();
			}
		}
		// if primary key was using an alias like TKT_ID = *T1 then cache it
		if (
			isset( $model_fields_and_values[ $primary_key ] )
			&& strpos( $model_fields_and_values[ $primary_key ], '*' ) !== false
			&& empty( $this->_object_cache[ $model_fields_and_values[ $primary_key ] ] )
		) {
			$this->_object_cache[ $model_fields_and_values[ $primary_key ] ] = $object;
			if ( ! $object->ID() && $this->_save_to_db ) {
				$object->save();
			}
		}
		if ( $this->_model->get_this_model_name() === 'Extra_Join' ) {
			//relations are set up for "Extra_Join" relations simply by creating the object
			$related_model_objects = array();
		} else {
			$related_model_objects = $this->_set_relations_for_foreign_keys_in_model_fields(
				$this->_model,
				$model_fields_and_values,
				$related_model_objects
			);
		}
		return $this->generate_related_objects( $object, $related_model_objects );
	}



	/**
	 * _parse_timezones_formats_and_sequences_in_model_fields
	 *
	 * @param array $model_fields_and_values
	 * @return array
 	 */
	protected function _parse_timezones_formats_and_sequences_in_model_fields( $model_fields_and_values ) {
		//timezone?
		if ( isset( $model_fields_and_values[ 'timezone' ] ) ) {
			$this->set_timezone( $model_fields_and_values[ 'timezone' ] );
			unset( $model_fields_and_values[ 'timezone' ] );
		}
		//date formats?
		if ( isset( $model_fields_and_values[ 'formats' ] ) && is_array( $model_fields_and_values[ 'formats' ] ) ) {
			$this->set_date_time_formats( $model_fields_and_values[ 'formats' ] );
			unset( $model_fields_and_values[ 'formats' ] );
		}
		// get values for fields using WP_UnitTest_Generator_Sequence
		foreach ( $model_fields_and_values as $field => $value ) {
			if ( $value instanceof WP_UnitTest_Generator_Sequence ) {
				$model_fields_and_values[ $field ] = $value->next();
			}
		}
		return $model_fields_and_values;
	}



	/**
	 * find_primary_key_in_model_fields
	 *
	 * @param EEM_Base $model
	 * @param array $model_fields_and_values
	 * @return string
 	 */
	protected function _find_primary_key_in_model_fields( EEM_Base $model, $model_fields_and_values ) {
		if ( $model->has_primary_key_field() ) {
			$this->_primary_key = $model->primary_key_name();
			foreach ( $model_fields_and_values as $field => $values ) {
				if ( $field === $this->_primary_key ) {
					return $field;
				}
			}
		} else {
			$this->_primary_key = '';
		}
		return '';
	}



	/**
	 * set_relations_for_foreign_keys_in_model_fields
	 *
	 * @param EEM_Base $model
	 * @param array    $model_fields_and_values
	 * @param array    $related_model_objects
	 * @return array
	 * @throws \Exception
	 */
	protected function _set_relations_for_foreign_keys_in_model_fields(
		EEM_Base $model,
		$model_fields_and_values,
		$related_model_objects = array()
	) {
		foreach ( $model_fields_and_values as $field => $values ) {
			if ( $field === 'OBJ_ID' ) {
				continue;
			}
			$field_obj = $model->field_settings_for( $field );
			if (
				$field_obj instanceof EE_Foreign_Key_Field_Base
				&& ! $field_obj instanceof EE_WP_User_Field
				&& ! empty( $values )
			) {
				$related_model_name = $field_obj->get_model_names_pointed_to();
				$related_model_name = reset( $related_model_name );
				if ( ! empty( $related_model_name ) )  {
					if ( isset( $related_model_objects[ $related_model_name ] ) ) {
						$related_model_objects[ $related_model_name ] = array_merge(
							array( $field => $values ),
							$related_model_objects[ $related_model_name ]
						);
					} else {
						$related_model_objects[ $related_model_name ] = array( $field => $values );
					}
				}
			}
		}
		return $related_model_objects;
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
		if ( ! empty( $properties_and_relations ) ) {
			$timezone_and_formats = array( 'timezone', 'formats' );
			foreach ( $properties_and_relations as $relation_name => $model_properties_and_relations ) {
				$model_fields = array();
				$related_models = array();
				$relation_name = $this->_prep_model_or_class_name( $relation_name );
				// if it doesn't already exist, add our primary key as a foreign key
				// assuming that any related models will have a relation pointing back at this model.
				// if it's NOT a field, it won't get added to the $model_fields array,
				// nor will it ever get added to the $related_models array
				if ( ! isset( $model_properties_and_relations[ $this->_primary_key ] ) ) {
					$model_properties_and_relations[ $this->_primary_key ] = $object->ID();
				}
				$model = EE_Registry::instance()->load_model( $relation_name );
				foreach ( $model_properties_and_relations as $field_or_model => $value_or_properties ) {
					if ( $model->has_field( $field_or_model ) || in_array( $field_or_model, $timezone_and_formats ) ) {
						$model_fields[ $field_or_model ] = $value_or_properties;
					} else if ( $field_or_model !== $this->_primary_key ) {
						$related_models[ $field_or_model ] = $value_or_properties;
					}
				}
				$related_object = $this->_generate_related_object( $relation_name, $model_fields, $related_models );
				$object->_add_relation_to( $related_object, $relation_name );
				if ( $this->_save_to_db ) {
					$object->save();
				}
			}
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
				sprintf(
					'An invalid "%1$s" object was generated. This is what was returned after calling the "%2$s" factory:%5$s %3$s %5$s with the following fields:%5$s %4$s',
					$related_object_class,
					$model_name,
					print_r( $related_object, true ),
					print_r( $model_fields, true ),
					'<br />'
				)
			);
		}
		return $related_object;
	}



	/**
	 * gets
	 *
	 * @param string $model_name
	 * @param bool   $chained
	 * @return \EE_UnitTest_Factory_for_Model_Object
	 * @throws \Exception
	 */
	protected function _get_model_factory( $model_name, $chained = false ) {
		$factory_model = strtolower( $model_name );
		$factory_model .= $chained ? '_chained' : '';
		$factory = $this->_factory->get_factory_for_model( $factory_model );
		if ( ! $factory instanceof EE_UnitTest_Factory_for_Model_Object ) {
			throw new Exception(
				sprintf(
					'"EE_UnitTest_Factory->%1$s" is not a valid WP_UnitTest_Factory_For_Thing class.',
					$factory_model
				)
			);
		}
		return $factory;
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



	/**
	 * @param       $count
	 * @param array $args
	 * @param null  $generation_definitions
	 * @return array
	 */
	function create_many( $count, $args = array(), $generation_definitions = NULL ) {
		$results = array();
		for ( $i = 0; $i < $count; $i++ ) {
			// reset factory properties and relations by passing null
			$this->set_properties_and_relations( null );
			$results[] = $this->create_object( $args );
		}
		return $results;
	}



	/**
	 * @return void
	 */
	public function reset() {
		$this->_timezone = '';
		$this->_date_time_formats = array();
		$this->_custom_properties_and_relations = null;
		$this->_default_properties = null;
		$this->_default_relations = null;
		$this->_related_model_object_properties = array();
		$this->_save_to_db = true;
		$this->_primary_key = '';
		$this->_object_cache = array();
	}



}
// End of file EE_UnitTest_Factory_for_Model_Object.class.php
// Location: /EE_UnitTest_Factory_for_Model_Object.class.php