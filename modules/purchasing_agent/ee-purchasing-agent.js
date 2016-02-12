jQuery(document).ready( function($) {


	/**
	 *  @function doAjax
	 */
	var displayPurchasingAgentInfo = function() {
		if ( ! purchasing_agent.length > 0 ) {
			purchasing_agent = $( '#ee_reg_qstn-purchasing_agent' );
		}
		var sync_info_notice = $( '#ee-attendee-sync-info-notice-dv' );
			var billing_form = $( '#purchaser-billing-form' );
			var agent = purchasing_agent.val();
			console.log( JSON.stringify( 'purchasing_agent.val(): ' + agent, null, 4 ) );
			console.log( JSON.stringify( 'sync_info_notice.length: ' + sync_info_notice.length, null, 4 ) );
			console.log( JSON.stringify( 'billing_form.length: ' + billing_form.length, null, 4 ) );
			if ( agent === 'attendee' ) {
				sync_info_notice.show();
				billing_form.hide();
			} else if ( agent === 'separate' ) {
				sync_info_notice.hide();
				billing_form.show();
			}
	};


	/**
	 * define dropdown input for selecting the purchasing agent
	 */
	var purchasing_agent = $( '#ee_reg_qstn-purchasing_agent' );
	/**
	 * listener for changes to ee_reg_qstn-purchasing_agent select box
	 */
	purchasing_agent.on( 'change', function() {
		displayPurchasingAgentInfo();
	});


	/**
	 * set initial state
	 */
	displayPurchasingAgentInfo();

});
