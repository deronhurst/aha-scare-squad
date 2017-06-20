<?php



/* 

 * To change this license header, choose License Headers in Project Properties.

 * To change this template file, choose Tools | Templates

 * and open the template in the editor.

 */

include_once('lib/DataContext.php');

$id = $_GET['ecard_linktrack'];

if(!empty($id) && is_numeric($id)){

	$db = new DataContext();
	
	$activity = $db->Activities->Find("id = ".$id);
	
	if(!empty($activity)) {
		
	
		$activity->email_open = '1';
		
		$activity->link_visit_open = '1';
		
		$db->Update($activity);
		
		$db->Submit();

		//ping recorder
		$activity = $db->Activities->Find("id = ".intval($_GET['ecard_linktrack']));
		file_get_contents("http://hearttools.heart.org/aha_ym18/api/activity/".$activity->event_id."/".$activity->supporter_id."/ecard_read?key=6Mwqh5dFV39HLDq7");
	
	}
	else{
		echo "Invalid tracking id";	
	}

}
else{
	echo "Invalid tracking id";	
}

?>

    