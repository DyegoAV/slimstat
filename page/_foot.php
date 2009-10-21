<div id="foot"><div id="footinner">
<a href="http://wettone.com/code/slimstat">SlimStat</a> v<?php
echo SlimStat::app_version();
?> © 2009 <a href="http://pieces-and-bits.com/">Pieces &amp; Bits</a><br />
This product includes GeoLite data created by MaxMind, available from <a href="http://www.maxmind.com/">http://www.maxmind.com/</a>
</div></div>

</div><!--/container-->

<script type="text/javascript" src="_js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
<!--
function setTitleBackground() {
	if (parseInt($(document).scrollTop()) < 20) {
		$('#title').css('background-image', 'none');
	} else {
		$('#title').css('background-image', 'url(\'./_img/title.png\')');
	}
}

$(function() {
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
//-->
</script>
</body>
</html>