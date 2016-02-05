<?php
namespace EventEspresso\core\libraries\rest_api\controllers\rpc;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Checkin_Test extends \EE_UnitTestCase {

	/** @var \EE_Registration $registration */
	protected $registration = null;

	/** @var \EE_Datetime $datetime */
	protected $datetime = null;

	/** @var \EE_Datetime $datetime */
	protected $datetime2 = null;


	/*
	 * we're doing stuff that we know will add error notices, so we don't care
	 * if there are errors (that's part of these tests)
	 */
	public function tearDown() {
		\EE_Error::reset_notices();
		parent::tearDown();
	}



	/**
	 *
	 * @param int $reg_id
	 * @param int $dtt_id
	 * @return \WP_REST_Request
	 */
	protected function _create_checkin_request( $reg_id, $dtt_id ) {
		$req = new \WP_REST_Request(
			'PUT',
			\EED_Core_Rest_Api::ee_api_namespace . '4.8.33/registrations/' . $reg_id . '/toggle_checkin_for_datetime/' . $dtt_id . '/force'
		);
		$req->set_url_params(
			array(
				'REG_ID' => $reg_id,
				'DTT_ID' => $dtt_id,
			)
		);
		$req->set_body_params(
			array(
				'force' => "false"
			)
		);
		return $req;
	}



	/**
	 * @param string $status
	 * @param bool   $two_datetimes
	 * @param array  $extra_ticket_args
	 */
	public function build_registration_with_dependencies(
		$status = \EEM_Registration::status_id_incomplete,
		$two_datetimes = false,
		$extra_ticket_args = array()
	) {
		$args = array(
			'STS_ID' => $status,
			'Ticket' => array(
				'Datetime' => array(),
			),
		);
		if ( $two_datetimes ) {
			$args['Ticket']['Datetime*'] = array();
		}
		if ( !empty( $extra_ticket_args ) ) {
			$args['Ticket'] = array_merge(
				$args[ 'Ticket' ],
				$extra_ticket_args
			);
		}
		$this->factory->registration->set_properties_and_relations( $args );
		$this->registration = $this->factory->registration->create_object();
		/** @var \EE_Datetime[] $datetimes */
		$datetimes = $this->registration->ticket()->datetimes();
		$this->datetime = reset( $datetimes );
		if ( $two_datetimes ) {
			$this->datetime2 = next( $datetimes );
		}
	}



	public function test_handle_checkin__success() {
		global $current_user;
		$checkins_before = \EEM_Checkin::instance()->count();
		$current_user = $this->wp_admin_with_ee_caps();
		$this->build_registration_with_dependencies( \EEM_Registration::status_id_approved );
		$response = Checkin::handle_request_toggle_checkin(
			$this->_create_checkin_request( $this->registration->ID(), $this->datetime->ID() )
		);
		$this->assertEquals( $checkins_before + 1, \EEM_Checkin::instance()->count() );
		$data = $response->get_data();
		$this->assertTrue( isset( $data[ 'CHK_ID' ] ) );
		$checkin_obj = \EEM_Checkin::instance()->get_one_by_ID( $data[ 'CHK_ID' ] );
		$this->assertEquals( $this->registration->ID(), $checkin_obj->get( 'REG_ID' ) );
		$this->assertEquals( $this->datetime->ID(), $checkin_obj->get( 'DTT_ID' ) );
		$this->assertEquals( true, $data[ 'CHK_in' ] );
		$this->assertDateWithinOneMinute(
			mysql_to_rfc3339(date( 'c' ) ),
			$data[ 'CHK_timestamp' ],
			'Y-m-d\TH:m:i'
		);
	}



	public function test_handle_checkin__fail_not_approved() {
		$checkins_before = \EEM_Checkin::instance()->count();
		global $current_user;
		$current_user = $this->wp_admin_with_ee_caps();
		$this->build_registration_with_dependencies();
		$response = Checkin::handle_request_toggle_checkin(
			$this->_create_checkin_request( $this->registration->ID(), $this->datetime->ID() )
		);
		$this->assertEquals( $checkins_before, \EEM_Checkin::instance()->count() );
		$data = $response->get_data();
		$this->assertTrue( isset( $data[ 'code' ] ) );
		$this->assertEquals( 'rest_toggle_checkin_failed', $data[ 'code' ] );
		$this->assertTrue( isset( $data[ 'additional_errors' ] ) );
		$this->assertFalse( empty( $data[ 'additional_errors' ][ 0 ][ 'message'] ) );
	}



	//doesnt have permission
	public function test_handle_checkin__fail_no_permitted() {
		//notice that we have NOT logged in!
		$checkins_before = \EEM_Checkin::instance()->count();
		$this->build_registration_with_dependencies();
		$response = Checkin::handle_request_toggle_checkin(
			$this->_create_checkin_request( $this->registration->ID(), $this->datetime->ID() )
		);
		$this->assertEquals( $checkins_before, \EEM_Checkin::instance()->count() );
		$data = $response->get_data();
		$this->assertTrue( isset( $data[ 'code' ] ) );
		$this->assertEquals( 'rest_user_cannot_toggle_checkin', $data[ 'code' ] );
	}



	//registered too many times
	public function test_handle_checkin__fail_checked_in_too_many_times() {
		global $current_user;
		$current_user = $this->wp_admin_with_ee_caps();
		$this->build_registration_with_dependencies(
			\EEM_Registration::status_id_approved, true, array( 'TKT_uses' => 1 )
		);
		//create a previous checkin entry, so the reg shouldn't be allowed to checkin more
		$this->new_model_obj_with_dependencies(
			'Checkin',
			array(
				'REG_ID' => $this->registration->ID(),
				'DTT_ID' => $this->datetime->ID(),
				'CHK_in' => true
			)
		);

		$checkins_before = \EEM_Checkin::instance()->count();
		$response = Checkin::handle_request_toggle_checkin(
			$this->_create_checkin_request( $this->registration->ID(), $this->datetime2->ID() )
		);

		$this->assertEquals( $checkins_before, \EEM_Checkin::instance()->count() );
		$data = $response->get_data();
		$this->assertTrue( isset( $data[ 'code' ] ) );
		$this->assertEquals( 'rest_toggle_checkin_failed', $data[ 'code' ] );
		$this->assertTrue( isset( $data[ 'additional_errors' ] ) );
		$this->assertFalse( empty( $data[ 'additional_errors' ][ 0 ][ 'message'] ) );
	}



	//registered too many times but force it
	public function test_handle_checkin__success_only_because_forced() {
		global $current_user;
		$current_user = $this->wp_admin_with_ee_caps();
		$this->build_registration_with_dependencies( \EEM_Registration::status_id_cancelled );
		$checkins_before = \EEM_Checkin::instance()->count();
		$req = $this->_create_checkin_request( $this->registration->ID(), $this->datetime->ID() );
		$req->set_body_params( array( 'force' => "true" ) );
		$response = Checkin::handle_request_toggle_checkin( $req );

		$this->assertEquals( $checkins_before + 1, \EEM_Checkin::instance()->count() );
	}



} // end of class Checkin_Test
// Location : tests/testcases/core/libraries/rest_api/controllers/Checkin_Test.php

