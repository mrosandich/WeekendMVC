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

class siteadmin_app extends cAPP{
	
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
	
	function index(){
		$this->addContent( "welcome to the admin panel." );
		return true;
	}
	
	function users(){
		
		if($this->web_helper->didPost() == true){
			LoadUser($this);
			$GoodData = $this->siteForms->bindAndFilter();
			if( $GoodData == true ){
				SaveUser($this);
				ShowUserList($this);
			}else{
				ShowUserForm($this);
			}
		}else{
			
			if($this->web_helper->getQueryValue("id","") == "" ){
				ShowUserList($this);
			}else{
				LoadUser($this);
				ShowUserForm($this);
			}
		}
		return true;
	}
	
	function enterprises(){
		$this->addContent( "welcome to the admin panel: Enterprises." );
		return true;
	}
	
	
	
	
	function loadAppPages(){
		
		$aStrCSS 	= array();
		$aStrJS		= array();
		
		//public welcome page
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Welcome", $aStrCSS, $aStrJS,									//meta title, extra CSS, extra JS
											"site", "index", 1, array(),									//AppName, AppPage, Is Public, Roles
											1, 1, "Welcome", "", "",										//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											0, "", 0,														//Does Post, Post To App Page name, Is Json Call Back
											"", "", "");													//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		
		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Administration", $aStrCSS, $aStrJS,							//meta title, extra CSS, extra JS									
											$this->AppName, "index", 0, array("site_admin"),				//AppName, AppPage, Is Public, Roles	 
											1, 0, "Administration", "", "",									//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											0, "", 0,														//Does Post, Post To App Page name, Is Json Call Back
											"", "", "");	
		
		
		
		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Manage Users", $aStrCSS, $aStrJS,								//meta title, extra CSS, extra JS
											$this->AppName, "users", 0, array("site_admin"),				//AppName, AppPage, Is Public, Roles 
											1, 0, "Users", "", "index",										//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											1, "users", 0,													//Does Post, Post To App Page name, Is Json Call Back
											"", "users", "users");											//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Manage Enterprises", $aStrCSS, $aStrJS,						//meta title, extra CSS, extra JS
											$this->AppName, "enterprises", 0, array("site_admin"),			//AppName, AppPage, Is Public, Roles 
											1, 0, "Enterprises", "", "index",								//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											1, "enterprises", 0,													//Does Post, Post To App Page name, Is Json Call Back
											"", "enterprises", "enterprises");											//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		
		
		
		//logout
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Logout", 	$aStrCSS, $aStrJS,									//meta title, extra CSS, extra JS
											'site', "logout", 0, array() ,									//AppName, AppPage, Is Public, Roles 
											1, 0, "Logout", "", "",											//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											0, "", 0,														//Does Post, Post To App Page name, Is Json Call Back
											"", "logout", "logout");										//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		//Profile Page
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Profile", $aStrCSS, $aStrJS,									//meta title, extra CSS, extra JS
											'site', "profile", 0, array("user_profile"),					//AppName, AppPage, Is Public, Roles 
											1, 0, "Profile", "", "profile",									//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											1, "profile_update", 0,											//Does Post, Post To App Page name, Is Json Call Back
											"", "profile", "profile");										//MVC-Model name, MVC-View Name, MVC-Controller Name
											
								
		
		
		
											
											
											
		
		
		//Don't delete this line. it sets the active index for the page being rendered.
		$this->getCurrentPageIndex();
	}

}//end site app
?>
