<?php
include_once('lib/auth.php');

$auth = new Auth();

if(!empty($_POST)){
	
	if($auth->login( $_POST['username'], $_POST['password'])){
		header("Location:index.php");
		exit();
	}
}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Puppify Tracking</title>
<script type="text/javascript" src="js/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="css/tracking.css">
</head>

<body>
<div id="header">
  <div class="content"><img src="images/logo.png" /></div>
</div>
<div class="content">
  <div id="loginbox">
  <form action="login.php" method="post">
  <table width="100%" border="0" cellspacing="0" cellpadding="5">
      <tr>
        <td colspan="2" style="color:red"><?php echo (!empty($_POST))? "Invalid username or password" : "";  ?></td>
      </tr>
      <tr>
        <td>Username</td>
        <td><input type="text" name="username" size="20"></td>
      </tr>
      <tr>
        <td>Password</td>
        <td><input type="password" name="password" size="20"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input type="submit" value="Login"></td>
      </tr>
    </table></form>    
  </div>
</div>
</body>
</html>