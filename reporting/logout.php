<?php
include_once('lib/auth.php');

$auth = new Auth();
$auth->logout();
header("Location:login.php");

?>