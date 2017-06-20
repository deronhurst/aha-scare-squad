<?php

class Util {
	
	public static function groupArray ($array, $key, $value = NULL){
		
		//order array and assign given KEY as array key, if VALUE present, array will have only given VALUE field
		
		$output = array();
		$objects = array();
		
		foreach ($array as $element){
			
			if(!empty($value)) {
				$output[$element[$key]] = $element[$value];	
			}
			else {
			//	print_r ( debug_backtrace());
				$output[$element[$key]] = $element;	
			}
			
			
		}		
		
		return $output;
		
	}
	
	public static function groupMultiArray ($array, $key, $value = NULL){
		
		$output = array();
		$objects = array();
		
		foreach ($array as $element){
			
			if(!isset($output[$element[$key]])){
				$output[trim($element[$key])] = array();
			}
			
			if(!empty($value)) {
				$output[trim($element[$key])][] = $element[$value];	
			}
			else {
				$output[trim($element[$key])][] = $element;	
			}
			
			
		}
		
		return $output;
		
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