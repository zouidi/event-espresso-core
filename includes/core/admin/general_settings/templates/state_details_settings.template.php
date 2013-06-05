	<table id="general-setting-country-states-tbl" class="form-table">
		<tbody>
			<tr>
				<th><?php _e( 'Code', 'event_espresso' );?></th>
				<th><?php _e( 'Name', 'event_espresso' );?></th>
				<th><?php _e( 'Active', 'event_espresso' );?></th>
				<th></th>
			</tr>
		<?php 
		//printr( $states, '$states  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
		if ( $states ) {
			foreach ( $states as $STA_ID => $state ) { 
		?>
			<tr id="state-<?php echo $STA_ID; ?>-tr">
			<?php 
				foreach ( $state['inputs'] as $ID => $input ) { 
					if ( $ID != 'STA_ID' && $ID != 'CNT_ISO' ) {
						echo EE_Form_Fields::generate_form_input( $input ); 
					}				
				 } 
			 ?>
				<td class="delete-state-td">
					<a id="delete-state-<?php echo $STA_ID; ?>-lnk" class="delete-state-lnk" rel="<?php echo $STA_ID; ?>" title="Delete State #<?php echo $STA_ID; ?>?" href="<?php echo $state['delete_state_url']; ?>">
						<img width="16" height="16" style="margin-top:3px;" src="<?php echo EVENT_ESPRESSO_PLUGINFULLURL;?>/images/trash-small-16x16.png" alt="delete state">
					</a>
				</td>
			</tr>
		<?php 
			}
		?>
			<tr>
				<td></td><td></td><td><br/><input id="country_settings_save3" class="button-primary save" type="submit" name="save" value="<?php _e('Save States / Provinces', 'event_espresso'); ?>"></td><td></td>
			</tr>
		
	<?php
		} 
	?>
			<tr>
				<td colspan="4"><h4><?php _e( 'Add New State / Province', 'event_espresso' );?></h4></td>
			</tr>
		
			<tr>
				<td class="general-settings-country-state-input-td">
					<label for="STA_abbrev_XXX"><?php _e( 'Code', 'event_espresso' );?></label>
					<input id="STA_abbrev-XXX" class="STA_abbrev small-text " type="text" title="" value="" name="STA_abbrev_XXX">
				</td>
				<td class="general-settings-country-state-input-td">
					<label for="STA_name_XXX"><?php _e( 'Name', 'event_espresso' );?></label>
					<input id="STA_name-XXX" class="STA_name regular-text " type="text" title="" value="" name="STA_name_XXX">
				</td>
				<td>
					<label>&nbsp;</label>
					<input  type="submit" id="add-new-state-btn" class="secondary-button button" value="<?php _e( 'Add New State / Province', 'event_espresso' );?>" />
					
				</td>
				<td class="delete-state-td">
				</td>
			</tr>
		
		</tbody>
	</table>
