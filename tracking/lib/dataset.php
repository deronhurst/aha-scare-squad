<?php
include_once('DataContext.php');
include_once('datatypes.php');
include_once('util.php');

class DataSet {
	
	private $db;
	
	public $start_date = NULL;
	public $end_date = NULL;
	public $challenge = NULL;
	public $type = NULL;
	public $school = NULL;
	
	public $data = array();
	
	
	function DataSet (){
		
		$this->db = new DataContext();
		
		// starting date
		if(!empty($_REQUEST['start'])){
			$this->start_date = date("Y-m-d",strtotime($_REQUEST['start']));	
		}
		else {
			$this->start_date = date("Y-m-d");
		}
		
		// ending date
		if(!empty($_REQUEST['end'])){
			$this->end_date = date("Y-m-d",strtotime($_REQUEST['end']));	
		}
		else {
			$this->end_date = date("Y-m-d");
		}
		
		// set specific date ranges
		if(!empty($_REQUEST['start'])){
			if($_REQUEST['start'] == 'thisweek'){
				$this->start_date = date("Y-m-d",strtotime("last sunday"));
				$this->end_date = date("Y-m-d");
			}
			elseif($_REQUEST['start'] == 'lastweek'){
				$this->end_date = date("Y-m-d",strtotime("last saturday"));
				$this->start_date = date("Y-m-d", strtotime("-1 week", strtotime("last saturday")));
			}
			elseif($_REQUEST['start'] == 'thismonth'){
				$this->start_date = date("Y-m-1");
				$this->end_date = date("Y-m-d");
			}
			elseif($_REQUEST['start'] == 'lastmonth'){
				$this->end_date = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-1"))));;
				$this->start_date = date("Y-m-d", strtotime("-1 month", strtotime(date("Y-m-1"))));
			}
			elseif($_REQUEST['start'] == 'lastmonth'){
				$this->end_date = NULL;
				$this->start_date = NULL;
			}
		}
		
		// add 1 day into end date
		if(!empty($this->end_date)) {
			$this->end_date = date("Y-m-d", strtotime("+1 day", strtotime($this->end_date)));
		}
		
		// challenge
		if(!empty($_REQUEST['challenge']) && $_REQUEST['challenge'] != 'all'){
			$this->challenge = $_REQUEST['challenge'];	
		}
		
		// activity type
		if(!empty($_REQUEST['type'])){
			$this->type = $_REQUEST['type'];	
		}
		
		//school
		if(!empty($_REQUEST['school'])){
			$this->school = $_REQUEST['school'];	
		}
		
		//echo $this->start_date." |  ".$_REQUEST['start']." | ".$this->end_date." | ".$_REQUEST['end'];
		
	}
	
	public function load ( $detail = false){
		
		if(!$detail){
			
			$sql = "SELECT a.supporter_id, p.school_id, p.school_name, a.choice, a.type , DATE_FORMAT(created, '%c/%e/%Y') as date
			FROM activities a
LEFT JOIN participants p ON (p.supporter_id = a.supporter_id && p.event_id = a.event_id) ";
			
		}
		else{
			$sql = "SELECT a.supporter_id, a.event_id,p.first_name,p.last_name,p.address_city,p.address_state,p.total_raised,p.affiliate,p.event_name,p.team_id,p.team_name,p.school_id,p.school_name, a.choice, a.type, DATE_FORMAT(created,'%c/%e/%Y') as date 
			FROM activities a
LEFT JOIN participants p ON (p.supporter_id = a.supporter_id && p.event_id = a.event_id) ";
		}

		$fileter = array();
		
		// date filter
		if(!empty($this->start_date)) {
			$fileter[] = " a.created >= '".$this->start_date."' && a.created < '".$this->end_date."' ";
		}
		
		// challenge filet
		if(!empty($this->challenge)){
			$fileter[] = " ( a.choice in (".$this->db->escape($this->challenge).") || isnull(a.choice) )";	
		}
		
		// type filer
		if(!empty($this->type)){
			$fileter[] = " a.type = '".$this->db->escape($this->type)."' ";
		}
		
		// school filter
		if($this->school == 'top5' || $this->school == 'top10'){
			
			$limit = str_replace('top','',$this->school);
			
			$fil = $this->db->Query("SELECT DISTINCT(p.school_id), SUM(a.type) AS active
FROM activities a
LEFT JOIN participants p ON (p.supporter_id = a.supporter_id && p.event_id = a.event_id)
GROUP BY p.school_id ORDER BY active DESC LIMIT ".$limit);

			$scs = array();
			
			foreach ($fil as $f){
				
				if(!empty($f['school_id'])) {
					$scs[] = $f['school_id'];
				}
			}
			
			$fileter[] = " p.school_id in (".implode(',',$scs).") ";
			
		}
		elseif(!empty($this->school) && $this->school != 'all'){
			$fileter[] = " p.school_id = '".$this->db->escape($this->school)."' ";
		}
		
		$sql .= " WHERE ".implode("&&", $fileter)." order by created";
		//echo $sql;
		$this->data = $this->db->Query($sql);
		
	}
	
	
	// Arrange data by activity -> schools -> students
	public function getByActivity (){
		
		$out = array(
			1 => array('total' => 0, 'parts' => 0, 'schools' => 0),
			2 => array('total' => 0, 'parts' => 0, 'schools' => 0),
			3 => array('total' => 0, 'parts' => 0, 'schools' => 0),
		);
		
		$tmp = Util::groupMultiArray($this->data,'type');
		
		foreach ($tmp as $type => $typedata){
			
			$out[$type]['total'] = count($typedata);
			
			$parts = Util::groupMultiArray($typedata,'supporter_id');
			$out[$type]['parts'] = count($parts);
			
			$schools = Util::groupMultiArray($typedata,'school_id');
			$out[$type]['schools'] = count($schools);	
		}
		
		return $out;
	}
	
	
	// Arrange data by challenge and activity
	public function getByChallenge (){
		
		$challenge = array();
		
		$tmp = Util::groupMultiArray($this->data, 'choice');
		
		foreach ($tmp as $choice_id => $choicedata){
			
			if(empty($choice_id)){
				continue;	
			}
			
			$activities = Util::groupMultiArray( $choicedata, 'type');
			/*echo '<pre>';
			print_r($choicedata);
			print_r($activities );
			echo '</pre>';*/
			$challenge[$choice_id][0] = count($choicedata);
			
			foreach ($activities as $typeid => $typedata){
				 
				$challenge[$choice_id][$typeid] = count($typedata);
			}
			
		}
		
		return $challenge; //uasort($challenge,'sortByActivity');
		
	}
	
	// Arrange data by school and acivity
	public function getBySchool (){
		
		$schools = array();
		
		$tmp = Util::groupMultiArray($this->data, 'school_id');
		
		foreach ($tmp as $school_id => $choicedata){
			
			
			
			$activities = Util::groupMultiArray( $choicedata, 'type');
			
			$schools[$school_id][0] = count($choicedata);
						
			foreach ($activities as $typeid => $typedata){
				$schools[$school_id]['id'] = $school_id;
				/*if(!empty($schools[$school_id]['name'])) {
					$schools[$school_id]['name'] =  $choicedata[0]['school_name'];
				}*/
				
				$schools[$school_id][$typeid] = count($typedata);
			}
			
		}
		
		uasort($schools,'sortByActivity');
		return $schools;
	}
	
	// organize activity by date
	public function getStatisticByDate (){
		
		$dates = array();
		$logins = array();
		$email = array();
		$shares = array();
		
		$tmp = Util::groupMultiArray($this->data, 'date');
		
		// transform it to to be abel to plot on the graph
		foreach ($tmp as $dt => $data){
			
			$dates[] = $dt;
			
			$types = Util::groupMultiArray($data, 'type');
			
			$logins[] = (isset($types[1])) ? count($types[1]) : 0;
			$email[] = (isset($types[2])) ? count($types[2]) : 0;
			$shares[] = (isset($types[3])) ? count($types[3]) : 0;
	
			
		}
		
		return array('label' => $dates, 'login' => $logins, 'email' => $email, 'share' => $shares);
		
		//print_r($out);
		//
	}
	
	
	function getSchoolNames  () {
		
		$data = $this->db->Query("SELECT DISTINCT(p.school_id), p.school_name
FROM activities a
LEFT JOIN participants p ON (p.supporter_id = a.supporter_id && p.event_id = a.event_id)");

		$out = array();
		
		foreach ($data as $item){
			
			if(!empty($item['school_id'])){
				$out[$item['school_id']] = $item['school_name'];
			}
			
		}
		
		
		return $out;
	}
	
	
} // class


?>