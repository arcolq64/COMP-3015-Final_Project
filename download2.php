<?php

	require 'includes/functions.php';
	
	$json = validateFILE2($_GET['json']);
	
	if($json == '') {
		setcookie("system_message", "Downloading JSON<br>file failed!");
		header("Refresh:0; url=index.php");
		exit();
	} else {
		$content = file_get_contents($json);
		$str = str_replace("json/", "", $json);
		header('Content-type: json');
		header('Content-Disposition: attachment; filename="'.$str.'"');
		echo $content;
		exit();
	}
	
?>