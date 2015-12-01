<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) { exit('No direct script access allowed'); }



/**
 * EE Factory Class for Attendee.
 *
 * When this is called as a chained object - the following relations will be also generated and attached:
 * - Registration (note this also sets all the relations on a registration up)
 *
 * relations that are NOT currently setup (@todo)
 * - State
 * - Country
 *
 * @since        4.3.0
 * @package        Event Espresso
 * @subpackage    tests
 *
 */
class EE_UnitTest_Factory_For_Attendee extends EE_UnitTest_Factory_for_Model_Object {


	/**
	 * constructor
	 *
	 * @param EE_UnitTest_Factory $factory
	 * @param array | null        $properties_and_relations
	 *          pass null (or nothing) to just get the default properties with NO relations
	 *          or pass empty array for default properties AND relations
	 *          or non-empty array to override default properties and manually set related objects and their properties,
	 */
	public function __construct( $factory, $properties_and_relations = null ) {
		$this->set_model_object_name( 'Attendee' );
		parent::__construct( $factory, $properties_and_relations );
	}



	/**
	 * _set_default_properties_and_relations
	 *
	 * @access protected
	 * @param string $called_class in order to avoid recursive application of relations,
	 *                             we need to know which class is making this request
	 * @return void
	 */
	protected function _set_default_properties_and_relations( $called_class ) {
		// set some sensible defaults for this model object
		if ( empty( $this->_default_properties ) ) {
			static $counter = 1;
			$fname = EE_UnitTest_Factory_For_Attendee::star_wars_first_name();
			$place = EE_UnitTest_Factory_For_Attendee::star_wars_place();
			$this->_default_properties = array(
				'ATT_fname'   => $fname,
				'ATT_lname'   => EE_UnitTest_Factory_For_Attendee::star_wars_last_name(),
				'ATT_address' => substr( uniqid(), 0, rand( 1, 4 ) ) . ' '
								 . EE_UnitTest_Factory_For_Attendee::star_wars_place(),
				'ATT_city'    => $place,
				'ATT_zip'     => substr( uniqid(), 0, rand( 0, 6 ) ),
				'ATT_email'   => "$fname@$place." . substr(
						EE_UnitTest_Factory_For_Attendee::star_wars_place(),
						2,
						rand( 2, 3 )
					),
				'ATT_phone'   => sprintf(
					'%1$d-%1$d-%2$d',
					EE_UnitTest_Factory_For_Attendee::random_number( 3 ),
					EE_UnitTest_Factory_For_Attendee::random_number( 4 )
				)
			);
			$counter++;
		}
		// and set some sensible default relations
		if ( empty( $this->_default_relations ) ) {
			$this->_default_relations = array(
				'Registration' => array(),
				'State'        => array(),
				'Country'      => array(),
				'Event'        => array(),
				'WP_User'      => array(),
			);
			$this->_resolve_default_relations( $called_class );
		}

	}



	/**
	 * @return string
	 */
	public static function star_wars_first_name() {
		$names = array(
			'Gyogdag',
			'Oberon',
			'Chaffery',
			'Noort',
			'Lukef',
			'Lasak',
			'Xislan',
			'Adan',
			'Edus',
			'Kalaila',
			'Melfina',
			'Arod',
			'Nihran',
			'Horreek',
			'Tyrria',
			'Ardler',
		);
		return $names[ rand( 0, 15 ) ];
	}



	/**
	 * @return string
	 */
	public static function star_wars_last_name() {
		$names = array(
			'Lighthopper',
			'Vand',
			'Ordona',
			'Nelant',
			'Zaria',
			'Bebec',
			'Narag',
			'Tarrk',
			'Renning',
			'Remex',
			'Corra',
			'Kuolor',
			'Mithric',
			'Meelux',
			'Nabkin',
			'Helran',
		);
		return $names[ rand( 0, 15 ) ];
	}



	/**
	 * @return string
	 */
	public static function star_wars_place() {
		$names = array(
			'Drendanwan',
			'Kaalecien',
			'Galored 5',
			'Nydeviel',
			'Uloetram',
			'Uloldan',
			'Loassi',
			'Yerrand',
			'Elelirien',
			'Aligoli',
			'Sevoewen',
			'Nyhar L7',
			'Acissi',
			'Etheib',
			'Kedilaniel',
			'Ocaowen',
		);
		return $names[ rand( 0, 15 ) ];
	}



	/**
	 * @param int $digits
	 * @return string
	 */
	public static function random_number( $digits = 1 ) {
		return rand( pow( 10, $digits - 1 ) - 1, pow( 10, $digits ) - 1 );
	}

}
// End of file EE_UnitTest_Factory_For_Attendee.class.php
// Location: /EE_UnitTest_Factory_For_Attendee.class.php