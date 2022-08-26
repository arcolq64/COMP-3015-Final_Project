<?php

	require 'includes/functions.php';
	
	session_start();
		
	if(count($_POST) > 0 && $_GET['from'] == 'signup')
	{
		$check = checkSignUp($_POST);	// validation for username & comment takes place in checkSignUp

		if($check !== true)
		{
			setcookie('system_message', $check);
			header('Location: signup.php');
		}
		else
		{
			$username = trim($_POST['username']);
			$comment = trim($_POST['comment']);
			
			$userfound = false;		// If a username is not found, this will remain false
			
			//-----------------------------------------------
			// If users.txt exists ...
			// Determine if a username entry exists.
			// The aim is to avoid duplicate entries.
			//-----------------------------------------------
			
			$file_pointer = 'text/users.txt';
			if(file_exists($file_pointer)) {
			
				$lines = file('text/users.txt');
				
				// for each line of the $lines array, alias it to $line
				foreach ($lines as $line)
				{
					// Split $line by the regex pattern - just a @ in this case
					// the pieces from the split are returned in an array and stored in $pieces
					$pieces = preg_split('/\|/', $line);
					
					if($username == trim($pieces[1])) {	// Valid Username
						$userfound = true;				// If true, record is found
					}
				}					
			}
			
			if($userfound == false) {
				
				$unixtime = new DateTime();				// Last login day/time ... not used so far
				$loginstr = $unixtime->format('U');		// Unix time is stored
				$value1 = 1;
				$value2 = 1;
				
				// Retrieve last users.txt record number and determine next increment
				if (file_exists('text/users.txt')){
					
					$line = '';
					$f = fopen('text/users.txt', 'r');
					$cursor = -1;
					fseek($f, $cursor, SEEK_END);
					$char = fgetc($f);
				
					while ($char === "\n" || $char === "\r") {
						fseek($f, $cursor--, SEEK_END);
						$char = fgetc($f);
					}

					while ($char !== false && $char !== "\n" && $char !== "\r") {
						$line = $char . $line;
						fseek($f, $cursor--, SEEK_END);
						$char = fgetc($f);
					}

					$pieces = preg_split('/\|/', $line);
					$value1 = (int)$pieces[0] + 1;
						
				}
				
				// Retrieve last comment.txt record number and determine next increment
				if (file_exists('text/comments.txt')){
					
					$line = '';
					$f = fopen('text/comments.txt', 'r');
					$cursor = -1;
					fseek($f, $cursor, SEEK_END);
					$char = fgetc($f);
				
					while ($char === "\n" || $char === "\r") {
						fseek($f, $cursor--, SEEK_END);
						$char = fgetc($f);
					}

					while ($char !== false && $char !== "\n" && $char !== "\r") {
						$line = $char . $line;
						fseek($f, $cursor--, SEEK_END);
						$char = fgetc($f);
					}

					$pieces = preg_split('/\|/', $line);
					$value2 = (int)$pieces[0] + 1;
						
				}
				
				// record the new user into a flat file named users.txt 
				$fp1 = fopen("text/users.txt", "a+");
				$fw1 = fwrite($fp1, $value1 . '|' . $username . '|' . $loginstr . PHP_EOL);
				fclose($fp1);
				
				// record the new comment into a flat file named comments.txt 
				$fp2 = fopen("text/comments.txt", "a+");
				$fw2 = fwrite($fp2, $value2 . '|' . $username . '|' . $comment . '|0' . PHP_EOL);
				fclose($fp2);
				
				if($fw1 && $fw2) {
					// Set session cookie for user -- to prevent invalid links from occuring 
					$_SESSION["usercmnt"] = $username;
					setcookie('usercmnt', $username, time() + (86400 * 30), "/");	// 86400 = 1 day 
					header('Location: index.php?type=signup&username='.$username);
					exit();
				} else {
					// Set cookie to expire after post_message is processed.
					setcookie("system_message", "Appending failed!");
					header("Refresh:0; url=signup.php");
					exit();
				}
				
			} else {
				setcookie('system_message', 'Username not available.');
				header('Location: signup.php');
			}
		}

		exit();
	}

	// should never reach here but if we do, back to index they go
	header('Location: index.php');
	exit();

?>