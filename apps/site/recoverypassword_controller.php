<?php

function CreateRecoveryForm($passThis){
	$passThis->siteForms->setTableName('users');
	$passThis->siteForms->setPKName('id');


	$passThis->siteForms->createElement('username',true,'',"", "UserName");
	$passThis->siteForms->boundElements['username']->addValidation('valueIsInDBTable',array('table'=>'users','col'=>'username'));
	$passThis->siteForms->boundElements['username']->addValidation('notEmpty',array());
	$passThis->siteForms->boundElements['username']->db = $passThis->db;
	$passThis->siteForms->boundElements['username']->addValidation('isValidUserName',array('userlen' => $passThis->config['security_user_allowed_name_min_length'] , 'userchars' => $passThis->config['security_user_allowed_name_characters']) );
	$passThis->siteForms->boundElements['username']->form_web_type = "text";
	$passThis->siteForms->boundElements['username']->form_label	 = "Username";
	
	$passThis->siteForms->createElement('email',true,'',"", "Email");
	$passThis->siteForms->boundElements['email']->addValidation('notEmpty',array());
	$passThis->siteForms->boundElements['email']->addValidation('isEmail',array());
	$passThis->siteForms->boundElements['email']->form_web_type = "text";
	$passThis->siteForms->boundElements['email']->form_label = "Email";

	$passThis->siteForms->createElement('save_btn',false);
	$passThis->siteForms->boundElements['save_btn']->form_web_type = "submit";
	$passThis->siteForms->boundElements['save_btn']->form_label	 = "";
	$passThis->siteForms->boundElements['save_btn']->form_value = "Send Recovery Link";
	$passThis->siteForms->boundElements['save_btn']->bind_to_query = false;
}


function CheckRecoveryForm($passThis){

	if( $passThis->web_helper->didPost() ){
		$GoodData = $passThis->siteForms->bindAndFilter();
		if( $GoodData == true ){
			$passThis->db->sql("select * from users where username=:username and email=:email");
			$passThis->db->addParam(":username",$passThis->web_helper->getFormValue("username","","UserName"));
			$passThis->db->addParam(":email",$passThis->web_helper->getFormValue("email","","Email"));
			$result_set = $passThis->db->execute();
			
			if( $result_set[0]->email == $passThis->web_helper->getFormValue("email","","Email") ){	
				$reset_guid = strtoupper(md5(uniqid()));
				$passThis->db->sql("update users set reset_guid=:reset_guid where username=:username and email=:email");
				$passThis->db->addParam(":username",$passThis->web_helper->getFormValue("username","","UserName"));
				$passThis->db->addParam(":email",$passThis->web_helper->getFormValue("email","","Email"));
				$passThis->db->addParam(":reset_guid",$reset_guid);
				$temp_ret = $passThis->db->execute();
			
			
				$passThis->app_user_message = "We have sent  an email to <b>" . $passThis->siteForms->boundElements['email']->form_value . "</b> with password reset instructions.";
				$passThis->app_user_message_type = "warning";
				$emailService = new cEmail($passThis->db,$passThis->config,$passThis->user);
				$emailContent['fullname'] 	= $result_set[0]->name_first. " " . $result_set[0]->name_last;
				$emailContent['sitename'] 	= $passThis->config['website_name'];
				$emailContent['reset_guid'] 	= $reset_guid;
				$emailContent['actlink'] 	= $passThis->config['website_url_base'] . "/index.php?app_name=site&app_page=recoverypassword_link&app_action=view&rg=" . $reset_guid . "&un=" . $result_set[0]->username;
				$emailContent['username'] 	= $result_set[0]->username;
				$emailService->addToAddress($emailContent['fullname'], $result_set[0]->email);
				$emailService->sendEmail( $passThis->config['email_server_registration_email_subject'], $emailContent);
				return true;
			}
		}else{
			
		}
	}
	return false;
}


?>