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
define("DEBUG_ECHO",true); //when set to true the classes will echo error messages as they come across issues.

//-----------------------------------------------------------------
//							CONFIG DATABASE
//-----------------------------------------------------------------
$CONFIG = array();
$CONFIG['db_host']		= '192.168.1.99';
$CONFIG['db_name']		= 'yourdb';
$CONFIG['db_username']	= 'someusername';
$CONFIG['db_password'] 	= 'anotherpassword';


//-----------------------------------------------------------------
//							CONFIG GENERAL
//-----------------------------------------------------------------
$CONFIG['website_name'] 		= "your site name";
$CONFIG['website_url_base'] 	= "http://somewebsiteyouhave.com"; // just the url nothing else follows: something like: http://www.yoursite8833.com/


//-----------------------------------------------------------------
//					CONFIG USER & USER SECURITY
//-----------------------------------------------------------------
$CONFIG['security_max_fails_before_lock'] 		= 20;
$CONFIG['security_max_fails_lock_message'] 		= "You account was locked due to too many failed logins";


$CONFIG['security_user_allowed_name_characters']		= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-0123456789';
$CONFIG['security_user_allowed_name_min_length']		= 6;


$CONFIG['security_password_min_length'] 				= 8;
$CONFIG['security_password_required_numbers'] 			= 1; //how many numbers need to be in the password
$CONFIG['security_password_required_uppercase'] 		= 1; //how many uppercase need to be in the password
$CONFIG['security_password_required_lowercase'] 		= 1; //how many lowercase need to be in the password
$CONFIG['security_password_required_specialcharacters'] = 1; //how many special charters need to be in the password
$CONFIG['security_password_specialcharacters'] 			= '!@#$%^&*()_+-={}[]|:;"<>,.?/'; //set the password special characters


//-----------------------------------------------------------------
//					CONFIG USER & USER SECURITY
//-----------------------------------------------------------------
$CONFIG['app_default_guest'] 	= "site"; //You'll probably leave this as site.
$CONFIG['app_default_loggedin'] = "site"; //After they login you can take them to a different app if needs be.
$CONFIG['app_default_page'] 	= "index"; //go to a certain page within the app


//-----------------------------------------------------------------
//								New User Registration
//-----------------------------------------------------------------
//user_email, enterprise_manager, site_aministrator, none 
//user_email=user clicks link in email
//enterprise_manager=someone in that enterprise approves has approve user roles
//site_aministrator=The site admin approves
//none=user is approved at the time of creation. use recaptcha or app_site_register_require_enterprise_code to prevent bots from signing up.
$CONFIG['app_site_register_user_activation_method'] 		= 'site_aministrator'; 
$CONFIG['app_site_register_require_enterprise_code'] 		= 0; 	//make the user enter the enterprise code
$CONFIG['app_site_register_user_default_enterprise_code'] 	= '';	//leave blank for none or set 


$CONFIG['app_site_register_require_recaptcha'] 				= 0; 	//0=off, 1=on.  if 1 go to https://www.google.com/recaptcha/admin#list to get your keys
$CONFIG['app_site_register_require_recaptcha_site_key'] 	= ''; 
$CONFIG['app_site_register_require_recaptcha_secret_key']	= ''; 


$CONFIG['app_site_user_activate_roles']						= array("1"); //default roles
$CONFIG['app_site_user_activate_enterprise']				= array("3"); //default enterprise id


$CONFIG['email_server_registration_email_subject']			= "Activation required for your account";


//-----------------------------------------------------------------
//						EMAIL CONFIGURATION
//-----------------------------------------------------------------
$CONFIG['email_server_type']			= "php";
$CONFIG['email_server_ip']				= "";
$CONFIG['email_server_port']			= "";
$CONFIG['email_server_protocol']		= "";
$CONFIG['email_server_auth_user']		= "";
$CONFIG['email_server_auth_password']	= "";
$CONFIG['email_server_from_address']	=  'Your CHC <contact@mychc.org>';


//-----------------------------------------------------------------
//								LDAP OPTIONS
//-----------------------------------------------------------------
$CONFIG['model_LDAP_ldap_use'] 				= 0;
$CONFIG['model_LDAP_ldap_ip'] 				= "192.168.1.100";
$CONFIG['model_LDAP_ldap_port'] 			= 389;
$CONFIG['model_LDAP_user'] 					= 'CN=MyADSearchAccount,OU=Austin,DC=ourace,DC=com';
$CONFIG['model_LDAP_pass'] 					= 'Com!plex%Passw0rd';
$CONFIG['model_LDAP_search_user_base'] 		= "OU=Austin,DC=ourace,DC=com";
$CONFIG['model_LDAP_use_activedirectory'] 	= 1;//set to 0 if you want to use a a normal search expression


//Auto Add user UponAuth
$CONFIG['model_LDAP_add_user_to_db']		= 0; //1=if users authenticates to LDAP then add that user to the user table in the app
$CONFIG['model_LDAP_user_activate']			= 0; //0|1 Set the activation states of the auto add user when enabled.
$CONFIG['model_LDAP_user_activate_roles']	= array("1");
$CONFIG['model_LDAP_user_activate_ent']		= array("3");


//these are used for another way to auth when you don't want your LDAP access on the same server or for something custom.
//An example of this can be seen in the file class_ldap validateUserRemote($username,$password) where it calls a curl to get the credentials
$CONFIG['model_LDAP_use_alt_method'] 		= 0; //set to 1 to use the validateUserRemote($username,$password) function
$CONFIG['model_LDAP_use_alt_method_url'] 	= "http://YourWebsitenameHere.com/api/service_ldap_remote.php"; //some remote curl page with auth
