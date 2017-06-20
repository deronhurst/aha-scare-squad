<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once('../lib/DataContext.php');
$db = new DataContext();
$data = $db->query("select Count(*) AS open_emails from activities where email_open = '1'");
$open =  $data[0]['open_emails'];
echo "Number of Open Emails: ". $open;
echo "<BR>";
$ldata = $db->query("select Count(*) AS link_visit_open from activities where link_visit_open = '1'");
$link_visit_open =  $ldata[0]['link_visit_open'];
echo "Number of Visited Emails: ". $link_visit_open;
?>
    