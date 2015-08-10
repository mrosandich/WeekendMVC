<?php
/*
	Author	: Mell Rosandich
	Date	: 6/29/2015
	email	: mell@ourace.com
	website : www.ourace.com
	
	Copyright 2015 Mell Rosandich

	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at

		http://www.apache.org/licenses/LICENSE-2.0

	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.

*/

defined('SYSLOADED') OR die('No direct access allowed.');
class cDB {
	
	var $db;
	var $statement 		= "";
	var $results;
	var $resultCount 	= -1;
	var $last_id 		= -1;
	var $affected_rows	= -1;
	var $pre_sql		= "";
	
	var $is_prepared = 0;
	var $error_message = "";
	
	function __construct($CONFIG){
		$this->db = new PDO("mysql:host={$CONFIG['db_host']};dbname={$CONFIG['db_name']};charset=utf8", $CONFIG['db_username'], $CONFIG['db_password']);
	}
	
	function sql($InString){
		$this->is_prepared = 1;
		$this->statement = $this->db->prepare($InString);
		$this->pre_sql	= $InString;
	}
	
	
	function addParam($InName,$InValue){
		if( $this->is_prepared == 0 ){
			$this->error_message = "Tried to addParams to an empty SQL statement";
			if(DEBUG_ECHO == true){
				echo "class_db - SQL: create your statement before you call addParam";
			}
			return;
		}
		
		if( strpos($this->pre_sql,$InName) > 0 ){
			if( gettype($InValue) == "integer" ){
				$this->statement->bindValue($InName, $InValue, PDO::PARAM_INT);
			}elseif(gettype($InValue) == "bool" ){
				$this->statement->bindValue($InName, $InValue, PDO::PARAM_BOOL);
			}elseif(gettype($InValue) == "string" ){
				$this->statement->bindValue($InName, $InValue, PDO::PARAM_STR);
			}else{
				$this->statement->bindValue($InName, $InValue, PDO::PARAM_STR);
			}
		}else{
			if(DEBUG_ECHO == true){
				echo "class_db - SQL param name not found in SQL: $InName" . ", in sql:" . $this->pre_sql . "<br />";
			}
		}
	}
	
	function execute(){
		if( $this->is_prepared == 0 ){
			$this->error_message = "Tried to execute an empty SQL statement";
			return;
		}
		$this->statement->execute();
		$this->results 		= $this->statement->fetchAll(PDO::FETCH_OBJ);
		$this->resultCount 	= $this->statement->rowCount();
		$this->last_id 		= $this->db->lastInsertId();
		if( gettype($this->results) == "integer" ){  
			$this->affected_rows = $this->results;
		}
		
		//clear statement and params
		return $this->results;
	}
	
	function getResultCount(){
		return $this->resultCount;
	}
	
	function getLastInsertId(){
		return $this->last_id;
	}
	
	function getAffectedRowCount(){
		return $this->affected_rows;
	}
	
} //end cDB
$db = new cDB($CONFIG);
?>