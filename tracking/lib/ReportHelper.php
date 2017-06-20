<?php
ini_set('memory_limit', '512M');
include_once('webrequest.php');


class ReportHelper {
	
	var $request;
	var $username;
	var $password;
	var $userid;
	
	function ReportHelper ($username, $password, $user_id = 0){
		
		$this->request = new WebRequest("report");
		$this->username = $username;
		$this->password = $password;
		$this->userid = $user_id;
	}
	
	function getReport ($account_id, $report_id) {
		
		// login
		$this->request->clear();
		
		$this->request->Post("https://www.kintera.com/Kintera_Sphere/login/asp/Login.aspx", "LoginName=" . $this->username. "&Password="  . $this->password);
		
		$this->request->Post("https://www.kintera.com/Kintera_Sphere/login/asp/LoginAccount.aspx", "LR1_CP_choosecolumns_flag=&SelUserID=".$account_id."&__EVENTARGUMENT=&__EVENTTARGET=&");
		
		// get report		
		$this->request->Get("https://www.kintera.com/kintera_sphere/reports/asp/individual_report.asp?id=".$report_id."&con=true");
		
		$this->request->Post("https://www.kintera.com/kintera_sphere/reports/asp/customize_b_adv.asp?prerun=1","__begindate=&__enddate=&__rangename=%2Fkintera_sphere%2Freports%2Fasp%2Fcustomize_b_adv.asp&__rangetype=TODATE&newselectedIDs=&popupflag=&submit_type=finish");
		
			$data = $this->request->Post('https://www.kintera.com/kintera_sphere/reports/asp/customize_prepare.asp', 'submit_type=finish&choose_offline=no');
		
			//echo $data;

		return $this->parse( $data);
		
		
	}

	
	function parse ($content, $table_index = 16){
		
		$data = explode("<table", $content);
		$data = explode("<tr", $data[$table_index]);
		
		$headers = array();
		$report = array();
		
		//extract headers
		$header_row = $data[1];
		$cols = explode("<td",$header_row);
		
		foreach ($cols as $col){
			
			$headers[] = trim(str_replace(array(" ",'&nbsp;'),array("_",""),strtolower(strip_tags("<td ".$col))));
		
		}
		
		$header_count = count($headers);
		
		for($i = 2; $i < count($data); $i++){
			
			$tds = explode("<td", $data[$i]);
			
			if(count($tds) == $header_count){
				
				$line = array();
				
				for($c = 1; $c < $header_count; $c++){				
					
					$line[$headers[$c]] = strip_tags("<td ".$tds[$c]);
				}
				$report[] = $line;
			}	
		}

		return $report;
	}
	
	
	function urlencode($str){
		// custom url encode function
		$str = str_replace( array("&amp;","&gt;","&lt;","&quot;"), array("&",">","<",'"'), $str);
		$find = array( "%", "¶", " ",  "\"", "#", "$", "&amp;","&",  "+", ",",   "/", ":", ";", "<", "=", ">", "?", "@", "", "\\", "", "^", "`", "{", "|", "}",']','[',"\t");

		$replace = array("%25", "%0D", "%20",  "%22", "%23", "%24", "%26", "%26",  "%2B", "%2C",   "%2F", "%3A", "%3B", "%3C", "%3D", "%3E", "%3F", "%40", "%5B", "%5C", "%5D", "%5E", "%60", "%7B", "%7C", "%7D",'%5D','%5B','%09');
		
		return str_replace($find, $replace, $str);
	}
	
	
	function extactElement ($html, $id, $encode = false){
		
		$parts = explode('id="'.$id.'"', $html);
		
		if(count($parts) == 1){
			$parts = explode('name="'.$id.'"', $html);
		}
		
		
		$parts = explode('value="', $parts[1]);
		$parts = explode('"', $parts[1]);
		
		
		if($encode){
			return $this->urlencode($parts[0]);	
		}
		
		return $parts[0];
		
	}
	
	
}

?>