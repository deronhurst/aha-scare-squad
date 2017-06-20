<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


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

//echo "CP 1 ";

$donationtotals = $data ->getDonationTotals('affliate');

//echo "CP 2 ";

$donationsummmary = $data ->getDonationSummary('affliate');

//echo "CP 3 ";

$donationlastupdated = $data ->getLastUpdated();

//echo "CP 4 ";

$donationsourcesummmary = $data ->getDonationSummary('source');

//echo "CP 5 ";

$donationsourcetotals = $data ->getDonationTotals('source');

//echo "CP 6 ";

$analysisemailcounts = $data ->getAnalysisCounts(1);

//echo "CP 7 ";

$analysisfbcounts = $data ->getAnalysisCounts(2); 

//echo "CP 8 ";

$data->load();


//echo "CP 9 ";

$static = $data->getByActivity();

//echo "CP 10 ";

$email_open_count = $data->getOpenemails();

//echo "CP 11 ";

$email_visit_count = $data->getVisitemails();


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

</head><style>

.content {

	

        padding-left: 4rem;

}

.footer {

	 background-color:#f6f6f6;

}</style><body>



<div class="subtitle" style="padding: 0 0 0 0;">Donations By Affliate / Source</div>

<h3>Last Updated: <?php echo $donationlastupdated;?></h3>

<div style="padding-left: 3rem;">

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

  	// making graph data

        $graphdata = $data ->getStatisticByAffliate();

        $graphdatasource = $data ->getStatisticBySource();

        if((count($graphdata['label']) > 1) OR (count($graphdatasource['label']) > 1)) {

        $overall = array(

			'labels' => $graphdata['label'],

			'datasets' => array(

				array(

                                        'data' => $graphdata['amounts'],			

				        'fillColor'=> $colors[0],

                                        

                                 )

				

			)

		);

         $doverall = array(

			'labels' => $graphdatasource['label'],

			'datasets' => array(

				array(

                                        'data' => $graphdatasource['amounts'],			

				        'fillColor'=> $colors[1],

                                        

                                 )

				

			)

		);

		

	      echo '<canvas id="overall" width="500" height="250" style="width:500px; height:250px;"></canvas>';

                    

              echo '<canvas id="doverall" width="500" height="250" style="width:500px; height:250px;"></canvas> <div id="legend">

                     <div style="float: left;padding-top: 1rem;"><a style="background-color:#5DA5DA;"></a> Donation Amount($) By Affliate</div><div style="padding-top :1rem;padding-left: 500px;">   

                    <a style="background-color:#60BD68"></a> Donation Amount($) By Source

                    </div></div>';

              }

?>

 

  </div><BR><BR>

  <div class="subtitle">National By Affliate</div>



 <table class="grid" width="75%" cellspacing="1" cellpadding="5" border="0">

 <!--<table  border="0" id="report_ids_table" style="margin-left: 2em;margin-top: 2em;" cellspacing="0" cellpadding="5">-->

    <tr>

      <th width="70">Affliate

        </td>

     

      <th width="100"># of Donations

        </td>

      <th width="100">Total Raised

        </td>

       <th width="100"> Average

        </td>

       <th width="100">% Count

        </td>

        <th width="100">% Income

        </td>

    </tr>

    <?php



		  foreach ($donationsummmary as $index => $r) {

                   echo ($index%2 == 0 ) ? '<tr class="alt">' : '<tr>'; 

                   echo ($index%2 == 0 ) ? '<tr class="alt">' : '<tr>';

                       echo '<td>'.$r['account'].'</td>

                             <td>'.$r['totalcount'].'</td>

                             <td>$'.number_format($r['totaldonation']).'</td>

                             <td>$'.$r['average'].'</td>  

                             <td>'.$r['activity_count_percent'].'%</td>

                             <td>'.$r['donation_count_percent'].'%</td>



                            </tr>';

        

                       

                  }

		echo '<tr style="border: 4px solid #DE4A55;">

                             <td style="padding-left: 5em;background-color:#f6f6f6;"><B><i>Totals:</i></B></td>

                             <td style="background-color:#f6f6f6;"><B><i>'.number_format($donationtotals['totalcount']).'</i></B></td>

                             <td style="background-color:#f6f6f6;"><B><i>$'.number_format($donationtotals['totaldonation']).'</td>

                             <td style="background-color:#f6f6f6;"><B><i>$'.$donationtotals['average'].'</td>  

                             <td style="background-color:#f6f6f6;"><B><i>'.number_format($donationtotals['activity_count_percent'],2).'%</i></B></td>

                             <td style="background-color:#f6f6f6;"><B><i>'.number_format($donationtotals['donation_count_percent'],2).'%</i></B></td>



                            </tr>';?>

  </table>  <BR><BR>  <BR><BR>

  <div class="subtitle">National By Source</div>



 <table class="grid" width="75%" cellspacing="1" cellpadding="5" border="0">

 <!--<table  border="0" id="report_ids_table" style="margin-left: 2em;margin-top: 2em;" cellspacing="0" cellpadding="5">-->

    <tr>

      <th width="70">Source

        </td>

     

      <th width="100">Count

        </td>

      <th width="100">Total Raised

        </td>

       <th width="100"> Average

        </td>

       <th width="100">% Count

        </td>

        <th width="100">% Income

        </td>

    </tr>

    <?php



		  foreach ($donationsourcesummmary as $source => $s) {

                   echo ($source%2 == 0 ) ? '<tr class="alt">' : '<tr>'; 

                   echo ($source%2 == 0 ) ? '<tr class="alt">' : '<tr>';

                       echo '<td>'.$s['name'].'</td>

                             <td>'.$s['totalcount'].'</td>

                             <td>$'.number_format($s['totaldonation']).'</td>

                             <td>$'.$s['average'].'</td>  

                             <td>'.$s['activity_count_percent'].'%</td>

                             <td>'.$s['donation_count_percent'].'%</td>



                            </tr>';

        

                       

                  }

		echo '<tr style="border: 4px solid #DE4A55;">

                             <td style="padding-left: 5em;background-color:#f6f6f6;"><B><i>Totals:</i></B></td>

                             <td style="background-color:#f6f6f6;"><B><i>'.$donationsourcetotals['totalcount'].'</i></B></td>

                             <td style="background-color:#f6f6f6;"><B><i>$'.number_format($donationsourcetotals['totaldonation']).'</td>

                             <td style="background-color:#f6f6f6;"><B><i>$'.$donationsourcetotals['average'].'</td>  

                             <td style="background-color:#f6f6f6;"><B><i>'.number_format($donationsourcetotals['activity_count_percent'],2).'%</i></B></td>

                             <td style="background-color:#f6f6f6;"><B><i>'.number_format($donationsourcetotals['donation_count_percent'],2).'%</i></B></td>



                            </tr>';?>

  </table>  <BR><BR>  

  <div class="subtitle">By Affliate and Source</div>



<table class="grid" width="75%" cellspacing="1" cellpadding="5" border="0">

 <!--<table  border="0" id="report_ids_table" style="margin-left: 2em;margin-top: 2em;" cellspacing="0" cellpadding="5">-->

    <tr>

      <th width="70">Affliate

        </td>

      <th width="100">Source

        </td>

      <th width="100">Count

        </td>

      <th width="100">Donation Amount

        </td>

       <th width="100">Average

        </td>

       <th width="100">% Count

        </td>

        <th width="100">% Income

        </td>

    </tr>

    <?php



		  foreach ($donationsummmary as $index => $r) {

                   $donationdetails = $data ->getDonationDetail($r['account']);   

                     foreach ($donationdetails as $indexes => $d) {

                        echo ($indexes%2 == 0 ) ? '<tr class="alt">' : '<tr>';

                        echo     '<td align="center">'.$d['account'].'</td>

                             <td>'.$d['name'].'</td>

                             <td>'.$d['counts'].'</td>

                             <td>$'.number_format($d['donation']).'</td>

                             <td>$'.$d['average'].'</td>

                             <td>'.$d['activity_count_percent'].'%</td>

                             <td>'.$d['donation_count_percent'].'%</td>

                           </tr>';

                     }

                       echo '<tr style="border: 4px solid #DE4A55;">

                             <td colspan="2" style="padding-left: 5em;background-color:#f6f6f6;"><B><i>Totals:</i></B></td>

                             <td style="background-color:#f6f6f6;"><B><i>'.$r['totalcount'].'</i></B></td>

                             <td style="background-color:#f6f6f6;"><B><i>$'.number_format($r['totaldonation']).'</td>

                             <td style="background-color:#f6f6f6;"><B><i>$'.$r['average'].'</td>  

                             <td style="background-color:#f6f6f6;"><B><i>'.$r['activity_count_percent'].'%</i></B></td>

                             <td style="background-color:#f6f6f6;"><B><i>'.$r['donation_count_percent'].'%</i></B></td>



                            </tr>';

		}

		?>

  </table>

<!---- Graph plotting data ----------->

<script type="application/javascript">





// data distribution graph

orldata = <?php echo json_encode($overall); ?>;

dorldata = <?php echo json_encode($doverall); ?>;





if(orldata) {

	orl = document.getElementById('overall').getContext('2d');

	orlgraph = new Chart(orl).Bar(orldata,{datasetFill : false});

}

if(dorldata) {

	dorl = document.getElementById('doverall').getContext('2d');

	orlgraph = new Chart(dorl).Bar(dorldata,{datasetFill : false});

}

</script>

</div>

</body>

</html>