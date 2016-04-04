<?php if ( ! defined('EVENT_ESPRESSO_VERSION')) exit('No direct script access allowed');
/**
 * WP User Meta Model. Not intended to replace meta queries, but this just allows
 * for EE model queries to more easily integrate with the WP User table
 *
 * @package			Event Espresso
 * @subpackage		includes/models/
 * @author				Michael Nelson
 */
class EEM_WP_User_Meta extends EEM_Base {

	/**
	 * private instance of the EEM_WP_User_Meta object
	 * @type EEM_WP_User_Meta
	 */
	protected static $_instance = NULL;



	/**
	 *    constructor
	 * @param null $timezone
	 * @throws \EE_Error
	 */
	protected function __construct( $timezone = NULL ){
		$this->singular_item = __('WP_User_Meta','event_espresso');
		$this->plural_item = __('WP_User_Metas','event_espresso');
		global $wpdb;
		$this->_tables = array(
			'WP_User_Meta'=> new EE_Primary_Table( $wpdb->usermeta, 'ID', true)
		);
		$this->_fields = array(
			'WP_User_Meta'=>array(
				'umeta_id'=> new EE_Primary_Key_Int_Field('umeta_id', __('WP_User_Meta ID','event_espresso')),
				'user_id' => new EE_Foreign_Key_Int_Field( 'user_id', __( 'User ID', 'event_espresso' ), false, 0, array( 'WP_User' ) ),
				'meta_key'=>new EE_Plain_Text_Field( 'meta_key', __('Meta Key', 'event_espresso'), false, ''),
				'meta_value'=>new EE_Maybe_Serialized_Text_Field( 'meta_value', __('Meta Value', 'event_espresso'), true)
			));
		$this->_model_relations = array(
			'WP_User' => new EE_Belongs_To_Relation(),
		);
		$this->_wp_core_model = true;
		foreach( $this->cap_contexts_to_cap_action_map() as $cap_context => $action ) {
			$this->_cap_restriction_generators[ $cap_context ] = new EE_Restriction_Generator_Meta( 'meta_key', 'meta_value' );
		}
		parent::__construct( $timezone );
	}

	/**
	 * We don't need a foreign key to the WP_User_Meta model, we just need its primary key
	 * @return string
	 */
	public function wp_user_field_name() {
		return $this->primary_key_name();
	}

	/**
	 * This WP_User_Meta model IS owned, even though it doesn't have a foreign key to itself
	 * @return boolean
	 */
	public function is_owned() {
		return true;
	}
}
// End of file EEM_WP_User_Meta.model.php
// Location: /core/db_models/EEM_WP_User_Meta.model.php