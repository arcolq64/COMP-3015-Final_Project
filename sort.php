<?php

	require 'includes/functions.php';

	session_start();   

	if(isset($_POST['radio']))
	{
		if($_POST['radio'] == "default") {
			$_SESSION["file"] = "text/default_sort.txt";
		}
		if($_POST['radio'] == "voting") {
			$_SESSION["file"] = "text/vote_sort.txt";
		}
		if($_POST['radio'] == "username") {
			$_SESSION["file"] = "text/username_sort.txt";
		}
		header("Refresh:0; url=index.php");
		exit();
	}
				
?>