<?php
defined( 'ABSPATH' ) || exit;



/**
 * Class EE_DMS_4_10_0_Questions_Refactor
 * Description
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
class EE_DMS_4_10_0_Questions_Refactor extends EE_Data_Migration_Script_Stage_Table {

	public function __construct() {
		global $wpdb;
		$this->_pretty_name = __( 'Registration Form Questions Refactor', 'event_espresso' );
		$this->_old_table = $wpdb->prefix . 'esp_question';
		parent::__construct();
	}



	/**
	 * @param array $question an associative array where keys are column names and values are their values.
	 * @return void
	 */
	protected function _migrate_old_row( $question ) {
		global $wpdb;
		if ( $question['QST_ID'] ) {
			$html_class = sanitize_key( $question['QST_type'] );
			$identifier = $html_class . '-' . time();
			$success = $wpdb->update(
				$this->_old_table,
				// data
				array(
					//QST_display_text
					//QST_admin_label
					//QST_system
					//QST_type
					//QST_required
					//QST_required_text
					//QST_order
					//QST_admin_only
					//QST_wp_user
					//QST_deleted
					//QST_max
					//QSG_ID
					'QST_identifier' => $identifier,
					//QST_desc
					'QST_html_name' => $identifier,
					'QST_html_id' => $identifier,
					'QST_html_class' => $html_class,
					'QST_html_label_id'    => "{$identifier}-lbl",
					'QST_html_label_class' => "{$html_class}-lbl",
					//QST_default_value
					'QST_validation_strategies' => $question['QST_required']
						? array( 'required' )
						: array(),
					'QST_validation_message' => $question['QST_required']
						? array( 'required' => $question['QST_required_text'] )
						: array(),
				),
				// where
				array( 'QST_ID' => $question['QST_ID'] ),
				// data format
				array( '%s' ),
				// where format
				array( '%d' )
			);
			if ( ! $success ) {
				$this->add_error(
					sprintf(
						__(
							'Could not update the "%1$s" question (ID:%2$d) because "%3$s"',
							'event_espresso'
						),
						json_encode( $question['QST_admin_label'] ),
						$question['QST_ID'],
						$wpdb->last_error
					)
				);
			}
		}
	}


}
// End of file EE_DMS_4_10_0_Questions_Refactor.dmsstage.php
// Location: ${NAMESPACE}/EE_DMS_4_10_0_Questions_Refactor.dmsstage.php