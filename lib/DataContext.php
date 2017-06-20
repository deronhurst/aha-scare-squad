<?php
/**************************************************************************************************
	Database context class
	Handles all tabase related transaction 
	
****************************************************************************************************/
include_once "Database.php";

class DataContext {
	
	var $connection;
	var $_Objects;		// object array to be updated or inserted
	var $BadObjects;
	
	static $INSERT = "insert";
	static $UPDATE = "update";
	static $DELETE = "delete";
	
	// context objects
	var $Activities;
	var $Fbshares;


	function DataContext (){
		// initialize and open database connection
		$this->connection = new Database();
		$this->connection->connect();
		
		// initialize
		$this->_Objects = array();
		$this->BadObjects = array();
		
		$this->Activities = new ContextModel($this->connection, 'activities','Activity');
		$this->Fbshares = new ContextModel($this->connection, 'fbshares','Fbshare');

	}
	
	function Save($obj){
		
		if ($this->validate($obj)){
			$this->_Objects[] = $obj;
			return true;
		}
		else {
			$this->BadObjects[] = $obj;
			return false;
		}		
	}
	
	function Update($obj){
		
		if ($this->validate($obj)){
			$obj->Action = DataContext::$UPDATE;
			$this->_Objects[] = $obj;
			return true;
		}
		else {
			$this->BadObjects[] = $obj;
			return false;
		}		
	}
	
	function Del($obj){
		$this->Delete($obj);
	}
	
	function Delete ($obj){
		
		if (!empty($obj)){
			$obj->Action = DataContext::$DELETE;
			$this->_Objects[] = $obj;
		}
	}
	
	function validate ($obj){
		
		// validate  data fields based on data types
		foreach ($obj->Fields as $field => $rules ){
			
			if (!$rules["Nullable"]){
				// Handle exceptional conditions
				if ($obj->Action == DataContext::$INSERT && $rules["Auto"]){
					// new record, auto incriment field can be null
					continue;
				}
				
				//check for null value	
				if (empty($obj->{$field})){
					$obj->addError($field, "Cannot have null value");
					continue;
				}
			}
			
			// now, ignore nul fields
			if (empty($obj->{$field})){
				continue;
			}
			
			// data type validation
			if (($rules["Type"] == 'int' ||  $rules["Type"] == 'bigint' || $rules["Type"] == 'float' || $rules["Type"] == 'double') && !is_numeric($obj->{$field})){
				
				$obj->addError($field, "Numeric field contains invalid value : " .$obj->{$field});
				continue;
			}
			
			// check char length
			if ($rules["Type"] == 'char' || $rules["Type"] == 'varchar'){
				
				if (strlen($obj->{$field}) > $rules["Length"]){
					//$obj->addError($field, "Data too long to field. Expected : ".$rules["Length"] . ", Contains : ". strlen($obj->{$field})  );
					//continue;
					// trim to fit
					$obj->{$field} = substr($obj->{$field},0,($rules["Length"]-1));
				}
			}
			
			// fix data/time value issues
			if ($rules["Type"] == 'datetime' || $rules["Type"] == 'date'){
				$dt = strtotime($obj->{$field});
				if (!empty($dt)){
					$obj->{$field} = date("Y-m-d H:i:s",strtotime($obj->{$field}));				 
				}
				else {
					$obj->{$field} = date("Y-m-d H:i:s");
				}
			}
			
			if ($rules["Type"] == 'bool' ){
				if ($obj->{$field}){
					$obj->{$field} = 1;
				}
				else {
					$obj->{$field} = 0;
				}
			}		
			
		}
		
		// auto fill created and modified values
		if($field == 'created' && empty($obj->{$field})){
			$obj->{$field} = time();
		}
		
		if(($field == 'modified' || $field == 'updated') && empty($obj->{$field})){
			$obj->{$field} = time();
		}
		
		
		if (empty($obj->Errors)){
			
			return $obj->validate();	
		}
		else {
			
			return false;	
		}
		
	}
	
	function Query ($q){
		
		// run a query on database (select)
		return $this->connection->fetch_all_array($q);	
		
	}
	
	function NonQuery ($q){
		// Run a non-query on database (insert, update, delete)
		$qid = $this->connection->query($q);
		if($qid != -1){
			return $this->connecttion->affected_rows;
		}
		else {
			return false;	
		}
	}
	
	
	function Submit ($CloseConnection = false) {
		// submit all data to database
				
		// execute statesmnts
		foreach ($this->_Objects as $index =>  $obj){
				
			if (empty($obj)){
				continue;
			}
			
			// prepare data array
			$data = array();
			foreach ($obj->Fields as $field => $val){
								
				if ($obj->{$field} !== $obj->defaultValues[$field] ){
					$data[$field] = $obj->{$field};
					//echo $field." = ".$obj->{$field}."<br>";
				}					
				elseif ( $obj->{$field} === 0 ){
					$data[$field] = '0';
					//echo $field." = 0<br>";
				}
				
				// for boolean values
				if($obj->Fields[$field]['Type'] == 'bool' && empty($obj->{$field})) {
					$data[$field] = '0';
				}
			}
						
			if ($obj->Action == DataContext::$INSERT){                                 
				/*
				echo "<h2>Inserting</h2>";
				echo '<pre>';
				var_dump($data);
				echo '</pre>';
				*/
				$res = $this->connection->query_insert($obj->Table,$data );
                                
				if ($res !== false){
					$obj->id = $res;return $obj->id;
					//$obj->Action = DataContext::$UPDATE;
				}
			}
			
			if ($obj->Action == DataContext::$UPDATE){
				//echo "<h2>Updating</h2>";
				unset($data[$obj->PrimaryKey]);
				
				if(!empty($data)) {
					$this->connection->query_update($obj->Table,$data ," {$obj->PrimaryKey} = {$obj->{$obj->PrimaryKey}} LIMIT 1");
				}
			}
			
			if ($obj->Action == DataContext::$DELETE){
				//echo "<h2>Updating</h2>";
				// make delete query
				$q = "DELETE FROM {$obj->Table} WHERE {$obj->PrimaryKey} = {$obj->{$obj->PrimaryKey}} LIMIT 1";
				$this->connection->query($q);
			}
			
			$this->_Objects[$index] = NULL;			
		}
		
		
		// close database connection
		if($CloseConnection){
			$this->connection->close();
		}
		
	}
	
	// make sql query safe to run in the server
	function escape ($string){
		
		if(get_magic_quotes_runtime()) 
			$string = stripslashes($string);
			
		return @mysql_real_escape_string($string,$this->connection->link_id);
		
	}

}


class ContextModel {
	
	var $Table;
	var $Model;
	var $connection;
	
	function ContextModel (&$con, $type, $model){
		$this->Table = $type;	
		$this->Model = $model;
		$this->connection = $con;
	}
	
	function Find ($conditions = "", $orderBy = "", $limit = "", $page = ""){

		$query = $this->BuildQuery($conditions, $orderBy , 1);		
		//echo $query;

		$data = $this->connection->query_first($query);
		/*
		echo '<h2>$data</h2>';
		echo '<pre>';
		var_dump($data);
		echo '</pre>';
		*/
		
		if (!empty($data)){				
			
			return $this->Parse($data);
		}
		else {
			// data not found
			return false;
		}
	}
	
	function FindAll ($conditions = "", $orderBy = "", $limit = "", $page = ""){
		
		$query = $this->BuildQuery($conditions, $orderBy , $limit,$page );
		$rows = $this->connection->fetch_all_array($query);
		
		$output = array();
		
		if (!empty($rows)){			
			
			foreach ($rows as $data){
				if (!empty($rows)){
					$output[] =  $this->Parse($data);
				}
			}
		}
		
		return $output;
	}
	
	
	
	function Parse ($data){
		
		if (!empty($data)){
			$obj = new $this->Model();
			
			foreach ($data as $field => $val){
				
				if ($obj->Fields[$field]['Type'] == 'bool'){
					if($val == 1)
						$obj->{$field} = true;
					else
						$obj->{$field} = false;
				}
				else {
					$obj->{$field} = $val;
				}
				
			}
			
			$obj->defaultValues = $data;
			
			$obj->Action = DataContext::$UPDATE;
			
			return $obj;
		}
		else {
			// data not found
			return NULL;
		}
	}
	
	
	function BuildQuery ($conditions = "", $orderBy = "", $limit = "", $page = ""){
		
		$query = "SELECT * FROM {$this->Table}";
		
		if (!empty($conditions)){
			 $query .= " WHERE ".$conditions;
		}
		
		if (!empty($orderBy)){
			$query .= " ORDER BY ".$orderBy;	   
		}
		
		if (!empty($limit) && !empty($page)){
			$lower = $limit * ($page-1);
			$query .= " LIMIT ".$lower.",".$limit;	   
		}
		elseif (!empty($limit)){
			$query .= " LIMIT ".$limit;	   
		}
		
		return $query;
	}
	

}

/*********************** Data context model classes ********************/


class Activity {
	
	/* Parameters required to validate data */
	var $Table = "activities";
	var $Action = "insert"; 	// insert | update | delete
	var $PrimaryKey = "id";
	var $Errors;
	// validation rules
	var $Fields = array (
		"id" => array ("Type" => 'int', "Nullable" => false, "Auto" => true, "Length" => 20 ),
		"supporter_id" => array ("Type" => 'int', "Nullable" => true, "Auto" => false, "Length" => 20),
		"event_id" => array ("Type" => 'int', "Nullable" => true, "Auto" => false, "Length" => 11),
		"team_id" => array ("Type" => 'int', "Nullable" => true, "Auto" => false, "Length" => 20),
		"character" => array ("Type" => 'varchar', "Nullable" => true, "Auto" => false, "Length" => 255),
		"choice" => array ("Type" => 'varchar', "Nullable" => true, "Auto" => false, "Length" => 255),
		"type" => array ("Type" => 'int', "Nullable" => true, "Auto" => false, "Length" => 6),
		"created" => array ("Type" => 'datetime', "Nullable" => true, "Auto" => false, "Length" => 0),
                "email_open" => array ("Type" => 'tinyint', "Nullable" => true, "Auto" => false, "Length" => 1),
                "link_visit_open" => array ("Type" => 'tinyint', "Nullable" => true, "Auto" => false, "Length" => 1)
	);

	// default values array
	var $defaultValues = array("id"=>NULL,"supporter_id"=>NULL,"event_id"=>NULL,"team_id"=>NULL,"character"=>NULL,"choice"=>NULL,"type"=>NULL,"created"=>NULL,"email_open"=>0,"link_visit_open"=>0);
	
			
	// Table property list 
	var $id;
	var $supporter_id;
	var $event_id;
	var $team_id;
	var $character;
	var $choice;
	var $type;
	var $created;
        var $email_open;
        var $link_visit_open;
	
	
	function Activity () {
		// init object
		
		// clear previous validation errors
		$this->Errors = array();
	}
	
	function validate (){
		// custom validation rue goes here	
		return true;
	}
	
	function addError($field, $message){
		// add validation error
		$this->Errors[] = array("Field"=> $field, "Message" => $message);
	}
	
}




class Fbshare {
	
	/* Parameters required to validate data */
	var $Table = "fbshares";
	var $Action = "insert"; 	// insert | update | delete
	var $PrimaryKey = "id";
	var $Errors;
	// validation rules
	var $Fields = array (
		"id" => array ("Type" => 'int', "Nullable" => false, "Auto" => true, "Length" => 20 ),
		"title" => array ("Type" => 'varchar', "Nullable" => true, "Auto" => false, "Length" => 255),
		"message" => array ("Type" => 'text', "Nullable" => false, "Auto" => false, "Length" => 0),
		"supporter_id" => array ("Type" => 'int', "Nullable" => true, "Auto" => false, "Length" => 20),
		"event_id" => array ("Type" => 'int', "Nullable" => true, "Auto" => false, "Length" => 20),
		"choice" => array ("Type" => 'int', "Nullable" => true, "Auto" => false, "Length" => 6),
		"created" => array ("Type" => 'datetime', "Nullable" => true, "Auto" => false, "Length" => 0),
		"character" => array ("Type" => 'varchar', "Nullable" => true, "Auto" => false, "Length" => 255)
	);

	// default values array
	var $defaultValues = array("id"=>NULL,"title"=>NULL,"message"=>NULL,"supporter_id"=>NULL,"event_id"=>NULL,"choice"=>NULL,"created"=>NULL,"character"=>NULL);
	
			
	// Table property list 
	var $id;
	var $title;
	var $message;
	var $supporter_id;
	var $event_id;
	var $choice;
	var $created;
	var $character;
	
	
	function Fbshare () {
		// init object
		
		// clear previous validation errors
		$this->Errors = array();
	}
	
	function validate (){
		// custom validation rue goes here	
		return true;
	}
	
	function addError($field, $message){
		// add validation error
		$this->Errors[] = array("Field"=> $field, "Message" => $message);
	}
	
}





/*********************** data Factory (manupulate assocative data ******/



?>