<?php
	session_start();
	//destroy session for logging out and then takes the user back to the homepage
	session_destroy();
	header("Location: homepage.php");
?>