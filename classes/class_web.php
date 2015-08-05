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
class cWeb {
	
	var $current_app_name 	= "";
	var $current_app_page 	= "";
	var $current_app_action = "";
	
	
	
	function __construct(){
		$this->loadPageState();
	}
	
	function loadPageState(){
		$this->current_app_name 	= $this->getRequestValue("app_name"		,"","SystemPageStates");
		$this->current_app_page 	= $this->getRequestValue("app_page"		,"","SystemPageStates");
		$this->current_app_action 	= $this->getRequestValue("app_action"	,"","SystemPageStates");
	}
	
	function getFormValue($Key,$DefaultValue,$RegCleanType=""){
		$ReturnValue = $DefaultValue;

		if(array_key_exists($Key,$_POST)){
			$ReturnValue = $_POST[$Key];
		}
		
		$ReturnValue = $this->cleanInput($ReturnValue,$RegCleanType);
		return $ReturnValue;
	}

	function getQueryValue($Key,$DefaultValue,$RegCleanType=""){
		$ReturnValue = $DefaultValue;

		if(array_key_exists($Key,$_GET)){
			$ReturnValue = $_GET[$Key];
		}
		
		$ReturnValue = $this->cleanInput($ReturnValue,$RegCleanType);
		return $ReturnValue;
	}
	
	function getRequestValue($Key,$DefaultValue,$RegCleanType=""){
		$ReturnValue = $DefaultValue;

		if(array_key_exists($Key,$_REQUEST)){
			$ReturnValue = $_REQUEST[$Key];
		}
		
		$ReturnValue = $this->cleanInput($ReturnValue,$RegCleanType);
		return $ReturnValue;
	}
	

	function cleanInput($InValue,$RegCleanType){
		
		if( $RegCleanType == "AlphaNumeric" ){
			$InValue = preg_replace("/[^a-zA-Z0-9\s\.\-\_]/", "", $InValue);
		}
		
		if( $RegCleanType == "SystemPageStates" ){
			$InValue = preg_replace("/[^a-zA-Z0-9\_]/", "", $InValue);
		}
		
		if( $RegCleanType == "UserName" ){
			$InValue = preg_replace("/[^a-zA-Z0-9]/", "", $InValue);
		}
		
		if( $RegCleanType == "Email" ){
			$InValue = preg_replace("/[^a-zA-Z0-9\s\.\_\@]/", "", $InValue);
		}
		
		if( $RegCleanType == "Password" ){
			
		}
		
		if( $RegCleanType == "Numeric" ){
			$InValue = preg_replace("/[^0-9\s\.\-]/", "", $InValue);
		}
		
		if( $RegCleanType == "Alpha" ){
			$InValue = preg_replace("/[^a-zA-Z\s]/", "", $InValue);
		}
		
		if( $RegCleanType == "AlphaNumericPunctuation" ){
			$InValue = preg_replace("/[^a-zA-Z0-9\s\'\.\"\?\!\@\*]/", "", $InValue);
		}
		
		if( $RegCleanType == "NumericDate" ){
			$InValue = preg_replace("/[^0-9\/]/", "", $InValue);
		}
		
		if( $RegCleanType == "Time" ){
			$InValue = preg_replace("/[^0-9a-z\:]/", "", $InValue);
		}
		
		if( $RegCleanType == "url" ){
			$InValue = filter_var($InValue, FILTER_SANITIZE_URL);// see http://php.net/manual/en/filter.filters.sanitize.php
		}
		if( $RegCleanType == "FILTER_SANITIZE_STRING" ){
			$InValue = filter_var($InValue, FILTER_SANITIZE_STRING);
		}
		
		
		return $InValue;
	}
} //end cWeb
?>