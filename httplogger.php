<?php
	session_start();
	$httplogger_id = -1;
	if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']!='64.135.149.145') {
		$tmp_link = mysql_connect("localhost", "root", "87ki*fdt#@") or die("Could not connect: " . mysql_error());
		mysql_select_db('astor', $tmp_link) or die ('Can\'t use foo : ' . mysql_error());

		$sql = 'INSERT INTO `httplogger` SET 
			`session_id` = "'.addslashes(isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : '~').'", 
			`ci_session` = "'.addslashes(isset($_COOKIE['ci_session']) ? $_COOKIE['ci_session'] : '~').'", 
			`s_server` = "'.addslashes(print_r($_SERVER, true)).'", 
			`s_cookie` = "'.addslashes(print_r($_COOKIE, true)).'",
			`dt` = NOW()';
		mysql_query($sql, $tmp_link);
		$httplogger_id = mysql_insert_id($tmp_link);
		mysql_close($tmp_link);
		unset($tmp_link);
	}
?>