<?php

include_once('lib/config.php');
include_once('lib/DataContext.php');

if(empty($_GET['id'])){
	exit("Page Not Found");
}

$id = $_GET['id'];

$db = new DataContext();

$share = $db->Fbshares->Find("id = '".$db->escape($id)."'");

if(empty($share)){
	exit("Page Not Found");
}

// if requesting from facebook
/*
if(strpos($_SERVER['HTTP_USER_AGENT'],'facebook') === false){
	header('Location: http://www.kintera.org/faf/donorReg/donorPledge.asp?ievent='.$share->event_id.'&supId='.$share->supporter_id);
	exit();
}*/


?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>The Scare Squad</title>
<meta property="og:title" content="<?php echo $share->title; ?>" />
<meta property="og:description" content="<?php echo str_replace("\\'","'",$share->message); ?>" >
<meta property="fb:app_id" content="<?php echo Config::$app_id; ?>" />
<meta property="og:image" content="<?php echo Config::$base_url."/shares/fb".$share->id.".png"; ?>" />
<script type="text/javascript">
	var sid = '<?php echo $share->supporter_id; ?>';
	var eid = '<?php echo $share->event_id; ?>';
	window.location.href = 'http://heartdev.convio.net/site/TR/Jump/General?px=' + sid + '&pg=personal&fr_id=' + eid + '&s_src=ecard&s_subsrc=facebook';

</script>
</head>

<body>
<h1><?php echo $share->title; ?></h1>
<img src="<?php echo Config::$base_url."/shares/fb".$share->id.".png"; ?>">
<p><?php echo str_replace("\\'","'",$share->message); ?></p>
</body>
</html>