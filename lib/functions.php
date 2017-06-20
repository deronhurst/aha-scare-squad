<?php
include_once('DataContext.php');

function getTeamID ( $eid, $sid){
	
	
	$db = new DataContext();

	$eid = intval($eid);
	$side = intval($sid);
	
	
	$data = $db->Query("SELECT team_id  FROM participants WHERE constituent_id = ".$sid." AND event_id = ".$eid);	
	
	//$db->close();
	
	
	if(!empty($data['team_id'])){
		return $data['team_id'];	
	}
	else{
		return '0';	
	}
	
}


function updateChallange ($eid, $sid, $challenge, $is_share = false ){
	
	$db = new DataContext();

  $sid = intval($sid);
  $eid = intval($eid);
  $record_type = false;

  // preg_match('/^([0-9])/', $challenge, $challenge);
  // $challenge = is_array($challenge) ? $challenge[0] : false;

  //transitional challenge identification
  if(!is_int($challenge)){
  	$challenges = array(
  		"Be physically active for 60 minutes everyday" => 1,
  		"Choose water over sugar drinks" => 2,
  		"Eat at least one serving of fruit or vegetables at every meal" => 3,
  		"I am learning how to take care of my heart." => 4
  		);

  	$id = $challenges[$challenge];

  	$challenge = $id;
  }

	if($is_share === true){
		//$db->NonQuery("update participants SET challenge_taken = '".$challenge."', ecards_shared = (SELECT COUNT(*) AS total FROM activities WHERE supporter_id = ".$sid." AND TYPE = 3) WHERE constituent_id = ".$sid." AND event_id = ".$eid);
		// Only update number of ecards shared		
		$res = $db->NonQuery("update participants SET ecards_shared = (SELECT COUNT(*) AS total FROM activities WHERE supporter_id = ".$sid." AND TYPE = 3) WHERE constituent_id = ".$sid." AND event_id = ".$eid);		
		$record_type = "ecard_shared";
	}
	else {
		//$db->NonQuery("update participants set challenge_taken = '".$challenge."', ecards_sent = (SELECT COUNT(*) AS total FROM activities WHERE supporter_id = ".$sid." AND TYPE = 2) WHERE constituent_id = ".$sid." AND event_id = ".$eid);
		// Only update number of ecards sent
		$db->NonQuery("update participants set ecards_sent = (SELECT COUNT(*) AS total FROM activities WHERE supporter_id = ".$sid." AND TYPE = 2) WHERE constituent_id = ".$sid." AND event_id = ".$eid);
		$record_type = "ecard_sent";
	}

	file_get_contents("http://hearttools.heart.org/aha_ym18/api/activity/".$eid."/".$sid."/".$record_type."?key=6Mwqh5dFV39HLDq7");
	
}


?>