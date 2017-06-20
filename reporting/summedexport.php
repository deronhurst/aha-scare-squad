<?php
include_once('lib/exportdataset.php');
include_once('lib/datatypes.php');
include_once('lib/auth.php');

$auth = new Auth();

if(!empty($_POST)){
	
	if($auth->login( $_POST['username'], $_POST['password'])){
		header("Location:index.php");
		exit();
	}
}

  $exporttype = $_REQUEST['export'];
    $exdata = new DataSetExport();
    $exdata->load($exporttype);
if(empty($exdata->exdata)){
	exit("No data to export");	
}
         Switch($exdata->group_type){
                            Case "sba":
                              $sumheaders = $affliateheaders;  
                            break;
                            Case "sbs":
                             $sumheaders = $schoolheaders;  

                            break;
                            Case "as":
                             $sumheaders = $headers;  

                            break;
                            default:
                            break;
                }
//$headers = array_keys($data->data[0]);



$str = trim(implode(",", $sumheaders))."\n";

foreach ($exdata->exdata as $item){
	
	

	foreach ($sumheaders as $hd){
		$str .= '"'.$item[$hd].'",';	
	}
	
	$str .= "\n";
}

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename=Socials_Reporting.csv');
header('Pragma: no-cache');
echo $str;

?>