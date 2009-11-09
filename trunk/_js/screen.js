var jDocument;
var jTitle;

function setTitleBackground() {
	if (parseInt(jDocument.scrollTop()) < 20) {
		jTitle.css('background-image', 'none');
	} else {
		jTitle.css('background-image', 'url(\'./_img/title.png\')');
	}
}

$(function() {
	jDocument = $(document);
	jTitle = $('#title');
	
	$('a.toggle').click(function() {
		var toggle = $(this);
		if (toggle.css('background-position').indexOf('-15') > -1) {
			toggle.css('background-position', '0 0');
		} else {
			toggle.css('background-position', '-15px 0');
		}
		var id = toggle.attr('id');
		// alert(id);
		if (id != '') {
			$('tr.detail_'+id).toggle();
		}
		return false;
	});
	
	$(window).scroll(function() {
		setTitleBackground();
	});
	setTitleBackground();
});