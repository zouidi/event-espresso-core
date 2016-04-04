<?php
if ( !defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 *
 * EE_Restriction_Generator_Protected
 *
 * Special restrictions for WP Users. Basically users can always access themselves,
 * but their access to other users is controlled by conditions
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Mike Nelson
 *
 */
class EE_Restriction_Generator_WP_User extends EE_Restriction_Generator_Base{

	/**
	 * @return \EE_Default_Where_Conditions
	 */
	protected function _generate_restrictions() {
		$restrictions =  array(
			//if they can't access users, they can still access themselves
			EE_Restriction_Generator_Base::get_cap_name( $this->model(), $this->action() ) => new EE_Default_Where_Conditions( array(
				EE_Default_Where_Conditions::user_field_name_placeholder => EE_Default_Where_Conditions::current_user_placeholder
			)),

		);
		//if its multisite things get more complicated
		if( is_multisite() ) {
			global $wpdb;
			//add a default where condition so we only ever return users for the current blog (on multisite)
			$restrictions[ EE_Restriction_Generator_Base::get_default_restrictions_cap() ] = new EE_Default_Where_Conditions( 
					array( 
						'WP_User_Meta.meta_key' => $wpdb->get_blog_prefix() . 'capabilities',
						'WP_User_Meta.meta_value' => array( 'IS_NOT_NULL' )
					)
				);
		}
		return $restrictions;
	}
}

// End of file EE_Restriction_Generator_Protected.strategy.php