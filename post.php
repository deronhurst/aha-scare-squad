<?php

	include_once('lib/DataContext.php');
	include_once('lib/config.php');
	
	if(empty($_POST['image'])){
		exit("Invalid request");
	}

	
	$eid = $_POST['eid'];
	$sid = $_POST['sid'];
	$title = $_POST['title'];
	$choice = $_POST['choice'];
	$character = $_POST['character'];
	$img = base64_decode(str_replace("data:image/png;base64,","",$_POST['image']));	
	$message = explode("\n",urldecode($_POST['message']));
	
	$db = new DataContext();
	
	// create new share record
	$share = new Fbshare();
	$share->title = $title;
	$share->message = $message[0];
	$share->supporter_id = (!empty($sid))? $sid : 0;
	$share->event_id = (!empty($eid))? $eid : 0;
	$share->choice = $choice;
	$share->character = $character;
	$share->created = 'now';

	/*
	echo '<h2>$share</h2>';
	echo '<pre>';
	var_dump($share);
	echo '</pre>';
	*/
	
	$db->Save($share);
	$db->Submit();

	// save image
	$path = dirname(__FILE__)."/shares/fb".$share->id.".png";
	file_put_contents($path, $img);
	
	$data = array();
	$data[] = 'app_id='.Config::$app_id;
	$data[] = 'display=popup';
	$data[] = 'caption='.urlencode($share->title);
	$data[] = 'link='.urlencode(Config::$base_url."/share.php?id=".$share->id);
	$data[] = 'redirect_uri='.urlencode(Config::$base_url."/share_return.php?post_id=".$share->id."&id=".$sid);

	$link = 'link='.urlencode(Config::$base_url."/share.php?id=".$share->id);
	$redirect_uri = 'redirect_uri='.urlencode(Config::$base_url."/share_return.php?post_id=".$share->id."&id=".$sid);
	//echo '<h2>$data</h2>';
	//echo '<pre>';
	//var_dump($data);
	//echo '</pre>';
	

	sleep(1);
	$url = 'https://www.facebook.com/dialog/feed?'.implode("&", $data);
	header('Location:'.$url);

?>