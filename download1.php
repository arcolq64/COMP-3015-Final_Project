<?php

	require 'includes/functions.php';
	
	$file = validateFILE1($_GET['file']); 
	if($file == '') {
		setcookie("system_message", "Downloading text<br>file failed!");
		header("Refresh:0; url=index.php");
		exit();
	} else {
		$content = file_get_contents($file);
		$str = str_replace("text/", "", $file);
		header('Content-type: text');
		header('Content-Disposition: attachment; filename="'.$str.'"');
		echo $content;
		exit();
	}
	
?>