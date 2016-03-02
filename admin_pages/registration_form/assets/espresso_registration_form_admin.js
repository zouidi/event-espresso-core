jQuery(document).ready(function($) {

	$('#post-body').on('change', '#QST_type', function(event){
		espresso_reg_forms_show_or_hide_question_options();
	});

	$('#post-body').on('click', '#new-question-option', function(){
		espresso_reg_forms_add_option();
	});

	$('#post-body').on('click', '.remove-option', function(){
		espresso_reg_forms_trash_option(this);
	});

	$('#post-body').on('click', '#QST_admin_only', function() {
		espresso_maybe_switch_required(this);
	});

	$('#post-body').on('keydown', '.question-options-table input', function(e) {
		var keyPressed = e.which;
		if ( keyPressed === 13 ) { //enter key
			e.preventDefault();
			e.stopPropagation();
			espresso_reg_forms_add_option();
		}
	});

	espresso_reg_forms_show_or_hide_question_options();

	/** sortable options **/
	//$('.question-options-table').sortable({
	//	cursor: 'move',
	//	items: '.ee-options-sortable',
	//	update: function(event,ui) {
	//		espresso_update_option_order();
	//	}
	//});



	var $active_inputs_list = $( '#ee-reg-form-editor-active-form-inputs-ul' );
	$active_inputs_list.on( 'click', '.ee-reg-form-input-settings-tab-js', function ( e ) {
			e.preventDefault();
			var $content_tab = $( this ).attr( 'href' );
			//console.log( JSON.stringify( '$content_tab: ' + $content_tab, null, 4 ) );
			//first set all content as hidden and other nav tabs as not active
			$( '.ee-reg-form-input-settings-tab-panel-dv' ).hide();
			$( '.ee-reg-form-input-settings-tab-js' ).removeClass( 'ee-reg-form-input-settings-tab-active' );
			//set new active tab
			$( this ).addClass( 'ee-reg-form-input-settings-tab-active' );
			$( $content_tab ).show();
		}
	);

	$active_inputs_list.sortable({
		connectWith : ".ee-reg-form-editor-active-form-inputs-li",
		handle : ".ee-form-input-control-sort",
		placeholder : "ee-reg-form-editor-form-new-input-dv ee-droppable-active",
		revert : true,
		receive : function ( event, ui ) {
			var $inputForm = getInputForm( $( ui.item ) );
			$( this ).find( '.draggable' ).replaceWith( $inputForm );
		}
	}).on( 'click', '.ee-delete-form-input', function () {
		if ( confirm( "The form input will be permanently deleted and cannot be recovered. Are you sure?" ) ) {
			$( this )
				.closest( '.ee-reg-form-editor-active-form-inputs-li' )
				.remove();
		}
		var listItems = $( "#ee-reg-form-editor-active-form-inputs-ul" ).find( 'li' ).length;
		//console.log( JSON.stringify( 'listItems: ' + listItems, null, 4 ) );
		if ( listItems < 1 ) {
			$( '.droppable' ).fadeIn();
		}
	}).on( 'click', '.ee-config-form-input', function () {
		// find the settings section
		var $config = $( this )
			.closest( '.ee-reg-form-editor-active-form-inputs-li' )
			.find( '.ee-new-form-input-settings-dv' );
		// close all other settings panels except this one
		$( '.ee-new-form-input-settings-dv' )
			.not( $config )
			.slideUp( 250 );
		// find all of the settings tabs, remove active status, then re-apply to first tab
		$config
			.find( '.ee-reg-form-input-settings-tab-ul .ee-reg-form-input-settings-tab-js' )
			.removeClass( 'ee-reg-form-input-settings-tab-active' )
			.first()
			.addClass( 'ee-reg-form-input-settings-tab-active' );
		// hide all tab panels, then make first visible again
		$config.find( '.ee-reg-form-input-settings-tab-panel-dv' ).hide().first().show();
		// finally... display this input's settings
		$config.slideToggle( 250 );
	}).on( 'change', '.ee-reg-form-label-text-js', function () {
		// if editing a form's label, find the target id
		var $target_id = $( this ).data('target');
		// and the associated label for that input
		var $target = $( "label[for='" + $target_id + "']")
		$( $target ).text( $( this ).val() );
	});

	$( ".draggable" ).draggable({
		connectToSortable : "#ee-reg-form-editor-active-form-inputs-ul",
		cursor : "move",
		helper : "clone",
		revert : "invalid"
	});

	$( ".droppable" ).droppable({
		hoverClass :  "ee-droppable-active",
		drop : function ( event, ui ) {
			var $inputForm = getInputForm( ui.draggable );
			$( '#ee-reg-form-editor-active-form-inputs-ul' ).append( $inputForm );
			$( this ).hide();
		}
	});

	function getInputForm( $draggableHelper ) {
		// get formInput target ID from helper and clone that form
		var $inputForm = $( '#' + $draggableHelper.data( 'form_input' ) ).clone();
		// generate timestamp for adding to IDs and form input attributes to make them unique
		var timestamp = "-" + new Date().getTime();
		$inputForm.attr( 'id', $inputForm.attr( 'id' ) + timestamp );
		// gather ALL form inputs
		//var $inputFormInputs = $inputForm.find( ':input' );
		 // and loop through them
		//$inputFormInputs.each( function () {
		$inputForm.find( ':input' ).each( function () {
			// and add timestamp to names and IDs in place of "_clone"
			$( this ).attr( 'id', $( this ).attr( 'id' ).replace( '_clone', timestamp ) );
			$( this ).attr( 'name', $( this ).attr( 'name' ).replace( '_clone', timestamp ) );
			//if ( $( this ).hasData( 'target' ) ) {
			if ( $( this ).attr( 'data-target' ) ) {
				$( this ).data( 'target', $( this ).data( 'target' ).replace( '_clone', timestamp ) );
			}
		});
		// now do the same for all of the input labels
		var $inputFormLabels = $inputForm.find( 'label' );
		// and loop through them
		$inputFormLabels.each( function () {
			// and add timestamp to "for" and IDs in place of "_clone"
			$( this ).attr( 'id', $( this ).attr( 'id' ).replace( '_clone', timestamp ) );
			$( this ).attr( 'for', $( this ).attr( 'for' ).replace( '_clone', timestamp ) );
		});
		// and the tabs
		var $inputFormTabs = $inputForm.find( '.ee-reg-form-input-settings-tab-js' );
		$inputFormTabs.each(
			function () {
				$( this ).attr( 'href', $( this ).attr( 'href' ).replace( '_clone', timestamp ) );
			}
		);
		// and the tab panels
		var $inputFormTabPanels = $inputForm.find( '.ee-reg-form-input-settings-tab-panel-dv' );
		$inputFormTabPanels.each(
			function () {
				$( this ).attr( 'id', $( this ).attr( 'id' ).replace( '_clone', timestamp ) );
			}
		);
		// make the new form visible
		return $inputForm.show();
	}



});




function espresso_update_option_order() {
	allOptions = jQuery( '.question-options-table tr.ee-options-sortable' );
	allOptions.each( function(i) {
		jQuery('.QSO_order', this).val(i);
	});
	return;
}



function espresso_reg_forms_show_or_hide_question_options(){
	var val=jQuery('#QST_type').val();
	if ( val === 'RADIO_BTN' || val === 'CHECKBOX' || val === 'DROPDOWN' ){
		jQuery('#question_options').show();
		espresso_reg_forms_show_option_desc();
	}else{
		jQuery('#question_options').hide();
	}
	if ( val === 'TEXT' || val === 'TEXTAREA' || val === 'HTML_TEXTAREA' ){
		jQuery('#text_input_question_options').show();
	}else{
		jQuery('#text_input_question_options').hide();
	}
}



function espresso_reg_forms_add_option(){
	var count=jQuery('#question_options_count').val();
	count++;
	var sampleRow=jQuery('#question_options tbody tr:first-child');
	var newRow=sampleRow.clone(true);
	var newRowName=newRow.find('.option-value');
	var newRowValue=newRow.find('.option-desc');
	var newRowOrder = newRow.find('.QSO_order');
	var name=newRowName.attr('name');
	newRowName.attr('name',name.replace("xxcountxx",count));
	var value=newRowValue.attr('name');
	newRowValue.attr('name', value.replace("xxcountxx",count));
	var order=newRowOrder.attr('name');
	newRowOrder.attr('name', order.replace("xxcountxx",count));
	newRowOrder.val(count);
	newRow.removeClass('sample');
	newRow.addClass('ee-options-sortable');
	jQuery('#question_options tr:last').after(newRow);
	//add new count to dom.
	jQuery('#question_options_count').val(count);
	newRowName.focus();
}

function espresso_reg_forms_show_option_desc(){
	jQuery('.option-desc-cell').show();
	jQuery('.option-desc-header').show();
	jQuery('.option-value-header').css('width', '45%');
	jQuery('.option-value-cell').css('width','45%');
	/** focus on value field **/
	jQuery('.option-value').focus();
}


function espresso_maybe_switch_required(item) {
	var admin_only = jQuery(item).prop('checked');
	if ( admin_only ) {
		jQuery('#QST_required').val('0');
		jQuery('#QST_required').prop('disabled', true);
		jQuery('#required_toggled_on').show();
		jQuery('#required_toggled_off').hide();
		return;
	} else {
		jQuery('#QST_required').prop('disabled', false);
		jQuery('#required_toggled_on').hide();
		jQuery('#required_toggled_off').show();
		return;
	}
}



function espresso_reg_forms_trash_option(item){
	jQuery(item).parents('.question-option').remove();
}
