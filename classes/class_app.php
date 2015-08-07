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

class cAPP{
	
	var $db;
	var $config;
	var $user;
	var $AppName 					= "";
	var $AppTemplate 				= "";
	var $page_content				= "";
	var $menu_content				= ""; 		// a blob of html for a quick easy render of the main menu
	var $menu_sub_content 			= ""; 		// a blob of html for a quick easy render of the sub menu
	var $menu_items_template		= array();  //use this if you want to generate you menu in your template properties: linktext, linkurl, linkimage, linkactive
	var $menu_sub_items_template	= array();  //use this if you want to generate you menu in your template properties: linktext, linkurl, linkimage, linkactive
	
	var $current_pages_index 	= -1;
	
	//this stores all the information about the pages in the app
	var $pages_array 		= array();
	var $current_app_name 	= "";
	var $current_app_page 	= "";
	
	var $app_user_message 		= "";
	var $app_user_message_type 	= ""; //good,bad,warning or what ever you want to use
	
	//extra classes
	var $web_helper;
	
	function __construct($cDBObject,$SiteConfig,$cUser,$sAppName,$sAppPage){
		$this->db 					= $cDBObject;
		$this->config 				= $SiteConfig;
		$this->user 				= $cUser;
		
		$this->current_app_name 	= $sAppName;
		$this->current_app_page 	= $sAppPage;
		$this->AppName				= $sAppName;
		$this->AppTemplate 			= $sAppName . "_template.php";
		$this->loadAppPages();
		$this->app_load();
		$this->web_helper 			= new cWeb();
	}
	
	function app_load(){
	}
	
	function loadAppPages(){
	}
	
	
	function loadRenderTemplate(){
		$SelectedPageIndex = $this->getCurrentPageIndex();
		if( $this->pages_array[ $SelectedPageIndex ]->page_is_callback == 0 ){
			//show template because this is not a call back page.
			if( file_exists("apps/" . $this->current_app_name . "/" . $this->AppTemplate) ){
				//try the app specific template in app/appname directory
				include("apps/" . $this->current_app_name . "/" . $this->AppTemplate);
			}elseif( file_exists("apps/" . $this->AppTemplate) ){
				//try the app specific template in app directory
				include("apps/" . $this->AppTemplate);
			}elseif( file_exists("apps/" . $this->AppTemplate) ){
				//try the generic template in app directory
				include("apps/generic_template.php");
			}else{
				echo "There needs to be a template specified. if you are trying to use this page as a call back set cPage->page_is_callback to 1";
			}
			
		}else{
			echo $this->page_content;
		}
	}
	
	function loadMenuHTML(){
		
		for($x=0;$x<count($this->pages_array);$x++){
			if($this->pages_array[$x]->menu_is_menu_item == 1 ){
				if($this->pages_array[$x]->menu_group == "" || $this->pages_array[$x]->menu_group == $this->pages_array[$x]->app_page){//has no submenu or is a parent menu
					$isActive = 0;
					if($this->pages_array[$x]->app_page == $this->current_app_page || $this->inParentSubMenu($this->pages_array[$x]->app_page) == true ){
						$isActive = 1;
					}
					if($this->pages_array[$x]->is_public == 1 && $this->compareRoles($this->pages_array[$x]->required_roles)==1 ){
						//menu item is public, always show
						$this->menu_content .= $this->pages_array[$x]->getURLLink($isActive) . " | ";
						$this->menu_items_template[] = $this->createMenuItem($this->pages_array[$x],$isActive);
						if( $this->pages_array[$x]->menu_group == $this->pages_array[$x]->app_page && $this->getSubMenuName() == $this->pages_array[$x]->menu_group){
							$this->menu_sub_content .= $this->pages_array[$x]->getURLLink($isActiveSub) . " | ";
							$this->menu_sub_items_template[] =  $this->createMenuItem($this->pages_array[$x],$isActive);
						}
					}
					if($this->pages_array[$x]->is_public == 0 && $this->user->isLoggedIn() == 1 && $this->compareRoles($this->pages_array[$x]->required_roles)==1){
						//menu item is logged in only
						$this->menu_content .= $this->pages_array[$x]->getURLLink($isActive) . " | ";
						$this->menu_items_template[] = $this->createMenuItem($this->pages_array[$x],$isActive);
						if( $this->pages_array[$x]->menu_group == $this->pages_array[$x]->app_page && $this->getSubMenuName() == $this->pages_array[$x]->menu_group){
							$this->menu_sub_content .= $this->pages_array[$x]->getURLLink($isActiveSub) . " | ";
							$this->menu_sub_items_template[] = $this->createMenuItem($this->pages_array[$x],$isActive);
						}
					}
					
					if($this->pages_array[$x]->is_public == 2 && $this->user->isLoggedIn() == 0 && $this->compareRoles($this->pages_array[$x]->required_roles)==1){
						//menu item is logged out only but not public.
						//Example - you wouldn't show the login menu if your already logged in.
						$this->menu_content .= $this->pages_array[$x]->getURLLink($isActive) . " | ";
						$this->menu_items_template[] = $this->createMenuItem($this->pages_array[$x],$isActive);
						if( $this->pages_array[$x]->menu_group == $this->pages_array[$x]->app_page && $this->getSubMenuName() == $this->pages_array[$x]->menu_group){
							$this->menu_sub_content .= $this->pages_array[$x]->getURLLink($isActiveSub) . " | ";
							$this->menu_sub_items_template[] = $this->createMenuItem($this->pages_array[$x],$isActive);
						}
					}
				}else{//end menu_group
					if($this->pages_array[$x]->menu_group == $this->current_app_page || $this->getSubMenuName() == $this->pages_array[$x]->menu_group){
						$isActiveSub = 0;
						if($this->pages_array[$x]->app_page == $this->current_app_page ){
							$isActiveSub = 1;
						}
						if($this->pages_array[$x]->is_public == 1 && $this->compareRoles($this->pages_array[$x]->required_roles)==1 ){
							//menu item is public, always show
							$this->menu_sub_content .= $this->pages_array[$x]->getURLLink($isActiveSub) . " | ";
							$this->menu_sub_items_template[] = $this->createMenuItem($this->pages_array[$x],$isActiveSub);
						}
						if($this->pages_array[$x]->is_public == 0 && $this->user->isLoggedIn() == 1 && $this->compareRoles($this->pages_array[$x]->required_roles)==1){
							//menu item is logged in only
							$this->menu_sub_content .= $this->pages_array[$x]->getURLLink($isActiveSub) . " | ";
							$this->menu_sub_items_template[] = $this->createMenuItem($this->pages_array[$x],$isActiveSub);
						}
						
						if($this->pages_array[$x]->is_public == 2 && $this->user->isLoggedIn() == 0 && $this->compareRoles($this->pages_array[$x]->required_roles)==1){
							//menu item is logged out only but not public.
							//Example - you wouldn't show the login menu if your already logged in.
							$this->menu_sub_content .= $this->pages_array[$x]->getURLLink($isActiveSub) . " | ";
							$this->menu_sub_items_template[] = $this->createMenuItem($this->pages_array[$x],$isActiveSub);
						}
					}
				}//end else menu_group
			}
		}
		$this->menu_content = rtrim($this->menu_content," | ");
		$this->menu_sub_content = rtrim($this->menu_sub_content," | ");
	}
	
	function inParentSubMenu($PageCheck){
		for($x=0;$x<count($this->pages_array);$x++){
			if( $this->pages_array[$x]->menu_group == $PageCheck && $this->current_app_page == $this->pages_array[$x]->app_page ){
				return true;
			}
		}
		return false;
	}
	
	function getSubMenuName(){
		for($x=0;$x<count($this->pages_array);$x++){
			if( $this->pages_array[$x]->app_page == $this->current_app_page){
				return $this->pages_array[$x]->menu_group;
			}
		}
		return "";
	}
	
	function createMenuItem($PageObject,$isActive){
		return new cMenuItem($PageObject-> getURL(),$PageObject->menu_title, $PageObject->menu_image, $PageObject->menu_group, $isActiveSub);				
	}
	
	
	function addContent($strInContent){
		$this->page_content .= $strInContent;
	}
	
	function getCurrentPageIndex(){
		for($x=0;$x<count($this->pages_array);$x++){
			if($this->pages_array[$x]->app_page == $this->current_app_page ){
				$this->current_pages_index = $x;
				return $x;
			}
		}
		return -1;
	}
	
	function loadMVCView(){
		$current_index = $this->getCurrentPageIndex();
		if($this->pages_array[$current_index]->mvc_view != ""){
			if( file_exists("apps/" . $this->current_app_name . "/" . $this->pages_array[$current_index]->mvc_view . "_view.php") ){
				ob_start();
				include("apps/" . $this->current_app_name . "/" . $this->pages_array[$current_index]->mvc_view . "_view.php");
				$this->addContent( ob_get_clean() );
			}
		}
	}
	
	function loadMVCController(){
		$current_index = $this->getCurrentPageIndex();
		if($this->pages_array[$current_index]->mvc_controller != ""){
			if( file_exists("apps/" . $this->current_app_name . "/" . $this->pages_array[$current_index]->mvc_controller . "_controller.php") ){
				include("apps/" . $this->current_app_name . "/" . $this->pages_array[$current_index]->mvc_controller . "_controller.php");
			}
		}
	}
	
	
	function compareRoles($aPageRolesRequired){
		if( count($aPageRolesRequired) == 0 ) {
			return 1;
		}
		for($x=0;$x<count($aPageRolesRequired);$x++){
			for($j=0;$j<count($this->user->user_roles);$j++){
				if( $this->user->user_roles[$j] == $aPageRolesRequired[$x]) {
					return 1;
				}
			}
		}
		return 0;
	}
	
	function hasAccessToPage($PageIDX){
		if($this->pages_array[$PageIDX]->is_public == 1 && $this->compareRoles($this->pages_array[$PageIDX]->required_roles)==1 ){
			return 1;
		}
		if($this->pages_array[$PageIDX]->is_public == 0 && $this->user->isLoggedIn() == 1 && $this->compareRoles($this->pages_array[$PageIDX]->required_roles)==1){
			return 1;
		}
		if($this->pages_array[$PageIDX]->is_public == 2 && $this->user->isLoggedIn() == 0 && $this->compareRoles($this->pages_array[$PageIDX]->required_roles)==1){
			return 1;
		}
		return 0;
	}
	
}//end cAPP
?>
