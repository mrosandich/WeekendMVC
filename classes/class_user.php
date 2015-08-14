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
class cUser {
	
	var $db;
	var $config;
	var $user_id 			= -1;
	var $user_result;
	var $user_login_message = "";
	var $user_login_state	= 0;
	var $user_roles			= array("guest");
	var $user_locations		= array(); //each item contains enterprise_id->enterprise_name
	
	var $security_max_fails_before_lock = 20;
	var $security_max_fails_lock_message = "You account was locked due to too many failed logins";
	
	function __construct($cDBObject,$SiteConfig){
		$this->db 		= $cDBObject;
		$this->config 	= $SiteConfig;
		if(array_key_exists('security_max_fails_before_lock',$this->config) ) {
			$this->security_max_fails_before_lock = $this->config['security_max_fails_before_lock'];
		}
		if(array_key_exists('security_max_fails_before_lock',$this->config) ) {
			$this->security_max_fails_lock_message = $this->config['security_max_fails_lock_message'];
		}
	}
	
	//returns 1 if user has role 0  if they don't
	function checkRole($RoleName){
		if( in_array($RoleName,$this->user_roles) == true ){
			return 1;
		}else{
			return 0;
		}
	}
	
	function isLoggedIn(){
		return $this->user_login_state;
	}
	
	//this is used to load the user object after login.
	function getActiveUserFromDB(){
		if( array_key_exists('user_id',$_SESSION) ){
			
			//load user information
			$this->db->sql('select * from users where id=:id limit 1');
			$this->db->addParam(":id",$_SESSION['user_id']);
			$this->user_result = $this->db->execute();
			$this->loadUserRoles($_SESSION['user_id']);
			$this->loadUserLocations($_SESSION['user_id']);
			$this->user_login_state = 1;
			$this->user_id = $_SESSION['user_id'];
			
		}
		return false;
	}
	
	function logout(){
		session_destroy();
		$this->user_login_state 	= 0;
		$this->user_id 				= -1;
		$this->user_result;
		$this->user_login_message 	= "";
		$this->user_login_state		= 0;
		$this->user_roles			= array("guest");
	}
	
	function loadUserRoles($UserID){
		//load user roles.
		$this->db->sql('SELECT RO.name FROM `roles_users` RU join roles RO on RO.id = RU.role_id where RU.user_id=:userid');
		$this->db->addParam(":userid",$UserID);
		$temp_results = $this->db->execute();
		for($x=0;$x<count($temp_results);$x++){
			$this->user_roles[] = $temp_results[$x]->name;
		}
	}
	
	function loadUserLocations($UserID){
		//load user roles.
		$this->db->sql('SELECT EN.enterprise_id,EN.enterprise_name FROM `enterprise` EN join user_to_enterprise UE on UE.ent_id = EN.enterprise_id where UE.user_id=:userid');
		$this->db->addParam(":userid",$UserID);
		$temp_results = $this->db->execute();
		for($x=0;$x<count($temp_results);$x++){
			$this->user_locations[$temp_results[$x]->enterprise_id] =$temp_results[$x]->enterprise_name;
		}
	}
	
	
	function login($UserName,$Password){
		$this->db->sql('select * from users where username=:username  limit 1');
		$this->db->addParam(":username",$UserName);
		$this->user_result = $this->db->execute();
		
		
		if( $this->db->getResultCount() > 0 ){
			//we have found the user
			if( $this->user_result[0]->is_locked == 0 ){
			
				if( $this->user_result[0]->password == md5($Password) ){
					//we have a good user and password at this point.
					if( $this->user_result[0]->is_activated == 1){
						//The current user account has been activated
						if( $this->user_result[0]->is_locked == 0 ){
							//the account is not locked
							//At this point we have passed all the requirements to login.
							$this->user_login_message 	= "You are logged in.";
							$this->user_login_state	= 1;
							$_SESSION['user_id'] = $this->user_result[0]->id;
							$this->updateLogin($UserName,$this->user_result[0]->id);
							$this->loadUserRoles($this->user_result[0]->id);
							$this->loadUserLocations($this->user_result[0]->id);
							
							
						}else{
							//This will never be hit because we look for the lock before we get here.
							$this->user_login_message 	= "Username and Password are correct but your account hasn't been locked.";
							if( $this->user_result[0]->locked_message != "" ){
								$this->user_login_message 	.= "<br />Reason for lock:" . $this->user_result[0]->locked_message;
							}
							$this->user_login_state	= 0;
						}
						
						
					}else{
						$this->user_login_message 	= "Username and Password are correct but your account hasn't been activated yet.";
						$this->user_login_state	= 0;
					}
					
					
				}else{
					$this->user_login_message 	= "Invalid username/password combination..";
					$this->user_login_state	= 0;
					$this->addFailedAttempt($UserName,$this->user_result[0]->failed_logins,$this->user_result[0]->id);
				}
			}else{
				$this->user_login_message 	= "The account hasn't been locked.";
				if( $this->user_result[0]->locked_message != "" ){
					$this->user_login_message 	.= "<br />Reason for lock:" . $this->user_result[0]->locked_message;
				}
				$this->user_login_state	= 0;
			}
			
		}else{
			$this->user_login_message 	= "Invalid username/password combination.";
			$this->user_login_state	= 0;
			$this->addFailedAttempt($UserName,$this->user_result[0]->failed_logins,$this->user_result[0]->id);
		}
		
		return $this->user_login_state;
	}
	
	
	function changePasswordViaRecovery($UserName,$ResetGuid,$NewPassword){
		$NewPassword = md5($NewPassword);
		$this->db->sql('update users set password=:password where username=:username and reset_guid=:reset_guid');
		$this->db->addParam(":username",$UserName);
		$this->db->addParam(":reset_guid",$ResetGuid);
		$this->db->addParam(":password",$NewPassword);
		$this->db->execute();
	}
	
	
	function addFailedAttempt($UserName,$FailedCount,$UserId){
		//lets increment login failure
		$this->db->sql('update users set failed_logins=failed_logins+1 where username=:username');
		$this->db->addParam(":username",$UserName);
		$this->db->execute();
		
		//let log the entry
		$this->db->sql('insert into user_audit_login_fail (user_id,user_name,date_time,ip_address)values(:user_id,:user_name,:date_time,:ip_address)');
		$this->db->addParam(":user_id",$UserId);
		$this->db->addParam(":user_name",$UserName);
		$this->db->addParam(":date_time",date("Y-m-d H:i:s") );
		$this->db->addParam(":ip_address",$_SERVER['REMOTE_ADDR']);
		$this->db->execute();
		
		//if the login failure gets to big lets lock the account and give the user a locked message
		if( $FailedCount > $this->security_max_fails_before_lock ){
			$this->db->sql('update users set is_locked=1,locked_message=:locked_message where username=:username');
			$this->db->addParam(":username",$UserName);
			$this->db->addParam(":locked_message",$this->security_max_fails_lock_message);
			$this->db->execute();
		}
	}
	
	function updateLogin($UserName,$UserId){
		//Lets increase the login count and clear the failed logins
		$this->db->sql('update users set logins=logins+1,failed_logins=:failed_logins where username=:username');
		$this->db->addParam(":username",$UserName);
		$this->db->addParam(":failed_logins",0);
		$this->db->execute();
		
		//let log the entry
		$this->db->sql('insert into user_audit_login (user_id,user_name,date_time,ip_address)values(:user_id,:user_name,:date_time,:ip_address)');
		$this->db->addParam(":user_id",$UserId);
		$this->db->addParam(":user_name",$UserName);
		$this->db->addParam(":date_time",date("Y-m-d H:i:s") );
		$this->db->addParam(":ip_address",$_SERVER['REMOTE_ADDR']);
		$this->db->execute();
	}
	
	function modifyRoles($userIdOverride="",$action,$roles=array()){
			//action add, delete, reload, clear, defaults
			//	add			= add roles to user
			//	delete		= delete roles from user
			//	reload		= deletes all roles and then readd from in array $roles
			//	clear 		= delete all roles
			//	defaults 	= delete all roles then readd defaults
			
			$temp_userid = $this->user_id;
			if($userIdOverride!=""){
				$temp_userid = $userIdOverride;
			}
			
			$SQLStatement = "";
			
			if($action == "clear"){
				$this->clearRoles($temp_userid);
			}
			
			if($action == "add"){
				$this->addRoles($temp_userid,$roles);
			}
			
			if($action == "reload"){
				$this->clearRoles($temp_userid);
				$this->addRoles($temp_userid,$roles);
			}
			
			if($action == "defaults"){
				$this->clearRoles($temp_userid);
				$this->addRoles($temp_userid,$this->config['app_site_user_activate_roles']);
			}
			if($action == "delete"){
				$this->deleteRoles($temp_userid,$roles);
			}
			
	}
	
	function addRoles($temp_userid,$roles){
		
		for($x=0;$x<count($roles);$x++){
			$SQLStatement = "insert into roles_users (role_id,user_id) values(:role_id,:user_id)";
			$this->db->sql($SQLStatement);
			$this->db->addParam(":user_id",$temp_userid);
			$this->db->addParam(":role_id",$roles[$x]);
			$this->db->execute();
		}
	}
	
	function deleteRoles($temp_userid,$roles){
		for($x=0;$x<count($roles);$x++){
			$SQLStatement = "delete from roles_users where role_id=:role_id and user_id=user_id)";
			$this->db->sql($SQLStatement);
			$this->db->addParam(":user_id",$temp_userid);
			$this->db->addParam(":role_id",$roles[$x]);
			$this->db->execute();
		}
	}
	
	function clearRoles($temp_userid){
		$SQLStatement = "delete from roles_users where user_id=:user_id";
		$this->db->sql($SQLStatement);
		$this->db->addParam(":user_id",$temp_userid);
		$this->db->execute();
	}
	
	
	
	
	function modifyEnterprises($userIdOverride="",$action,$enterprises=array()){
			//action add, delete, reload, clear, defaults
			//	add			= add enterprise to user
			//	delete		= delete enterprise from user
			//	reload		= delete all enterprise and then readd from in array $roles
			//	clear 		= delete all enterprise
			//	defaults 	= delete all enterprise then readd defaults
			
			$temp_userid = $this->user_id;
			if($userIdOverride!=""){
				$temp_userid = $userIdOverride;
			}
			
			$SQLStatement = "";
			
			if($action == "clear"){
				$this->clearEnterprise($temp_userid);
			}
			
			if($action == "add"){
				$this->addEnterprise($temp_userid,$enterprises);
			}
			
			if($action == "reload"){
				$this->clearEnterprise($temp_userid);
				$this->addEnterprise($temp_userid,$enterprises);
			}
			
			if($action == "defaults"){
				$this->clearEnterprise($temp_userid);
				$this->addEnterprise($temp_userid,$this->config['app_site_user_activate_enterprise']);
			}
			if($action == "delete"){
				$this->deleteEnterprise($temp_userid,$enterprises);
			}
			
	}
	
	function addEnterprise($temp_userid,$EntArray){
		for($x=0;$x<count($EntArray);$x++){
			$SQLStatement = "insert into user_to_enterprise (ent_id,user_id) values(:ent_id,:user_id)";
			$this->db->sql($SQLStatement);
			$this->db->addParam(":user_id",$temp_userid);
			$this->db->addParam(":ent_id",$EntArray[$x]);
			$this->db->execute();
		}
	}
	
	function deleteEnterprise($temp_userid,$EntArray){
		for($x=0;$x<count($EntArray);$x++){
			$SQLStatement = "delete from user_to_enterprise where ent_id=:ent_id and user_id=user_id)";
			$this->db->sql($SQLStatement);
			$this->db->addParam(":user_id",$temp_userid);
			$this->db->addParam(":ent_id",$EntArray[$x]);
			$this->db->execute();
		}
	}
	
	function clearEnterprise($temp_userid){
		$SQLStatement = "delete from user_to_enterprise where user_id=:user_id";
		$this->db->sql($SQLStatement);
		$this->db->addParam(":user_id",$temp_userid);
		$this->db->execute();
	}
	
	
	
	
	function addLDAPUser($user_username, $user_password, $user_email, $user_namelast, $user_namefirst,$app_auto_user_activate){
		
		//check if user is in table
		$this->db->sql('select * from users where username=:username limit 1');
		$this->db->addParam(":username",$user_username);
		$this->user_result = $this->db->execute();
		
		//if the user name doesn't exist add it and mark it as LDAP
		if( $this->db->getResultCount() == 0 ){
			$user_password = MD5($user_password);
			$this->db->sql('insert into user_audit_login (username, password, email, name_last, name_first, is_activated, is_ldap )values(:username, :password, :email, :name_last, :name_first, :is_activated, :is_ldap)');
			$this->db->addParam(":username",$user_username);
			$this->db->addParam(":password",$user_password);
			$this->db->addParam(":email",$user_email );
			$this->db->addParam(":name_last",$user_namelast);
			$this->db->addParam(":name_first",$user_namefirst);
			$this->db->addParam(":is_activated",$app_auto_user_activate);
			$this->db->addParam(":is_ldap",1);
			$this->db->execute();
			
			/* 
				ToDO:
				Add roles
				Add enterprises
				
				$CONFIG['model_LDAP_user_activate_roles']	= array("guest");
				$CONFIG['model_LDAP_user_activate_ent']		= array("1");
			*/
			
		}
	}
} //end cUser
$user = new cUser($db,$CONFIG);
$user->getActiveUserFromDB();
?>