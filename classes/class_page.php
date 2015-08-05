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
class cPage {
	
	var $db;
	var $config;
	var $user;
	
	var $page_title = "";
	var $page_css 	= array();
	var $page_js 	= array();
	
	var $app_name		= "";
	var $app_page		= "";
	
	var $is_public		= 0; //0=not public required to be logged in. 1=always available in application logged in or not. 2=only available when logged out.
	var $required_roles = array();
	
	var $menu_is_menu_item 	= 1; //is this a menu item. it could be just a processing page.
	var $menu_always_show 	= 0;	//should this always show regardless of which app your in. A good example is logout.
	var $menu_title 		= "";
	var $menu_image 		= "";
	
	var $page_posts 		= 0;
	var $page_post_to 		= ""; //this is the app_page it's going to when posting
	var $page_is_callback 	= 0; //set to 1 remove template outputs. used for call back to a json or ajax call
	
	var $mvc_model 			= "";
	var $mvc_view			= "";
	var $mvc_controller 	= "";
	
	//this var is not initialized during construct. This will be changed based on the controller logic.
	//default is view
	var $app_action		= "view";

	function __construct(	$cDBObject, $SiteConfig, $cUser, 
							$strPageTitle, $aStrCSS, $aStrJS, 
							$strAppName, $strAppPage, $iIsPublic, $aStrRoles, 
							$iIsMenu, $iMenuAlwaysShow,$sMenuTitle, $sMenuItemImage,
							$iDoesPost,$sPostToAppPage,$iIsJsonCallBack,
							$sMVCModel,$sMVCVIew,$sMVCController){
								
		$this->db 					= $cDBObject;
		$this->config 				= $SiteConfig;
		$this->user 				= $cUser;
		
		$this->page_title 			= $strPageTitle;
		$this->page_css 			= $aStrCSS;
		$this->page_js 				= $aStrJS;
		
		$this->app_name				= $strAppName;
		$this->app_page				= $strAppPage;
		$this->is_public			= $iIsPublic;
		$this->required_roles 		= $aStrRoles;
		
		$this->menu_is_menu_item 	= $iIsMenu; 
		$this->menu_always_show 	= $iMenuAlwaysShow;	
		$this->menu_title 			= $sMenuTitle;
		$this->menu_image 			= $sMenuItemImage;
		
		$this->page_posts 			= $iDoesPost;
		$this->page_post_to 		= $sPostToAppPage; 
		$this->page_is_callback		= $iIsJsonCallBack;
		
		$this->mvc_model 			= $sMVCModel;
		$this->mvc_view				= $sMVCVIew;
		$this->mvc_controller 		= $sMVCController;
	}
	
	function getLinkPageQueryParts(){
		return "app_name=" . $this->app_name . "&app_page=" . $this->app_page . "&app_action=" . $this->app_action;
	}
	
	function getURL(){
		return "/index.php?app_name=" . $this->app_name . "&app_page=" . $this->app_page . "&app_action=" . $this->app_action;
	}
	
	function getURLLink($isActive){
		if( $isActive==1){
			return "<a class=\"menu_link active\" href=\"" . $this->getURL(). "\">{$this->menu_title}</a>\n";
		}else{
			return "<a class=\"menu_link inactive\" href=\"" . $this->getURL(). "\">{$this->menu_title}</a>\n";
		}
	}
	
	function getFormActionHTML(){
		$temp_form_id= uniqid();
		$_SESSION['form_model_uid'] = $temp_form_id;
		return "<form method=\"post\" name=\"{$this->app_name}_{$this->app_page}\" id=\"{$this->app_name}_{$this->app_page}\" action=\"\">\n\t<input type=\"hidden\" name=\"app_page\" value=\"{$this->page_post_to}\">\n\t<input type=\"hidden\" name=\"app_name\" value=\"{$this->app_name}\">\n\t<input type=\"hidden\" name=\"app_action\" value=\"{$this->app_action}\">\n\t<input type=\"hidden\" name=\"form_model_uid\" value=\"$temp_form_id\">\n";
	}
} //end cPage

?>