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

class site_app extends cAPP{
	
	var $siteForms;
	
	function app_load(){
		$this->siteForms = new cForms($this->db,$this->config,$this->user);
	}
	function loadFormStartBLock(){
		$this->siteForms->HTMLFormStart = $this->pages_array[$this->getCurrentPageIndex()]->getFormActionHTML();
	}
	function renderForm(){
		$this->loadFormStartBLock();
		return $this->siteForms->renderForm();
	}
	
	/* Quick understanding of page flow
		The first thing to get called is the Controller
		Then the function below is called
		If that function below returns true it will load the view
			If that function below returns am app_page it will load that app page controller then call that app page function, the reurn the view.
		
		
	
	*/
	
	function logout(){
		return true;
	}
	
	function profile(){
		
		return true;
	}
	
	function login(){
		
		return true;
	}
	
	function login_do(){
		
		$temp_is_loggenin = $this->user->login( $this->web_helper->getFormValue("username",""),$this->web_helper->getFormValue("password","") );
		if( $temp_is_loggenin == 1 ){
			$this->addContent( $this->user->user_login_message  );
			return true;
		}else{
			$this->app_user_message = $this->user->user_login_message;
			return "login";
		}
	}
	
	function profile_update(){
		
		return true;
	}
	
	
	function index(){
		$this->addContent( "welcome to the website." );
		return true;
	}
	
	function loadAppPages(){
		
		$aStrCSS 	= array();
		$aStrJS		= array();
		
		//public welcome page
		//																	  TITLE										app_page   page_public			
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"Welcome", $aStrCSS, $aStrJS,$this->AppName, "index", 		1, 		array()				, 1, 1,"Welcome", 	"",0,""			,0,"","","");
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"helloworld", $aStrCSS, $aStrJS,"helloworld", "index", 		0, 		array("helloworld")	 , 1, 0,"Hello World Home", "",0,""	,0,"","","");
	
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"Login", 	$aStrCSS, $aStrJS,$this->AppName, "login", 		2, 		array()				, 1, 0,"Login", 	"",1,"login_do"	,0,"","login","login");
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"Login", 	$aStrCSS, $aStrJS,$this->AppName, "login_do", 	2, 		array()				, 0, 0,"Login", 	"",1,""		,0,"","","");
		
		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"Logout", 	$aStrCSS, $aStrJS,$this->AppName, "logout", 	0, 		array()				 , 1, 0,"Logout", 	"",0,""		,0,"","logout","logout");
		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"Profile", $aStrCSS, $aStrJS,$this->AppName, "profile", 	0, 		array("user_profile"), 1, 0,"Profile", 	"",1,"profile_update"		,0,"","profile","profile");
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"Profile", $aStrCSS, $aStrJS,$this->AppName, "profile_update", 0, 	array("user_profile"), 0, 0,"Profile", 	"",0,"profile_update"		,0,"","profile","profile");
	
		
		//Don't delete this line. it sets the active index for the page being rendered.
		$this->getCurrentPageIndex();
	}

}//end site app
?>
