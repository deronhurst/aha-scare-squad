<?php

	set_time_limit(0);

	$path = dirname(__FILE__)."/shares";
	$d = dir($path);	
	$now = time();
	$span = strtotime("-21 days"); // 1 day
	
	$i = 0; 

	while (false !== ($entry = $d->read())) {

		if(is_file($path."/".$entry)){
			
			//if($now - filemtime($path."/".$entry) >= $span){
			$t = filemtime($path."/".$entry);
			if($t < $span) {
				unlink($path."/".$entry);
				//echo $entry."<br>";
				$i++;
			}
				
		}

	}
	
	echo "Total : ".$i;
	
?> 