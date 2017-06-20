<?php
include_once("lib/ez_sql.php");
include_once("lib/config-zc.php");
$db = new ezSQL_mysql(DATABASE_USER,DATABASE_PASS,DATABASE_NAME,DATABASE_HOST);
function jsonpResponse($resp){
	return $_GET['jsonp_callback'] . '(' . json_encode($resp) . ');';
}

$tid = mysql_real_escape_string($_GET['tid']);
$all = $_GET['all'];

if($all=="1"){
	$lb = $db->get_results("SELECT * FROM leaderboard ORDER BY total_raised DESC LIMIT 10",ARRAY_A);
}else{
	$lb = $db->get_results("SELECT * FROM leaderboard WHERE school_id = '".$tid."' ORDER BY total_raised DESC LIMIT 10",ARRAY_A);
}
error_reporting(E_ALL);
$c = 0;

if(!empty($lb)) {

foreach($lb as $l){
	if( strlen($l['first']) > 11){
		$lb[$c]['first'] = substr($l['first'],0,11);
	}
	$c++;
}

}

$last_updated = $db->get_var("SELECT last_updated FROM import_info WHERE type='participants' order by last_updated desc");
$lut = date("H:i:sA",$last_updated);
$lud = date("Y-m-d",$last_updated);
echo jsonpResponse( array("success"=>1,"count"=>$db->num_rows,"leaderboard"=>$lb,"last_updated_time"=>$lut,"last_updated_date"=>$lud) );
exit;
?>