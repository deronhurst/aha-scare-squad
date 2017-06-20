<link rel="stylesheet" type="text/css" href="css/tracking.css">

<?php
//session_start();
include_once('lib/auth.php');
include_once('lib/dataset.php');
include_once('lib/datatypes.php');
include_once('lib/DataContext.php');

$auth = new Auth();
if(!$auth->isAuthenticated()){
	header("Location:login.php");
	exit();
}

if(empty($_REQUEST['start'])) {
	$_REQUEST['start'] = 'thisweek';
}
include("header.php");
$data = new DataSet();
if(!empty($_POST)){
	
	//sending data back to kintertools
	$db = new DataContext();
        $report = new Report();
                
        foreach ($_POST as $key => $val){
            $report->id = $key;
            $report->report_id = $val;
            $db->Update($report);
            $db->Submit();
        }
	

	}
    $reports = $data->reports();
    
?>

  
<div class="content_inner">
  <p> Please update the Report IDs for the Donation by Source reports. Current Reports are named with the following convention: 2017 FDA Jump Hoops Donations with Source.<br>
    </p>
  
  <br><form action="reportids.php" method="post" style="padding-left: 2em;">
<table class="grid" cellspacing="1" cellpadding="5" border="0">
  <!--<table  border="0" id="report_ids_table" cellspacing="0" cellpadding="5">-->
    <tr>
      <th width="20">#
        </td>
      <th width="300">Account Name
        </td>
      <th width="100">Report ID
        </td>
        </td>
    </tr>
    <?php
		  foreach ($reports as $index => $r) {
                      echo ' <tr>
		<td>'.$r['id'].'</td>
		<td>'.$r['account_name'].'</td>
		<td><input type="text" value="'.$r['report_id'].'" style="width:100px" name="'.$r['id'].'" /></td>
	         </tr>';
		}
		?>
  </table>
  <p style="padding-left:180px;"><input type="submit" value=" Update " /></p>
  </form>

</div>
