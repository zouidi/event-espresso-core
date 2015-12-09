<?php
if ( !defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 *
 * EEM_Line_Item_Test
 *
 * @package			Event Espresso
 * @subpackage
 * @author				Mike Nelson
 *
 */
class EEM_Line_Item_Test extends EE_UnitTestCase {
	/**
	 * @group 7239
	 */
	public function test_get_all_non_ticket_line_items_for_transaction(){
		$this->factory->transaction->set_properties_and_relations( null );
		$txn = $this->factory->transaction->create_object();
		$this->factory->ticket->set_properties_and_relations( null );
		$ticket = $this->factory->ticket->create_object();
		$this->factory->price->set_properties_and_relations( null );
		$tax = $this->factory->price->create_object();
		$line_item_for_ticket = $this->new_model_obj_with_dependencies(
				'Line_Item',
				array(
					'TXN_ID' => $txn->ID(),
					'LIN_type' => EEM_Line_Item::type_line_item,
					'OBJ_type' => 'Ticket',
					'OBJ_ID' => $ticket->ID()
					) );
		$line_item_for_tax = $this->new_model_obj_with_dependencies( 'Line_Item',
				array(
					'TXN_ID' => $txn->ID(),
					'LIN_type' => EEM_Line_Item::type_tax,
					'OBJ_type' => 'Price',
					'OBJ_ID' => $tax->ID()
				));
		$line_item_for_nothing = $this->new_model_obj_with_dependencies( 'Line_Item',
				array(
					'TXN_ID' => $txn->ID(),
					'LIN_type' => EEM_Line_Item::type_line_item,
					'OBJ_type' => NULL,
					'OBJ_ID' => 0
				)
				);
		$line_item_for_venue = $this->new_model_obj_with_dependencies( 'Line_Item',
				array(
					'TXN_ID' => $txn->ID(),
					'LIN_type' => EEM_Line_Item::type_line_item,
					'OBJ_type' => 'Venue',
					'OBJ_ID' => 0
				)
		);
		$non_ticket_line_items = EEM_Line_Item::instance()->get_all_non_ticket_line_items_for_transaction( $txn );
		$this->assertEquals( 2, count( $non_ticket_line_items ) );
		$this->assertTrue( in_array( $line_item_for_nothing, $non_ticket_line_items ) );
		$this->assertTrue( in_array( $line_item_for_venue, $non_ticket_line_items ) );
	}

	/**
	 * @group 7965
	 */
	function test_delete_line_items_with_no_transaction(){
		$deletable_count = 5;
		$safe_count = 3;
		for( $i = 1; $i <= $deletable_count; $i++ ) {
			// first reset any defaults that the factories set automagically
			$this->factory->line_item->set_properties_and_relations( null );
			// create a default object with NO relations
			$this->factory->line_item->create_object(
				array( 'LIN_timestamp' => time() - WEEK_IN_SECONDS * 2 )
			);
		}
		for( $i = 1; $i <= $safe_count; $i++ ){
			// reset default properties and relations again
			$this->factory->line_item->set_properties_and_relations(
				array(
					'LIN_timestamp' => time() - DAY_IN_SECONDS,
					'Transaction' => array()
				)
			);
			$this->factory->line_item->create_object();
		}
		$deleted = EEM_Line_Item::instance()->delete_line_items_with_no_transaction();
		$this->assertEquals( $deletable_count, $deleted );
	}



}
// End of file EEM_Line_Item_Test.php
// Location: tests\testcases\core\db_models\EEM_Line_Item_Test.php