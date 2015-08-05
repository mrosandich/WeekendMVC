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
	var $AppName 		= "";
	var $AppTemplate 	= "";
	var $page_content	= "";
	var $menu_content	= "";
	var $current_pages_index = -1;
	
	//this stores all the information about the pages in the app
	var $pages_array = array();
	var $current_app_name = "";
	var $current_app_page = "";
	
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
				$isActive = 0;
				if($this->pages_array[$x]->app_page == $this->current_app_page ){
					$isActive = 1;
				}
				if($this->pages_array[$x]->is_public == 1 && $this->compareRoles($this->pages_array[$x]->required_roles)==1 ){
					//menu item is public, always show
					$this->menu_content .= $this->pages_array[$x]->getURLLink($isActive) . " | ";
				}
				if($this->pages_array[$x]->is_public == 0 && $this->user->isLoggedIn() == 1 && $this->compareRoles($this->pages_array[$x]->required_roles)==1){
					//menu item is logged in only
					$this->menu_content .= $this->pages_array[$x]->getURLLink($isActive) . " | ";
				}
				
				if($this->pages_array[$x]->is_public == 2 && $this->user->isLoggedIn() == 0 && $this->compareRoles($this->pages_array[$x]->required_roles)==1){
					//menu item is logged out only but not public.
					//Example - you wouldn't show the login menu if your already logged in.
					$this->menu_content .= $this->pages_array[$x]->getURLLink($isActive) . " | ";
				}
			}
		}
		$this->menu_content = rtrim($this->menu_content," | ");
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
