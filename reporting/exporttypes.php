<?php

include_once('lib/auth.php');

include_once('lib/dataset.php');
include_once('lib/datatypes.php');
include_once('lib/exportdataset.php');



$auth = new Auth();

if(!$auth->isAuthenticated()){

	header("Location:login.php");

	exit();

}

$data = new ExportDataSet();

$static = $data->getStats();
$affliatessource = $data->getAffliates();
$schools = $data->getSchools();




include("header.php");

//$exdata = new DataSetExport();

//$exdata->load();

//$static = $exdata->getByActivity();

//$schools = $exdata->getSchoolNames($schoolfilter);

//$affliatessource = $exdata->getAffliates();





?>
<!doctype html>

<html>
<head>
<meta charset="utf-8">
<title>Puppify Tracking</title>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/Chart.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-datepicker.min.js"></script>
<script type="text/javascript" src="js/exporttracking.js"></script>
<script type="text/javascript">


function selectAffiliate () {
	
	$('#school_id').val('0');
	document.forms[0].submit();
	
}

function selectSchool () {
	$('#event_id').val('0');
	document.forms[0].submit();
}

</script>
<link rel="stylesheet" type="text/css" href="css/tracking.css">
<style>
select {
	-webkit-appearance: none;
	-moz-appearance: none;
	appearance: none;
	padding: 2px 30px 2px 2px;
	border: none;
	background-image: url(images/down-arrow.png);
	background-position: 98% center;
	background-repeat: no-repeat;
	background-color: #f1f1f1;
    border: 1px solid #e5e5e5;
	border-radius:3px;
}

.col3 { width:33%}

#affliatefilters {
	display:inline;
	position:relative;
	 padding: 2px 30px 2px 2px;
}
</style>
</head>

<body>
<div style="padding:15px; width:600px; margin:auto;">
<div style="border:#e5e5e5 solid 1px; padding:15px;">
<form method="post" action="exporttypes.php">
	
  <div class="panel">
  	<select onChange="selectAffiliate()" name="event_id" id="event_id">
        	<option value="0">     All Affliates    </option>
            <?php 
             
			 foreach($affliatessource as $nameIndex=>$nameVal){ 
							
				
				echo '<option value="'.$nameVal['event_id'].'"';
			
                        if (isset($_REQUEST['event_id']) && $_REQUEST['event_id'] == $nameVal['event_id']) { 
                       
                       	 echo  ' selected="true" ';
                        
                        }
                        echo '>'; 
                        echo  $nameVal['name'].'</option>';
                                 
                        }
			
			  ?>
        </select>
  </div>
  <div class="panel"></div>
  <div class="panel" id="schools">
  <select onChange="selectSchool()" name="school_id" id="school_id">
        	<option value="0">     All Schools    </option>
            <?php
			
			if(!empty($schools)) {
				
				foreach ($schools as $sc) {
					
				if(!empty($_REQUEST['school_id']) && $_REQUEST['school_id'] == $sc['school_id']) {
					
					echo '<option  selected="true" value="'.$sc['school_id'].'">'.$sc['school_name'].'</option>';
				}
				
				else {
			
					echo '<option value="'.$sc['school_id'].'">'.$sc['school_name'].'</option>';
				}
			
				
			}

					
		}
			
			?>
          
  </select>
  </div>
 </form>
  <div class="panel"></div>
  <!--
  <div class="panel">
    <div class="col3">
    	<div class="colhead">Logins</div>
        <div class="numbers"><?php echo (isset($static[1])) ? number_format($static[1]) : 0; ?></div>
           
    </div>
    <div class="col3">
    	<div class="colhead">Facebook Share</div>
        <div class="numbers"><?php echo  (isset($static[3])) ? number_format($static[3]) : 0; ?></div>
          

    </div>
    <div class="col3">
    	<div class="colhead">Ecards Sent</div>
        <div class="numbers"><?php echo (isset($static[2])) ? number_format($static[2]) : 0; ?></div>
       
    </div>
     
       
  </div>
  -->
  <div class="subtitle">EXPORT</div>
  <div class="panel" style="padding:10px 0;">
     
    <a href="#" onClick="submitFilters('affiliates')" class="exportertype" >SUMMED BY AFFLIATE</a> 
    <a href="#" onClick="submitFilters('schools')" class="exportertype" >SUMMED BY SCHOOL </a> 
    <a href="#" onClick="submitFilters('students')" class="exportertype" >ALL STUDENTS </a> </div>
  </div>
</div>
<script>

function submitFilters (type){
	
	$('#exp_event_id').val( $('#event_id').val());
	$('#exp_school_id').val( $('#school_id').val());
	$('#exp_type').val(type);
	document.forms[1].submit();
}

</script>
<form method="post" action="exporttypes_a.php" target="_blank">
	<input type="hidden" id="exp_type" value="" name="type">
    <input type="hidden" id="exp_school_id" value="" name="school_id">
    <input type="hidden" id="exp_event_id" value="" name="event_id">
</form>
</body>
</html>