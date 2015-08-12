<?php
function RegisterSetupForm($passThis){
	if( $passThis->web_helper->current_app_page== "register" || $passThis->web_helper->current_app_page == "register_do" ){
		$passThis->siteForms->setTableName('users');
		$passThis->siteForms->setPKName('id');

		$passThis->siteForms->createElement('username',true,'',"", "UserName");
		$passThis->siteForms->boundElements['username']->addValidation('valueNotInDBTable',array('table'=>'users','col'=>'username'));
		$passThis->siteForms->boundElements['username']->addValidation('notEmpty',array());
		$passThis->siteForms->boundElements['username']->db = $passThis->db;
		$passThis->siteForms->boundElements['username']->addValidation('isValidUserName',array('userlen' => $passThis->config['security_user_allowed_name_min_length'] , 'userchars' => $passThis->config['security_user_allowed_name_characters']) );
													

		$passThis->siteForms->createElement('password',true,'',"", "Password");
		$passThis->siteForms->boundElements['password']->auto_populate_db = false;
		$passThis->siteForms->boundElements['password']->auto_populate_post = false;
		$passThis->siteForms->boundElements['password']->addValidation('notEmpty',array());
		$passThis->siteForms->boundElements['password']->addValidation('isValidPassword',
													array(
														'len' => $passThis->config['security_password_min_length'] , 
														'cntup' => $passThis->config['security_password_required_uppercase'], 
														'cntlw' => $passThis->config['security_password_required_lowercase'], 
														'cntspc' => $passThis->config['security_password_required_specialcharacters'], 
														'cntnum' => $passThis->config['security_password_required_numbers'], 
														'spchar' => $passThis->config['security_password_specialcharacters']
													)
													);



		$passThis->siteForms->createElement('pass2',false,'',"", "Password");
		$passThis->siteForms->boundElements['pass2']->auto_populate_db = false;
		$passThis->siteForms->boundElements['pass2']->auto_populate_post = false;
		$passThis->siteForms->boundElements['pass2']->addValidation('otherFormElementSameValue',array('formname'=>'password','formlabel'=>'Password'));


		$passThis->siteForms->createElement('name_first',true,'',"", "AlphaNumeric");
		$passThis->siteForms->createElement('name_last',true,'',"", "AlphaNumeric");

		$passThis->siteForms->createElement('email',true,'',"", "Email");
		$passThis->siteForms->boundElements['email']->addValidation('notEmpty',array());
		$passThis->siteForms->boundElements['email']->addValidation('isEmail',array());
		$passThis->siteForms->boundElements['email']->form_web_type = "text";
		$passThis->siteForms->boundElements['email']->form_label = "Email";



		$passThis->siteForms->createElement('recovery_q1',true,'',"", "AlphaNumeric");
		$passThis->siteForms->boundElements['recovery_q1']->addValidation('notEmpty',array());
		$passThis->siteForms->createElement('recover_an1_enc',true,'',"", "AlphaNumeric");
		$passThis->siteForms->boundElements['recover_an1_enc']->addValidation('notEmpty',array());


		$passThis->siteForms->createElement('recovery_q2',true,'',"", "AlphaNumeric");
		$passThis->siteForms->boundElements['recovery_q2']->addValidation('notEmpty',array());
		$passThis->siteForms->createElement('recover_an2_enc',true,'',"", "AlphaNumeric");
		$passThis->siteForms->boundElements['recover_an2_enc']->addValidation('notEmpty',array());


		$passThis->siteForms->createElement('recovery_q3',true,'',"", "AlphaNumeric");
		$passThis->siteForms->boundElements['recovery_q3']->addValidation('notEmpty',array());
		$passThis->siteForms->createElement('recover_an3_enc',true,'',"", "AlphaNumeric");
		$passThis->siteForms->boundElements['recover_an3_enc']->addValidation('notEmpty',array());

		//if config recaptcha enable lets show the form.
		if($passThis->config['app_site_register_require_recaptcha'] == 1 ){
			
		}
		
		//if config enterprise id enable lets show the form element.
		if($passThis->config['app_site_register_require_enterprise_code'] == 1 ){
			$passThis->siteForms->createElement('enterprise_code',true,'',"", "AlphaNumeric");
			$passThis->siteForms->boundElements['enterprise_code']->addValidation('valueIsInDBTable',array('table'=>'enterprise','col'=>'enterprise_invite_code'));
			$passThis->siteForms->boundElements['enterprise_code']->addValidation('notEmpty',array());
			$passThis->siteForms->boundElements['enterprise_code']->db = $passThis->db;
			$passThis->siteForms->boundElements['enterprise_code']->auto_populate_db = false;
			$passThis->siteForms->boundElements['enterprise_code']->form_web_type = "text";
			$passThis->siteForms->boundElements['enterprise_code']->form_label	 = "Enterprise Code";
		}
		
		$passThis->siteForms->createElement('save_btn',false);
	}
}// end RegisterSetupForm()


function RegisterDoPost($passThis){

	$GoodData = false;
	if( $passThis->web_helper->current_app_page== "register_do" ){
		$passThis->siteForms->setTableAction('new');
		
		$GoodData = $passThis->siteForms->bindAndFilter();
		
		//if passwords match and is not blank then send to sql with value of password MD5 hashed.
		if( $passThis->siteForms->boundElements['password']->form_value  == $passThis->siteForms->boundElements['pass2']->form_value){
			$passThis->siteForms->boundElements['password']->form_value = MD5($passThis->siteForms->boundElements['password']->form_value);
		}else{
			$passThis->siteForms->HTMLErrors[$passThis->web_helper->current_app_page] .= "Password did not match.";
			$GoodData = false;
		}
		
		
		if( $GoodData == true ){	
			$Result = $passThis->siteForms->doSQLInsert();
			//we  need the new user id to add roles and enterprises
			
			//if the user is good we need to see if the activation method is none
			if($passThis->config['app_site_register_user_activation_method']=='none'){
				$newuserid = $passThis->siteForms->db->getLastInsertId();
				if($newuserid>0){
					if(count($passThis->config['app_site_user_activate_roles'])>0){
						$passThis->user->modifyRoles($newuserid,"defaults");
					}
					if(count($passThis->config['app_site_user_activate_roles'])>0){
						$passThis->user->modifyEnterprises($newuserid,"defaults");
					}
				}
				$passThis->web_helper->current_app_page= "register_complete";
				$passThis->app_user_message = "Account Created.";
				$passThis->app_user_message_type = "good";
				
			}elseif($passThis->config['app_site_register_user_activation_method']=='user_email'){
				$newuserid = $passThis->siteForms->db->getLastInsertId();
				$activation_guid = strtoupper(uniqid()) . $newuserid;
				$passThis->db->sql("update users set activation_guid=:activation_guid where id=:user_id");
				$passThis->db->addParam(":user_id",$newuserid);
				$passThis->db->addParam(":activation_guid",$activation_guid);
				$passThis->db->execute();
				
				$emailService = new cEmail($passThis->db,$passThis->config,$passThis->user);
				
				$emailContent['fullname'] 	= $passThis->siteForms->boundElements['name_first']->form_value . " " . $passThis->siteForms->boundElements['name_last']->form_value;
				$emailContent['sitename'] 	= $passThis->config['website_name'];
				$emailContent['actcode'] 	= $activation_guid;
				$emailContent['actlink'] 	= $passThis->config['website_url_base'] . "/index.php?app_name=site&app_page=register_code&app_action=view&ac=" . $activation_guid . "&un=" . $passThis->siteForms->boundElements['username']->form_value;
				$emailContent['username'] 	= $passThis->siteForms->boundElements['username']->form_value;
				
				$emailService->addToAddress($emailContent['fullname'], $passThis->siteForms->boundElements['email']->form_value);
				$emailService->sendEmail( $passThis->config['email_server_registration_email_subject'], $emailContent);
				
				$passThis->web_helper->current_app_page= "register_code";
				$passThis->app_user_message = "We have sent you an email to <b>" . $passThis->siteForms->boundElements['email']->form_value . "</b> with activation instructions.";
				$passThis->app_user_message_type = "warning";
			}elseif($passThis->config['app_site_register_user_activation_method']=='enterprise_manager'){
				
				$passThis->web_helper->current_app_page= "register_complete";
				$passThis->app_user_message = "Your account will need to be activated by a manager.";
				$passThis->app_user_message_type = "warning";
				
			}elseif($passThis->config['app_site_register_user_activation_method']=='site_aministrator'){
				
				$passThis->web_helper->current_app_page= "register_complete";
				$passThis->app_user_message = "Your account will need to be activated the site administrator.";
				$passThis->app_user_message_type = "warning";
			}else{
				//shouldn't have made it here
				$passThis->web_helper->current_app_page= "register_complete";
				$passThis->app_user_message = "Your account will need to be activated the site administrator.";
				$passThis->app_user_message_type = "warning";
			}
			
			
			
			$passThis->user->getActiveUserFromDB();
			
		}else{
			$passThis->app_user_message = $passThis->siteForms->HTMLErrors[$passThis->web_helper->current_app_page];
			$passThis->app_user_message_type = "warning";
		}
		
		
	}
	return $GoodData;
}//end RegisterDo()


function RegisterSetupFormDisplay($passThis){
	if( $passThis->web_helper->current_app_page== "register" || $passThis->web_helper->current_app_page == "register_do"){
		$passThis->siteForms->setTableAction('edit');
		//set data elements / columns used.

		
		$passThis->siteForms->boundElements['username']->form_web_type = "text";
		$passThis->siteForms->boundElements['username']->form_label	 = "username";

		$passThis->siteForms->boundElements['password']->form_web_type = "password";
		$passThis->siteForms->boundElements['password']->form_label	 = "Password";

		//set html parts for this col
		$passThis->siteForms->boundElements['pass2']->form_web_type = "password";
		$passThis->siteForms->boundElements['pass2']->form_label	 = "Password";
		$passThis->siteForms->boundElements['pass2']->form_caption	 = "<br />Retype password to confirm.";
		
		//set html parts for this col
		$passThis->siteForms->boundElements['name_first']->form_web_type = "text";
		$passThis->siteForms->boundElements['name_first']->form_label	 = "First Name";
		
		$passThis->siteForms->boundElements['name_last']->form_web_type = "text";
		$passThis->siteForms->boundElements['name_last']->form_label = "Last Name";
			

		
		$passThis->siteForms->boundElements['recovery_q1']->form_web_type = "select";
		$passThis->siteForms->boundElements['recovery_q1']->form_label = "Recovery Question 1";
		$passThis->siteForms->boundElements['recover_an1_enc']->form_web_type = "text";
		$passThis->siteForms->boundElements['recover_an1_enc']->form_label = "Answer to question 1";


		$passThis->siteForms->boundElements['recovery_q2']->form_web_type = "select";
		$passThis->siteForms->boundElements['recovery_q2']->form_label = "Recovery Question 2";
		$passThis->siteForms->boundElements['recover_an2_enc']->form_web_type = "text";
		$passThis->siteForms->boundElements['recover_an2_enc']->form_label = "Answer to question 2";

		$passThis->siteForms->boundElements['recovery_q3']->form_web_type = "select";
		$passThis->siteForms->boundElements['recovery_q3']->form_label = "Recovery Question 3";
		$passThis->siteForms->boundElements['recover_an3_enc']->form_web_type = "text";
		$passThis->siteForms->boundElements['recover_an3_enc']->form_label = "Answer to question 3";


		$passThis->siteForms->boundElements['recovery_q1']->form_cntrl_values = array("what's you pets name?"=>"what's you pets name?","What city were you born in"=>"What city were you born in?");
		$passThis->siteForms->boundElements['recovery_q2']->form_cntrl_values = array("what's you mom's name?"=>"what's you mom's name?","What city is your favorite"=>"What city is your favorite?");
		$passThis->siteForms->boundElements['recovery_q3']->form_cntrl_values = array("what's you dad's name?"=>"what's you dad's name?","What is your favorite color"=>"What is your favorite color?");
		
		
		$passThis->siteForms->boundElements['save_btn']->form_web_type = "submit";
		$passThis->siteForms->boundElements['save_btn']->form_label	 = "";
		$passThis->siteForms->boundElements['save_btn']->form_value = "Save";

	}
}//end RegisterSetupFormDisplay()

if( $passThis->web_helper->current_app_page== "register_code"){
	
}

if( $passThis->web_helper->current_app_page== "register_complete"){
	
}


?>