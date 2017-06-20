<?php
set_time_limit(0);
include_once('lib/DataContext.php');

$db = new DataContext();

$data = $db->Query("select * from activities");

foreach ($data as $item){
	
	$tid = file_get_contents('http://ahatools.heart.org/ahaym/taskmanager/getteam.php?sid='.$item['supporter_id'].'&eid='.$item['event_id']);
	
	$db->NonQuery("update activities set team_id = ".$tid." where supporter_id = ".$item['supporter_id']." and event_id = ".$item['event_id']);
}

?>