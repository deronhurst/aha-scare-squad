<?php
include_once(dirname(__FILE__).'/lib/config.php');
include_once(dirname(__FILE__).'/lib/DataContext.php');

if(isset($_GET['sid']) && is_numeric($_GET['sid'])){
	
	$sid = trim($_GET['sid']);
		
	$db = new DataContext();
	
	$data = $db->query("SELECT COUNT(*) AS taken FROM activities WHERE supporter_id = '{$sid}' AND `type` != 1");
	
	$out =  $data[0]['taken'];
	
}
else{
	$out = 0;	
}

if(!empty($_REQUEST['callback'])){
	echo $_REQUEST['callback']."(".$out.")";
}
else{
	echo $out;	
}

?>