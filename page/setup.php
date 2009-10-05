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

$steps = array(
	'Connect to database',
	'Create tables',
	'Finish'
);

function check_table_exists( $_table ) {
	global $config, $connection;
	
	$query = 'DESCRIBE `'.SlimStat::esc( $config->db_database ).'`.`'.SlimStat::esc( $_table ).'`';
	$result = @mysql_query( $query, $connection );
	return ( @mysql_num_rows( $result ) > 0 );
}

$step = -1;

$config_file_lines = file( realpath( dirname( dirname( __FILE__ ) ).'/_lib/config.php' ) );

page_head();

?>
<h2 id="title" class="grid16">Setting up SlimStat</h2>

<div id="main" class="grid16">

<div id="side" class="grid4"><div id="sideinner" class="grid3 first">
<p><?php echo sizeof( $steps ); ?> simple steps to complete your SlimStat installation.</p>

<ol>
<?php
foreach ( $steps as $step_name ) {
	echo '<li>'.htmlspecialchars( $step_name ).'</li>'."\n";
}
?>
</ol>
</div>
</div>

<div id="content" class="grid12 first">

<div class="grid12 first">
<?php

function step_header() {
	global $step, $steps;
	?>
	<h3>Step <?php echo $step + 1; ?> of <?php echo sizeof( $steps ); ?>: <?php echo $steps[$step]; ?></h3>
	<?php
}

//////////////////////////////////////////////////////////// Connect to database

if ( $step == -1 ) {
	$connection = SlimStat::connect();
	if ( !$connection ) {
		$step = array_search( 'Connect to database', $steps );
		step_header();
		?>
		<p>SlimStat needs to be able to connect to your MySQL database.</p>
		<p>You can use SlimStat with an existing database, or create a new database if you prefer.</p>
		<p>In <tt>_lib/config.php</tt>, edit these variables:</p>
		<pre><?php
		foreach ( $config_file_lines as $config_file_line ) {
			if ( strstr( $config_file_line, 'var $db_' ) ) {
				echo htmlspecialchars( trim( $config_file_line ) )."\n";
			}
		}
		?></pre>
		<p>When they match your connection settings, click the button below.</p>
		<?php
	}
}

////////////////////////////////////////////////////////////////// Create tables

if ( $step == -1 ) {
	$hidden_field_before = 'do_create';
	$hidden_field_after = 'done_create';
	$hits_table_exists = check_table_exists( $config->tbl_hits );
	$visits_table_exists = check_table_exists( $config->tbl_visits );
	$cache_table_exists = check_table_exists( $config->tbl_cache );
	
	if ( !$hits_table_exists || !$visits_table_exists || !$cache_table_exists ) {
		$step = array_search( 'Create tables', $steps );
		step_header();
		
		if ( !isset( $_POST[$hidden_field_before] ) ) {
			
			?>
			<p>SlimStat needs to create three database tables to store its data. They will be called 
			<tt><?php echo $config->tbl_hits; ?></tt>, <tt><?php echo $config->tbl_visits; ?></tt> and <tt><?php echo $config->tbl_cache; ?></tt>. 
			To change this, edit these lines in <tt>_lib/config.php</tt>:</p>
			<pre><?php
			foreach ( $config_file_lines as $config_file_line ) {
				if ( strstr( $config_file_line, 'var $tbl_' ) ) {
					echo htmlspecialchars( trim( $config_file_line ) )."\n";
				}
			}
			?></pre>
			<p>Click the button below to create the tables.</p>
			<?php
			$hidden_field = $hidden_field_before;
			
		} else {
			
			// try to create the tables

			$hits_create_query = 'CREATE TABLE `'.SlimStat::esc( $config->db_database ).'`.`'.SlimStat::esc( $config->tbl_hits ).'` ('.
			"\n\t".'`remote_ip` varchar(255) collate utf8_bin default NULL,	'.
			"\n\t".'`country` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`language` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`domain` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`referrer` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`search_terms` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`resource` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`title` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`platform` varchar(50) collate utf8_bin default NULL,'.
			"\n\t".'`browser` varchar(50) collate utf8_bin default NULL,'.
			"\n\t".'`version` varchar(15) collate utf8_bin default NULL,'.
			"\n\t".'`resolution` varchar(10) collate utf8_bin default NULL,'.
			"\n\t".'`mi` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`hr` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`dy` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`mo` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`yr` smallint(5) unsigned NOT NULL default \'0\','.
			"\n\t".'`hits` int(10) unsigned NOT NULL default \'0\','.
			"\n\t".'KEY `ts` (`yr`,`mo`,`dy`,`hr`,`mi`)'.
			"\n".') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin';

			$visits_create_query = 'CREATE TABLE `'.SlimStat::esc( $config->db_database ).'`.`'.SlimStat::esc( $config->tbl_visits ).'` ('.
			"\n\t".'`remote_ip` varchar(15) collate utf8_bin default NULL,'.
			"\n\t".'`country` char(2) collate utf8_bin default NULL,'.
			"\n\t".'`language` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`domain` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`referrer` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`search_terms` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`start_resource` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`end_resource` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`user_agent` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`platform` varchar(50) collate utf8_bin default NULL,'.
			"\n\t".'`browser` varchar(50) collate utf8_bin default NULL,'.
			"\n\t".'`version` varchar(15) collate utf8_bin default NULL,'.
			"\n\t".'`resolution` varchar(10) collate utf8_bin default NULL,'.
			"\n\t".'`start_mi` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`start_hr` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`start_dy` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`start_mo` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`start_yr` smallint(5) unsigned NOT NULL default \'0\','.
			"\n\t".'`end_mi` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`end_hr` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`end_dy` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`end_mo` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`end_yr` smallint(5) unsigned NOT NULL default \'0\','.
			"\n\t".'`hits` int(10) unsigned NOT NULL default \'0\','.
			"\n\t".'`start_ts` int(10) unsigned NOT NULL default \'0\','.
			"\n\t".'`end_ts` int(10) unsigned NOT NULL default \'0\','.
			"\n\t".'`duration` int(11) NOT NULL default \'0\','.
			"\n\t".'`resource` text collate utf8_bin,'.
			"\n\t".'KEY `start_ts` (`start_yr`,`start_mo`,`start_dy`,`start_hr`,`start_mi`),'.
			"\n\t".'KEY `end_ts` (`end_yr`,`end_mo`,`end_dy`,`end_hr`,`end_mi`)'.
			"\n".') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin';

			$cache_create_query = 'CREATE TABLE `'.SlimStat::esc( $config->db_database ).'`.`'.SlimStat::esc( $config->tbl_cache ).'` ('.
			"\n\t".'`remote_ip` varchar(15) collate utf8_bin default NULL,'.
			"\n\t".'`country` char(2) collate utf8_bin default NULL,'.
			"\n\t".'`language` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`resource` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`platform` varchar(50) collate utf8_bin default NULL,'.
			"\n\t".'`browser` varchar(50) collate utf8_bin default NULL,'.
			"\n\t".'`version` varchar(15) collate utf8_bin default NULL,'.
			"\n\t".'`resolution` varchar(10) collate utf8_bin default NULL,'.
			"\n\t".'`search_terms` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`domain` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`referrer` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`start_resource` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`end_resource` varchar(255) collate utf8_bin default NULL,'.
			"\n\t".'`hits` int(10) unsigned NOT NULL default \'0\','.
			"\n\t".'`tz` varchar(50) collate utf8_bin NOT NULL,'.
			"\n\t".'`yr` smallint(5) unsigned NOT NULL default \'0\','.
			"\n\t".'`mo` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`dy` tinyint(3) unsigned NOT NULL default \'0\','.
			"\n\t".'`app_version` varchar(10) collate utf8_bin NOT NULL,'.
			"\n\t".'`cache` longblob NOT NULL,'.
			"\n\t".'KEY `ts` (`tz`,`yr`,`mo`,`dy`)'.
			"\n".') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin';
			
			if ( !$hits_table_exists ) {
				@mysql_query( $hits_create_query, $connection );
			}
			if ( !$visits_table_exists ) {
				@mysql_query( $visits_create_query, $connection );
			}
			if ( !$cache_table_exists ) {
				@mysql_query( $cache_create_query, $connection );
			}
			
			$hits_table_exists = check_table_exists( $config->tbl_hits );
			$visits_table_exists = check_table_exists( $config->tbl_visits );
			$cache_table_exists = check_table_exists( $config->tbl_cache );
			
			if ( $hits_table_exists && $visits_table_exists && $cache_table_exists ) {
				?>
				<p>All required tables have been created. Click the button below to continue to the next step.</p>
				<?php
			} else {
				?>
				<p>SlimStat was unable to create the tables. This is most likely because the MySQL user does not have permission to create tables.</p>
				<p>You will need to create the tables yourself, by executing the following queries.</p>
				<?php
				if ( !$hits_table_exists ) {
					?>
					<p>To create the hits table:</p>
					<pre><?php echo htmlspecialchars( $hits_create_query.';' ); ?></pre>
					<?php
				}
				if ( !$visits_table_exists ) {
					?>
					<p>To create the visits table:</p>
					<pre><?php echo htmlspecialchars( $visits_create_query.';' ); ?></pre>
					<?php
				}
				if ( !$cache_table_exists ) {
					?>
					<p>To create the cache table:</p>
					<pre><?php echo htmlspecialchars( $cache_create_query.';' ); ?></pre>
					<?php
				}
			}
			
			$hidden_field = $hidden_field_before;
			
		}
	}
}

///////////////////////////////////////////////////////////////////////// Finish

if ( $step == -1 ) {
	$step = array_search( 'Finish', $steps );
	step_header();
	?>
	<p>That’s it! Remove <tt>page/setup.php</tt> from the server and click the button below to start using SlimStat.</p>
	<?php
}

///////////////////////////////////////////////////////////// 'Next step' button

if ( $step < ( sizeof( $steps ) - 1 ) ) {
	?>
	<form action="<?php echo ( isset( $_SERVER['REQUEST_URI'] ) ) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']; ?>" method="post">
	<p><?php
	if ( isset( $hidden_field ) ) {
		?><input type="hidden" name="<?php echo $hidden_field; ?>" value="1" /><?php
	}
	?><input type="submit" value="Next step" /></p>
	</form>
	<?php
} else {
	?>
	<form action="./" method="post">
	<p><input type="submit" value="Finish" /></p>
	</form>
	<?php
}

?>
</div></div>

</div>
<?php

page_foot();