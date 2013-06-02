$(function() {
	
	var $bt_fields = $('.bt_tabs');
	var $tabs_navs = $('.bt_tabs > .bt_tabnav');
	
	// 1. Hide all tabs except for the first one (for each instance of babeltext)
	$bt_fields.each(function() {
		$(this).children('.bt_tab').not(':first').addClass('closed');
		$(this).children('.bt_tabnav').children('li:first').children('a').addClass('selected');
	});
	
	// 2. Tab Toggle Function
	function toggle_tabs(e) {
		var tab_id = '#' + $(this).attr('href');
		var $open_nav = $(this).addClass('selected');
		var $open_tab = $(this).parents('.bt_tabs').find(tab_id).removeClass('closed');
		$open_nav.parent().siblings('li').children('a').removeClass('selected');
		$open_tab.siblings('.bt_tab').addClass('closed');
		e.preventDefault();
	}
	
	// 3. Bind the action to the tab navigation
	$tabs_navs.on('click', 'a', toggle_tabs);
	
});