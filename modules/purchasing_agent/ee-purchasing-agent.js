jQuery(document).ready( function($) {

	var PurchasingAgent = {

		/**
		 *  @function displayInfo
		 */
		displayInfo : function () {
			var agent                  = $( 'input:radio[name="ee_purchasing_agent"]:checked' ).val();
			var additional_info_notice = $( '#ee-additional-attendee-info-notice-dv' );
			var separate_info_notice   = $( '#ee-additional-separate-info-notice-dv' );
			var billing_form           = $( '#purchaser-billing-form' );
			//console.log( JSON.stringify( 'purchasing_agent.val(): ' + agent, null, 4 ) );
			//console.log( JSON.stringify( 'additional_info_notice.length: ' + additional_info_notice.length, null, 4 ) );
			//console.log( JSON.stringify( 'separate_info_notice.length: ' + separate_info_notice.length, null, 4 ) );
			//console.log( JSON.stringify( 'billing_form.length: ' + billing_form.length, null, 4 ) );
			if ( agent === 'attendee' ) {
				additional_info_notice.show();
				separate_info_notice.hide();
				PurchasingAgent.hideBillingForm( billing_form );
			} else if ( agent === 'separate' ) {
				additional_info_notice.hide();
				separate_info_notice.show();
				PurchasingAgent.displayBillingForm( billing_form );
			}
		},

		/**
		 *  @function hideBillingForm
		 */
		hideBillingForm : function ( billing_form ) {
			billing_form.hide();
			billing_form.find( 'input' ).addClass( 'ee-do-not-validate' );
			billing_form.find( 'select' ).addClass( 'ee-do-not-validate' );
		},

		/**
		 *  @function displayBillingForm
		 */
		displayBillingForm : function ( billing_form ) {
			billing_form.show();
			billing_form.find( 'input' ).removeClass( 'ee-do-not-validate' );
			billing_form.find( 'select' ).removeClass( 'ee-do-not-validate' );
		}


	};


	/**
	 * listener for changes to ee_reg_qstn-purchasing_agent select box
	 */
	$( 'input:radio[name="ee_purchasing_agent"]' ).on( 'click', function() {
		PurchasingAgent.displayInfo();
	});

	/**
	 * set initial state
	 */
	PurchasingAgent.displayInfo();

});
