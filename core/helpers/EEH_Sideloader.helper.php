<?php
if ( ! defined( 'EVENT_ESPRESSO_VERSION' ) ) {
	exit( 'NO direct script access allowed' );
}
/**
 * EEH_Sideloader
 *
 * This is a helper utility class that provides "sideloading" functionality.
 * Sideloading simply refers to retrieving files hosted elsewhere (usually github) that are downloaded into EE.
 *
 * @package		Event Espresso
 * @subpackage	/helpers/EEH_Sideloader.helper.php
 * @author		Darren Ethier
 */
class EEH_Sideloader extends EEH_Base {

	private $_upload_to;
	private $_upload_from;
	private $_permissions;
	private $_new_file_name;



	/**
	 * EEH_Sideloader constructor.
	 */
	public function __construct() {}


	/**
	 * sets the properties for class either to defaults or using incoming initialization array
	 *
	 * @param  array  $init array on init (keys match properties others ignored)
	 * @return void
	 */
	public function init( $init ) {
		$defaults = array(
			'_upload_to' => $this->_get_wp_uploads_dir(),
			'_upload_from' => '',
			'_permissions' => 0644,
			'_new_file_name' => 'EE_Sideloader_' . uniqid( get_current_blog_id(), true ) . '.default'
		);

		$props = array_merge( $defaults, $init );

		foreach ( $props as $key => $val ) {
			if ( property_exists( $this, $key ) ) {
				$this->{$key} = $val;
			}
		}

		//make sure we include the required wp file for needed functions
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}


	//utilities
	private function _get_wp_uploads_dir() {}

	//setters
	/**
	 * @param $upload_to_folder
	 */
	public function set_upload_to( $upload_to_folder ) {
		$this->_upload_to = $upload_to_folder;
	}



	/**
	 * @param $upload_from_folder
	 */
	public function set_upload_from( $upload_from_folder ) {
		$this->_upload_from = $upload_from_folder;
	}



	/**
	 * @param $permissions
	 */
	public function set_permissions( $permissions ) {
		$this->_permissions = $permissions;
	}



	/**
	 * @param $new_file_name
	 */
	public function set_new_file_name( $new_file_name ) {
		$this->_new_file_name = $new_file_name;
	}

	//getters
	/**
	 * @return mixed
	 */
	public function get_upload_to() {
		return $this->_upload_to;
	}



	/**
	 * @return mixed
	 */
	public function get_upload_from() {
		return $this->_upload_from;
	}



	/**
	 * @return mixed
	 */
	public function get_permissions() {
		return $this->_permissions;
	}



	/**
	 * @return mixed
	 */
	public function get_new_file_name() {
		return $this->_new_file_name;
	}



	/**
	 * upload methods
	 *
	 * @return bool
	 */
	public function sideload() {
		//setup temp dir
		$temp_file = wp_tempnam( $this->_upload_from );

		if ( !$temp_file ) {
			EE_Error::add_error( __('Something went wrong with the upload.  Unable to create a tmp file for the uploaded file on the server', 'event_espresso'), __FILE__, __FUNCTION__, __LINE__ );
			return false;
		}

		do_action( 'AHEE__EEH_Sideloader__sideload__before', $this, $temp_file );

		$wp_remote_args = apply_filters( 'FHEE__EEH_Sideloader__sideload__wp_remote_args', array( 'timeout' => 500, 'stream' => true, 'filename' => $temp_file ), $this, $temp_file );

		$response = wp_safe_remote_get( $this->_upload_from, $wp_remote_args );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			unlink( $temp_file );
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				EE_Error::add_error(
					sprintf(
						__(
							'Unable to upload the file.  Either the path given to upload from is incorrect, or something else happened.  Here is the response returned:%3$s%1$s%3$sHere is the path given: %2$s',
							'event_espresso'
						),
						var_export( $response, true ),
						$this->_upload_from,
						'<br />'
					),
					__FILE__, __FUNCTION__, __LINE__
				);
			}
			return false;
		}

		//possible md5 check
		$content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );
		if ( $content_md5 ) {
			$md5_check = verify_file_md5( $temp_file, $content_md5 );
			if ( is_wp_error( $md5_check ) ) {
				unlink( $temp_file );
				EE_Error::add_error( $md5_check->get_error_message(), __FILE__, __FUNCTION__, __LINE__ );
				return false;
			}
		}

		$file = $temp_file;

		//now we have the file, let's get it in the right directory with the right name.
		$path = apply_filters(
			'FHEE__EEH_Sideloader__sideload__new_path',
			$this->_upload_to . $this->_new_file_name,
			$this
		);
		//move file in
		if ( false === @ rename( $file, $path ) ) {
			unlink( $temp_file );
			EE_Error::add_error(
				sprintf(
					__(
						'Unable to move the file to new location (possible permissions errors). This is the path the class attempted to move the file to: %s',
						'event_espresso'
					),
					$path
				),
				__FILE__,
				__FUNCTION__,
				__LINE__
			);
			return false;
		}

		//set permissions
		$permissions = apply_filters( 'FHEE__EEH_Sideloader__sideload__permissions_applied', $this->_permissions, $this );
		chmod( $path, $permissions );

		//that's it.  let's allow for actions after file uploaded.
		do_action( 'AHEE__EE_Sideloader__sideload_after', $this, $path );

		//unlink temp file
		@unlink( $temp_file );
		return true;
	}

} //end EEH_Template class
