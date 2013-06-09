$(function() {
	
	// -----------------------------------------------
	// DOM Elements we will work with
	// -----------------------------------------------
	
	var $bt_fields = $('.bt_tabs');
	var $tabs_navs = $('.bt_tabs > .bt_tabnav');
	
	
	// -----------------------------------------------
	// Language Tabs Functionality
	// -----------------------------------------------
	
	// Hide all tabs except for the first one (for each instance of babeltext)
	$bt_fields.each(function() {
		$(this).children('.bt_tab').not(':first').addClass('closed');
		$(this).children('.bt_tabnav').children('li:first').children('a').addClass('selected');
	});
	
	// TOGGLE TABS - Function to toggle the tabs and text fields for languages
	
	function toggle_tabs(e) {
		var tab_id = '#' + $(this).attr('href');
		var $open_nav = $(this).addClass('selected');
		var $open_tab = $(this).parents('.bt_tabs').find(tab_id).removeClass('closed');
		$open_nav.parent().siblings('li').children('a').removeClass('selected');
		$open_tab.siblings('.bt_tab').addClass('closed');
		e.preventDefault();
	}
	
	// Bind the action to the tab navigation
	$tabs_navs.on('click', 'a', toggle_tabs);
	
});