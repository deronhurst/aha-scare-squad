<?php
ini_set('memory_limit', '1024M');

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/


include_once('lib/exportdataset.php');
include_once('lib/datatypes.php');
include_once('lib/auth.php');


$auth = new Auth();

if(!isset($_POST['school_id']) || !isset($_POST['event_id']) || empty($_POST['type'])){
	exit("Invalid request");	
}

$name = "all-students.csv";

if($_POST['type'] == 'schools'){
	$name = "summed-by-schools.csv";	
}
elseif($_POST['type'] == 'affiliates'){
	$name = "summed-by-affiliate.csv";	
}



header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename='.$name);
header('Pragma: no-cache');


$ds = new ExportDataSet();

$data = $ds->getExportData();

echo $data;

?>