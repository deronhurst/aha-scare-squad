<?php
include_once('lib/config.php');
include_once('lib/DataContext.php');
include_once('lib/functions.php');

if(!empty($_REQUEST['post_id'])){
	$db = new DataContext();
	
	$sid = $_REQUEST['id'];
	$sid = preg_replace("/[^0-9]/", '', $sid);
	
	$share = $db->Fbshares->Find("supporter_id  = '".$db->escape($sid)."'" );
	/*
	echo '<h2>$share</h2>';
	echo '<pre>';
	var_dump($share);
	echo '</pre>';
	*/

	
	// get teaM id
	// get team id
	$tid = getTeamID ( $share->event_id, $share->supporter_id);
	
	// record challange
	updateChallange($share->event_id, $share->supporter_id, $activity->choice, true);
	
	
	if(!empty($share)){	
		$activity = new Activity();
		$activity->type = Config::$ACTIVITY_SHARE;
		$activity->supporter_id = $share->supporter_id;
		$activity->event_id = $share->event_id;
		$activity->team_id = (!empty($tid))? $tid : 0;
		$activity->character = $share->character;
		$activity->choice = $share->title;
		$activity->created = "now";
		
		$db->Save($activity);
		$db->Submit();
	}
}


?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>The Scare Squad</title>
<script type="text/javascript">
window.close();
</script>
</head>

<body>
<pre>
</pre>
</body>
</html>