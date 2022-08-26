<?php
	
	$message = '';
	if(isset($_COOKIE['system_message']))
	{
		$message = '<div id="message" class="alert alert-danger text-center">'
			. $_COOKIE['system_message'] .
		'</div>';

		setcookie('system_message', null, time() - 3600);
	}
	
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

<div id="wrapper" style="margin-bottom: 85px;">

    <div class="container">

        <div class="row">
            <div class="col-md-4 col-md-offset-4" style="margin-top: 25px;">
                <h1 class="login-panel text-center text-muted">
                    COMP 3015<br>Final Project
                </h1><br>
                <hr/>
                <?php echo $message; ?>
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Sign Up and Comment</h3>
                    </div>
                    <div class="panel-body">
                        <form name="signup" role="form" action="redirect.php?from=signup" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control"
                                           value=""
                                           name="username"
                                           placeholder="Username"
                                           type="text"
                                           autofocus
                                    />
                                </div>
                                <div class="form-group">
                                    <input class="form-control"
                                           name="comment"
                                           placeholder="Comment"
                                    />
                                </div>
                                <input type="submit" class="btn btn-lg btn-info btn-block" value="Sign Up!"/>
                            </fieldset>
                        </form>
                    </div>
                </div>
				<hr><br>
				<div class="text-center">
					<<&nbsp;&nbsp;<a class="btn btn-sm btn-default" href="index.php">Homepage</a>&nbsp;&nbsp;>>
				</div>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
