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
//							CONFIG PATHS
//-----------------------------------------------------------------



//-----------------------------------------------------------------
//					CONFIG USER & USER SECURITY
//-----------------------------------------------------------------
$CONFIG['security_max_fails_before_lock'] = 20;
$CONFIG['security_max_fails_lock_message'] = "You account was locked due to too many failed logins";


//-----------------------------------------------------------------
//					CONFIG USER & USER SECURITY
//-----------------------------------------------------------------
$CONFIG['app_default_guest'] 	= "site"; //You'll probably leave this as site.
$CONFIG['app_default_loggedin'] = "site"; //After they login you can take them to a different app if needs be.
$CONFIG['app_default_page'] 	= "index"; //go to a certain page within the app







//-----------------------------------------------------------------
//								LDAP OPTIONS
//-----------------------------------------------------------------
$CONFIG['model_LDAP_ldap_use'] 				= 0;
$CONFIG['model_LDAP_ldap_ip'] 				= "192.168.1.100";
$CONFIG['model_LDAP_ldap_port'] 				= 389;
$CONFIG['model_LDAP_user'] 					= 'CN=MyADSearchAccount,OU=Austin,DC=ourace,DC=com';
$CONFIG['model_LDAP_pass'] 					= 'Com!plex%Passw0rd';
$CONFIG['model_LDAP_search_user_base'] 		= "OU=Austin,DC=ourace,DC=com";
$CONFIG['model_LDAP_use_activedirectory'] 	= 1;//set to 0 if you want to use a a normal search expression


//Auto Add user UponAuth
$CONFIG['model_LDAP_add_user_to_db']		= 0; //1=if users authenticates to LDAP then add that user to the user table in the app
$CONFIG['model_LDAP_user_activate']			= 0; //0|1 Set the activation states of the auto add user when enabled.
$CONFIG['model_LDAP_user_activate_roles']	= array("guest");
$CONFIG['model_LDAP_user_activate_ent']		= array("1");


//these are used for another way to auth when you don't want your LDAP access on the same server or for something custom.
//An example of this can be seen in the file class_ldap validateUserRemote($username,$password) where it calls a curl to get the credentials
$CONFIG['model_LDAP_use_alt_method'] 		= 0; //set to 1 to use the validateUserRemote($username,$password) function
$CONFIG['model_LDAP_use_alt_method_url'] 	= "http://YourWebsitenameHere.com/meeting/service_ldap_remote.php"; //some remote curl page with auth
