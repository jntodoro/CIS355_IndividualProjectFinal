<?php
//must be logged in to see
session_start();

# ---------- LoginPage() ----------------------------------------------------
// shows the login page and checks to see if the person trying to log in has
//a valid login or not
function LoginPage() {
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
   <head>
      <title>Login</title>
      <meta http-equiv="content-type" content="text/html;charset=utf-8" />
      <meta name="generator" content="Geany 1.23.1" />
      <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
   </head>
   <body>
      <div class="col-md-4"></div>
      <div class="col-md-4" style="margin-top: 40px;">
         <br/>
         <div class="panel panel-default" style="box-shadow: 2px 2px 7px #888888;">
            <div class="panel-heading"><b>Login</b></div>
            <div class="panel-body">';
    if ($_SESSION['InvalidLogin'] == 1) {
        echo '<p><font size="3" color="red">Invalid Username/Password!</font></p>';
        $_SESSION['InvalidLogin'] = 0;
    }
    echo '<form method="POST" action="index.php">
                           <input type="text" size="10" name="username" class="form-control" placeholder="Username">
                           <input type="password" size="10" name="password" style="margin-top: 5px;" class="form-control" placeholder="Password"><br>
                           <button type="submit" name="loginSubmit" style="width: 100%;" class="btn btn-success">Submit</button>
                           </form>
            </div>
         </div>
      </div>
      <div class="col-md-4"></div>
      </div>
      </center>
   </body>
</html>';
}

# ---------- displayHTMLHead ----------------------------------------------------
// shows the log out button at the top of each form if session_start = true
function displayHTMLHead() {
    echo '<!DOCTYPE html>
    <html> 
	<head>
	<title>Data</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/
	3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/
	3.2.0/css/bootstrap-theme.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/
	3.2.0/js/bootstrap.min.js">
	</script></head><body>';
    if ($_SESSION['LoggedIn'] == TRUE) {
	echo '<form class="navbar-form navbar-right" style="margin-top: 35px;" method="POST" 
	    action="Logout.php"><font size="4"> Hello ' . $_SESSION['UserLoggedIn'] . '                                         
                                                       </font><button type="submit" 
            name="loginSubmit" class="btn btn-danger">Log Out</button></form>';
    } else {
        echo '<form class="navbar-form navbar-right" style="margin-top: 35px;" method="POST" 
	    action="index.php"><button type="submit" name="login" class="btn btn-primary">Login</button></form>';
    }
    echo '<br><br></div>';
}