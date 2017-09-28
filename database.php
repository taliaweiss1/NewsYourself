<?php

$mysqli = new mysqli('localhost', 'wustl_inst', 'wustl_pass', 'NewsYourself');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>