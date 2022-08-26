<?php

	function checkSignUp($data)
	{
		$valid = true;

		// if any of the fields are missing, return an error
		if( trim($data['username']) == '' ||
			trim($data['comment'])  == '')
		{
			$valid = "All inputs are required.";
		}
		elseif(!preg_match('/^[a-zA-Z]{1}[a-zA-Z0-9]{1,9}$/', trim($data['username'])))	 /* 2 characters minimum */
		{
			$valid = "Invalid username!";
		}
		elseif(!preg_match('/^[a-zA-Z\s+\.\?\!]{1,500}$/', trim($data['comment'])))
		{
			$valid = "Invalid comment!";
		}

		return $valid;
	}
	
	function validateUN($username)
	{	
		$username = trim($username);
		
		if(!preg_match('/^[a-zA-Z]{1}[a-zA-Z0-9]{1,9}$/', $username))	 /* 2 characters minimum */
		{
			echo "<br>&nbsp;&nbsp;Error: Corrupted Username";
			echo "<br>You will be redirected in 5 seconds";
			header("Refresh:0; url=signup.php");
			exit();
			
		} else {
			return $username;
		}
	}
	
	function filterLink($link) 
	{
		
		
		$trmlink = trim($link);
		
		$userfound = false;
		
		if(!isset($_COOKIE['usercmnt']))	// This cookie contains a user's name ... but what if it is missing?
		{
			// echo "No cookie found";
		}
		
		$file_pointer = 'text/users.txt';
		if(file_exists($file_pointer)) {
	
			$lines = file('text/users.txt');
			
			// for each line of the $lines array, alias it to $line
			foreach ($lines as $line)
			{
				// Split $line by the regex pattern - just a @ in this case
				// the pieces from the split are returned in an array and stored in $pieces
				$pieces = preg_split('/\|/', $line);
				
				if($trmlink == trim($pieces[1])) {	// Valid Username
					$userfound = true;				// If true, record is found
				}
			}
		}
		
		if($userfound)
		{
			if(!preg_match('/^[a-zA-Z]{1}[a-zA-Z0-9]{1,9}$/', $trmlink)) 
			{
				return '';
			} 
			else
			{
				return $trmlink;
			}
		} 
		else
		{
			return '';
		}
	}
	
	function validateFILE1($file) {
		$str = str_replace("text/", "", $file);
		if(!preg_match('/^(default_sort.txt|vote_sort.txt|username_sort.txt)$/', trim($str))) 
		{ 
			return '';
		} else {
			return $file;
		}
	}
	
	function validateFILE2($json) {
		$str = str_replace("json/", "", $json);
		if(!preg_match('/^(default_sort.json|vote_sort.json|username_sort.json)$/', trim($str))) 
		{ 
			return '';
		} else {
			return $json;
		}
	}

?>