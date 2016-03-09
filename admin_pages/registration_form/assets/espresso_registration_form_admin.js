var EE_RegFormEditor;
jQuery(document).ready(function($) {

	/**
	 * @namespace EE_RegFormEditor
	 * 	@property {object} active_inputs_list  - sortable list representing the reg form inputs
	 * 	@property {object} reg_form_input_list  - hidden input for tracking what gets added to reg form
	 */
	EE_RegFormEditor = {


		active_inputs_list : $( '#ee-reg-form-editor-active-form-ul' ),
		reg_form_input_list : $( '#reg_form-input_list' ),


		/**
		 * @function initialize
		 */
		initialize : function() {
			if ( typeof eei18n !== 'undefined' ) {
				// reset input list
				EE_RegFormEditor.reg_form_input_list.val('');
				EE_RegFormEditor.setListenerForSettingsTab();
				EE_RegFormEditor.setListenerForSubmitButton();
				EE_RegFormEditor.initializeDraggableItems();
				EE_RegFormEditor.initializeDroppableItems();
				EE_RegFormEditor.initializeActiveInputsSortableList();
				EE_RegFormEditor.initializeInputOptionsSortableList();
			}
		},



		/**
		 * @function setListenerForSettingsTab
		 */
		setListenerForSettingsTab : function() {
			EE_RegFormEditor.active_inputs_list.on(
				'click', '.ee-reg-form-input-settings-tab-js',
				function ( e ) {
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
		},



		/**
		 * @function setListenerForSubmitButton
		 */
		setListenerForSubmitButton : function() {
			$( '#insert_question_group_event_form' ).submit(
				function () {
					EE_RegFormEditor.getFormInputListOrder();
				}
			);
		},



		/**
		 * @function initializeDraggableItems
		 */
		initializeDraggableItems : function() {
			$( ".draggable" ).draggable(
				{
					connectToSortable : "#ee-reg-form-editor-active-form-ul",
					cursor :            "move",
					helper :            "clone",
					revert :            "invalid"
				}
			);
		},



		/**
		 * @function initializeDroppableItems
		 */
		initializeDroppableItems : function() {
			$( ".droppable" ).droppable(
				{
					hoverClass : "ee-droppable-active",
					drop : function ( event, ui ) {
						var $inputForm = EE_RegFormEditor.getInputForm( ui.draggable );
						$( '#ee-reg-form-editor-active-form-ul' ).append( $inputForm );
						$( this ).hide();
					}
				}
			);
		},



		/**
		 * @function functionName
		 */
		initializeActiveInputsSortableList : function() {
			EE_RegFormEditor.active_inputs_list.sortable(
				{
					connectWith : ".ee-reg-form-editor-active-form-li",
					handle :      ".ee-form-input-control-sort",
					placeholder : "ee-reg-form-editor-form-new-input-dv ee-droppable-active",
					revert : 	  true,

					receive : function ( event, ui ) {
						var $inputForm = EE_RegFormEditor.getInputForm( $( ui.item ) );
						$( this ).find( '.draggable' ).replaceWith( $inputForm );
					},

					stop : function () {
						EE_RegFormEditor.getFormInputListOrder();
					}
				}
			).on(
				'click', '.ee-delete-form-input', function () {
					EE_RegFormEditor.deleteActiveFormInput( $( this ) );
				}
			).on(
				'click', '.ee-config-form-input', function () {
					EE_RegFormEditor.displayActiveFormInputSettings( $( this ) );
				}
			).on(
				'change', '.ee-reg-form-label-text-js', function () {
					EE_RegFormEditor.changeActiveFormInputLabel( $( this ) );
				}
			).on(
				'change', '.ee-reg-form-option-label-text-js', function () {
					EE_RegFormEditor.changeActiveFormInputOptionLabel( $( this ) );
				}
			).on(
				'click', '.ee-input-option-add', function () {
					EE_RegFormEditor.addNewInputOption( $( this ) );
				}
			).on(
				'click', '.ee-input-option-delete ', function () {
					EE_RegFormEditor.deleteInputOption( $( this ) );
				}
			).on(
				'keydown', ':input', function ( event ) {
					EE_RegFormEditor.blockFormSubmissionOnEnterKeyPress( event );
				}
			);

		},



		/**
		 * @function initializeInputOptionsSortableList
		 */
		initializeInputOptionsSortableList : function() {
			$( '.ee-input-options-table-body' ).sortable(
				{
					containment : 'parent',
					cursor :      "move",
					items :       '> .ee-input-option-sortable-row',
					handle :      ".ee-input-option-sort"
				}
			);
		},



		/**
		 * @function deleteActiveFormInput
		 * @param {object} $delete_btn
		 */
		deleteActiveFormInput : function( $delete_btn ) {
			if ( confirm( "The form input will be permanently deleted and cannot be recovered. Are you sure?" ) ) {
				$delete_btn
					.closest( '.ee-reg-form-editor-active-form-li' )
					.remove();
			}
			var listItems = $( "#ee-reg-form-editor-active-form-ul" ).find( 'li' ).length;
			//console.log( JSON.stringify( 'listItems: ' + listItems, null, 4 ) );
			if ( listItems < 1 ) {
				$( '.droppable' ).fadeIn();
			}
		},



		/**
		 * @function displayActiveFormInputSettings
		 * @param {object} $config_btn
		 */
		displayActiveFormInputSettings : function( $config_btn ) {
			// find the settings section
			var $config = $config_btn
				.closest( '.ee-reg-form-editor-active-form-li' )
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
		},



		/**
		 * @function changeActiveFormInputLabel
		 * @param {object} $label_input
		 */
		changeActiveFormInputLabel : function( $label_input ) {
			// if editing a form's label, find the target id
			var $target_id = '#' + $label_input.data( 'target' );
			// and the associated label for that input
			$( $target_id ).text( $label_input.val() );
		},



		/**
		 * @function changeActiveFormInputOptionLabel
		 * @param {object} $option_label_input
		 */
		changeActiveFormInputOptionLabel : function( $option_label_input ) {
			// if editing a form's label, find the target id
			var $target_id = '#' + $option_label_input.data( 'target' ) + ' span';
			// and the associated label for that input
			$( $target_id ).text( $option_label_input.val() );
		},



		/**
		 * @function functionName
		 * @param {object} $addOptionButton
		 */
		addNewInputOption : function( $addOptionButton ) {
			var $input_order = $( '#ee_new_input_option_order' );
			var $this_row    = $addOptionButton.closest( 'tr' );
			$this_row
				.siblings( '.ee-input-option-new-row' )
				.clone()
				.insertAfter( $this_row )
				.removeClass( 'ee-input-option-new-row' )
				.show()
				.find( ':input' ).each(
				function () {
					//console.log( JSON.stringify( '$( this ).attr(id): ' + $( this ).attr( 'id' ), null, 4 ) );
					$( this ).attr( 'id', $( this ).attr( 'id' ).replace( 'order', $input_order.val() ) );
					//console.log( JSON.stringify( '$( this ).attr(id): ' + $( this ).attr( 'id' ), null, 4 ) );
					$( this ).attr( 'name', $( this ).attr( 'name' ).replace( 'order', $input_order.val() ) );
				}
			);
			var new_order = parseInt( $input_order.val() ) + 1;
			$input_order.val( new_order ).attr( 'value', new_order );
		},



		/**
		 * @function deleteInputOption
		 * @param {object} $deleteOptionButton
		 */
		deleteInputOption : function( $deleteOptionButton ) {
			var $table_body = $deleteOptionButton.closest( 'tbody' );
			var $this_row   = $deleteOptionButton.closest( 'tr' );
			//console.log( JSON.stringify( '$table_body.find( tr ).length: ' + $table_body.find( 'tr' ).length, null, 4 ) );
			if ( $table_body.find( 'tr' ).length <= 3 ) {
				$table_body
					.find( '.ee-input-option-new-row' )
					.clone()
					.insertAfter( $this_row )
					.removeClass( 'ee-input-option-new-row' )
					.show();
			}
			$this_row.remove();
		},



		/**
		 * @function getInputForm
		 * @param {object} $draggableHelper
		 */
		getInputForm : function( $draggableHelper ) {
			// get formInput target ID from helper, clone that form, and make the new form visible
			var $inputForm = $( '#' + $draggableHelper.data( 'form_input' ) ).clone().show();
			// generate timestamp for adding to IDs and form input attributes to make them unique
			var timestamp  = new Date().getTime();
			// add timestamp to this form's id
			$inputForm.attr( 'id', $inputForm.attr( 'id' ) + "-" + timestamp );
			// gather ALL form inputs, loop through them, and edit attributes
			$inputForm.find( ':input' ).each(
				function () {
					EE_RegFormEditor.editInputAttributes( $( this ), timestamp );
				}
			);
			// now do the same for some of the input div containers
			$inputForm.find( '.ee-new-form-input-dv' ).each(
				function () {
					// and add timestamp to "for" and IDs in place of "clone"
					$( this ).attr( 'id', $( this ).attr( 'id' ).replace( 'clone', timestamp ) );
				}
			);
			// now do the same for some of the input div containers
			$inputForm.find( '.ee-new-form-input-dv' ).find( 'div' ).each(
				function () {
					// and add timestamp to "for" and IDs in place of "clone"
					$( this ).attr( 'id', $( this ).attr( 'id' ).replace( 'clone', timestamp ) );
				}
			);
			// now do the same for all of the input labels
			$inputForm.find( 'label' ).each(
				function () {
					// and add timestamp to "for" and IDs in place of "clone"
					$( this ).attr( 'id', $( this ).attr( 'id' ).replace( 'clone', timestamp ) );
					$( this ).attr( 'for', $( this ).attr( 'for' ).replace( 'clone', timestamp ) );
				}
			);
			// and the tabs
			$inputForm.find( '.ee-reg-form-input-settings-tab-js' ).each(
				function () {
					$( this ).attr( 'href', $( this ).attr( 'href' ).replace( 'clone', timestamp ) );
				}
			);
			// and the tab panels
			$inputForm.find( '.ee-reg-form-input-settings-tab-panel-dv' ).each(
				function () {
					$( this ).attr( 'id', $( this ).attr( 'id' ).replace( 'clone', timestamp ) );
				}
			);
			return $inputForm;
		},



		/**
		 * @function functionName
		 * @param {object} $formInput
		 * @param {number} timestamp
		 */
		editInputAttributes : function( $formInput, timestamp ) {
			// and add timestamp to names and IDs in place of "clone"
			$formInput.attr( 'id', $formInput.attr( 'id' ).replace( 'clone', timestamp ) );
			if ( $formInput.attr( 'name' ) ) {
				$formInput.attr( 'name', $formInput.attr( 'name' ).replace( 'clone', timestamp ) );
			}
			var target = null;
			if ( $formInput.hasClass( 'ee-reg-form-label-text-js' ) ) {
				target = $formInput.data( 'target' ).replace( 'clone', timestamp );
				$formInput.data( 'target', target );
				$formInput.attr( 'data-target', target );
			}
			if ( $formInput.hasClass( 'ee-reg-form-option-label-text-js' ) ) {
				target = $formInput.data( 'target' ).replace( 'clone', timestamp );
				$formInput.data( 'target', target );
				$formInput.attr( 'data-target', target );
			}
		},



		/**
		 * @function getFormInputListOrder
		 */
		getFormInputListOrder : function () {
			var $input_list = EE_RegFormEditor.active_inputs_list.sortable( "toArray" );
			$.each(
				$input_list, function ( key, value ) {
					$input_list[ key ] = value.replace( 'ee-reg-form-editor-active-form-li-', '' );
					console.log( JSON.stringify( key + ': ' + $input_list[ key ], null, 4 ) );
				}
			);
			EE_RegFormEditor.reg_form_input_list.val( $input_list );
		},



		/**
		 * @function blockFormSubmissionOnEnterKeyPress
		 * @param {object} event
		 */
		blockFormSubmissionOnEnterKeyPress : function( event ) {
			var keyPressed = event.which;
			if ( keyPressed === 13 ) {
				event.preventDefault();
			}
		}//,



		/**
		 * @function functionName
		 * @param {object} parameter
		 */
		//functionName2 : function( parameter ) {
		//
		//}



	};


	EE_RegFormEditor.initialize()


});

//$('#post-body').on('change', '#QST_type', function(){
//	espresso_reg_forms_show_or_hide_question_options();
//}).on('click', '#new-question-option', function(){
//	espresso_reg_forms_add_option();
//}).on('click', '.remove-option', function(){
//	espresso_reg_forms_trash_option(this);
//}).on('click', '#QST_admin_only', function() {
//	espresso_maybe_switch_required(this);
//}).on('keydown', '.question-options-table input', function(e) {
//	var keyPressed = e.which;
//	if ( keyPressed === 13 ) { //enter key
//		e.preventDefault();
//		e.stopPropagation();
//		espresso_reg_forms_add_option();
//	}
//});
//
//espresso_reg_forms_show_or_hide_question_options();

//function espresso_update_option_order() {
//	$( '.question-options-table tr.ee-options-sortable' ).each(
//		function ( i ) {
//			$( '.QSO_order', this ).val( i );
//		}
//	);
//}

//function espresso_reg_forms_show_or_hide_question_options() {
//	var val = $( '#QST_type' ).val();
//	if ( val === 'RADIO_BTN' || val === 'CHECKBOX' || val === 'DROPDOWN' ) {
//		$( '#question_options' ).show();
//		espresso_reg_forms_show_option_desc();
//	} else {
//		$( '#question_options' ).hide();
//	}
//	if ( val === 'TEXT' || val === 'TEXTAREA' || val === 'HTML_TEXTAREA' ) {
//		$( '#text_input_question_options' ).show();
//	} else {
//		$( '#text_input_question_options' ).hide();
//	}
//}

//function espresso_reg_forms_add_option() {
//	var $questionOptionsCount = $( '#question_options_count' ).val();
//	var count = $questionOptionsCount.val();
//	count++;
//	var $questionOptions   = $( '#question_options' );
//	var sampleRow   = $questionOptions.find( 'tbody tr:first-child' );
//	var newRow      = sampleRow.clone( true );
//	var newRowName  = newRow.find( '.option-value' );
//	var newRowValue = newRow.find( '.option-desc' );
//	var newRowOrder = newRow.find( '.QSO_order' );
//	var name        = newRowName.attr( 'name' );
//	newRowName.attr( 'name', name.replace( "xxcountxx", count ) );
//	var value = newRowValue.attr( 'name' );
//	newRowValue.attr( 'name', value.replace( "xxcountxx", count ) );
//	var order = newRowOrder.attr( 'name' );
//	newRowOrder.attr( 'name', order.replace( "xxcountxx", count ) );
//	newRowOrder.val( count );
//	newRow.removeClass( 'sample' );
//	newRow.addClass( 'ee-options-sortable' );
//	$questionOptions.find( 'tr:last' ).after( newRow );
//	//add new count to dom.
//	$questionOptionsCount.val( count );
//	newRowName.focus();
//}

//function espresso_reg_forms_show_option_desc() {
//	$( '.option-desc-cell' ).show();
//	$( '.option-desc-header' ).show();
//	$( '.option-value-header' ).css( 'width', '45%' );
//	$( '.option-value-cell' ).css( 'width', '45%' );
//	/** focus on value field **/
//	$( '.option-value' ).focus();
//}

//function espresso_maybe_switch_required( item ) {
//	var admin_only = $( item ).prop( 'checked' );
//	if ( admin_only ) {
//		$( '#QST_required' )
//			.val( '0' )
//			.prop( 'disabled', true );
//		$( '#required_toggled_on' ).show();
//		$( '#required_toggled_off' ).hide();
//	} else {
//		$( '#QST_required' ).prop( 'disabled', false );
//		$( '#required_toggled_on' ).hide();
//		$( '#required_toggled_off' ).show();
//	}
//}

//function espresso_reg_forms_trash_option( item ) {
//	$( item ).parents( '.question-option' ).remove();
//}

/** sortable options **/
//$('.question-options-table').sortable({
//	cursor: 'move',
//	items: '.ee-options-sortable',
//	update: function(event,ui) {
//		espresso_update_option_order();
//	}
//});

