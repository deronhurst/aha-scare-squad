<?php
include_once('lib/config.php');
include_once('lib/DataContext.php');

$challanges = array(
		'Be physically active for 60 minutes everyday',
		'Choose water over sugar drinks',
		'Eat at least one serving of fruit or vegetables at every meal',
		'I am learning how to take care of my heart.'
);

$out = array();


if(isset($_GET['tid']) && is_numeric($_GET['tid'])){
	
	
		
	$db = new DataContext();
	$tid = $db->escape($_GET['tid']);
	
	$data = $db->query("SELECT choice,COUNT(DISTINCT supporter_id) AS taken FROM activities WHERE team_id = '{$tid}' AND `type` != 1 GROUP BY choice");
	
	$tmp = array();
	foreach ($data as $item){
		$tmp[$item['choice']] = $item['taken'];	
	}
	
	foreach ( $challanges as $c => $h){
	
		$out[($c+1)] = array(
				'choice' => $h,
				'taken' => (!empty($tmp[$h])) ? $tmp[$h] : 0
				 );
		
	}
	
	
	
}
else{
	$data = array();
}

if(!empty($_REQUEST['callback'])){
	echo $_REQUEST['callback']."(".json_encode($out).")";
}
else{
	echo json_encode($out);	
}

?>