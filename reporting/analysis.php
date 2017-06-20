<?php

//session_start();

include_once('lib/auth.php');

include_once('lib/dataset.php');

include_once('lib/datatypes.php');

//include_once('lib/DataContext.php');



$auth = new Auth();

if(!$auth->isAuthenticated()){

	header("Location:login.php");

	exit();

}

if(empty($_REQUEST['start'])) {

	$_REQUEST['start'] = 'all';

}

include("header.php");

$data = new DataSet();

$analysisemail = $data ->getAnalysisEmails(1);

$analysisfb = $data ->getAnalysisEmails(2);

$donationlastupdated = $data ->getLastUpdated();

$analysisemailcounts = $data ->getAnalysisCounts(1);

$analysisfbcounts = $data ->getAnalysisCounts(2);      

$data->load();

$static = $data->getByActivity();

$email_open_count = $data->getOpenemails();

$email_visit_count = $data->getVisitemails();

$email_clicked = $data->getClickedByEvent();
$email_clicked = Util::groupArray($email_clicked, 'affiliate', 'total');

if(!empty($static[2]['total'])) {

$openpercent = number_format(($email_open_count[0]['email_open'] / $analysisemailcounts['sent']) * 100);

$visitpercent = number_format(($email_visit_count[0]['link_visit_open'] / $analysisemailcounts['sent']) * 100);

}

?>
<html>
<head>
<meta charset="utf-8">
<title>ZooCrew Tracking</title>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/Chart.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-datepicker.min.js"></script>

<!--<link rel="stylesheet" type="text/css" href="css/adminv2.css"/>-->

<link rel="stylesheet" type="text/css" href="css/tracking.css">
</head>
<style>
.content {
	padding-left: 4rem;
}
</style>

<body>
<div class="subtitle" style="padding: 0 0 0 0;">Ecard to Donation Stats, To-Date</div>
<h3>Last Updated: <?php echo $donationlastupdated;?></h3>
<div class="panel">
  <div class="col3">
    <div class="colhead">Logins</div>
    <div class="numbers"><?php echo number_format($static[1]['total']); ?></div>
  </div>
  <div class="col3">
    <div class="colhead">Facebook Share</div>
    <div class="numbers"><?php echo number_format($analysisfbcounts['shared']); ?></div>
  </div>
  <div class="col3">
    <div class="colhead">Ecards Sent</div>
    <div class="numbers"><?php echo number_format($analysisemailcounts['sent']); ?></div>
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

  // making graph dataaxisY: {

     

        $graphdata = $data->getStatisticByAnalysis();

	$overall = array();

        

        if(count($graphdata['label']) > 1) {

        $overall = array(

			'labels' => $graphdata['label'],

                        'options'  => array('title' => array('display' => 'true','text' =>'Custom Chart Title')),

			'datasets' => array(

				array(

                                        'data' => $graphdata['emailsent'],			

				        'fillColor'=> $colors[0],

                                        

                                 )

				

			)

		);

         $doverall = array(

			'labels' => $graphdata['label'],

			'datasets' => array(

				array(

                                        'data' => $graphdata['averagesent'],			

				        'fillColor'=> $colors[1],

                                        

                                 )

				

			)

		);

	     

	      echo '<canvas id="overall" width="500" height="250" style="width:500px; height:250px;"></canvas>';

              

              echo '<canvas id="doverall" width="500" height="250" style="width:500px; height:250px;"></canvas> <div id="legend">

                    <div style="float: left;"><a style="background-color:#5DA5DA"></a> Email Conversion Rate as a %</div><div style="padding-left: 500px;"> 

                    <a style="background-color:#60BD68"></a> Average Return in Dollars per Email</div>

                    </div>';

              }

 



  ?>
</div>
<div class="subtitle" style="padding: 0 0 0 0;">Zoo Ecards</div>
<table class="grid" width="75%" cellspacing="1" cellpadding="5" border="0">
  
  <!--<table  border="0" id="report_ids_table" style="margin-left: 2em;margin-top: 2em;" cellspacing="0" cellpadding="5">-->
  
  <tr>
    <th width="70">Affliate </th>
    <th width="100">Ecards Sent </th>
    <th width="100">Ecard Clicked </th>
    <th width="100">Donations Made </th>
    <th width="100">Amount Raised </th>
    <th width="100">Average return per Email Sent</th>       
    <th width="100">Average return per Email Click</th>
    <th width="100">Conversion Rate </th> 
  </tr>
  <?php

  $clicksum = 0;

		  foreach ($analysisemail as $index => $r) {

                

                       echo ($index%2 == 0 ) ? '<tr class="alt">' : '<tr>';

                       echo '<td>'.$r['account'].'</td>

                             <td>'.number_format($r['sent']).'</td>
							 
							 <td>'.$email_clicked[$r['account']].'</td>

                             <td>'.number_format($r['donatecount']).'</td>

                             <td>$'.number_format($r['raised'],2).'</td>  

                             <td>$'.$r['avg_sent'].'</td>  							 
							 
							 <td>$'.number_format(($r['raised']/$email_clicked[$r['account']]),2).'</td>

                            <td>'.$r['email_sentpercent'].'%</td> 

                           </tr>';

						
						$clicksum += $email_clicked[$r['account']];

                       

                  }

                  echo '<tr style="border: 4px solid #DE4A55;">

                             <td  style="padding-left: 5em;background-color:#f6f6f6;"><B><i>Totals:</i></B></td>

                             

                             <td style="background-color:#f6f6f6;"><B><i>'.number_format($analysisemailcounts['sent']).'</td>
							 
							 <td style="background-color:#f6f6f6;"><B><i>'.number_format($clicksum).'</i></B></td>

                             <td style="background-color:#f6f6f6;"><B><i>'.number_format($analysisemailcounts['donatecount']).'</td>  

                             <td style="background-color:#f6f6f6;"><B><i>$'.number_format($analysisemailcounts['raised'],2).'</i></B></td>

                             <td style="background-color:#f6f6f6;"><B><i>$'.($analysisemailcounts['avg_sent']).'</i></B></td>
							
							 
							 
							 
							 <td style="background-color:#f6f6f6;"><B><i>$'.number_format(($analysisemailcounts['raised']/$clicksum),2).'</i></B></td>
							 
							  <td style="background-color:#f6f6f6;"><B><i>'.$analysisemailcounts['email_sentpercent'],'%</i></B></td>



</tr>';

		?>
</table>
<BR>
<BR>
<div class="subtitle" style="padding: 0 0 0 0;">Zoo FB Shares</div>
<div class="panel">
  <?php

  // making graph data

        

	$foverall = array();

        $dfoverall = array();

        if(count($graphdata['flabel']) > 1) {

        $foverall = array(

			'labels' => $graphdata['flabel'],

			'datasets' => array(

				array(

                                        'data' => $graphdata['fconversionrate'],			

				        'fillColor'=> $colors[0],

                                        

                                 )

				

			)

		);

         $dfoverall = array(

			'labels' => $graphdata['flabel'],

			'datasets' => array(

				array(

                                        'data' => $graphdata['faveragesent'],			

				        'fillColor'=> $colors[1],

                                        

                                 )

				

			)

		);

		

	      echo '<canvas id="foverall" width="500" height="250" style="width:500px; height:250px;"></canvas>';

                    

              echo '<canvas id="dfoverall" width="500" height="250" style="width:500px; height:250px;"></canvas> <div id="legend">

                    <div style="float: left;"><a style="background-color:#5DA5DA"></a> Average Return in Dollars per Share</div><div style="padding-left: 500px;">   

                    <a style="background-color:#60BD68"></a> Conversion Rate as a %  </div>

                    </div>';

              }



  ?>
</div>
<table class="grid" width="75%" cellspacing="1" cellpadding="5" border="0">
  
  <!--<table  border="0" id="report_ids_table" style="margin-left: 2em;margin-top: 2em;" cellspacing="0" cellpadding="5">-->
  
  <tr>
    <th width="70">Affliate
      </td>
    <th width="100"> FB Shares
      </td>
    <th width="100"> Donations Made
      </td>
    <th width="100">Amount Raised
      </td>
    <th width="100">Average return per Share
      </td>
    <th width="100">Conversion Rate
      </td>
  </tr>
  <?php



		  foreach ($analysisfb as $indexes => $fb) {

                 //   $email_clickedpercent = number_format(($r['clicked_emails'] / $r['emails_sent']) * 100);  

                 //  $fb_sentpercent = ROUND((($fb['donatecount'] / $fb['shared']) * 100),2);

                  // $avg_fbsent = ROUND((($fb['raised'] / $fb['shared'])), 2);

                       echo ($index%2 == 0 ) ? '<tr class="alt">' : '<tr>';

                       echo '<td>'.$fb['account'].'</td>

                             <td>'.number_format($fb['shared']).'</td>

                             <td>'.number_format($fb['donatecount']).'</td>

                             <td>$'.number_format($fb['raised'],2).'</td>  

                             <td>$'.number_format($fb['fb_sent'],2).'</td>  

                             <td>'.number_format($fb['fb_sentpercent'],2).'%</td>

                             

                           </tr>';

                        

		}

                echo '<tr style="border: 4px solid #DE4A55;">

                             <td  style="padding-left: 5em;background-color:#f6f6f6;"><B><i>Totals:</i></B></td>

                             

                             <td style="background-color:#f6f6f6;"><B><i>'.number_format($analysisfbcounts['shared']).'</td>

                             <td style="background-color:#f6f6f6;"><B><i>'.number_format($analysisfbcounts['donatecount']).'</td>  

                             <td style="background-color:#f6f6f6;"><B><i>$'.number_format($analysisfbcounts['raised'],2).'</i></B></td>

                             <td style="background-color:#f6f6f6;"><B><i>$'.number_format($analysisfbcounts['fb_sent'],2).'</i></B></td><td style="background-color:#f6f6f6;"><B><i>'.$analysisfbcounts['fb_sentpercent'],'%</i></B></td>

                       </tr>';

		?>
</table>

<!---- Graph plotting data -----------> 

<script type="application/javascript">





// data distribution graph

orldata = <?php echo json_encode($overall); ?>;

dorldata = <?php echo json_encode($doverall); ?>;

forldata = <?php echo json_encode($foverall); ?>;

dforldata = <?php echo json_encode($dfoverall); ?>;



if(orldata) {

	orl = document.getElementById('overall').getContext('2d');

        

	orlgraph = new Chart(orl).Bar(orldata,{datasetFill : false, title: "Downloads"});

}

if(dorldata) {

	dorl = document.getElementById('doverall').getContext('2d');

	orlgraph = new Chart(dorl).Bar(dorldata,{datasetFill : false});

}

if(forldata) {

	forl = document.getElementById('foverall').getContext('2d');

	forlgraph = new Chart(forl).Bar(forldata,{datasetFill : false});

}

if(dforldata) {

	dforl = document.getElementById('dfoverall').getContext('2d');

	forlgraph = new Chart(dforl).Bar(dforldata,{datasetFill : false});

}



</script>
</div>
</body>
</html>