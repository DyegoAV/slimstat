<?php

/*
 * SlimStat: simple web analytics
 * Copyright (C) 2009 Pieces & Bits Limited
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

page_head();

error_reporting( E_ALL );

echo '<h2 id="title" class="grid16">Paths taken by recent visitors</h2>'."\n";

// main

echo '<div id="main" class="grid16">';

// side

echo '<div id="side" class="grid4"><div id="sideinner" class="grid3 first"></div></div>'."\n";

echo '<div id="content" class="grid12"><div class="grid12">';

// get requests

$query = 'SELECT * FROM `'.SlimStat::esc( $config->db_database ).'`.`'.SlimStat::esc( $config->tbl_visits ).'`';
$query .= ' ORDER BY `start_yr` DESC, `start_mo` DESC, `start_dy` DESC, `start_hr` DESC, `start_mi` DESC LIMIT 50';

$visits = array();
if ( $result = mysql_query( $query, $connection ) ) {
	while ( $assoc = mysql_fetch_assoc( $result ) ) {
		$visits[] = $assoc;
	}
}

// draw table

echo '<table><thead>'."\n";
echo '<tr><th class="first" style="width:230px">'.$i18n->title( 'remote_ip' ).'</th>'."\n";
echo '<th class="center" style="width:75px">When</th>'."\n";
echo '<th class="center" style="width:120px">'.$i18n->title( 'browser' ).'</th>'."\n";
echo '<th class="center" style="width:145px">'.$i18n->title( 'platform' ).'</th>'."\n";
echo '<th class="center last" style="width:100px">'.$i18n->title( 'country' ).'</th></tr></thead></table>'."\n";

echo '<div class="tbody"><table><tbody>'."\n";

foreach ( $visits as $visit ) {
	$start_ts = -1;
	$end_ts = -1;
	
	$hits = explode( "\n", $visit['resource'] );
	foreach ( $hits as $hit ) {
		if ( $hit == '' ) {
			continue;
		}
		
		foreach ( $hits as $hit ) {
			if ( $hit == '' ) {
				continue;
			}
			
			@list( $yr, $mo, $dy, $hr, $mi, $sc, $resource, $title ) = explode( ' ', $hit, 8 );
			$local_time = SlimStat::local_time_fields( array( 'yr' => $yr, 'mo' => $mo, 'dy' => $dy, 'hr' => $hr, 'mi' => $mi, 'sc' => $sc ) );
			$dt = mktime( $local_time['hr'], $local_time['mi'], $local_time['sc'], $local_time['mo'], $local_time['dy'], $local_time['yr'] );
			
			if ( $start_ts == -1 ) {
				$start_ts = $dt;
			}
			if ( $dt > $end_ts ) {
				$end_ts = $dt;
			}
		}
	}
	
	$start_ts = date( 'H:i', $start_ts );
	$end_ts = date( 'H:i', $end_ts );
	
	echo '<tr>'."\n".'<td class="first accent" style="width:230px; max-width:230px">';
	echo '<a class="external" title="'.str_replace( '%i', $visit['remote_ip'], $config->whoisurl ).'" href="'.str_replace( '%i', $visit['remote_ip'], $config->whoisurl ).'" rel="nofollow">&rarr;</a> ';
	echo htmlspecialchars( $visit['remote_ip'] ).'</td>'."\n";
	echo '<td class="center accent" style="width:75px; max-width:75px">';
	echo ( ( $start_ts == $end_ts ) ? $start_ts : $start_ts.'–'.$end_ts );
	echo '</td>'."\n";
	echo '<td class="center accent" style="width:120px; max-width:120px">'.htmlspecialchars( $visit['browser'] );
	if ( $visit['version'] != $i18n->indeterminable ) {
		echo ' '.htmlspecialchars( $visit['version'] );
	}
	echo '</td>'."\n".'<td class="center accent" style="width:145px; max-width:145px">'.htmlspecialchars( $visit['platform'] ).'</td>'."\n";
	echo '<td class="center last accent" style="width:100px; max-width:100px">'.htmlspecialchars( $i18n->label( 'country', $visit['country'] ) ).'</td></tr>'."\n";
	
	$prev_ts = '';
	foreach ( $hits as $hit ) {
		if ( $hit == '' ) {
			continue;
		}
		
		@list( $yr, $mo, $dy, $hr, $mi, $sc, $resource, $title ) = explode( ' ', $hit, 8 );
		$local_time = SlimStat::local_time_fields( array( 'yr' => $yr, 'mo' => $mo, 'dy' => $dy, 'hr' => $hr, 'mi' => $mi, 'sc' => $sc ) );
		$dt = mktime( $local_time['hr'], $local_time['mi'], $local_time['sc'], $local_time['mo'], $local_time['dy'], $local_time['yr'] );
		
		echo '<tr>'."\n".'<td class="first"><span class="text truncate">';
		echo '<a href="'.$resource.'" class="external"';
		echo ' title="'.htmlspecialchars( $resource ).'">&rarr;</a>';
		echo '<a href="./'.filter_url( array( 'resource' => $resource ) );
		echo '" title="'.htmlspecialchars( $resource ).'">';
		if ( $title != '' ) {
			echo htmlspecialchars( $title );
		} else {
			echo htmlspecialchars( $resource );
		}
		echo '</a></span></td>'."\n";
		$dt_label = date( 'H:i', $dt );
		if ( ( $prev_ts == '' && $dt_label != $start_ts ) || ( $prev_ts != '' && $dt_label != $prev_ts ) ) {
			echo '<td class="center">'.$dt_label.'</td>'."\n";
		} else {
			echo '<td class="center">&nbsp;</td>'."\n";
		}
		
		if ( $prev_ts == '' && $visit['referrer'] != '' ) {
			echo '<td colspan="3" class="right last">';
			echo '<a href="'.$visit['referrer'].'" class="external" rel="nofollow"';
			echo ' title="'.htmlspecialchars( $visit['referrer'] ).'">&rarr;</a>';
			echo '<span class="text truncate">';
			if ( $visit['search_terms'] != '' ) {
				echo '“<a href="./'.filter_url( array( 'search_terms' => $visit['search_terms'] ) );
				echo '" title="'.htmlspecialchars( $visit['search_terms'] ).'"';
				echo '>'.htmlspecialchars( $visit['search_terms'] ).'</a>”';
			} else {
				echo '<a href="./'.filter_url( array( 'domain' => $visit['domain'] ) );
				echo '" title="'.htmlspecialchars( $visit['domain'] ).'"';
				echo '>'.htmlspecialchars( $visit['domain'] ).'</a>';
			}
			echo '</span></td>'."\n";
		} else {
			echo '<td colspan="3" class="last">&nbsp;</td>'."\n";
		}
		echo '</tr>'."\n";
		
		$prev_ts = $dt_label;
	}
}

echo '</tbody></table></div>'."\n";

echo '</div></div></div>'."\n";

?>
<script type="text/javascript">
function resizePathsTbody() {
	var viewportHeight = window.innerHeight ? window.innerHeight : $(window).height();
	var footerHeight = $('#foot').height();
	var tbodyOffset = $('.tbody').offset().top;
	$('.tbody').css('height', Math.max(198, viewportHeight - tbodyOffset - footerHeight - 42) + 'px');
}

$(function() {
	resizePathsTbody();
});
$(window).resize(function() {
	resizePathsTbody();
});
</script>
<?php

page_foot();
