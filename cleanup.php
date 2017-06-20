<?php

	$path = dirname(__FILE__)."/prints";
	$d = dir($path);	
	$now = time();
	$span = 86400; // 1 day

	while (false !== ($entry = $d->read())) {

		if(is_file($path."/".$entry)){
			
			if($now - filemtime($path."/".$entry) >= $span){
				unlink($path."/".$entry);
			}
				
		}

	}
	
?> 