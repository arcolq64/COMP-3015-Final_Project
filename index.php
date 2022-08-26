<?php
	
	require 'includes/functions.php';
	
	$message = '';
	
	if(isset($_GET['username']) && $_GET['type'] == 'signup')			
	{		
		$username = validateUN($_GET['username']);		
		$message = 'Thank you, ' .$username . ', for submitting a comment.';
		
	} else {
		
		if(isset($_COOKIE['usercmnt']))
		{
			$message = 'Welcome back, '.$_COOKIE['usercmnt'].'.';
		} 
	}
	
	if(isset($_COOKIE['system_message']))
	{
		$message = '<div id="message" class="alert alert-danger text-center">'
			. $_COOKIE['system_message'] .
		'</div>';
		setcookie('system_message', null, time() - 3600);
		
	} 
	
	session_start();
	
	// Default sort file ...
	
	if(!isset($_SESSION["file"])) {
		$_SESSION["file"] = "text/default_sort.txt";
	}
	
	$file = $_SESSION["file"];
	
?>
<!DOCTYPE html>
<html>
<head>
    <title>COMP 3015</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div id="wrapper">

    <div class="container">

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h1 class="login-panel text-center text-muted">
                    COMP 3015 Final Project
                </h1><br>
            </div>
        </div>

		<?php
		
			// Tabulate results based on commenter field in 
			// comment_votes.txt file (index|voter|commenter)
			
			$cnt_array = [];	
			
			if (file_exists('text/comments.txt')) 
			{
				$lines = file('text/comments.txt');
				foreach ($lines as $line)
				{
					$count = 0;
					$pieces = preg_split('/\|/', $line);
					$file_pointer = 'text/comment_votes.txt';
					if(file_exists($file_pointer)) {
						
						$comm = file('text/comment_votes.txt');
						foreach ($comm as $ln)
						{
							$sections = preg_split('/\|/', $ln);
							if(trim($pieces[1]) == trim($sections[2])) {	// comments.txt & comment_votes.txt
								$count += 1;
							}
						}
					}
					
					array_push($cnt_array, $count);
					
				}
			}
			
			// -------------------
			// Create Sorted Files
			// -------------------
			
			// Default Sort
			
			if (file_exists('text/default_sort.txt')) 
			{
				unlink('text/default_sort.txt');
			}
			
			$index = 0;		// All fields in comments.txt are duplicated except the last one.
			
			$fp = fopen("text/default_sort.txt", "a+");
			if(file_exists('text/comments.txt')) 
			{
				$lines = file('text/comments.txt');
				foreach ($lines as $line)
				{	
					$pieces = preg_split('/\|/', $line);
					$fw = fwrite($fp, $pieces[0] . '|' . $pieces[1] . '|' . $pieces[2] . '|' . $cnt_array[$index] . PHP_EOL);
					$index += 1;	// Next value from $cnt_array
				}
			}	
			fclose($fp);

			// These arrays are needed for sorting and JSON file creation
			
			$array1 = file('text/default_sort.txt');
			$array2 = array();							// Needed for Username Sort
			$array3 = array();							// Needed for Vote Sort
			
			// Username Sort
			
			if (file_exists('text/username_sort.txt')) 
			{
				unlink('text/username_sort.txt');
			}
	
			$count = 0;
			
			$lines = file('text/default_sort.txt');
			foreach ($lines as $line)
			{
				$pieces = preg_split('/\|/', $line);
				$array2[$count] = array();
				$array2[$count]['index'] = $pieces[0];
				$array2[$count]['username'] = $pieces[1];
				$array2[$count]['comment'] = $pieces[2];
				$array2[$count]['votes'] = $pieces[3];
				$count += 1;
			}
			
			$column = array_column($array2, 'username');
			array_multisort($column, SORT_ASC, SORT_NATURAL|SORT_FLAG_CASE, $array2);
			
			// --- Create username_sort.txt ---
			// --- Multidimensional array ---
			
			$count = 0;
			
			$fp = fopen("text/username_sort.txt", "a+");
			foreach($array2 as $pieces) 
			{
				$fw = fwrite($fp, $array2[$count]['index'] .'|'.
								  $array2[$count]['username'] .'|'.
								  $array2[$count]['comment'] .'|'.
								  $array2[$count]['votes']);
				$count += 1;
			}
			fclose($fp);
			
			// Vote Sort
			
			if (file_exists('text/vote_sort.txt')) 
			{
				unlink('text/vote_sort.txt');
			}
			
			foreach( $array2 as $key => $value )
			{
				$array3[] = $value;
			}
			
			$column = array_column($array3, 'votes');
			array_multisort($column, SORT_DESC, SORT_NUMERIC, $array3);
			
			// --- Create vote_sort.txt ---
			// --- Multidimensional array ---
			
			$count = 0;
			
			$fp = fopen("text/vote_sort.txt", "a+");
			foreach($array3 as $pieces) 
			{
				$fw = fwrite($fp, $array3[$count]['index'].'|'. $array3[$count]['username'].'|'. $array3[$count]['comment'].'|'. $array3[$count]['votes']);
				$count += 1;
			}
			fclose($fp);
			
			// Display Main Table
			
			if(isset($_COOKIE['usercmnt'])) {
				
				echo '<div id="user_msg" class="row">';
				echo '<div class="col-md-6 col-md-offset-3 text-center">';
				echo '<hr class="gray">';
				echo '<p>' . $message . '</p>';
				echo '<hr class="gray">';
				echo '</div>';
				echo '</div>';
				
			} else {
				
				if(!isset($_COOKIE['usercmnt']))	  // Entry is only for new users	
				{
					echo '<div class="row">';
					echo '<div class="col-md-6 col-md-offset-3 text-center">';
					echo '<hr class="gray">';
					echo '<a href="signup.php" class="btn btn-default btn-lg">';
					echo '<i class="fa fa-user"></i> Sign Up and Comment!';
					echo '</a>';
					echo '<hr class="gray">';
					echo '</div>';
					echo '</div>';
					
				} else {
					
					if(isset($_SESSION['usercmnt']))
					{
						echo '<div id="usr-message" class="row">';
						echo '<div class="col-md-6 col-md-offset-3 text-center">';
						echo '<hr class="gray">';
						echo '<p>Enjoy the site, '. $_SESSION['usercmnt'] . '!</p>';
						echo '<hr class="gray">';
						echo '</div>';
						echo '</div>';
					} else {
						echo "Status: User cookie and No session!!";
					}	
					
				}
			}
				
			if(isset($_COOKIE['usercmnt']) || isset($_SESSION['usercmnt'])) {
				echo '<br>';
				echo '<form action="sort.php" method="post" style="text-align: center">';
				echo '<label class="heading">Select the type of sorting:&nbsp;&nbsp;</label>';
				if($_SESSION["file"] == "text/default_sort.txt") {
					echo '<input type="radio" name="radio" value="default" checked> Default&nbsp;&nbsp;';
				} else {
					echo '<input type="radio" name="radio" value="default"> Default&nbsp;&nbsp;';
				}
				if($_SESSION["file"] == "text/vote_sort.txt") {
					echo '<input type="radio" name="radio" value="voting" checked> Voting&nbsp;&nbsp;';
				} else {
					echo '<input type="radio" name="radio" value="voting"> Voting&nbsp;&nbsp;';
				}
				if($_SESSION["file"] == "text/username_sort.txt") {
					echo '<input type="radio" name="radio" value="username" checked> Username&nbsp;&nbsp;';
				} else {
					echo '<input type="radio" name="radio" value="username"> Username&nbsp;&nbsp;';
				}
				echo '<input type="submit" name="submit" value="Submit" />';
				echo '</form>';
			}
			
			echo "<br>";
			
			if(file_exists("$file") && file_exists('text/users.txt')) 
			{
				echo '<div class="row">';
				echo '<div class="col-md-6 col-md-offset-3 text-center">';
				echo '<table class="table table-bordered">';
				echo '<tr>';
				echo '<th class="text-center">Username</th>';
				echo '<th class="text-center">Comment</th>';
				echo '<th class="text-center">Votes</th>';
				echo '<th class="text-center">Link</th>';
				echo '</tr>';
				
				$lines = file("$file");

				// for each line of the $lines array, alias it to $line
				
				foreach ($lines as $line)
				{
					$count = 0;
					
					$pieces = preg_split('/\|/', $line);
					echo '<tr>';
					echo '<td>' . $pieces[1] . '</td>';
					echo '<td>' . $pieces[2] . '</td>';
					
					$file_pointer = 'text/comment_votes.txt';
					if(file_exists($file_pointer)) {
						
						$comm = file('text/comment_votes.txt');
						
						// for each line of the $lines array, alias it to $line
						foreach ($comm as $ln)
						{
							// Split $line by the regex pattern - just a | in this case
							// the pieces from the split are returned in an array and stored in $pieces
							$sections = preg_split('/\|/', $ln);
							
							if(trim($pieces[1]) == trim($sections[2])) {	// comments.txt & comment_votes.txt
								$count += 1;
							}
						}
						
					}
					
					array_push($cnt_array, $count);
					
					echo '<td>'.$count.'</td>';
					echo '<td><a href="voting.php?link='.trim($pieces[1]).'">+1</a></td>';
					echo '</tr>';
				}
				
				echo '</table>';
				echo '</div>';
				echo '</div>';
				echo '<div id="voting_msg" class="row">';
				echo '<div class="col-md-6 col-md-offset-3 text-center">';
				echo '<p>If you are signed up, clicking the +1 link will increase the vote for a comment (once only). This does not apply to your own comment.</p><br>'; 
				echo '</div>';
				echo '</div>';
				
			} else {
				
				echo '<div id="cf_missing" class="row">';
				echo '<div class="col-md-6 col-md-offset-3 text-center">';
				echo '<p style="margin-bottom: 15px;">Provide the first comment!</p>';
				echo '</div>';
				echo '</div>';
			}
			
			// Encode array? to JSON
			
			$json = '';
			
			if($file == "text/default_sort.txt") {
				$enc = json_encode(array('data' => $array1));
				$fpc = file_put_contents("json/default_sort.json", $enc);
				$json = "json/default_sort.json";
			}
			if($file == "text/username_sort.txt") {
				$enc = json_encode(array('data' => $array2));
				$fpc = file_put_contents("json/username_sort.json", $enc);
				$json = "json/username_sort.json";
			}
			if($file == "text/vote_sort.txt") {
				$enc = json_encode(array('data' => $array3));
				$fpc = file_put_contents("json/vote_sort.json", $enc);
				$json = "json/vote_sort.json";
			}
			
			// Write JSON to file
			/*
			if($fpc)
			{
				echo '<p style="text-align: center">JSON file created successfully.</p>';
			}
			else 
			{
				echo '<p style="text-align: center">Oops! Error creating JSON file.</p>';
			}
			*/
			
			if(file_exists("$file") && file_exists('text/comments.txt'))
			{
				echo '<div class="row">';
				echo '<div class="col-md-6 col-md-offset-3 text-center">';
				echo '<hr class="gray">';
				echo '<p>Download sorted TXT or JSON files containing the data shown above.</p>';
				echo '<a href="download1.php?file='.$file.'" class="btn btn-default btn-lg">';
				echo '<i class="fa fa-sort"></i>&nbsp;&nbsp;Download TXT File';
				echo '</a>';
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				echo '<a href="download2.php?json='.$json.'" class="btn btn-default btn-lg">';
				echo '<i class="fa fa-sort"></i>&nbsp;&nbsp;Download JSON File';
				echo '</a>';
				echo '<hr class="gray">';
				echo '</div>';
				echo '</div>';
			}
			
			// Expire the session if a user is inactive for 5 minutes.
			$expireAfter = 5;
 
			// Check to see if our "last action" session variable has been set.
			if(isset($_SESSION['last_active']))
			{
				
				//Figure out how many seconds have passed since the user was last active.
				$secondsInactive = time() - $_SESSION['last_active'];
				
				//Convert our minutes into seconds.
				$expireAfterSeconds = $expireAfter * 60;
				
				//Check to see if they have been inactive for too long.
				if($secondsInactive >= $expireAfterSeconds){
					//User has been inactive for too long. Kill their session.
					$_SESSION = [];
					session_unset();
					session_destroy();
					exit();
				}
			}
			 
			//Assign the current timestamp as the user's last activity
			$_SESSION['last_active'] = time();
			date_default_timezone_set('America/Vancouver');
			$last_active = date("l, jS \of F -- h:i:s A") . "<br>";
			
			echo '<br><br>';
			echo '<div id="voting_msg" class="row">';
			echo '<div class="col-md-6 col-md-offset-3 text-center">';
			echo 'Last Active Use: '. $last_active;
			echo '</div>';
			echo '</div>';
			
			echo '<div id="voting_msg" class="row">';
			echo '<div class="col-md-6 col-md-offset-3 text-center">';
			echo '<hr class="red">';
			echo '</div>';
			echo '</div>';
			
			if(isset($_COOKIE['usercmnt'])) {
				echo '<div id="voting_msg" class="row">';
				echo '<div class="col-md-6 col-md-offset-3 text-center">';
				echo '<a href="delete.php">Delete User Cookie</a> for ' . $_COOKIE['usercmnt'] . '.';	  
			} else {
				echo '<div class="row">';
				echo '<div class="col-md-6 col-md-offset-3 text-center">';
				echo 'No cookies are currently in use.';	  
			}
			echo '&nbsp;&nbsp;/&nbsp;&nbsp;';
			echo '<a href="destroy.php">Destroy Session</a>';	  
			echo '</div>';
			echo '</div>';
			echo '<br>';
			
		?>
       
    </div>

</div>

</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</html>
