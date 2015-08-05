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
	
	var $is_visible			= true;
	var $is_bound			= true; //binds the element to a column. you would set this to false for a submit button
	var $auto_populate_db	= true; //when a select is called fill in the data
	var $auto_populate_post	= true; //when a post is done fill in the data from $POST
	
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
	
	function notEmpty($arrayParams){
		if( $this->form_value == "" ){
			$this->HTMLErrors[] = array($this->col_name => $this->form_label . " can't be blank");
		}
	}
	
	function isEmail($arrayParams){
		if (!filter_var($this->form_value, FILTER_VALIDATE_EMAIL) === false) {
			//valid email
		} else {
			$this->HTMLErrors[] = array($this->col_name => $this->form_label . " is not a valid email address");
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
		
		
	}
}
?>