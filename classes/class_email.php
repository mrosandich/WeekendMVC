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

class cEmail{
	var $db;
	var $config;
	var $user;
	
	var $email_server_type				= "";
	var $email_server_ip 				= "";
	var $email_server_port 				= 25;
	var $email_server_protocol			= "smtp";
	var $email_server_auth_user 		= "";
	var $email_server_auth_password 	= "";
	var $email_server_from_address		= "";
	var $web_helper;
	
	var $toAddress						= array();
	var $ccAddress						= array();
	var $bccAddress						= array();
	
	function __construct($cDBObject,$SiteConfig,$cUser){

		$this->db 					= $cDBObject;
		$this->config 				= $SiteConfig;
		$this->user 				= $cUser;

		$this->email_server_type			= $this->config['email_server_type'];
		$this->email_server_ip 				= $this->config['email_server_ip'];
		$this->email_server_port 			= $this->config['email_server_port'];
		$this->email_server_protocol		= $this->config['email_server_protocol'];
		$this->email_server_auth_user 		= $this->config['email_server_auth_user'];
		$this->email_server_auth_password 	= $this->config['email_server_auth_password'];
		$this->email_server_from_address	= $this->config['email_server_from_address'];
		
		$this->web_helper 			= new cWeb();
	}
	
	function addToAddress($name,$email){
		$this->toAddress[$email] = $name;
	}
	function addCCAddress($name,$email){
		$this->ccAddress[$email] = $name;
	}
	function addBCCAddress($name,$email){
		$this->bccAddress[$email] = $name;
	}
	
	
	function getAppEmailTemplate($htmlMixed){
		$retString = "";
		//looks for the app name , app page in the email template folder inside the apps/yourapp/email_templates/app_pagename.php		
		if( file_exists("apps/" . $this->web_helper->current_app_name . "/email_templates/" . $this->web_helper->current_app_page . ".php") ){
			include("apps/" . $this->web_helper->current_app_name . "/email_templates/" . $this->web_helper->current_app_page . ".php");
			$retString = getEmailContent($htmlMixed);
		}
		else
		{
			if(is_array($htmlMixed)){
				$retString = implode("\r\n", $htmlMixed);
				
			}else{
				$retString = $htmlMixed;
			}
		}
		
		return $retString;
	}
	
	function sendEmail( $subject, $htmlMixed){
		$headers="";
		$temp_to_addresses = "";
		foreach( $this->toAddress as $email => $Toname ){
			$temp_to_addresses.= $Toname . '<' . $email . '>,';
			
		}
		$temp_to_addresses = rtrim($temp_to_addresses,",");
		
		
		
		
		$headers .= 'From: ' . $this->email_server_from_address . "\r\n";  
		
		if( count($this->ccAddress) > 0 ){
			$temp_cc_addresses = "";
			foreach( $this->ccAddress as $email => $Toname ){
				$temp_cc_addresses.= $Toname . '<' . $email . '>,';
			}
			$temp_cc_addresses = rtrim($temp_cc_addresses, ",");
			$headers .= 'Cc: ' . $temp_cc_addresses . "\r\n";
		}
		
		if( count($this->bccAddress) > 0 ){
			$temp_bcc_addresses = "";
			foreach( $this->bccAddress as $email => $Toname ){
				$temp_bcc_addresses.= $Toname . '<' . $email . '>,';
			}
			$temp_bcc_addresses = rtrim($temp_bcc_addresses, ",");
			$headers .= 'Bcc: ' . $temp_bcc_addresses . "\r\n";
		}
		
		$message = $this->getAppEmailTemplate($htmlMixed);
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		
		mail($temp_to_addresses, $subject, $message, $headers);
	}
}
?>