<?php

class Util {
	
	public static function groupArray ($array, $key, $value = NULL){
		
		//order array and assign given KEY as array key, if VALUE present, array will have only given VALUE field
		
		$output = array();
		$objects = array();
		
		foreach ($array as $element){
			
			if(!empty($value)) {
				$output[strtolower(trim($element[$key]))] = trim($element[$value]);	
			}
			else {
			//	print_r ( debug_backtrace());
				$output[strtolower(trim($element[$key]))] = $element;	
			}
			
			
		}		
		
		return $output;
		
	}
	
	public static function groupMultiArray ($array, $key, $value = NULL){
		
		$output = array();
		$objects = array();
		
		foreach ($array as $element){
			
			if(!isset($output[$element[$key]])){
				$output[$element[$key]] = array();
			}
			
			if(!empty($value)) {
				$output[$element[$key]][] = $element[$value];	
			}
			else {
				$output[$element[$key]][] = $element;	
			}
			
			
		}
		
		return $output;
		
	}
	
	public static function getEventTotal ($event_id){

		$c = curl_init();
		curl_setopt($c, CURLOPT_URL,"http://www.kintera.org/gadgets/data/thermometer.aspx?eid=".$event_id);		   
		curl_setopt($c, CURLOPT_HEADER,0);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.12) Gecko/20080201 Firefox/2.0.0.12");
		curl_setopt($c,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($c, CURLOPT_MAXREDIRS, 10);
		$page = curl_exec($c);
		curl_close($c); 
		
		$page = str_replace('=','":', $page);
		$page = str_replace('&',', "',$page);
		
		return json_decode('{"'.$page.'}', true);

		
	}
	
}



/**************** sorting ******************/
// sort by activity | desc
function sortByActivity ( $a, $b){
		
	if($a[0] == $b[0]){
		return 0;	
	}
	
	return ( $a[0] < $b[0] ) ? 1 : -1;
}

?>