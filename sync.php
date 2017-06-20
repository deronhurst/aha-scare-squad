<pre><?php
/**
* This script sync number of ecards sent and number of ecard shsred
* with ahaym database participant table
*/

include_once('lib/DataContext.php');

$db = new DataContext();

$supporters = $db->Query("SELECT DISTINCT supporter_id,team_id FROM zoocrew.activities");

foreach ($supporters as $sup){
	
	//echo $sup['supporter_id']."\n";
	
	$db->NonQuery("UPDATE kinterat_ahaym_prod.participants SET ecards_sent = (SELECT COUNT(*) AS total FROM zoocrew.activities WHERE supporter_id = ".$sup['supporter_id']." AND TYPE = 2 ) WHERE supporterid = ".$sup['supporter_id']."");
	
	$db->NonQuery("UPDATE kinterat_ahaym_prod.participants SET ecards_shared = (SELECT COUNT(*) AS total FROM zoocrew.activities WHERE supporter_id = ".$sup['supporter_id']." AND TYPE = 3 ) WHERE supporterid = ".$sup['supporter_id']."");
	
	$db->NonQuery("UPDATE kinterat_ahaym_prod.participants SET challenge_taken = (SELECT choice FROM zoocrew.activities WHERE supporter_id = ".$sup['supporter_id']." AND TYPE > 1 ORDER BY created DESC LIMIT 1 ) WHERE supporterid = ".$sup['supporter_id']."");
	
		if(empty($sup['team_id'])){
		
		$db->NonQuery("UPDATE zoocrew.activities SET team_id = (
	SELECT t.parent_team_id  FROM kinterat_ahaym_prod.teams t
	JOIN kinterat_ahaym_prod.participants p ON (p.team_id = t.teamid)
	WHERE p.supporterid =  ".$sup['supporter_id'].")
	WHERE supporter_id =  ".$sup['supporter_id']."");
		}

}

echo count($supporters)." supporters updated";
?>
</pre>