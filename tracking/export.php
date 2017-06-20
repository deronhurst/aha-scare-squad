<?php
include_once('lib/dataset.php');
include_once('lib/datatypes.php');
include_once('lib/auth.php');

$auth = new Auth();

if(!empty($_POST)){
	
	if($auth->login( $_POST['username'], $_POST['password'])){
		header("Location:index.php");
		exit();
	}
}


if(empty($_REQUEST['start'])) {
	$_REQUEST['start'] = 'thisweek';
}

$data = new DataSet();
$data->load(true);

if(empty($data->data)){
	exit("No data to export");	
}

$headers = array_keys($data->data[0]);

$str = trim(implode(",", $headers))."\n";

foreach ($data->data as $item){
	$item['type'] = $activities[$item['type']];
	$item['choice'] = isset($challenges[$item['choice']]) ? $challenges[$item['choice']] : '';
	
	foreach ($headers as $hd){
		$str .= '"'.$item[$hd].'",';	
	}
	
	$str .= "\n";
}

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename=puppify_tracking.csv');
header('Pragma: no-cache');
echo $str;

?>