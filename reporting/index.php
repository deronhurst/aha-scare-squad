<?php
include_once('lib/auth.php');
include_once('lib/dataset.php');
include_once('lib/datatypes.php');

$auth = new Auth();
if(!$auth->isAuthenticated()){
	header("Location:login.php");
	exit();
}

if(empty($_REQUEST['start'])) {
	$_REQUEST['start'] = 'thisweek';
}
if(empty($_REQUEST['affliatename'])) {
	$schoolfilter = '';
}
else{
   $schoolfilter = $_REQUEST['affliatename'];
   
}
include("header.php");
$data = new DataSet();
$data->load(false, true);
$static = $data->getByActivity();
$schools = $data->getSchoolNames($schoolfilter);
$affliatessource = $data->getAffliates();
$email_open_count = $data->getOpenemails();
$email_visit_count = $data->getVisitemails();
if(!empty($static[2]['total'])) {
$openpercent = number_format(($email_open_count[0]['email_open'] / $static[2]['total']) * 100);
$visitpercent = number_format(($email_visit_count[0]['link_visit_open'] / $static[2]['total']) * 100);
}

 

?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Puppify Tracking</title>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/Chart.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-datepicker.min.js"></script>

<script type="text/javascript" src="js/tracking.js"></script>
<script type="text/javascript">
var start = "<?php echo (!empty($data->start_date))? $data->start_date : 'today'; ?>";
var end = "<?php echo $data->end_date; ?>";
var challenge = "<?php echo (!empty($data->challenge))? $data->challenge : 'all'; ?>";
var school = "<?php echo (!empty($data->school))? $data->school : 'all'; ?>";
var affliatename = "<?php echo (!empty($data->affliatename))? $data->affliatename : ''; ?>";
</script>
<link rel="stylesheet" type="text/css" href="css/tracking.css">
<style>
select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding: 2px 30px 2px 2px;
    border: none;
    background-image: url(../images/down-arrow.png);
    background-position: 98% center;
    background-repeat: repeat;
}
</style>
</head>

<body>
<!--<div id="header">
  <div class="content"><img src="images/logo.png" /><a href="logout.php">Logout</a></div>
</div>-->
<div class="content">
     <div id = "affliatefilters"><select onChange="setAffliate(this.value);submitFilters();">
        	<option value="0">     All Affliates    </option>
            <?php 
                             foreach($affliatessource as $nameIndex=>$nameVal){ 
				//echo '<option value="'.$nameVal['event_id'].'">'.$nameVal['name'].'</option>';
			echo '<option value="'.$nameVal['event_id'].'"';
                        if ($_REQUEST['affliatename'] == $nameVal['event_id']) { 
                       
                        echo  ' selected="true" ';
                        
                        }
                        echo '>'; 
                        echo  $nameVal['name'].'</option>';
                                 
                             }
			
			  ?>
        </select></div> 
    <div id="filters">
  <?php
  $secialdays = array(
  	'today' => 'Today',
	'yesterday' => 'Yesterday',
	'thisweek' => 'This week',
	'lastweek' => 'Last week',
	'thismonth' => 'This month',
	'lastmonth' => 'Last Month',
	//'all' => 'All time'
	);
  
  	if(isset($secialdays[$data->start_date])){
		echo 	$secialdays[$data->start_date];
	}
	else {
		echo date("m/d/Y", strtotime($data->start_date));	
	}
	
	if(!empty($data->end_date)){
		echo " to ".date("m/d/Y", strtotime($data->end_date));
	}
  
  
  ?> | 
  <?php
   if(empty($data->school) || $data->school == 'all'){
		echo 'All schools';   
   }
   elseif(isset($schools[$data->school])){
	  echo $schools[$data->school]; 
   }
   elseif($data->school == 'top5'){
		echo "Top 5 Schools";   
   }
   elseif($data->school == 'top10'){
		echo "Top 10 Schools";   
   }
     ?>
  </div>
  <a href="#" onClick="submitFilters(true)" id="exporter">EXPORT DATA</a>
  <div id="filter_panel">
  
  	<div id="filter_panel_inner">
        <div class="col">
        	<div class="colhead">Date</div>
            <div class="subcol">
           	 	<a href="#" data-key="start" data-value="today">Today</a>
                <a  href="#"data-key="start" data-value="thisweek">This week</a>
                <a href="#"  data-key="start" data-value="thismonth">This month</a>
<a>&nbsp;</a>

                <br>

                From<br>
<input type="text" class="text" id="start" value="<?php echo $data->start_date; ?>">
            </div>
        	<div class="subcol">
           	 	<a href="#" data-key="start" data-value="yesterday">Yesterday</a>
                <a href="#" data-key="start" data-value="lastweek">Last week</a>
                <a href="#" data-key="start" data-value="lastmonth">Last month</a>
                <a>&nbsp;</a>
                <br>

                To<br>
<input type="text" class="text" id="end" value="<?php echo $data->end_date; ?>">
            </div>
        </div>
        <div class="col"><div class="colhead">School</div>
        <a href="#" data-key="school" data-value="all">All schools</a>
        <a href="#" data-key="school" data-value="top5">Top 5 schools</a>
        <a href="#" data-key="school" data-value="top10">Top 10 schools</a>
        <a>&nbsp;</a>
		<br>
        <br>


		
        </div>
        <div class="col"><div class="colhead">Challenge</div>
        <a href="#" data-key="challenge" data-value="all">All challenges</a>
        <br>

        <div class="row">
        	<input type="checkbox" class="challenge"  data-key="challenge" data-value="1" <?php echo ($data->challenge == 'all' || empty($data->challenge) || strpos($data->challenge,'1') !== false) ? 'checked="checked"' : ''; ?>> Be physically active...
        </div>
        <div class="row">
        	<input type="checkbox" class="challenge" data-key="challenge" data-value="2" <?php echo ($data->challenge == 'all' || empty($data->challenge) || strpos($data->challenge,'2') !== false) ? 'checked="checked"' : ''; ?>> Choose water over ...
        </div>
        <div class="row">
        	<input type="checkbox" class="challenge" data-key="challenge" data-value="3" <?php echo ($data->challenge == 'all' || empty($data->challenge) || strpos($data->challenge,'3') !== false) ? 'checked="checked"' : ''; ?>> Eat at least one serving...
        </div><div class="row">
        	<input type="checkbox" class="challenge" data-key="challenge" data-value="4" <?php echo ($data->challenge == 'all' || empty($data->challenge) || strpos($data->challenge,'4') !== false) ? 'checked="checked"' : ''; ?>> I am learning how to...
        </div>
        </div>
    </div>
    <div class="buttons"><a class="btn" href="#" onClick="submitFilters()">Apply Filters</a><a href="#" onClick="close_filters()">Cancel</a></div>

  </div>
  
  
  <div class="subtitle">Overview</div>
  <div class="panel">
    <div class="col3">
    	<div class="colhead">Logins</div>
        <div class="numbers"><?php echo number_format($static[1]['total']); ?></div>
        <?php
		if(!empty($static[1]['total'])) {
			echo '<div class="info">
        <a class="green">'.$static[1]['parts'].' Unique Students</a><br>
	<a>in '.$static[1]['schools'].' Schools</a></div>';
        
		}		
		?>        
    </div>
    <div class="col3">
    	<div class="colhead">Facebook Share</div>
        <div class="numbers"><?php echo number_format($static[3]['total']); ?></div>
        <?php
		if(!empty($static[3]['total'])) {
			echo '<div class="info">
        <a class="green">'.$static[3]['parts'].' Unique Students</a><br>
	<a>in '.$static[3]['schools'].' Schools</a></div>';
        
		}		
		?>      

    </div>
    <div class="col3">
    	<div class="colhead">Ecards Sent</div>
        <div class="numbers"><?php echo number_format($static[2]['total']); ?></div>
        <?php
		if(!empty($static[2]['total'])) {
			echo '<div class="info">
        <a class="green">'.$static[2]['parts'].' Unique Students</a><br>
	<a>in '.$static[2]['schools'].' Schools</a></div>';
        
		}		
		?>
    </div>
    <div class="col3">
    	<div class="colhead">Ecards Opened</div>
        <div class="numbers"><?php echo number_format($email_open_count[0]['email_open']); ?></div>
        <?php
		if(!empty($static[2]['total'])) {
			echo '<div class="info">
        <a class="green">'.$openpercent.'% </a></div>';
        
		}		
		?>
    </div> 
      <div class="col3">
    	<div class="colhead">Ecards Clicked</div>
        <div class="numbers"><?php echo number_format($email_visit_count[0]['link_visit_open']); ?></div>
        <?php
		if(!empty($static[2]['total'])) {
			echo '<div class="info">
        <a class="green">'.$visitpercent.'% </a></div>';
        
		}		
		?>
    </div> 
  </div>
  <div class="panel">
  <?php
  	// making graph data
  	$graph = $data->getStatisticByDate();
	$overall = array();
	
	if(count($graph['label']) > 1) {
	
		$overall = array(
			'labels' => $graph['label'],
			'datasets' => array(
				array(
					'label' => 'Logins',
					'strokeColor' => $colors[0],
					'pointColor' =>$colors[0],
					'data' => $graph['login']				
				),
				array(
					'label' => 'Ecards',
					'strokeColor' => $colors[1],
					'pointColor' =>$colors[1],
					'data' => $graph['email']				
				),
				array(
					'label' => 'Share',
					'strokeColor' => $colors[2],
					'pointColor' =>$colors[2],
					'data' => $graph['share']				
				),
                               array(
					'label' => 'Ecards Opened',
					'strokeColor' => $colors[3],
					'pointColor' => $colors[3],
					'data' => $graph['open_email_count']				
				),
                               array(
					'label' => 'Ecards Clicked',
					'strokeColor' => $colors[5],
					'pointColor' => $colors[5],
					'data' => $graph['link_visit_open']				
				)
			)
		);
		
		echo '<canvas id="overall" width="800" height="200" style="width:800px; height:200px;"></canvas> <div id="legend">
  	<a style="background-color:#5DA5DA"></a> Logins
        <a style="background-color:#60BD68"></a> Ecards sent
        <a style="background-color:#F15854"></a> Facebook shares
        <a style="background-color:#DECF3F"></a> Ecards Opened
        <a style="background-color:#B276B2"></a> Ecards Clicked
  </div>';
	
	}

  ?>
 
  </div>
   <div class="subtitle">Challenges</div>
  <div class="panel">
  	<div class="col6">
    	<table width="100%" border="0" cellspacing="1" cellpadding="5" class="grid">
 	 <tr class="head">
     <td width="0"></td>
    <td>Challenge</td>
    <td width="60">Shares</td>
    <td width="60">Ecards</td>
    <td width="60">Open Ecards</td>
    <td width="60">Clicked Ecards</td>
  </tr>
  <?php 
  
  $bychallange = $data->getByChallenge();
  $avc = array();
  
  foreach ($challenges as $index => $challenge) {
      
	  	echo ($index%2 == 0 ) ? '<tr class="alt">' : '<tr>';
	  
	  	echo '<td style="background-color:'.$colors[$index-1].'; padding:0"></td>';
		echo '<td>'.$challenge.'</td><td align="center">';
				

		echo isset($bychallange[$challenge][3]) ? number_format($bychallange[$challenge][3]) : '';
		
		echo '</td><td align="center">';
		
		echo isset($bychallange[$challenge][2]) ? number_format($bychallange[$challenge][2]) : '';
		echo '</td><td align="center">';
		
		echo isset($bychallange[$challenge][4]) ? number_format($bychallange[$challenge][4]) : '';
		echo '</td><td align="center">';
		
		echo isset($bychallange[$challenge][5]) ? number_format($bychallange[$challenge][5]) : '';
                echo '</td></tr>';
		
		// Graph data
		if(!isset($bychallange[$challenge])) {
			continue;
		}
		
		$avc[] = array(
				'value' => $bychallange[$challenge][0],
       			'color' => $colors[$index-1],
        		'highlight' => $colors[$index-1],
       			'label' => $challenge
			);
  
  }
  ?>
</table>

    </div>
    <div class="col3" style="float:right">
    <div class="chartwrap">
    	<canvas id="cva" style="width:250px; height:150px;" width="250" height="150"></canvas>
    </div>
    <div style="text-align:center">Challenges vs Activities</div>
    </div>
    
  </div>
  <div class="subtitle">Active Schools</div>
  <div class="panel">
  	<div class="col6">
    	<table width="100%" border="0" cellspacing="1" cellpadding="5" class="grid">
 	 <tr class="head">
     <td width="1"></td>
    <td>School</td>
    <td width="60">Logins</td>
    <td width="60">Shares</td>
    <td width="60">Ecards</td>
    <td width="60">Open Ecards</td>
    <td width="60">Clicked Ecards</td>
  </tr>
  <?php
  
  	$byschool = $data->getBySchool();
          
	$avs = array();
	
	$i = 1;
	$total = array(0 => 0, 1=> 0, 2 => 0, 3=> 0); // total activities
	foreach ( $byschool as $school ){
		
		$name = isset($schools[$school['id']]) ? $schools[$school['id']] : 'Unknown School';
		
		echo ($i%2 == 0 ) ? '<tr class="alt">' : '<tr>';
		echo '<td style="background-color:'.$colors[$i-1].'; padding:0"></td>';
    	echo '<td>';
	echo $name;
    	echo '</td><td align="center">';
		echo !empty($school['1']) ? number_format($school['1']) : '';
    	echo '</td><td align="center">';
		echo !empty($school['3']) ? number_format($school['3']) : '';
    	echo '</td><td align="center">';
		echo !empty($school['2']) ? number_format($school['2']) : '';
 		echo ' </td><td align="center">';
		echo !empty($school['4']) ? number_format($school['4']) : '';
 		echo ' </td><td align="center">';
		echo !empty($school['5']) ? number_format($school['5']) : '';
 		echo ' </td></tr>';
		$avs[] = array(
				'value' => $school[0],
       			'color' => $colors[$i-1],
        		'highlight' => $colors[$i-1],
       			'label' => $name
			);
		
		$i++;
		
		$total[0] += (!empty($school['0']))? $school['0'] : 0;
		$total[1] += (!empty($school['1']))? $school['1'] : 0;
		$total[2] += (!empty($school['2']))? $school['2'] : 0;
		$total[3] += (!empty($school['3']))? $school['3'] : 0;
		
		if($i > 10){
			// Only top 10 schools will be shown
			break;	
		}
		
	}
	
	// there are more schools to be shown
	// sum up them and show in one row
	if($i == 11){
		
		echo ' <tr>
				<td style="background-color:'.$colors[$i-1].'; padding:0"></td>
				<td>Other Schools</td>
				<td width="60"  align="center">'.($static[1]['total'] - $total[1]).'</td>
				<td width="60"  align="center">'.($static[3]['total'] - $total[3]).'</td>
				<td width="60"  align="center">'.($static[2]['total'] - $total[2]).'</td>
			  </tr>';
			  
		$avs[] = array(
				'value' => $static[1]['total'] - $total[1],
       			'color' => $colors[$i-1],
        		'highlight' => $colors[$i-1],
       			'label' => $name
			);
		
	}
  
  ?>
 
 
</table>

    </div>
    <div class="col3" style="float:right">
    <div class="chartwrap">
    	<canvas id="sva" style="width:250px; height:150px;" width="250" height="150"></canvas>
    </div>
    <div style="text-align:center">Schools vs Activities</div></div>
  </div>
  <div class="panel">
    <div class="col2"></div>
    <div class="col2"></div>
  </div>
 </div>

<!---- Graph plotting data ----------->
<script type="application/javascript">

// Activity vs Challanges
avcdata = <?php echo json_encode($avc); ?>;
avc = document.getElementById('cva').getContext('2d');
avcpie = new Chart(avc).Pie(avcdata,{showTooltips:false});

// activity vs school
avsdata = <?php echo json_encode($avs); ?>;
avs = document.getElementById('sva').getContext('2d');
avspie = new Chart(avs).Pie(avsdata,{showTooltips:false});

// data distribution graph
orldata = <?php echo json_encode($overall); ?>;
if(orldata) {
	orl = document.getElementById('overall').getContext('2d');
	orlgraph = new Chart(orl).Line(orldata,{datasetFill : false});
}
</script>
</body>
</html>