<?php
include_once('DataContext.php');
include_once('datatypes.php');
include_once('util.php');

//ini_set("memory_limit","256M");

class DataSet {
	
	private $db;
	
	public $start_date = NULL;
	public $end_date = NULL;
	public $challenge = NULL;
	public $type = NULL;
	public $school = NULL;
	public $affliatessource = NULL;
	public $data = array();
	public $analysisemail = NULL;
	public $analysisfb = NULL;
	
	private $filters;
	
	
        public $affliatename = NULL;
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
		// Affliate Filter
		if(!empty($_REQUEST['affliatename'])) {
			$this->affliatename = $_REQUEST['affliatename'];
		}
                else{
                    $this->affliatename = '';
                }
		//echo $this->start_date." |  ".$_REQUEST['start']." | ".$this->end_date." | ".$_REQUEST['end'];
		
	}
        
        public function getVisitemails ( $detail = false){
		
		if(!$detail){
			
			$sql = "SELECT COUNT(link_visit_open) AS link_visit_open
				FROM activities a LEFT JOIN participants p ON (p.supporter_id = a.supporter_id && p.event_id = a.event_id) 

                        where link_visit_open = '1' ";
			
		}
		else{
			$sql = "SELECT COUNT(link_visit_open) AS link_visit_open
						FROM activities a LEFT JOIN participants p ON (p.supporter_id = a.supporter_id && p.event_id = a.event_id) 

                         where link_visit_open = '1' ";
		}

		$fileter = array();
		/**
		// date filter
		if(!empty($this->start_date)) {
			$fileter[] = " a.created >= '".$this->start_date."' && a.created < '".$this->end_date."' ";
		}
		
		// challenge filet
		if(!empty($this->challenge)){
			$fileter[] = " ( a.choice in (".$this->db->escape($this->challenge).") || isnull(a.choice) )";	
		}
		// affliate filter
		if(!empty($this->affliatename)) {
			$fileter[] = " a.event_id = '".$this->affliatename."'  ";
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
		*/
		
	      $sql .= " AND ".implode("&&", $this->filters);
			
			
		
		return ($this->db->Query($sql));
		
	}
        public function getOpenemails ( $detail = false){
		
		if(!$detail){
			
			$sql = "SELECT COUNT(email_open) AS email_open
			FROM activities a LEFT JOIN participants p ON (p.supporter_id = a.supporter_id && p.event_id = a.event_id) 
                        where email_open = '1' ";
			
		}
		else{
			$sql = "SELECT COUNT(email_open) AS email_open
						FROM activities a LEFT JOIN participants p ON (p.supporter_id = a.supporter_id && p.event_id = a.event_id) 

                         where email_open = '1' ";
		}

		$fileter = array();
		
		/*
		
		// date filter
		if(!empty($this->start_date)) {
			$fileter[] = " a.created >= '".$this->start_date."' && a.created < '".$this->end_date."' ";
		}
		
		// challenge filet
		if(!empty($this->challenge)){
			$fileter[] = " ( a.choice in (".$this->db->escape($this->challenge).") || isnull(a.choice) )";	
		}
		// affliate filter
		if(!empty($this->affliatename)) {
			$fileter[] = " a.event_id = '".$this->affliatename."'  ";
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
		
		*/
		
	     $sql .= " AND ".implode("&&", $this->filters);
		
		return ($this->db->Query($sql));
		
	}
	
	public function load ( $detail = false, $loaddata = false){
		
		if(!$detail){
			
			$sql = "SELECT a.supporter_id, p.school_id, p.school_name, a.choice, a.type , DATE_FORMAT(created, '%c/%e/%Y') as date,a.email_open AS email_open,a.link_visit_open AS link_visit_open
			FROM activities a
LEFT JOIN participants p ON (p.supporter_id = a.supporter_id && p.event_id = a.event_id) ";
			
		}
		else{
			/*
			$sql = "SELECT a.supporter_id, a.event_id,p.first_name,p.last_name,
                                SUM(p.total_raised) AS total_raised,
                                p.affiliate,p.event_name,p.team_id,p.team_name,p.school_name, 
                                (select count(*) AS '# Logins' from activities where activities.supporter_id = a.supporter_id and type = '1')	AS '# Logins',
                                (select count(*) AS '# Emails' from activities where activities.supporter_id = a.supporter_id and type = '2')	AS '# Emails',
                                (select count(*) AS '# Shared' from activities where activities.supporter_id = a.supporter_id and type = '3')	AS '# Shared',
                                COUNT(a.email_open) AS email_open,COUNT(a.link_visit_open) AS link_visit_open FROM activities a
                                LEFT JOIN participants p ON (p.supporter_id = a.supporter_id && p.event_id = a.event_id)  ";
				*/
				
				$sql = "SELECT a.supporter_id, a.event_id,p.first_name,p.last_name,
p.total_raised AS total_raised,
p.affiliate,p.event_name,p.team_id,p.team_name,p.school_id,p.school_name, a.choice,a.type , COUNT(*) AS total,
COUNT(a.email_open) AS email_open,COUNT(a.link_visit_open) AS link_visit_open FROM activities a
LEFT JOIN participants p ON (p.supporter_id = a.supporter_id )                                ";
								
								
								
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
		// affliate filter
		if(!empty($this->affliatename)) {
			$fileter[] = " a.event_id = '".$this->affliatename."'  ";
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
LEFT JOIN participants p ON (p.supporter_id = a.supporter_id )
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
		
	        $sql .= " WHERE ".implode("&&", $fileter);
			
			$this->filters = $fileter;
			
			
                if($detail){
					// this will give max 3 rows per supporter (for activity type)
                  $sql .= " group by a.supporter_id, a.type";  
                }
                $sql .= " order by created ";;
				
                if(!$detail){
					
					 if($loaddata) {
						 $this->data = $this->db->Query($sql);
					 }
					 else{
						 return true;
					 }
                }
                else{
					
					
					if(!$loaddata) {
						return true;
					}
		
					
		$data = $this->db->Query($sql);
		
		
		
		$this->data = array();
		
		// not loading data *****************************************
		return true;
		
		// formatting data
		foreach ($data as $row){
			
			if(empty($this->data[$row['supporter_id']])){
				
				$this->data[$row['supporter_id']] = $row;
								
			}
			
			if($row['type'] == 1){
				$this->data[$row['supporter_id']]['# Logins'] = $row['total'];	
			}
			elseif($row['type'] == 2){
				$this->data[$row['supporter_id']]['# Ecards'] = $row['total'];	
			}
			elseif($row['type'] == 3){
				$this->data[$row['supporter_id']]['# Shared'] = $row['total'];	
			}
				
		}
       }
	}
	
	
	// Arrange data by activity -> schools -> students
	public function getByActivity (){
		
		if(empty($this->filters)){
			$this->filters[] = " 1 = 1 ";	
		}
		
		$f = array('a.', 'p.school_id');
		$r = array('','team_id');
		
		$totals = $this->db->Query("select type,count(*) as total  from activities where ".str_replace($f, $r,implode("&&", $this->filters))." group by type");

		$schools = $this->db->Query("select type,count(distinct team_id) as total  from activities where ".str_replace($f, $r,implode("&&", $this->filters))." group by type");

		$parts = $this->db->Query("SELECT type,COUNT(DISTINCT supporter_id) AS total  FROM activities where ".str_replace($f, $r,implode("&&", $this->filters))." GROUP BY TYPE");
		
		$out = array(
			1 => array('total' => 0, 'parts' => 0, 'schools' => 0),
			2 => array('total' => 0, 'parts' => 0, 'schools' => 0),
			3 => array('total' => 0, 'parts' => 0, 'schools' => 0)                       
		);
		
		
		foreach ( $totals as $item) {
			
			$out[$item['type']]['total'] = $item['total'];
		}
		
		foreach ( $schools as $item) {
			
			$out[$item['type']]['schools'] = $item['total'];
		}
		
		foreach ( $parts as $item) {
			
			$out[$item['type']]['parts'] = $item['total'];
		}
		
		/*
		$tmp = Util::groupMultiArray($this->data,'type');
		
		foreach ($tmp as $type => $typedata){
			
			$out[$type]['total'] = count($typedata);
			
			$parts = Util::groupMultiArray($typedata,'supporter_id');
			$out[$type]['parts'] = count($parts);
			
			$schools = Util::groupMultiArray($typedata,'school_id');
			$out[$type]['schools'] = count($schools);	
		}*/
		
		return $out;
	}
	
	
	// Arrange data by challenge and activity
		// Arrange data by challenge and activity
	public function getByChallenge (){
		
		$challenge = array();
		
		$tmp = Util::groupMultiArray($this->data, 'choice');
		
		foreach ($tmp as $choice_id => $choicedata){
			
			if(empty($choice_id)){
				continue;	
			}
			
			$open_email_count = '0';
            $link_visit_open = '0';
			
			foreach ($choicedata as $choiceid => $choices){
				
			   if(($choices['email_open'] == '1')) {
					  $open_email_count++;
				}
						
				if(($choices['link_visit_open'] == '1')) {
					  $link_visit_open++;
				}
			}
			$challenge[$choice_id][4] = $open_email_count;
			$challenge[$choice_id][5] = $link_visit_open;
						
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
                        $open_email_count = '0';
                        $link_visit_open = '0';
			foreach ($choicedata as $choiceid => $choices){
                               if(($choices['email_open'] == '1')) {
                                      $open_email_count++;
                                    }
                                if(($choices['link_visit_open'] == '1')) {
                                      $link_visit_open++;
                                    }
                        }
                        $schools[$school_id][4] = $open_email_count;
                        $schools[$school_id][5] = $link_visit_open;
			$activities = Util::groupMultiArray( $choicedata, 'type');
			
			$schools[$school_id][0] = count($choicedata);
						
			foreach ($activities as $typeid => $typedata){
				$schools[$school_id]['id'] = $school_id;
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
                $open_email = array();
		$link_visit = array();
		$tmp = Util::groupMultiArray($this->data, 'date');
		
		// transform it to to be abel to plot on the graph
		foreach ($tmp as $dt => $data){
                       $open_email_count = '0';
                       $link_visit_open = '0';
			foreach ($data as $choiceid => $choices){
                               if(($choices['email_open'] == '1')) {
                                      $open_email_count++;
                                    }
                                if(($choices['link_visit_open'] == '1')) {
                                      $link_visit_open++;
                                    }
                        }
                        $open_email[] = $open_email_count;
			$link_visit[] = $link_visit_open;
                        
			$dates[] = $dt;
			
			$types = Util::groupMultiArray($data, 'type');
			
			$logins[] = (isset($types[1])) ? count($types[1]) : 0;
			$email[] = (isset($types[2])) ? count($types[2]) : 0;
			$shares[] = (isset($types[3])) ? count($types[3]) : 0;
	                
		}
		
		return array('label' => $dates, 'login' => $logins, 'email' => $email, 'share' => $shares, 'open_email_count' => $open_email, 'link_visit_open' => $link_visit);
		
		//print_r($out);
		//
	}
	// organize activity by date
	public function getStatisticByAnalysis (){
          //  var_dump($this->analysisemail);exit();
		$affliates = array();
		$conversionrates = array();
                $averagerates = array();
                $fconversionrates = array();
                $faveragerates = array();
                $faffliates = array();
	       // $analysis = $this ->getAnalysisEmails(1);
                  foreach ($this->analysisemail as $index => $r) {
                
                        $email_sentpercent = ROUND((($r['donatecount'] / $r['sent']) * 100),2);
                        $avg_sent = ROUND((($r['raised'] / $r['sent'])), 2);
                        $affliates[] = $r['account'];
                        $averagerates[] = $avg_sent;
                        $conversionrates[] = $email_sentpercent;
		}
                  foreach ($this->analysisfb as $indexes => $fb) {
                        $faffliates[] = $fb['account'];
                        $fb_sentpercent = ROUND((($fb['donatecount'] / $fb['shared']) * 100),2);
                        $avg_fbsent = ROUND((($fb['raised'] / $fb['shared'])), 2);
                        $faveragerates[] = $fb_sentpercent;
                        $fconversionrates[] = $avg_fbsent;
		}
               return array('label' => $affliates, 'emailsent' => $conversionrates, 'averagesent' => $averagerates, 'fconversionrate' => $fconversionrates, 'faveragesent' => $faveragerates,'flabel' => $faffliates);
       }
	
	function getSchoolNames  ($schoolfilter) {
		$query =    "SELECT DISTINCT(p.school_id), p.school_name
                             FROM activities a
                             LEFT JOIN participants p ON (p.supporter_id = a.supporter_id && p.event_id = a.event_id)";
               if($schoolfilter != ''){
                   $query .= " where a.event_id =  ".$schoolfilter;
                }
                $query .= " order by p.school_name";
               // $data = $this->db->Query("SELECT DISTINCT(p.school_id), p.school_name
                //                            FROM activities a
                //                            LEFT JOIN participants p ON (p.supporter_id = a.supporter_id && p.event_id = a.event_id)");
                 $data = $this->db->Query($query);
		$out = array();
		
		foreach ($data as $item){
			
			if(!empty($item['school_id'])){
				$out[$item['school_id']] = $item['school_name'];
			}
			
		}
		
		
		return $out;
	}
	  public function reports (){
		$sql = "SELECT * from reports ";
		return ($this->db->Query($sql));
  }		
  
          public function getDonationTotals($grouptype=''){
                 Switch($grouptype){
                  Case "affliate":
                      $grouptype = 'group by account';
                    break;
                  Case "source":
                      $grouptype = 'group by name';
                    break;
                  default:
                       $grouptype = '';
                    break;  
              }
               $data = $this->db->Query("select SUM(counts) AS totalcount, ROUND(SUM(donation),2) AS totaldonation,((SUM(counts)/SUM(counts)) *100) AS activity_count_percent,(SUM(donation) / SUM(donation)) *100 AS donation_count_percent,ROUND(SUM(donation) / SUM(counts), 2) AS average from donations");
               
               return array('totalcount' => $data[0]['totalcount'], 'totaldonation' => $data[0]['totaldonation'], 'activity_count_percent' => $data[0]['activity_count_percent'], 'donation_count_percent' => $data[0]['donation_count_percent'], 'average' => $data[0]['average']);



          }		
          public function getDonationSummary($grouptype){
              $dontotals = $this ->getDonationTotals();
              
              Switch($grouptype){
                  Case "affliate":
                      $grouptype = 'group by account';
                    break;
                  Case "source":
                      $grouptype = 'group by name';
                    break;

                  default:
                    $grouptype = '';

                    break;  
              }
              $sql = "select account,name, SUM(counts) AS totalcount,ROUND(SUM(donation),2) AS totaldonation,ROUND(((SUM(counts)/".$dontotals['totalcount'].")*100),2) AS activity_count_percent,roUND(((SUM(donation)/".$dontotals['totaldonation'].") * 100),2) AS donation_count_percent,round((SUM(donation) / SUM(counts)), 2) AS average from donations ".$grouptype;
		
              return ($this->db->Query($sql));
          }
          public function getDonationDetail($account){
              $sql = "select *,round((donation / counts), 2) AS average from donations where account = '".$account."' ";
              return ($this->db->Query($sql));
          }
          public function getDonationsByAffliate(){
              $sql = "select account,ROUND(SUM(donation),2) AS donation,SUM(counts) AS donorcount from donations group by account";
              return ($this->db->Query($sql));
          }
          public function getDonationsBySource(){
              $sql = "select name,ROUND(SUM(donation),2) AS donation,SUM(counts) AS donorcount from donations group by name";
              return ($this->db->Query($sql));
          }
          public function getLastUpdated(){
              $data = $this->db->Query("select CONCAT(DATE_FORMAT(created,'%m-%d-%Y %r'),' EST') AS lastupdated from donations order by created DESC limit 1");
           
              return $data[0]['lastupdated'];
        }
              public function getAnalysisCounts($type) {
               Switch($type){
                  Case "1" :
                      $types = 'zooEmail';
                      break;
                  Case "2" :
                      $types = 'zooFB';
                      break;
                  default :
                      break;
               }
                    $sql = "Select DonationCodeSummary.code AS code, DonationCodeSummary.account AS account, SUM(DonationCodeSummary.donatecount) AS donatecount,
                            SUM(DonationCodeSummary.raised) AS raised, SentShared.name, SUM(SentCount.sent) AS sent, SUM(SentShared.shared) AS shared  
                            ,ROUND((SUM(DonationCodeSummary.donatecount)/SUM(SentCount.sent))*100,2) AS email_sentpercent
                           ,ROUND((SUM(DonationCodeSummary.raised)/SUM(SentCount.sent)),2) AS avg_sent  
                           ,ROUND((SUM(DonationCodeSummary.donatecount)/SUM(SentShared.shared))*100,2) AS fb_sentpercent
                           ,((SUM(DonationCodeSummary.raised)/SUM(SentShared.shared))) AS fb_sent
                              From
                           (select affliates.name, activities.event_id,COUNT(*) AS shared
                                FROM `activities` 
                                JOIN  affliates
                               ON activities.event_id=affliates.event_id
                                where type = '3'
                                Group by activities.event_id) AS SentShared
                            JOIN
                            (select affliates.name, activities.event_id,COUNT(*) AS sent
                                FROM `activities` 
                                JOIN  affliates
                               ON activities.event_id=affliates.event_id
                                where type = '2'
                                Group by activities.event_id) AS SentCount
                            Join DonationCodeSummary on
                            (SentShared.name=DonationCodeSummary.account  && SentCount.name=DonationCodeSummary.account)
                            where DonationCodeSummary.code LIKE '%".$types."%' order by DonationCodeSummary.account";
                    $data = $this->db->Query($sql);
                  return array( 'donatecount' => $data[0]['donatecount'], 'raised' => $data[0]['raised'], 'sent' => $data[0]['sent'], 'shared' => $data[0]['shared'], 'email_sentpercent' => $data[0]['email_sentpercent'], 'avg_sent' => $data[0]['avg_sent'], 'fb_sentpercent' => $data[0]['fb_sentpercent'], 'fb_sent' => $data[0]['fb_sent']  );

                     
                    
               }
			   
           public function getAnalysisEmails($type) {
               Switch($type){
                  Case "1" :
                      $types = 'zooEmail';
                      break;
                  Case "2" :
                      $types = 'zooFB';
                      break;
                  default :
                      break;
               }
                    $sql = "Select DonationCodeSummary.code, DonationCodeSummary.account, DonationCodeSummary.donatecount,
                            DonationCodeSummary.raised, 
                           SentShared.name, SentCount.sent, SentShared.shared ,ROUND((DonationCodeSummary.donatecount/(SentCount.sent))*100,2) AS email_sentpercent
                           ,ROUND(((DonationCodeSummary.raised)/(SentCount.sent)),2) AS avg_sent  
                           ,ROUND(((DonationCodeSummary.donatecount)/(SentShared.shared))*100,2) AS fb_sentpercent
                           ,ROUND(((DonationCodeSummary.raised)/(SentShared.shared)),2) AS fb_sent
                            From
                           (select affliates.name, activities.event_id,COUNT(*) AS shared
                                FROM `activities` 
                                JOIN  affliates
                               ON activities.event_id=affliates.event_id
                                where type = '3'
                                Group by activities.event_id) AS SentShared
                            JOIN
                            (select affliates.name, activities.event_id,COUNT(*) AS sent
                                FROM `activities` 
                                JOIN  affliates
                               ON activities.event_id=affliates.event_id
                                where type = '2'
                                Group by activities.event_id) AS SentCount
                            Join DonationCodeSummary on
                            (SentShared.name=DonationCodeSummary.account  && SentCount.name=DonationCodeSummary.account)
                            where DonationCodeSummary.code  LIKE '%".$types."%' order by DonationCodeSummary.account";
                    		
                     Switch($type){
                            Case "1" :
                                $this->analysisemail = $this->db->Query($sql);
                                return($this->analysisemail);
                                break;
                            Case "2" :
                                $this->analysisfb = $this->db->Query($sql);
                                return($this->analysisfb);
                                break;
                            default :
                                break;
               }
                     
                    
               }
			   
			   
	public function getClickedByEvent () {
		
		
		return $this->db->Query("SELECT f.name AS affiliate, SUM(link_visit_open) AS total FROM activities a LEFT JOIN affliates f ON (f.event_id = a.event_id) GROUP BY a.event_id");
		
	}
              
             
	// organize affliates by donation Amounts
	
     public function getStatisticByAffliate (){
		
		$affliate = array();
		$amounts = array();
		$donorcount = array();
		$tmp = $this->getDonationsByAffliate();
		
		// transform it to to be abel to plot on the graph
		foreach ($tmp as $dt => $data){
                   
            $affliate[] = $data['account'];
			$amounts[] =  $data['donation'];
            $donorcount[] =  $data['donorcount'];
		}
                
		return array('label' => $affliate, 'amounts' => $amounts, 'donorcount' => $donorcount);
		
	}
        // organize affliates by donation Amounts
	
	public function getStatisticBySource (){
		
		$source = array();
		$amounts = array();
		$donorcount = array();
		$tmp = $this->getDonationsBySource();
		
		// transform it to to be abel to plot on the graph
		foreach ($tmp as $dt => $data){
                   
                        $source[] = $data['name'];
			$amounts[] =  $data['donation'];
                        $donorcount[] =  $data['donorcount'];
		}
                
		return array('label' => $source, 'amounts' => $amounts, 'donorcount' => $donorcount);
		
	}
          public function getAffliates (){
		    $sql = "select event_id, name from affliates ";
		    $this->affliatessource = $this->db->Query($sql);
                    return($this->affliatessource);
}
} // class


?>