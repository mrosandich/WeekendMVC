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

class cFormElement{
	
	var $db			= ""; //optional add on for validation that uses the database
	var $col_name 	= "";
	var $post_name 	= "";
	var $roles		= array();
	var $hooks		= array(); //this is all the validtion calls
	var $HTMLErrors	= array();
	
	var $form_value 		= ""; //the value of the form value="here"
	var $form_web_type		= ""; //text, hidden,select,etc..
	var $form_value_default = ""; //if no value set then load it with this after post
	var $form_label			= ""; //the title part of the html 
	var $form_caption		= "";
	var $form_cntrl_values	= array(); //hold thing like the option values in a select control
	
	
	var $is_visible			= true;
	var $is_bound			= true; //binds the element to a column. you would set this to false for a submit button
	var $auto_populate_db	= true; //when a select is called fill in the data
	var $auto_populate_post	= true; //when a post is done fill in the data from $POST
	var $bind_to_post		= true;
	
	var $post_filter_type = "FILTER_SANITIZE_STRING";
	
	function __construct($ColName,$IsBound=true, $PostName="", $Roles="", $PostFilter=""){
		$this->col_name 	= $ColName;
		$this->post_name 	= $PostName;
		$this->is_bound		= $IsBound;
		
		if($Roles !=""){
			$this->roles = $Roles;
		}else{
			$this->roles = array();
		}
		
		if($PostFilter !=""){
			$this->post_filter_type = $PostFilter;
		}else{
			$this->post_filter_type = "FILTER_SANITIZE_STRING";//default in case people are lazy. pass raw to turn off filtering
		}
		
		if( $PostName == "" ){
			$this->post_name = $ColName;
		}

	}
	
	function getErrorText(){
		$RetText = "";
		for($x=0;$x<count($this->HTMLErrors);$x++){
			$RetText .= $this->HTMLErrors[$x][$this->col_name];
		}		
		return $RetText;
	}
	function addValidation($funCall,$paramsArray){
		$this->hooks[$funCall] = $paramsArray;
	}
	
	function selfValidate(){
		foreach($this->hooks as $key => $val){
			$functionToCall = $key;
			$this->$functionToCall($val);
		}
	}
	
	
	
	
	
	function renderElement(){
		$temp_value = $this->form_value;
		if( $this->auto_populate_post == false ){
			$temp_value = "";
		}
		
		if( $this->form_web_type == "text" ){
			$this->is_visible = true;
			return "<input type=\"text\" value=\"$temp_value\" name=\"{$this->post_name}\" id=\"{$this->post_name}\" />";
		}
		if( $this->form_web_type == "hidden" ){
			$this->is_visible = false;
			return "<input type=\"hidden\" value=\"$temp_value\" name=\"{$this->post_name}\" id=\"{$this->post_name}\" />";
		}
		if( $this->form_web_type == "password" ){
			$this->is_visible = true;
			return "<input type=\"password\" value=\"$temp_value\" name=\"{$this->post_name}\" id=\"{$this->post_name}\" />";
		}
		if( $this->form_web_type == "submit" ){
			$this->is_visible = true;
			return "<input type=\"submit\" value=\"$temp_value\" name=\"{$this->post_name}\" id=\"{$this->post_name}\" />";
		}
		if( $this->form_web_type == "static" ){
			$this->is_visible = true;
			return "$temp_value";
		}
		
		if( $this->form_web_type == "select" ){
			$this->is_visible = true;
			$RetHTML = "<select name=\"{$this->post_name}\" id=\"{$this->post_name}\">\n";
			$HitAnItem = 0;
			foreach( $this->form_cntrl_values as $optval => $opttext ){
				if($optval == $temp_value ){
					$RetHTML .= "\t<option value=\"$optval\" selected>$opttext</option>\n";
					$HitAnItem = 1;
				}else{
					$RetHTML .= "\t<option value=\"$optval\">$opttext</option>\n";
				}
			}
			//if they have the value of an item but it is not in the list lets make that an option.
			if( $HitAnItem == 0 && $temp_value != "" ){
				$RetHTML .= "\t<option value=\"$temp_value\">$temp_value</option>\n";
			}
			
			$RetHTML .= "</select>\n";
			
			return $RetHTML;
		}
		
		
	}
	
	//-----------------------------------------------------------------------------------------------
	//											Validation.		
	//-----------------------------------------------------------------------------------------------
	
	//check that thee is a value
	function notEmpty($arrayParams){
		if( $this->form_value == "" ){
			$this->HTMLErrors[] = array($this->col_name =>  " can't be blank. ");
		}
	}
	
	//check to see if it is a valid email address
	function isEmail($arrayParams){
		if (!filter_var($this->form_value, FILTER_VALIDATE_EMAIL) === false) {
			//valid email
		} else {
			$this->HTMLErrors[] = array($this->col_name => " is not a valid email address. ");
		}
	}
	
	
	function isValidPassword($arrayParams){
		if( !array_key_exists('len',$arrayParams) || !array_key_exists('cntup',$arrayParams) || !array_key_exists('cntlw',$arrayParams) || !array_key_exists('cntspc',$arrayParams) || !array_key_exists('spchar',$arrayParams)  || !array_key_exists('cntnum',$arrayParams)  ){
			if(DEBUG_ECHO == true){
				echo "called class-form_element isValidPassword: missing one or more: len, cntup, cntlw, cntspc, cntnum, spchar ";
			}
			return false;
		}
		
		if( strlen($this->form_value) < $arrayParams['len'] ){
			$this->HTMLErrors[] = array($this->col_name =>  " is too short. Min length required is " . $arrayParams['len'] );
		}
		
		$match = "";
		preg_match_all('/[A-Z]/', $this->form_value, $match);
		$total_ucase = count($match[0]);
		
		$match = "";
		preg_match_all('/[a-z]/', $this->form_value, $match);
		$total_lcase = count($match[0]);
		
		$match = "";
		preg_match_all('/[0-9]/', $this->form_value, $match);
		$total_numbers = count($match[0]);
		
		if( $total_ucase < $arrayParams['cntup'] ){
			$this->HTMLErrors[] = array($this->col_name =>  " needs at least " . $arrayParams['cntup'] . " upper case letters. " );
		}
		
		if( $total_lcase < $arrayParams['cntlw'] ){
			$this->HTMLErrors[] = array($this->col_name =>  " needs at least " . $arrayParams['cntlw'] . " lower case letters. " );
		}
		
		if( $total_numbers < $arrayParams['cntnum'] ){
			$this->HTMLErrors[] = array($this->col_name =>  " needs at least " . $arrayParams['cntnum'] . " numbers. " );
		}
		
		$total_special=0;
		for($x=0;$x <  strlen($this->form_value);$x++){
			$currentchar = substr($this->form_value,$x,1);
			$total_special += substr_count( $arrayParams['spchar'],$currentchar);
		}
		
		if( $total_special < $arrayParams['cntspc'] ){
			$this->HTMLErrors[] = array($this->col_name =>  " needs at least " . $arrayParams['cntspc'] . " special characters:" . $arrayParams['spchar'] );
		}
		
	}
	
	
	
	function isValidUserName($arrayParams){
		if( !array_key_exists('userchars',$arrayParams) || !array_key_exists('userlen',$arrayParams)  ){
			if(DEBUG_ECHO == true){
				echo "called class-form_element isValidUserName: missing one or more: userchars, userlen";
			}
			return false;
		}
		
		if( strlen($this->form_value) < $arrayParams['userlen'] ){
			$this->HTMLErrors[] = array($this->col_name =>  " is too short. Min length required is " . $arrayParams['userlen'] );
		}
	
		$char_not_allowed_found="";
		for($x=0;$x <  strlen($this->form_value);$x++){
			$currentchar = substr($this->form_value,$x,1);
			if( substr_count( $arrayParams['userchars'],$currentchar) == 0 ){
				$char_not_allowed_found .= $currentchar . ", ";
			}
		}
		$char_not_allowed_found = rtrim($char_not_allowed_found,", ");
		if( $char_not_allowed_found != ""){
			$this->HTMLErrors[] = array($this->col_name =>  " you can't use " .$char_not_allowed_found . " in your username."  );
		}
		
	}
	
	
	
	//this function check to see if another form elemnts has the same value. usecase: confirm password
	//this form assumes the same type of filtering and validation.
	function otherFormElementSameValue($arrayParams){
		if( !array_key_exists('formname',$arrayParams) || !array_key_exists('formlabel',$arrayParams)     ){
			if(DEBUG_ECHO == true){
				echo "called class-form_element otherFormElementSameValue: missing formname or formlabel ";
			}
			return false;
		}
		$temp_cweb 		= new cWeb();
		$temp_value 	= $temp_cweb->getFormValue($arrayParams['formname'],"", $post_filter_type);
		if( $this->form_value != $temp_value ){
			$temp_verb = "";
			if( $this->form_label == $arrayParams['formlabel'] ){
				$temp_verb = "other ";
			}
			$this->HTMLErrors[] = array($this->col_name =>  " needs to match " . $temp_verb . $arrayParams['formlabel'] . ". ");
		}
	}
	
	
	
	//check to see if a value is in a certain table.col
	function valueNotInDBTable($arrayParams){
		if( !array_key_exists('table',$arrayParams) || !array_key_exists('col',$arrayParams) ){
			if(DEBUG_ECHO == true){
				echo "called class-form_element valueNotInDBTable: missing arrayparams table or col";
			}
			return false;
		}
		if( $this->db == "" ){
			if(DEBUG_ECHO == true){
				echo "called class-form_element valueNotInDBTable: you need to set the ->db from the parent to use this validation call";
			}
			return false;
		}
		
		$Statement = "select * from {$arrayParams['table']} where {$arrayParams['col']}=:formvalue";
		$this->db->sql($Statement);
		$this->db->addParam(":formvalue" ,$this->form_value);
		$result = $this->db->execute();
		$RecordsReturned = $this->db->getResultCount();
		if( $RecordsReturned > 0 ){
			$this->HTMLErrors[] = array($this->col_name => " is already taken. Please choose another. ");
		}
	}
	
	function valueIsInDBTable($arrayParams){
		if( !array_key_exists('table',$arrayParams) || !array_key_exists('col',$arrayParams) ){
			if(DEBUG_ECHO == true){
				echo "called class-form_element valueNotInDBTable: missing arrayparams table or col";
			}
			return false;
		}
		if( $this->db == "" ){
			if(DEBUG_ECHO == true){
				echo "called class-form_element valueNotInDBTable: you need to set the ->db from the parent to use this validation call";
			}
			return false;
		}
		
		$Statement = "select * from {$arrayParams['table']} where {$arrayParams['col']}=:formvalue";
		$this->db->sql($Statement);
		$this->db->addParam(":formvalue" ,$this->form_value);
		$result = $this->db->execute();
		$RecordsReturned = $this->db->getResultCount();
		if( $RecordsReturned < 1 ){
			$this->HTMLErrors[] = array($this->col_name => " not valid. ");
		}
	}
}
?>