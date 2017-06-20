<pre><?php
set_time_limit(0);
include_once(dirname(__FILE__) .'/lib/DataContext.php');
include_once(dirname(__FILE__) .'/lib/ReportHelper.php');
include_once(dirname(__FILE__) .'/lib/util.php');


$db = new DataContext();
$db->NonQuery("truncate table donations");

$login = file_get_contents('http://atlas.zurigroup2.com/api/login?api_key=UyueAsWo1rQmLu7vXz2IB8H22BmUMmGWzwzymlLy');
$login = json_decode($login);

$helper = new ReportHelper($login->username, $login->password);

$reports = $db->Query("SELECT r.account_id, r.account_name, r.report_id, a.event_id FROM reports r LEFT JOIN affliates a ON (a.name = r.account_name)");

$codetype = $db->Query("SELECT * FROM sourcecodetypes");
$codetype = Util::groupArray($codetype,'code','type');


foreach ($reports as $rpt) {
	
	echo $rpt['account_name']." ";

	$data = $helper->getReport($rpt['account_id'], $rpt['report_id']);
	
	//print_r($data);
		
	$cdata = array();
	
	echo (count($data)-1)."\n";

	foreach ($data as $item){
		
		if(trim($item['name']) == 'Totals'){
			continue;	
		}
		
		$item['code'] = trim($item['code']);
		
		if(empty($item['code'])){
			$item['code'] = 'N/A';
			$item['name'] = 'N/A';
		}
		else{
			$item['code'] = strtolower($item['code']);
		}
		
		
		
		// map code type
		if(isset($codetype[$item['code']])){
			
			if(!isset($cdata[$item['code']])){
				
				$cdata[$codetype[$item['code']]] = array(
					'name' => $codetype[$item['code']],
					'code' => $codetype[$item['code']],
					'activity_count_percent' => 0,
					'counts' => 0,
					'donation' => 0,
					'donation_count_percent' => 0,
					'total' => 0
				);
			}
			
			$type = $codetype[$item['code']];
			
		}
		else{
			
			$cdata[$item['code']] = array(
					'name' => $item['name'],
					'code' => $item['code'],
					'activity_count_percent' => 0,
					'counts' => 0,
					'donation' => 0,
					'donation_count_percent' => 0,
					'total' => 0
				);
			
			$type = $item['code'];
				
		}
		
		$cdata[$type]['total']++;
		$cdata[$type]['activity_count_percent'] += cleanup($item['%_of_count_activity']);
		$cdata[$type]['counts'] += cleanup($item['counts']);
		$cdata[$type]['donation'] += cleanup($item['$_donation']);
		$cdata[$type]['donation_count_percent'] += cleanup($item['%_of_donation_activity']);
		
		/*
		$don = new Donation();
		$don->account = $rpt['account_name'];
		$don->name = $item['name'];
		$don->code = $item['code'];
		$don->activity_count_percent = cleanup($item['%_of_count_activity']);
		$don->counts = cleanup($item['counts']);
		$don->donation = cleanup($item['$_donation']);
		$don->donation_count_percent = cleanup($item['%_of_donation_activity']);
		$don->created = 'now';
		
		$db->Save($don);
		$db->Submit();
		*/
				
	}
	
	
	
	// calculate and add data into the table
	$total = 0;
	foreach ($cdata as $code => $item){
		
		$don = new Donation();
		$don->account = $rpt['account_name'];
		$don->name = $item['name'];
		$don->code = $item['code'];
		$don->activity_count_percent = $item['activity_count_percent']/$item['total'];
		$don->counts = $item['counts'];
		$don->donation = $item['donation'];
		$don->donation_count_percent = $item['donation_count_percent'];
		$don->created = 'now';
		
		$db->Save($don);
		$db->Submit();
		
		$total += 	$item['donation'];	
	}
	
	$raised = Util::getEventTotal($rpt['event_id']);
	
	print_r($cdata);
	
	if($raised['moneyraised'] > $total){
		
		$don = new Donation();
		$don->account = $rpt['account_name'];
		$don->name = 'Not Tracked';
		$don->code = 'Not Tracked';
		$don->activity_count_percent = 0;
		$don->counts = 0;
		$don->donation = $raised['moneyraised'] - $total;
		$don->donation_count_percent = 0;
		$don->created = 'now';
		
		$db->Save($don);
		$db->Submit();
		
	}
	
	

} // each rpt

echo "Done";

function cleanup ( $val) {
	
	return trim(str_replace(
			array('$','%',','),
			array('','',''),
			$val));	
}
?></pre>