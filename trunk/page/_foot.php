<div id="foot"><div id="footinner">
<a href="http://slimstat.net/">SlimStat</a> v<?php
echo SlimStat::app_version();
?> © 2009 <a href="http://pieces-and-bits.com/">Pieces &amp; Bits</a><br />
This product includes GeoLite data created by MaxMind, available from <a href="http://www.maxmind.com/">http://www.maxmind.com/</a>
</div></div>

</div><!--/container-->

<?

if ( !$GLOBALS['is_handheld'] ) {
	echo '<script type="text/javascript" src="_js/jquery-1.3.2.min.js"></script>'."\n";
	echo '<script type="text/javascript" src="_js/screen.js"></script>'."\n";
}

?>
</body>
</html>