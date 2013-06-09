$(function() {
				
	// -----------------------------------------------
	// DOM Elements we will work with
	// -----------------------------------------------
	
	// Language table and inputs
	var $bt_sortable_table = $('table.bt_languages tbody');
	var $bt_language_keys = $('input[name=bt_language_keys]');
	var $bt_language_names = $('input[name=bt_language_names]');
	var $bt_lang_select = $('select[name=bt_lang_select]');
	var $bt_lang_add = $('input[name=bt_lang_add]');
	
	// Field input type & rows input
	var $bt_content_type = $('select[name=bt_content_type]');
	var $bt_ta_rows = $('input[name=bt_ta_rows]');
	
	// Standard EE Form Fields
	var $field_required = $('input[name=field_required]');
	var $field_submit = $('input[name=field_edit_submit]');
	
	
	// -----------------------------------------------
	// Sortable Language table
	// -----------------------------------------------
	
	// Helper which preserves the widths of cells
	
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};
	
	// Global function to sort selects alphabetically and leave blank at top and selected
	
	$.fn.sortOptions = function() {
	    $(this).each(function(){
	        var $select = $(this);
	        var op = $select.children('option');
	        var $blank = op.filter('[value=""]');
	        $blank.remove();
	        op.sort(function(a, b) {
	            return a.text > b.text ? 1 : -1;
	        })
	        return $select.empty().append(op).prepend($blank);
	    });
	}
	
	// Apply sortable plugin to table
	
	$bt_sortable_table.sortable({
		helper : fixHelper,
		axis: 'y',
		cursor: 'move',
		update: function(event, ui) {
			var reorder_lang_ids = $(this).sortable('toArray', {attribute : 'data-language_key'});
			var reorder_lang_names = $(this).sortable('toArray', {attribute : 'data-language_name'});
			set_values(reorder_lang_ids, reorder_lang_names);
		}
	}).disableSelection();
	
	// SET VALUES - Function to reorder the hidden form elements values

	function set_values(id_arr, name_arr) {
		
		// Set the value on the hidden form elements
		$bt_language_keys.val(id_arr.toString());
		$bt_language_names.val(name_arr.toString());
		
	}
	
	// REMOVE LANGUAGE - Function to remove a languages from the table
	
	function remove_language(e) {
		
		// Get the row element
		var $target = $(e.target);
		var $row = $target.parent().parent();
		var $sibling_rows = $row.siblings('tr');
		var $key = $row.data('language_key');
		var $name = $row.data('language_name');
		
		// Check if at least one language will be left
		if($sibling_rows.length > 0) {
		
			// Add the language back into the options dropdown and reorder it alphabetically
			var $new_option = $('<option value="' + $key + '">' + $name + '</option>');
			$bt_lang_select.append($new_option);
			$bt_lang_select.sortOptions();
			
			// Remove the entire row element, refresh the table and reset the values
			$row.remove();
			$bt_sortable_table.sortable('refresh');
			var reorder_lang_ids = $bt_sortable_table.sortable('toArray', {attribute : 'data-language_key'});
			var reorder_lang_names = $bt_sortable_table.sortable('toArray', {attribute : 'data-language_name'});
			set_values(reorder_lang_ids, reorder_lang_names);
		
		} else {
			
			alert(BT_LANG_ERR_ONE_ROW);
		}
		
		e.preventDefault();
		
	}
	
	// ADD LANGUAGE - Function to add langauges to the table
	
	function add_language(e) {
		
		// Get the selected option's data
		var $lang_option = $bt_lang_select.children('option').filter(':selected');
		var lang_key = $bt_lang_select.val();
		var lang_name = $lang_option.text();
		
		// Check for a valid option (not '')
		if(lang_key != '') {
			
			// Add the language key and name to the hidden fields
			var current_keys = $bt_language_keys.val().split(',');
			current_keys.push(lang_key);
			$bt_language_keys.val(current_keys.toString());
			
			var current_names = $bt_language_names.val().split(',');
			current_names.push(lang_name);
			$bt_language_names.val(current_names.toString());
			
			// Create and add the new table row
			var $new_row = $('<tr data-language_key="' + lang_key + '" data-language_name="' + lang_name + '"></tr>');
			$new_row.append('<td>' + lang_name + '</td>');
			$new_row.append('<td class="bt_cell_dir"><label><input type="radio" name="bt_' + lang_key + '_dir" value="ltr" checked="checked"> ' + BT_LANG_LTR + '</label> &nbsp; <label><input type="radio" name="bt_' + lang_key + '_dir" value="rtl"> ' + BT_LANG_RTL + '</label></td>');
			$new_row.append('<td class="bt_cell_req"><input type="checkbox" name="bt_required[]" value="' + lang_key + '"></td>');
			$new_row.append('<td class="bt_cell_del"><a href="#" class="bt_remove">Delete</a></td>');
			$bt_sortable_table.append($new_row);
			
			// Remove the option from the select and reset it
			$lang_option.remove();
			$bt_lang_select[0].selectedIndex = 0;
			
			e.preventDefault();
			
		}
		
	}
	
	// Bind the add language and remove langauge functions to the controls in the table
	$bt_lang_add.on('click', add_language);
	$bt_sortable_table.on('click', 'a.bt_remove', remove_language);
	
	
	// -----------------------------------------------
	// Validation
	// -----------------------------------------------
	
	// SET REQUIRED - Function to enable and disable required languages based on if the field is required
	
	function set_required(e) {
		
		var $target = $(e.target);
		var val = $target.val();
		var $required_boxes = $bt_sortable_table.find('input[type=checkbox]');
		
		if(val == 'y') {
			$required_boxes.removeAttr("disabled");
		} else {
			$required_boxes.attr("disabled", true);
		}
		
	}
	
	// CHECK REQUIRED - Function to check if required languages need to be set
	
	function check_required(e) {
		
		var $required_boxes = $bt_sortable_table.find('input[type=checkbox]');
		var field_type = $('select#field_type').val();
		var is_required = $('#field_required_y').is(':checked');
		var required_count = 0;
		
		$required_boxes.each(function(){
			if($(this).is(':checked')) {
				required_count++;
			}
		});
		
		if( (field_type == 'babeltext') && (is_required) && (required_count == 0) ) {
			alert(BT_LANG_ERR_REQUIRED);
			e.preventDefault();
		}
		
	}
	
	// Bind the set required and check required functions to the EE 'required' field and submit button
	$field_required.on('change', set_required);
	$field_submit.on('click', check_required);
	
	
	// -----------------------------------------------
	// Content Type Rows
	// -----------------------------------------------
	
	// Hide the input rows if the type is text
	if($bt_content_type.val() == 'text') {
		$bt_ta_rows.parent().parent().hide();
	}
	
	// TOGGLE ROWS - Function to toggle the text input rows option
	
	function toggle_rows(e) {
		
		var $target = $(e.target);
		var input_type = $target.val();
		
		if(input_type == 'text') {
			$bt_ta_rows.parent().parent().hide();
		} else {
			$bt_ta_rows.parent().parent().show();
		}
		
	}
	
	// Bind the toggle rows function to the input type select
	$bt_content_type.on('change', toggle_rows);
	
});