<?php
namespace EventEspresso\core\services\activation;

if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}



/**
 * Class TableDataGenerator
 *
 * Description
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 *
 */
class TableDataGenerator {

	/**
	 * WP User ID of the table creator
	 *
	 * @var int $wp_user_id
	 */
	private $wp_user_id = 0;

	/**
	 * @var array $table_data_generators
	 */
	private $table_data_generators = array();



	/**
	 * TableDataGenerator constructor.
	 *
	 * @param int $wp_user_id
	 * @throws \Exception
	 */
	public function __construct( $wp_user_id ) {
		$this->setWpUserId( $wp_user_id );
		if ( empty( $this->wp_user_id ) ) {
			throw new \Exception(
				__( 'A valid WP User ID is required in order to generate tables and default data', 'event_espresso' )
			);
		}
	}



	/**
	 * @return int
	 */
	public function wpUserId() {
		return $this->wp_user_id;
	}



	/**
	 * @param int $wp_user_id
	 */
	public function setWpUserId( $wp_user_id ) {
		$this->wp_user_id = absint( $wp_user_id );
	}



	/**
	 * @return array
	 */
	public function tableDataGenerators() {
		return $this->table_data_generators;
	}



	/**
	 * @param array $table_data_generators
	 */
	public function setTableDataGenerators( $table_data_generators ) {
		$this->table_data_generators = $table_data_generators;
	}



	/**
	 * tableNameWithPrefix
	 *
	 * @access public
	 * @static
	 * @param $table_name
	 * @return string
	 */
	public static function tableNameWithPrefix( $table_name ) {
		global $wpdb;
		return strpos( $table_name, $wpdb->prefix ) === 0 ? $table_name : $wpdb->prefix . $table_name;
	}



	/**
	 * loadTableDataGenerators
	 *
	 * @access protected
	 * @param array  $filepaths
	 * @param string $namespace
	 * @param string $subclass_of
	 * @param mixed  $arguments
	 * @param array  $exclude
	 * @return array
	 */
	protected function loadTableDataGenerators(
		array $filepaths,
		$namespace,
		$subclass_of,
		$arguments = array(),
		$exclude = array()
	) {
		$table_data_generators = array();
		if ( empty( $filepaths ) ) {
			return $table_data_generators;
		}
		foreach ( $filepaths as $filepath ) {
			if ( is_readable( $filepath ) ) {
				require_once( $filepath );
				$classname = str_replace( '.php', '', basename( $filepath ) );
				$FQCN = $namespace . $classname;
				if (
					! in_array( $classname, $exclude )
					&& class_exists( $FQCN )
					&& is_subclass_of( $FQCN, $subclass_of )
				) {
					$table_data_generators[ $classname ] = new $FQCN( $arguments );
				}
			}
		}
		return $table_data_generators;
	}



	/**
	 * @param string $table_name
	 * @param array  $data
	 * @param array $data_types
	 * @return int The number of rows inserted
	 * @throws \Exception
	 */
	protected function insertData( $table_name, $data, $data_types ) {
		if ( empty( $table_name ) ) {
			throw new \Exception(
				sprintf(
					__( '"%1$s" is not a valid table name. Could not perform an insert query.', 'event_espresso' ),
					$table_name
				)
			);
		}
		if ( empty( $data ) || ! is_array( $data ) ) {
			throw new \Exception(
				sprintf(
					__(
						'A valid array of data is required in order to perform an insert query for table "%1$s"',
						'event_espresso'
					),
					$table_name
				)
			);
		}
		if ( empty( $data_types ) || ! is_array( $data_types ) ) {
			throw new \Exception(
				sprintf(
					__(
						'A valid array of data types is required in order to perform an insert query for table "%1$s".',
						'event_espresso'
					),
					$table_name
				)
			);
		}
		if ( count( $data ) != count( $data_types ) ) {
			throw new \Exception(
				sprintf(
					__( 'There is a mismatch between the data and data type arrays for table "%1$s".', 'event_espresso' ),
					$table_name
				)
			);
		}
		/** @var \WPDB $wpdb */
		global $wpdb;
		// insert table data
		$rows_inserted = $wpdb->insert(
			$table_name,
			$data,
			$data_types
		);
		if ( $rows_inserted === false ) {
			throw new \Exception(
				sprintf(
					__(
						'An unknown error occurred while attempting to insert the following data into table "%1$s".%3$s %2$s',
						'event_espresso'
					),
					$table_name,
					print_r( $data, true ),
					'<br />'
				)
			);
		}
		return $wpdb->insert_id;
	}

}
// End of file TableDataGenerator.php
// Location: /TableDataGenerator.php