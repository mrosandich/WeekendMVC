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
class cPageFlow {

	var $db;
	var $config;
	var $user;
	var $app_default_guest 		= "site";
	var $app_default_loggedin 	= "site";
	var $app_default_page 		= "index";
	var $apps_all				= array();
	
	var $current_app = "site";
	var $current_app_page = "index";
	var $current_app_action = "";
	//app_action defined states: view,edit,update,insert,delete
	//view - shows the page in html format with no form elements. DB select -> render html 
	//edit - show form elements. Db select -> validation -> [update,delete,copy] - > [view, other page]
	//update - called after [edit|new]
	//delete - called after view or edit
	//blankform - shows and empty form
	
	var $app_page_last = "";
	var $app_last = "";
	
	var $app_user_message = "";

	function __construct($cDBObject,$SiteConfig,$cUser){
		$this->db 					= $cDBObject;
		$this->config 				= $SiteConfig;
		$this->user 				= $cUser;
		
		$this->app_default_guest 	= $this->config['app_default_guest'];
		$this->app_default_loggedin = $this->config['app_default_loggedin'];
		$this->app_default_page 	= $this->config['app_default_page'];
		
		
		
		//find the session app
		if( in_array('pageflow_app_current',$_SESSION) ){
			$this->current_app = $_SESSION['pageflow_app_current'];
		}else{
			$this->current_app = $this->app_default_guest;
		}
		
		//find the session page
		if( in_array('pageflow_app_page',$_SESSION) ){
			$this->current_app_page = $_SESSION['pageflow_app_page'];
		}else{
			$this->current_app_page = $this->app_default_page;
		}
		
		$web_helper = new cWeb();
		
		//Check if we are moving to a new app or page.
		//We store the last app and page in case 
		$AppRequest = $web_helper->getRequestValue("app_name","");
		if( $AppRequest != "" ){
			$_SESSION['app_last'] = $this->current_app;
			$this->current_app = $AppRequest;
		}
		
		$AppPageRequest = $web_helper->getRequestValue("app_page","");
		if( $AppPageRequest != "" ){
			$_SESSION['app_page_last'] = $this->current_app_page;
			$this->current_app_page = $AppPageRequest;
		}
		
		//make sure a page state is not blank
		if($this->current_app_page == "" ){
			$this->current_app_page = $this->app_default_page; 
		}
		
		//make sure the app is not blank
		if($this->current_app == "" ){
			if( $this->user->isLoggedIn()==0 ){
				$this->current_app = $this->app_default_guest; 
			}else{
				$this->current_app = $this->app_default_loggedin; 
			}
		}
		
		
		//include the app main file /app/appname/appname.php
		if( file_exists("apps/" . $this->current_app . "/" . $this->current_app . ".php") ){
			include("apps/" . $this->current_app . "/" . $this->current_app . ".php");
			$LoadClass = $this->current_app . "_app";
			if( class_exists($LoadClass) ){
				$CurrentAppClass = new $LoadClass($this->db,$this->config,$this->user,$this->current_app,$this->current_app_page);
				
				//from the object call the function in this case it is the page.
				$temp_function = $this->current_app_page;
				
				//get the current page information we are trying to load.
				//validate roles and user stats before we call anything
				$temp_page_index = $CurrentAppClass->getCurrentPageIndex();
				$temp_has_access = $CurrentAppClass->hasAccessToPage($temp_page_index);
				
				if( $temp_has_access == 1 ){
					if( method_exists($CurrentAppClass,$temp_function) ){
						$CurrentAppClass->loadMVCController();
						$temp_continue_rendering = $CurrentAppClass->$temp_function(); 
						$this->app_user_message = $CurrentAppClass->app_user_message;
						if($temp_continue_rendering===true){
							$CurrentAppClass->loadMVCView();
							$CurrentAppClass->loadMenuHTML();
							$CurrentAppClass->loadRenderTemplate();
						}elseif( $temp_continue_rendering !== false ){
							$this->callMVC($temp_continue_rendering,$CurrentAppClass);
						}else{
							if(DEBUG_ECHO == true){
								echo "Your app function needs to return true|false|or function name to call";
							}
						}
							
					}else{
						if(DEBUG_ECHO == true){
							echo "$temp_function() doesn't exsist in apps/" . $this->current_app . "/" . $this->current_app . ".php";
						}
					}
				}else{
					$CurrentAppClass->page_content = "There is no access to that at this time.";
					$CurrentAppClass->loadMenuHTML();
					$CurrentAppClass->loadRenderTemplate();
				}
					
			}else{if(DEBUG_ECHO == true){echo "class was not able to load";}}
		
		}else{if(DEBUG_ECHO == true){echo "class app file doesn't exist";}}
	}//end construct
	
	function callMVC($strPageName,$CurrentAppClass){
		$CurrentAppClass->loadMVCController();
		$CurrentAppClass->current_app_page = $strPageName;
		$temp_page_index = $CurrentAppClass->getCurrentPageIndex();
		$temp_has_access = $CurrentAppClass->hasAccessToPage($temp_page_index);
		if( $temp_has_access == 1 ){
			if( method_exists($CurrentAppClass,$strPageName) ){
				$CurrentAppClass->loadMVCView();
				$CurrentAppClass->loadMenuHTML();
				$CurrentAppClass->loadRenderTemplate();
			}else{
				if(DEBUG_ECHO == true){
					echo "$temp_function() doesn't exsist in apps/" . $this->current_app . "/" . $this->current_app . ".php";
				}
			}
		}else{
			$CurrentAppClass->page_content = "There is no access to that at this time.";
			$CurrentAppClass->loadMenuHTML();
			$CurrentAppClass->loadRenderTemplate();
		}
		
	}//end callMVC
	
	
} //end cPageFlow
$pageflow = new cPageFlow($db,$CONFIG,$user);
?>