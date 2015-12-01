<?php
/**
 * Contains test class for espresso.php
 *
 * @since  		4.3.0
 * @package 	Event Espresso
 * @subpackage 	tests
 */


/**
 * All tests for the espresso.php file.
 *
 * @since 		4.3.0
 * @package 	Event Espresso
 * @subpackage 	tests
 */
class espresso_Tests extends EE_UnitTestCase {


	/**
	 * Tests all constants that should be defined on plugin load.
	 *
	 * @since 4.3.0
	 */
	function test_defined_constants() {
		$this->assertTrue( defined('EVENT_ESPRESSO_VERSION') );
		$this->assertTrue( defined('EE_MIN_WP_VER_REQUIRED') );
		$this->assertTrue( defined('EE_MIN_WP_VER_RECOMMENDED') );
		$this->assertTrue( defined('EE_MIN_PHP_VER_RECOMMENDED') );
		$this->assertTrue( defined('EVENT_ESPRESSO_POWERED_BY') );
		$this->assertTrue( defined('EVENT_ESPRESSO_MAIN_FILE') );
		$this->assertTrue( defined('DS') );
		$this->assertTrue( defined('PS') );
		$this->assertTrue( defined('SP') );
		$this->assertTrue( defined('EE_SUPPORT_EMAIL') );
		$this->assertTrue( defined('EE_PLUGIN_BASENAME') );
		$this->assertTrue( defined('EE_PLUGIN_DIR_PATH') );
		$this->assertTrue( defined('EE_PLUGIN_DIR_URL') );
		$this->assertTrue( defined('EE_ADMIN_PAGES') );
		$this->assertTrue( defined('EE_CORE') );
		$this->assertTrue( defined('EE_MODULES') );
		$this->assertTrue( defined('EE_SHORTCODES') );
		$this->assertTrue( defined('EE_TEMPLATES') );
		$this->assertTrue( defined('EE_WIDGETS') );
		$this->assertTrue( defined('EE_CAFF_PATH') );
		$this->assertTrue( defined('EE_ADMIN') );
		$this->assertTrue( defined('EE_CPTS') );
		$this->assertTrue( defined('EE_CLASSES') );
		$this->assertTrue( defined('EE_MODELS') );
		$this->assertTrue( defined('EE_HELPERS') );
		$this->assertTrue( defined('EE_LIBRARIES') );
		$this->assertTrue( defined('EE_THIRD_PARTY') );
		$this->assertTrue( defined('EE_GLOBAL_ASSETS') );
		$this->assertTrue( defined('EE_GATEWAYS') );
		$this->assertTrue( defined('EE_GATEWAYS_URL') );
		$this->assertTrue( defined('EE_TEMPLATES_URL') );
		$this->assertTrue( defined('EE_GLOBAL_ASSETS_URL') );
		$this->assertTrue( defined('EE_IMAGES_URL') );
		$this->assertTrue( defined('EE_THIRD_PARTY_URL') );
		$this->assertTrue( defined('EE_HELPERS_ASSETS') );
		$this->assertTrue( defined('EVENT_ESPRESSO_UPLOAD_DIR') );
		$this->assertTrue( defined('EVENT_ESPRESSO_UPLOAD_URL') );
		$this->assertTrue( defined('EVENT_ESPRESSO_TEMPLATE_DIR') );
		$this->assertTrue( defined('EVENT_ESPRESSO_TEMPLATE_URL') );
		$this->assertTrue( defined('EVENT_ESPRESSO_GATEWAY_DIR') );
		$this->assertTrue( defined('EVENT_ESPRESSO_GATEWAY_URL') );
		$this->assertTrue( defined('EE_LANGUAGES_SAFE_LOC') );
		$this->assertTrue( defined('EE_LANGUAGES_SAFE_DIR') );
		$this->assertTrue( defined('EE_FRONT_AJAX') );
		$this->assertTrue( defined('EE_ADMIN_AJAX') );
		$this->assertTrue( defined('EE_INF_IN_DB') );
	}


	/**
	 * espresso_load_required is run automatically when the plugin is loaded.
	 *
	 * That means we should have the following files already loaded:
	 *  - EE_System
	 *  - EE_Debug_Tools
	 *  - EE_Error
	 *
	 * @since 4.3.0
	 */
	function test_espresso_load_required() {
		$this->assertTrue( class_exists( 'EE_System') );

		//depends on WP_DEBUG
		if( defined('WP_DEBUG') && WP_DEBUG )
			$this->assertTrue( class_exists( 'EEH_Debug_Tools') );
		else
			$this->assertFalse( class_exists( 'EEH_Debug_Tools') );

		$this->assertTrue( class_exists( 'EE_Error' ) );
	}



	//function test_EE_UnitTest_Factory_example_usage() {
	//	echo "\n\n espresso_Tests::test_EE_UnitTest_Factory() START";
	//	$datetimes = array();
	//	echo "\n\n\n\n CREATE EE_DATETIME WITH DEFAULT PROPERTIES AND ZERO RELATIONS \n";
	//	$datetimes['EE_Datetime with default properties and zero relations'] = $this->factory->datetime->create_object();
	//	echo "\n\n\n\n CREATE EE_DATETIME WITH DEFAULT PROPERTIES AND DEFAULT RELATIONS \n";
	//	$datetimes['EE_Datetime with default properties and default relations'] = $this->factory->datetime_chained->create_object();
	//	echo "\n\n\n\n SET_PROPERTIES_AND_RELATIONS for CUSTOM EE_Datetime \n";
	//	$this->factory->datetime->set_properties_and_relations(
	//		array(
	//			'DTT_name'        => 'CUSTOM DATETIME NAME',
	//			'DTT_description' => 'CUSTOM DATETIME DESCRIPTION',
	//			'DTT_EVT_start'   => time() + ( 14 * DAY_IN_SECONDS ),
	//			'DTT_EVT_end'     => time() + ( 14.5 * DAY_IN_SECONDS ),
	//			'DTT_reg_limit'   => 12,
	//			'DTT_sold'        => 0,
	//			'Ticket' => array(
	//				'TKT_ID'   		  => '*',
	//				'TKT_name' 		  => 'CUSTOM TICKET NAME',
	//				'TKT_description' => 'CUSTOM TICKET DESCRIPTION',
	//				'TKT_price' 	  => 10.00,
	//				'TKT_qty'  		  => 12,
	//				'TKT_sold' 		  => 0,
	//				'Price' => array(
	//					'PRC_name'   => 'CUSTOM BASE PRICE',
	//					'PRC_desc'   => 'CUSTOM BASE PRICE DESCRIPTION',
	//					'PRC_amount' => 5.00,
	//				),
	//				'Price*' => array(
	//					'PRT_ID'     => 5,
	//					'PRC_name'   => 'CUSTOM SURCHARGE 1',
	//					'PRC_desc'   => 'CUSTOM SURCHARGE 1 DESCRIPTION',
	//					'PRC_amount' => 2.00,
	//				),
	//				'Price**' => array(
	//					'PRT_ID'     => 5,
	//					'PRC_name'   => 'CUSTOM SURCHARGE 2',
	//					'PRC_desc'   => 'CUSTOM SURCHARGE 2 DESCRIPTION',
	//					'PRC_amount' => 3.00,
	//				),
	//			),
	//		)
	//	);
	//	echo "\n\n CREATE EE_DATETIME WITH CUSTOM PROPERTIES AND CUSTOM RELATIONS \n";
	//	$datetimes['EE_Datetime with custom properties and custom relations'] = $this->factory->datetime->create_object();
	//	foreach ( $datetimes as $test => $datetime ) {
	//		if ( $datetime instanceof EE_Datetime ) {
	//			echo "\n\n\n\n TEST $test";
	//			echo "\n\n  datetime->ID(): " . $datetime->ID();
	//			echo "\n  datetime->name(): " . $datetime->name();
	//			$tickets = $datetime->tickets();
	//			foreach ( $tickets as $ticket ) {
	//				if ( $ticket instanceof EE_Ticket ) {
	//					echo "\n\n   ticket->ID(): " . $ticket->ID();
	//					echo "\n   ticket->name(): " . $ticket->name();
	//					echo "\n   ticket->price(): " . $ticket->price();
	//					$ticket_datetimes = $ticket->datetimes();
	//					foreach ( $ticket_datetimes as $ticket_datetime ) {
	//						if ( $ticket_datetime instanceof EE_Datetime ) {
	//							echo "\n\n    ticket_datetime->ID(): " . $ticket_datetime->ID();
	//							echo "\n    ticket_datetime->name(): " . $ticket_datetime->name();
	//						}
	//					}
	//
	//					$prices = $ticket->prices();
	//					foreach ( $prices as $price ) {
	//						if ( $price instanceof EE_Price ) {
	//							echo "\n\n    price->ID(): " . $price->ID();
	//							echo "\n    price->name(): " . $price->name();
	//							echo "\n    price->amount(): " . $price->amount();
	//							$price_type = $price->type_obj();
	//							if ( $price_type instanceof EE_Price_Type ) {
	//								echo "\n\n     price_type->ID(): " . $price_type->ID();
	//								echo "\n     price_type->name(): " . $price_type->name();
	//								echo "\n     price_type->base_type_name(): " . $price_type->base_type_name();
	//							}
	//						}
	//					}
	//				}
	//			}
	//		}
	//	}
	//	echo "\n\n\n\n CREATE EE_Transaction WITH DEFAULT PROPERTIES AND DEFAULT RELATIONS \n";
	//	$transaction = $this->factory->transaction_chained->create_object();
	//	if ( $transaction instanceof EE_Transaction ) {
	//		echo "\n\n transaction->ID(): " . $transaction->ID();
	//		echo "\n transaction->status_ID(): " . $transaction->status_ID();
	//		$registrations = $transaction->registrations();
	//		foreach ( $registrations as $registration  ){
	//			if ( $registration instanceof EE_Registration ) {
	//				echo "\n\n   registration->ID(): " . $registration->ID();
	//				echo "\n   registration->status_ID(): " . $registration->status_ID();
	//				$attendee = $registration->attendee();
	//				if ( $attendee instanceof EE_Attendee ) {
	//					echo "\n\n     attendee->ID(): " . $attendee->ID();
	//					echo "\n     attendee->full_name(): " . $attendee->full_name();
	//					echo "\n     attendee->state_name(): " . $attendee->state_name();
	//					echo "\n     attendee->country_name(): " . $attendee->country_name();
	//				}
	//				$ticket = $registration->ticket();
	//				if ( $ticket instanceof EE_Ticket ) {
	//					echo "\n\n   ticket->ID(): " . $ticket->ID();
	//					echo "\n   ticket->name(): " . $ticket->name();
	//					echo "\n   ticket->price(): " . $ticket->price();
	//					$ticket_datetimes = $ticket->datetimes();
	//					foreach ( $ticket_datetimes as $ticket_datetime ) {
	//						if ( $ticket_datetime instanceof EE_Datetime ) {
	//							echo "\n\n    ticket_datetime->ID(): " . $ticket_datetime->ID();
	//							echo "\n    ticket_datetime->name(): " . $ticket_datetime->name();
	//						}
	//					}
	//					$prices = $ticket->prices();
	//					foreach ( $prices as $price ) {
	//						if ( $price instanceof EE_Price ) {
	//							echo "\n\n    price->ID(): " . $price->ID();
	//							echo "\n    price->name(): " . $price->name();
	//							echo "\n    price->amount(): " . $price->amount();
	//							$price_type = $price->type_obj();
	//							if ( $price_type instanceof EE_Price_Type ) {
	//								echo "\n\n     price_type->ID(): " . $price_type->ID();
	//								echo "\n     price_type->name(): " . $price_type->name();
	//								echo "\n     price_type->base_type_name(): " . $price_type->base_type_name();
	//							}
	//						}
	//					}
	//				}
	//			}
	//		}
	//	}
	//	echo "\n\n espresso_Tests::test_EE_UnitTest_Factory() END \n\n";
	//}

}
// Location: /tests/testcases/espresso_Tests.php