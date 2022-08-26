<?php

	require 'includes/functions.php';
	
	setcookie('usercmnt', null, time() - 3600, "/");
	header("Refresh:0; url=index.php");
	exit();
	
?>