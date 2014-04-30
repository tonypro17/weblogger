<?php

$db = new SQLite3('temperatures.db') or die('Unable to open database');

$result = $db->query('SELECT * FROM temptable') or die('Query failed');

$row = $result->fetchArray();
while($row) {
	if(!($next = $result->fetchArray())){
		echo "$row[0] $row[1] $row[2]\n";
		break;
	}
	$row = $next;
}


?>
