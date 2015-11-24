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

class helloworld_app extends cAPP{
	
	function index(){
		$this->addContent( "welcome to the helloworld test drop in app." );
		return true;
	}
	
	function start(){
		$this->addContent( "start a new thing" );
		return true;
	}
	
	function start_do(){
		$this->addContent( "this would perform the search or something" );
		return true;
	}
	
	function loadAppPages(){
		
		$aStrCSS 	= array();
		$aStrJS		= array();
		
		//public welcome page	
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"Welcome", $aStrCSS, $aStrJS,"site", "index", 		1, 		array(), 1, 1,"Welcome", 	"",0,""			,0,"","","");
		
		
		//this apps gaes
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"helloworld", $aStrCSS, $aStrJS,"helloworld", "index", 		0, 		array("helloworld")	 , 1, 0,"Hello World Home", "",0,""	,0,"","","");
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"Start Interview", $aStrCSS, $aStrJS,$this->AppName, "start", 		0, 		array("helloworld"), 1, 0,"Start", 		"",1,"start_do"	,0,"","","");
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"Start Interview", $aStrCSS, $aStrJS,$this->AppName, "start_do", 	0, 		array("helloworld"), 0, 0,"Login", 		"",1,""	,0,"","","");
		
		
		//user rpofile pages so they appear on the site all the time
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"Profile", $aStrCSS, $aStrJS,"site", "profile", 	0, 		array("user_profile"), 1, 0,"Profile", 	"",0,""			,0,"","profile","profile");
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,"Logout", 	$aStrCSS, $aStrJS,"site", "logout", 	0, 		array(), 1, 0,"Logout", 	"",0,""			,0,"","logout","logout");
		
	
	}

}//end site app
?>