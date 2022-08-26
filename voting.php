<?php
	
	require 'includes/functions.php';
	
	session_start();
	
	$link = filterLink($_GET['link']);
	
	if($link !== '') 
	{
		echo $link . "<br>";
		
		if(!isset($_COOKIE['usercmnt'])) 
		{
			echo "No cookie present -- unlikely";
			exit();
		} else {
			if(!isset($_SESSION['usercmnt'])) {
				echo "No session! Impossible";
			}
		}
		
		if(isset($_COOKIE['usercmnt']) || isset($_SESSION["usercmnt"]))
		{
			$user = '';
			
			if(isset($_COOKIE['usercmnt'])) 	// A bit redundant, as all we need is the session variable
			{		
				$user = $_COOKIE['usercmnt'];
			}
			if(isset($_SESSION['usercmnt']))
			{
				$user = $_SESSION['usercmnt'];
			}
				
			if($_COOKIE['usercmnt'] == $link || $_SESSION["usercmnt"] == $link) 
			{
				setcookie("system_message", $user . ", you cannot vote for your own comment.");	// Result 1A
				header("Refresh:0; url=index.php");
				exit();
			} 
			else
			{
				
				$file_pointer = 'text/comment_votes.txt';
				if(file_exists($file_pointer)) 
				{
					$value = 1;
					
					// Retrieve Last users.txt Record and determine next increment
					if (file_exists('text/comment_votes.txt'))
					{
						
						$line = '';
						$f = fopen('text/comment_votes.txt', 'r');
						$cursor = -1;
						fseek($f, $cursor, SEEK_END);
						$char = fgetc($f);
					
						while ($char === "\n" || $char === "\r") 
						{
							fseek($f, $cursor--, SEEK_END);
							$char = fgetc($f);
						}

						while ($char !== false && $char !== "\n" && $char !== "\r") 
						{
							$line = $char . $line;
							fseek($f, $cursor--, SEEK_END);
							$char = fgetc($f);
						}

						$pieces = preg_split('/\|/', $line);
						$value = (int)$pieces[0] + 1;
							
					}


					$bothfound = false;
					
					$lines = file('text/comment_votes.txt');
					
					// for each line of the $lines array, alias it to $line
					foreach ($lines as $line)
					{
						// Split $line by the regex pattern - just a @ in this case
						// the pieces from the split are returned in an array and stored in $pieces
						$pieces = preg_split('/\|/', $line);
						
						// echo "Cookie: " . $_COOKIE['usercmnt'] . "<br>";
						// echo "Pieces[1]: " . $pieces[1] . "<br>";
						
						// echo "Link: " . $link . "<br>";
						// echo "Pieces[2]: " . $pieces[2] . "<br>";
						
						if($user == trim($pieces[1]) && $link == trim($pieces[2])) 
						{
							$bothfound = true;
						}
					}
						
					if($bothfound) 
					{
						setcookie("system_message", "You can't vote for a comment more than once, " . $user . ".");
						header("Refresh:0; url=index.php");
						exit();	
					} 
					else
					{
						$fp = fopen("text/comment_votes.txt", "a+");
						$fw = fwrite($fp, $value . '|' . $user . '|' . $link . PHP_EOL);
						fclose($fp);
						setcookie("system_message", "Voting has increased by 1 for " . $link . ".");
						header("Refresh:0; url=index.php");	
						exit();
					}
				} 
				else 
				{
					$fp = fopen("text/comment_votes.txt", "a+");
					$fw = fwrite($fp, '1|' . $user . '|' . $link . PHP_EOL);	// Write new file for voting
					fclose($fp);
					setcookie("system_message", "Voting has increased by 1 for " . $link . ".");
					header("Refresh:0; url=index.php");	
				}	
			}
		} 
		else
		{
			echo "<br>&nbsp;&nbsp;A system error has occured.";
			echo "<br>&nbsp;&nbsp;Contact the administrator.";
			header("Refresh:5; url=index.php");
			exit();
		}
		
	} else {
		setcookie("system_message", "Invalid link detected");
		header("Refresh:0; url=index.php");	
		exit();
	}
	
	
?>