<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once('../lib/DataContext.php');
$db = new DataContext();
$activity = new Activity();
$activity->id = $_GET['ecard_linktrack'];
$activity->email_open = '1';
$activity->link_visit_open = '1';
$db->Update($activity);
$db->Submit();
?>
    