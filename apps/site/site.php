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
	
	function index(){
		$this->addContent( "welcome to the website." );
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
	
	function logout(){
		return true;
	}
	
	function profile(){
		
		return true;
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
		 CreateRecoveryForm($this);
		return true;
	}
	function recoverypassword_do(){
		 CreateRecoveryForm($this);
		 $isValidEmail = CheckRecoveryForm($this);
		 if($isValidEmail){
			 //good email and user combo
			 $this->addContent( "Please check your email for the recovery link." );
			 return true;
		 }else{
			 //
			  //ShowRecoveryForm($this);
			  $this->app_user_message = "We were unable to find a username and email combination that matched your request.";
			  $this->app_user_message_type = "bad";
			  return true;
		 }
		return true;
	}
	
	function recoverypassword_link(){
		//$this->siteForms = new cForms($this->db,$this->config,$this->user);
		if( $this->web_helper->didPost() ){
				$this->db->sql("select * from users where reset_guid=:reset_guid and username=:username");
				$this->db->addParam(":username",$this->web_helper->getFormValue("username","","UserName"));
				$this->db->addParam(":reset_guid",$this->web_helper->getFormValue("reset_guid","","ActivationGUID"));
				$resultset = $this->db->execute();
				
				if( $resultset[0]->username ==  $this->web_helper->getFormValue("username","","UserName") ){
					$this->app_user_message = "You code was accepted. Please update your password in the below form.";
					$this->app_user_message_type = "good";
					if($this->web_helper->getFormValue("passchange","","Alpha") == "Yes"){
						createResetPassForm($this,$this->web_helper->getFormValue("reset_guid","","ActivationGUID"),$this->web_helper->getFormValue("username","","UserName"));
						$GoodData = $this->siteForms->bindAndFilter();
						
						//if passwords match and is not blank then send to sql with value of password MD5 hashed.
						if( $this->siteForms->boundElements['password']->form_value  == $this->siteForms->boundElements['pass2']->form_value){
							//good pass
						}else{
							$this->siteForms->HTMLErrors[$this->web_helper->current_app_page] .= "Password did not match.";
							$GoodData = false;
						}

						if( $GoodData == true ){
							$this->user->changePasswordViaRecovery($this->siteForms->boundElements['username']->form_value,$this->siteForms->boundElements['reset_guid']->form_value,$this->siteForms->boundElements['password']->form_value);
							$this->app_user_message = "Password Updated";
							$this->app_user_message_type = "good";
							return 'login';
						}else{
							$this->app_user_message = $this->siteForms->HTMLErrors[$this->web_helper->current_app_page];
							$this->app_user_message_type = "warning";
						}
						
						
					}else{
						createResetPassForm($this,$this->web_helper->getFormValue("reset_guid","","ActivationGUID"),$this->web_helper->getFormValue("username","","UserName"));
					}
				}else{
					$this->app_user_message = "The information you entered is not valid or has expired";
					$this->app_user_message_type = "warning";
					createResetCodeForm($this);
				}
		}else{
			createResetCodeForm($this);
		}
		return true;
	}
	
	
	
	function register(){
		
		 RegisterSetupForm($this);
		 RegisterSetupFormDisplay($this);
		return true;
	}
	
	function register_do(){
		
		 RegisterSetupForm($this);
		 $good_regpost = RegisterDoPost($this);
		 if( !$good_regpost){
			  RegisterSetupFormDisplay($this);
		 }else{
			
			 return $this->web_helper->current_app_page;
		 }
		return true;
	}
	
	function register_complete(){
		return true;
	}
	
	function register_code(){
		
		//clear the form data
		$this->siteForms = new cForms($this->db,$this->config,$this->user);
		if( $this->web_helper->didPost() ){
				$this->db->sql("update users set is_activated='1' where activation_guid=:activation_guid and username=:username");
				$this->db->addParam(":username",$this->web_helper->getFormValue("username","","UserName"));
				$this->db->addParam(":activation_guid",$this->web_helper->getFormValue("activation_guid","","ActivationGUID"));
				$this->db->execute();
				if( $this->db->getAffectedRowCount() > 0  ){
					$this->app_user_message = "You account has been activated please login.";
					$this->app_user_message_type = "good";
					return 'login';
				}else{
					$this->app_user_message = "The information you entered is not valid or has expired";
					$this->app_user_message_type = "warning";
					ShowActCodeForm($this);
				}
		}else{
			ShowActCodeForm($this);
		}
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

		//revocer password
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
											"Recover Password", $aStrCSS, $aStrJS,							//meta title, extra CSS, extra JS
											$this->AppName, "recoverypassword_link", 2, array(),				//AppName, AppPage, Is Public, Roles 
											0, 0, "Recover", "", "",											//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											1, "recoverypassword_link", 0,									//Does Post, Post To App Page name, Is Json Call Back
											"", "recoverypassword_link", "recoverypassword_link");			//MVC-Model name, MVC-View Name, MVC-Controller Name
		
		
		
		
		//logout
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
		
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Register - Complete", $aStrCSS, $aStrJS,						//meta title, extra CSS, extra JS
											$this->AppName, "register_complete", 2, array(),				//AppName, AppPage, Is Public, Roles 
											0, 0, "Register", "", "",										//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											0, "register_complete"	,0,										//Does Post, Post To App Page name, Is Json Call Back
											"","register_complete","register_complete");					//MVC-Model name, MVC-View Name, MVC-Controller Name
											
		$this->pages_array[] = new cPage($this->db,$this->config,$this->user,								//Database, Config, user
											"Register - Activation", $aStrCSS, $aStrJS,						//meta title, extra CSS, extra JS
											$this->AppName, "register_code", 2, array(),					//AppName, AppPage, Is Public, Roles 
											0, 0, "Register", "", "",										//Is Menu, Menu Always Show, MenuTitle, MenuItemImage, MenuGroup
											0, "register_code"	,0,											//Does Post, Post To App Page name, Is Json Call Back
											"","register_code","register_code");							//MVC-Model name, MVC-View Name, MVC-Controller Name
											
											
											
		
		
		//Don't delete this line. it sets the active index for the page being rendered.
		$this->getCurrentPageIndex();
	}

}//end site app
?>
