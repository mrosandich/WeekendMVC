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
	function password(){
		return true;
	}
	function password_update(){
		return true;
	}
	
	function security(){
		return true;
	}
	function security_update(){
		return true;
	}
	
	
	function recoverypassword(){
		return true;
	}
	function index(){
		$this->addContent( "welcome to the website." );
		return true;
	}
	
	function register(){
		return true;
	}
	
	function register_do(){
		return true;
	}
	
	
	function loadAppPages(){
		
		$aStrCSS 	= array();
		$aStrJS		= array();
		
		//public welcome page
	
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Welcome", $aStrCSS, $aStrJS,									//meta title, extra CSS, extra JS
											$this->AppName, "index", 1, array(),							//AppName, AppPage, Is Public, Roles
											1, 1, "Welcome", "", "",										//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											0, "", 0,														//Does Post, Post To App Page name, Is Json Call Back
											"", "", "");													//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"helloworld", $aStrCSS, $aStrJS,								//meta title, extra CSS, extra JS									
											"helloworld", "index", 0, array("helloworld"),					//AppName, AppPage, Is Public, Roles	 
											1, 0, "Hello World Home", "", "",								//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											0, "", 0,														//Does Post, Post To App Page name, Is Json Call Back
											"", "", "");													//MVC-Model name, MVC-View Name, MVC-Controller Name
	
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Login", 	$aStrCSS, $aStrJS,									//meta title, extra CSS, extra JS
											$this->AppName, "login",2, 	array(),							//AppName, AppPage, Is Public, Roles 
											1, 0,"Login", "", "",											//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											1, "login_do", 0,												//Does Post, Post To App Page name, Is Json Call Back
											"", "login", "login");											//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Login", 	$aStrCSS, $aStrJS,									//meta title, extra CSS, extra JS
											$this->AppName,"login_do", 2, array(),							//AppName, AppPage, Is Public, Roles 
											0, 0, "Login", "", "",											//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											1, "" ,0,														//Does Post, Post To App Page name, Is Json Call Back
											"", "", "");													//MVC-Model name, MVC-View Name, MVC-Controller Name

		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Recover Password", 	$aStrCSS, 								//meta title, extra CSS, extra JS
											$aStrJS,$this->AppName, "recoverypassword", 2, array(),			//AppName, AppPage, Is Public, Roles 
											0, 0, "Login", "", "",											//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											1, "recoverypassword_do", 0,									//Does Post, Post To App Page name, Is Json Call Back
											"", "recoverypassword", "recoverypassword");					//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Recover Password", $aStrCSS, $aStrJS,							//meta title, extra CSS, extra JS
											$this->AppName, "recoverypassword_do", 2, array(),				//AppName, AppPage, Is Public, Roles 
											0, 0, "Login", "", "",											//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											1, "recoverypassword_do", 0,									//Does Post, Post To App Page name, Is Json Call Back
											"", "recoverypassword", "recoverypassword");					//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Logout", 	$aStrCSS, $aStrJS,									//meta title, extra CSS, extra JS
											$this->AppName, "logout", 0, array() ,							//AppName, AppPage, Is Public, Roles 
											1, 0, "Logout", "", "",											//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											0, "", 0,														//Does Post, Post To App Page name, Is Json Call Back
											"", "logout", "logout");										//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		//Profile Page
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Profile", $aStrCSS, $aStrJS,									//meta title, extra CSS, extra JS
											$this->AppName, "profile", 0, array("user_profile"),			//AppName, AppPage, Is Public, Roles 
											1, 0, "Profile", "", "profile",										//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											1, "profile_update", 0,											//Does Post, Post To App Page name, Is Json Call Back
											"", "profile", "profile");										//MVC-Model name, MVC-View Name, MVC-Controller Name
											
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Profile", $aStrCSS, $aStrJS,									//meta title, extra CSS, extra JS
											$this->AppName, "profile_update", 0, array("user_profile"),		//AppName, AppPage, Is Public, Roles 
											0, 0, "Profile", "", "profile",										//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											0, "profile_update"	,0,											//Does Post, Post To App Page name, Is Json Call Back
											"","profile","profile");										//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		//Profile Password Page	
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Profile - Password", $aStrCSS, $aStrJS,						//meta title, extra CSS, extra JS
											$this->AppName, "password", 0, array("user_profile"),			//AppName, AppPage, Is Public, Roles 
											1, 0, "Password", "", "profile",								//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											1, "password_update"	,0,										//Does Post, Post To App Page name, Is Json Call Back
											"","password","password");										//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Profile - Password", $aStrCSS, $aStrJS,						//meta title, extra CSS, extra JS
											$this->AppName, "password_update", 0, array("user_profile"),	//AppName, AppPage, Is Public, Roles 
											0, 0, "Password", "", "profile",								//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											1, "password_update"	,0,										//Does Post, Post To App Page name, Is Json Call Back
											"","password","password");										//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		//Register User		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Register", $aStrCSS, $aStrJS,									//meta title, extra CSS, extra JS
											$this->AppName, "register", 2, array(),							//AppName, AppPage, Is Public, Roles 
											1, 0, "Register", "", "",										//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											0, "register_do"	,0,											//Does Post, Post To App Page name, Is Json Call Back
											"","register","register");										//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Register", $aStrCSS, $aStrJS,									//meta title, extra CSS, extra JS
											$this->AppName, "register_do", 2, array(),						//AppName, AppPage, Is Public, Roles 
											0, 0, "Register", "", "",										//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											0, "register_do"	,0,											//Does Post, Post To App Page name, Is Json Call Back
											"","register","register");										//MVC-Model name, MVC-View Name, MVC-Controller Name

											
											
											
		
		
		//Don't delete this line. it sets the active index for the page being rendered.
		$this->getCurrentPageIndex();
	}

}//end site app
?>
