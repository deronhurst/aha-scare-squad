<?php

//error_reporting(E_WARNING);
ini_set("memory_limit","256M");

include_once('DataContext.php');

include_once('datatypes.php');

include_once('util.php');

//supporter_id,event_id,first_name,last_name,total_raised,affiliate,event_name,team_id,team_name,school_id,school_name,choice,total,Ecard Open,link_visit_open,# Ecards,# Logins,# Shared

class ExportDataSet {


	private $db;
	
	public $event_id = NULL;
	
	public $school_id = NULL;

	
	function ExportDataSet (){
		
		$this->db = new DataContext();
		
		if(!empty($_REQUEST['event_id'])){
			$this->event_id = $_REQUEST['event_id'];
		}
		
		if(!empty($_REQUEST['school_id'])){
			$this->school_id = $_REQUEST['school_id'];
		}
		
	}
	
	public function getAffliates (){
		    $sql = "select event_id, name from affliates ";
		    $this->affliatessource = $this->db->Query($sql);
                    return($this->affliatessource);
}
	
	public function getSchools () {
		
		// schools in affiliate
		if(!empty($this->affiliate)){
			
			return $this->db->Query("SELECT p.school_id, p.school_name, a.event_id
FROM activities a
JOIN participants p ON (a.supporter_id = p.supporter_id)
WHERE p.event_id = '".$this->db->escape($this->event_id)."' and !ISNULL(p.team_id)
GROUP BY p.school_id ORDER BY school_name");
			
		}
		else{
			// all schools
			return $this->db->Query("SELECT p.school_id, p.school_name, a.event_id
FROM activities a
JOIN participants p ON (a.supporter_id = p.supporter_id)
WHERE  !ISNULL(p.team_id)
GROUP BY p.school_id ORDER BY school_name");
			
		}
		
	}
	
	public function getStats (){
		
		
		if( !empty ($this->school_id)){
			// single school
			$data = $this->db->Query("SELECT type, count(*) as total from activities where team_id = '".$this->db->escape($this->school_id)."' group by type");
			
		}
		elseif ( !empty($this->event_id)){
			// schools in affiliate
			$data = $this->db->Query("SELECT type, count(*) as total from activities where event_id = '".$this->db->escape($this->event_id)."' group by type");
			
		}
		else {
			// all schools
			$data = $this->db->Query("SELECT type, count(*) as total from activities  group by type");
		}
		
		return Util::groupArray($data,'type','total');
		
		
	}
	
	public function getExportData () {
		
		$type = $_REQUEST['type'];
		$event_id = $_REQUEST['event_id'];
		$school_id = $_REQUEST['school_id'];
		
		if($type == 'affiliates'){
			
			return $this->_byAffiliate();
			
		}
		elseif( $type == 'schools'){
			
			return $this->_bySchool($event_id);	
		}
		else {
			return $this->_byStudent($school_id, $event_id);	
		}
		
		
	}
	
	
	private function _byAffiliate () {
		
		
			// sum up by affiliate
		
			$students = $this->db->Query("SELECT event_id, `type`,COUNT(id) AS total
FROM activities
WHERE !ISNULL(team_id)
GROUP BY `type`, `event_id`");

			$totals = $this->db->Query("SELECT event_id, COUNT(DISTINCT supporter_id) AS students , COUNT(DISTINCT school_id) AS schools
FROM  participants WHERE !ISNULL(school_id) GROUP BY event_id");
			$totals = Util::groupArray($totals, 'event_id');
		
			$email_open  = $this->db->Query("SELECT event_id, COUNT(email_open) AS `total`
FROM activities
WHERE !ISNULL(team_id) AND `type` = 2 AND email_open = 1
GROUP BY `event_id`");

			$email_open = Util::groupArray($email_open,'event_id','total');

			$email_visit = $this->db->Query("SELECT event_id, COUNT(link_visit_open) AS `total` FROM activities WHERE !ISNULL(team_id) AND `type` = 2 AND link_visit_open = 1 GROUP BY `event_id`");
			
			$accountid = $this->db->Query("SELECT event_id, at.Account_ID AS accountid FROM participants left join kinterat_ahaym_reporting.AHA_TeamLinks at on (participants.team_id = at.Team_ID ) GROUP BY event_id");
			$accountid = Util::groupArray($accountid,'event_id','accountid');
			
			$rorybadge = $this->db->Query("SELECT event_id, count(*) AS rorybadge FROM `participants` WHERE ecards_sent >=10 group by event_id");
           	$rorybadge = Util::groupArray($rorybadge,'event_id','rorybadge');
			
			
			
			$affiliates = $this->db->Query("select event_id, name from affliates");
			
			$raised = $this->db->Query("SELECT event_id, SUM(total_raised) AS raised FROM participants GROUP BY event_id");
			$raised = Util::groupArray($raised,'event_id','raised');
			

			// organize data
			$data = array();
			
			foreach ( $students as $s){
				
				$event = $s['event_id'];
				
				if(!isset($data[$event])){
					$data[$event] = array();	
				}
				
				if($s['type'] == 1){
					$data[$event]['logins'] = $s['total'];
				}
				elseif($s['type'] == 2){
					$data[$event]['emails'] = $s['total'];
				}
				elseif($s['type'] == 3){
					$data[$event]['shares'] = $s['total'];
				}
				
				if(isset($email_open[$event])){
					$data[$event]['email_open'] = $email_open[$event];	
				}
				else{
					$data[$event]['email_open'] = 0;
				}
				
				if(isset($email_visit[$event])){
					$data[$event]['link_visit'] = $email_visit[$event];	
				}
				else{
					$data[$event]['link_visit'] = 0;
				}
				
				
				
			}
			
			foreach ($affiliates as $aff){
				
				$data[$aff['event_id']]['name'] = $aff['name'];
				$data[$aff['event_id']]['students'] = $totals[$aff['event_id']]['students'];
				$data[$aff['event_id']]['schools'] = $totals[$aff['event_id']]['schools'];
				$data[$aff['event_id']]['raised'] = ($raised[$aff['event_id']]) ? $raised[$aff['event_id']] : "0.00";
				$data[$aff['event_id']]['accountid'] = ($accountid[$aff['event_id']]) ? $accountid[$aff['event_id']] : "";
				$data[$aff['event_id']]['roybadge'] = ($rorybadge[$aff['event_id']]) ? $rorybadge[$aff['event_id']] : "0";
			}
			
			
			// build report
			$str = "Affiliate,# Schools,# Students,Logins,Ecards Sent,FB Shares,Ecards Open,Ecard Link Visits,Total Raised,Rory Badge\n";
			foreach ($data as $item){
				
				if(empty($item['name'])){
					continue;	
				}
				
				$str .= $item['name'].",".$item['schools'].",".$item['students'].",".$item['logins'].",".$item['emails'].",".$item['shares'].",".$item['email_open'].",".$item['link_visit'].",".$item['raised'].",".$item['roybadge'];
				
				$str .= "\n";
			}
			
			return $str;
		
		
	}
	
	
	private function _bySchool ( $event_id ) {
		
		// sum up by school
			$event_id = $this->db->escape($event_id);
			//$report_data = $this->db->Query("SELECT * FROM kinterat_ahaym_reporting.AHA_TeamLinks");
                        
			if(!empty($event_id)) {
				
			
				$students = $this->db->Query("SELECT event_id, team_id, `type`,COUNT(id) AS total
	FROM activities
	WHERE !ISNULL(team_id) AND event_id = ".$event_id."
	GROUP BY `type`, `team_id`");
	
				$totals = $this->db->Query("SELECT school_id, COUNT(DISTINCT supporter_id) AS students FROM participants WHERE !ISNULL(school_id) and event_id = ".$event_id." GROUP BY school_id ");
							
			
				$email_open  = $this->db->Query("SELECT team_id, COUNT(email_open) AS `total`
	FROM activities
	WHERE event_id = ".$event_id." AND !ISNULL(team_id) AND `type` = 2 AND email_open = 1
	GROUP BY `team_id`");			
	
				$email_visit = $this->db->Query("SELECT team_id, COUNT(email_open) AS `total`
	FROM activities
	WHERE event_id = ".$event_id." AND !ISNULL(team_id) AND `type` = 2 AND link_visit_open = 1
	GROUP BY `team_id`" );
			
			
			}
			else {
				
				$students = $this->db->Query("SELECT event_id, team_id, `type`,COUNT(id) AS total
	FROM activities
	WHERE !ISNULL(team_id) 	GROUP BY `type`, `team_id` order by event_id");
	
				$totals = $this->db->Query("SELECT school_id, COUNT(DISTINCT supporter_id) AS students FROM participants WHERE !ISNULL(school_id) GROUP BY school_id ");			
			
				$email_open  = $this->db->Query("SELECT team_id, COUNT(email_open) AS `total`
	FROM activities
	WHERE !ISNULL(team_id) AND `type` = 2 AND email_open = 1
	GROUP BY `team_id`");			
	
				$email_visit = $this->db->Query("SELECT team_id, COUNT(email_open) AS `total`
	FROM activities
	WHERE !ISNULL(team_id) AND `type` = 2 AND link_visit_open = 1
	GROUP BY `team_id`" );
				
				
			}
			
			$totals = Util::groupArray($totals, 'school_id');
			$email_open = Util::groupArray($email_open,'team_id','total');
			$email_visit = Util::groupArray($email_visit,'team_id','total');
			
			$affiliates = $this->db->Query("select event_id, name from affliates");
			$affiliates = Util::groupArray($affiliates,'event_id','name');
			
			$schools = $this->db->Query("select distinct school_id, school_name from participants");
			$schools = Util::groupArray($schools,'school_id','school_name');
			
			$raised = $this->db->Query("SELECT school_id, SUM(total_raised) AS raised FROM participants GROUP BY school_id");
			$raised = Util::groupArray($raised,'school_id','raised');
                        
			$accountid = $this->db->Query("SELECT school_id, at.Account_ID AS accountid FROM participants left join kinterat_ahaym_reporting.AHA_TeamLinks at on (participants.team_id = at.Team_ID ) GROUP BY school_id");
			
            $accountid = Util::groupArray($accountid,'school_id','accountid');
           $rorybadge = $this->db->Query("SELECT school_id, count(*) AS rorybadge FROM `participants` WHERE ecards_sent >=10 group by school_id");
           $rorybadge = Util::groupArray($rorybadge,'school_id','rorybadge');
			// organize data
			$data = array();
			
			//print_r($students);//
			
			
			foreach ( $students as $s){
				
				$school = $s['team_id'];
				//$rory = $this->db->Query("select count(*) AS rorycount from (select count(*) n from activities where team_id = ".$school." and type = '1' group by supporter_id having n>10) AS te");
	                        
				if(!isset($affiliates[$s['event_id']])){
					continue;	
				}
				
				if(!isset($data[$school])){
					$data[$school] = array();
					$data[$school]['logins'] = 0;
					$data[$school]['emails'] = 0;
					$data[$school]['shares'] = 0;	
                                        
				}
				
				if($s['type'] == 1){
					$data[$school]['logins'] = $s['total'];
				}
				elseif($s['type'] == 2){
					$data[$school]['emails'] = $s['total'];
				}
				elseif($s['type'] == 3){
					$data[$school]['shares'] = $s['total'];
				}
				
				if(isset($email_open[$school])){
					$data[$school]['email_open'] = $email_open[$school];	
				}
				else{
					$data[$school]['email_open'] = 0;
				}
				
				if(isset($email_visit[$school])){
					$data[$school]['link_visit'] = $email_visit[$school];	
				}
				else{
					$data[$school]['link_visit'] = 0;
				}
				
				$data[$school]['affiliate'] = $affiliates[$s['event_id']];
				$data[$school]['students'] = $totals[$school]['students'];
				$data[$school]['name'] = (isset($schools[$school])) ? $schools[$school] : "";
				$data[$school]['raised'] = (isset($raised[$school])) ? $raised[$school] : "0.00";
               $data[$school]['accountid'] = (isset($accountid[$school])) ? $accountid[$school] : "";
                $data[$school]['rorybadge'] = (isset($rorybadge[$school])) ? $rorybadge[$school] : "0";
				
			}
			
		
			//print_r($data);exit();
			
			// build report
			$str = "Affiliate,School ID, School Name,# Students,Logins,Ecards Sent,FB Shares,Ecards Open,Ecard Link Visits,Total Raised,Account ID,Rory Badge\n";
			foreach ($data as $sid => $item){
				
				if(empty($item['name'])){
					continue;	
				}
				
				$str .= $item['affiliate'].",".$sid.",\"".str_replace('"',"",$item['name'])."\",".$item['students'].",".$item['logins'].",".$item['emails'].",".$item['shares'].",".$item['email_open'].",".$item['link_visit'].",".$item['raised'].",".$item['accountid'].",".$item['rorybadge'];
				
				$str .= "\n";
			}
			
			return $str;		
			
	}
	
	private function _byStudent ($school_id, $event_id ) {
		
		$school_id = $this->db->escape($school_id);
		$event_id = $this->db->escape($event_id);
		
		// file str
		$str = "Affiliate,Event ID,School ID, School Name,Supporter ID,First Name,Last Name,Logins,Ecards Sent,FB Shares,Ecards Open,Ecard Link Visits,Total Raised\n";
		
		
		if(!empty($school_id)){
			
			$students = $this->db->Query("SELECT f.name as affiliate, f.event_id, p.supporter_id, p.first_name, p.last_name,p.school_id, p.school_name, p.total_raised
FROM participants p 
JOIN affliates f ON (f.event_id = p.event_id)
where p.school_id = '".$school_id."'
");


		$activities = $this->db->Query("SELECT  supporter_id, `type`, COUNT(id) AS total FROM 
activities where team_id = '".$school_id."'
GROUP BY supporter_id, `type`");
			

				$email_open  = $this->db->Query("SELECT supporter_id, COUNT(email_open) AS `total`
	FROM activities
	WHERE !ISNULL(team_id) AND `type` = 2 AND email_open = 1 and team_id = '".$school_id."'
	GROUP BY `supporter_id`");			
	
				$email_visit = $this->db->Query("SELECT supporter_id, COUNT(email_open) AS `total`
	FROM activities
	WHERE !ISNULL(team_id) AND `type` = 2 AND link_visit_open = 1 and team_id = '".$school_id."'
	GROUP BY `supporter_id`" );
		
			
		}
		elseif(!empty($event_id)){
			
			$students = $this->db->Query("SELECT f.name as affiliate, f.event_id, p.supporter_id, p.first_name, p.last_name,p.school_id, p.school_name, p.total_raised
FROM participants p 
JOIN affliates f ON (f.event_id = p.event_id)
where p.event_id = '".$event_id."'
");


		$activities = $this->db->Query("SELECT  supporter_id, `type`, COUNT(id) AS total FROM 
activities where event_id = '".$event_id."'
GROUP BY supporter_id,`type`");
			

		$email_open  = $this->db->Query("SELECT supporter_id, COUNT(email_open) AS `total`
	FROM activities
	WHERE !ISNULL(team_id) AND `type` = 2 AND email_open = 1 and event_id = '".$event_id."'
	GROUP BY `supporter_id`");			
	
		$email_visit = $this->db->Query("SELECT supporter_id, COUNT(email_open) AS `total`
	FROM activities
	WHERE !ISNULL(team_id) AND `type` = 2 AND link_visit_open = 1 and event_id = '".$event_id."'
	GROUP BY `supporter_id`" );
			
		}
		else {
			
		$students = $this->db->Query("SELECT f.name as affiliate, f.event_id, p.supporter_id, p.first_name, p.last_name,p.school_id, p.school_name, p.total_raised
FROM participants p 
JOIN affliates f ON (f.event_id = p.event_id)");


		$activities = $this->db->Query("SELECT  supporter_id, `type`, COUNT(id) AS total FROM 
activities
GROUP BY supporter_id");

		$email_open  = $this->db->Query("SELECT supporter_id, COUNT(email_open) AS `total`
	FROM activities
	WHERE !ISNULL(team_id) AND `type` = 2 AND email_open = 1 
	GROUP BY `supporter_id`");			
	
		$email_visit = $this->db->Query("SELECT supporter_id, COUNT(email_open) AS `total`
	FROM activities
	WHERE !ISNULL(team_id) AND `type` = 2 AND link_visit_open = 1 
	GROUP BY `supporter_id`" );

		}
		
		
		$email_open = Util::groupArray($email_open,'supporter_id','total');
		$email_visit = Util::groupArray($email_visit,'supporter_id','total');
		$activities = Util::groupMultiArray($activities,'supporter_id');
		//print_r($activities);
		$data = array();
		
		foreach ($students as $s){
			
			$sup_id = $s['supporter_id'];
			
			
				$item = array();
				$item['logins'] = 0;
				$item['emails'] = 0;
				$item['shares'] = 0;
				
				$item['affiliate'] = $s['affiliate'];
				$item['event_id'] = $s['event_id'];
				$item['supporter_id'] = $sup_id;
				$item['first_name'] = $s['first_name'];
				$item['last_name'] = $s['last_name'];
				$item['school_id'] = $s['school_id'];
				$item['school_name'] = $s['school_name'];
				$item['raised'] = $s['total_raised'];
			
			
			
			if(isset($activities[$sup_id])){
				
				$activity = Util::groupArray($activities[$sup_id],'type');
				
				foreach ($activities[$sup_id] as $act){
					
					if($act['type'] == 1){
						$item['logins'] = $act['total'];
					}
					elseif($act['type'] == 2){
						$item['emails'] = $act['total'];
					}
					elseif($act['type'] == 3){
						$item['shares'] = $act['total'];
					}
					
				}
					
			}
			
			
			
			if(isset($email_open[$sup_id])){
				$item['email_open'] = $email_open[$sup_id];	
			}
			else{
				$item['email_open'] = 0;
			}
			
			if(isset($email_visit[$sup_id])){
				$item['email_visit'] = $email_visit[$sup_id];	
			}
			else{
				$item['email_visit'] = 0;
			}		
			
			
			$str .= $item['affiliate'].",".$item['event_id'].",".$item['school_id'].",\"".str_replace('"',"",$item['school_name'])."\",".$item['supporter_id'].",\"".str_replace('"',"",$item['first_name'])."\",\"".str_replace('"',"",$item['last_name'])."\",".$item['logins'].",".$item['emails'].",".$item['shares'].",".$item['email_open'].",".$item['email_visit'].",".$item['raised'];
			
			$str .= "\n";
			
		}

		
				
		return $str;	
		
		
		
	}
	
	
	
	
	private function _clean ($str){
		
		return str_replace(
			array('"',','),
			array('',''),
			$str
			);
			
	}
	

} // class





?>