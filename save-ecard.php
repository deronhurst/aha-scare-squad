<?php
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');

	$image = base64_decode(str_replace("data:image/png;base64,","",$_POST['image']));	

	//$image = 'http://hearttools.heart.org/aha_ym18/scare-squad/img/monster-rocky-thumb.png';
	// Read image path, convert to base64 encoding
	//$imageData = base64_encode(file_get_contents($image));
	
	$url = "/prints/" .$_POST['sid']. ".png";
	//$url = "/prints/" .$_GET['sid']. ".png";
	
	$path = dirname(__FILE__).$url;
	
	
	$success = file_put_contents($path,$image);
	
	echo $success ? $_POST['sid']. ".png" : null;
?>