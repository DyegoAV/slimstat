function positionSidebar() {
	if ($(window).scrollTop() > 120) {
		$('#side, #ajaxindicator').css({position: 'fixed', top: '-1.6666em'});
	} else {
		$('#side, #ajaxindicator').css({position: 'absolute', top: '8.3333em'});
	}
}

$(function() {
	// handle clicks on toggle links
	$('a.toggle').live('click', function() {
		var toggle = $(this);
		if (toggle.css('background-position').indexOf('-15') > -1) {
			toggle.css('background-position', '0 0');
		} else {
			toggle.css('background-position', '-15px 0');
		}
		var id = toggle.attr('id');
		if (id != '') {
			$('tr.detail_'+id).toggle();
		}
		return false;
	});
	
	// handle filters changing
	$('#filters select').live('change', function() {
		var href = './';
		var separator = '?';
		$('#filters select').each(function() {
			var sel = $(this);
			if (sel.attr('selectedIndex') > 0) {
				href += separator;
				href += sel.attr('name') + '=' + encodeURI(sel.val());
				separator = '&';
			}
		});
		$('#main').load(href + ' #main > *', function() {
			positionSidebar();
		});
	});
	
	// handle details page links being clicked
	$('#detailspage #content a[href^="./?filter_"]').live('click', function() {
		var field = $(this).closest('div.table').attr('class');
		field = field.substring(field.indexOf('filter_'));
		field = field.substring(0, field.indexOf(' '));
		
		var value = $(this).attr('href');
		value = value.substring(value.indexOf(field) + field.length + 1);
		value = decodeURI(value);
		// var value = $(this).parent().parent().attr('title');
		
		var sel = $('select[name="'+field+'"]');
		sel.val(value).trigger('change');
		
		return false;
	});
	
	// ajax activity indicator
	$('body').append('<div id="ajaxindicator"><img src="./_img/loading.gif" width="16" height="16" alt="Activity indicator" /></div>');
	$('#ajaxindicator').css({
		display: 'none',
		margin: '0 0 0 460px',
		padding: '3.5em 0 0 0',
		position: 'absolute',
		left: '50%',
		top: '0',
		width: '16px',
		'z-index': '20'
	});
	
	// handle scrolling
	$(window).scroll(function() {
		positionSidebar();
	});
	positionSidebar();

	// show/hide ajax activity indicator
	$(document).ajaxStart(function() { 
		$('#ajaxindicator').show(); 
	}).ajaxStop(function() { 
		$('#ajaxindicator').hide();
	});

});
