<?php

function createResetCodeForm($passThis){
	$passThis->siteForms->setTableName('users');
	$passThis->siteForms->setPKName('id');

	$passThis->siteForms->createElement('username',true,'',"", "UserName");
	$passThis->siteForms->boundElements['username']->addValidation('notEmpty',array());
	$passThis->siteForms->boundElements['username']->addValidation('isValidUserName',array('userlen' => $passThis->config['security_user_allowed_name_min_length'] , 'userchars' => $passThis->config['security_user_allowed_name_characters']) );
	$passThis->siteForms->boundElements['username']->form_web_type = "text";
	$passThis->siteForms->boundElements['username']->form_label	 = "User Name";		
	$passThis->siteForms->boundElements['username']->query_key = "un";
	
	$passThis->siteForms->createElement('reset_guid',true,'',"", "AlphaNumeric");
	$passThis->siteForms->boundElements['reset_guid']->addValidation('notEmpty',array());
	$passThis->siteForms->boundElements['reset_guid']->form_web_type = "text";
	$passThis->siteForms->boundElements['reset_guid']->form_label	 = "Reset Code";	
	$passThis->siteForms->boundElements['reset_guid']->query_key = "rg";
	
	$passThis->siteForms->createElement('save_btn',false);
	$passThis->siteForms->boundElements['save_btn']->form_web_type = "submit";
	$passThis->siteForms->boundElements['save_btn']->form_label	 = "";
	$passThis->siteForms->boundElements['save_btn']->form_value = "Verify";
	$passThis->siteForms->boundElements['save_btn']->bind_to_query = false;
	$passThis->siteForms->setRawQueryBind();
}

function  createResetPassForm($passThis,$reset_guid="",$username=""){
	$passThis->siteForms->setTableName('users');
	$passThis->siteForms->setPKName('id');
	
	$passThis->siteForms->createElement('username',false,'username',"", "UserName");
	$passThis->siteForms->boundElements['username']->form_web_type = "hidden";
	
	$passThis->siteForms->createElement('reset_guid',false,'reset_guid',"", "AlphaNumeric");
	$passThis->siteForms->boundElements['reset_guid']->form_web_type = "hidden";
	
	$passThis->siteForms->createElement('passchange',false);
	$passThis->siteForms->boundElements['passchange']->form_web_type = "hidden";
	$passThis->siteForms->boundElements['passchange']->form_value='Yes';
	
	if($reset_guid!=""){
		//when calling this from the previous form ue the values we pass other wise use the report if there is an issue
		$passThis->siteForms->boundElements['reset_guid']->form_value=$reset_guid;
		$passThis->siteForms->boundElements['username']->form_value=$username;
	}
	
	

	$passThis->siteForms->createElement('password',true,'',"", "Password");
	$passThis->siteForms->boundElements['password']->auto_populate_db = false;
	$passThis->siteForms->boundElements['password']->auto_populate_post = false;
	$passThis->siteForms->boundElements['password']->addValidation('notEmpty',array());
	$passThis->siteForms->boundElements['password']->form_web_type = "password";
	$passThis->siteForms->boundElements['password']->form_label	 = "Password";
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
	$passThis->siteForms->boundElements['pass2']->form_web_type = "password";
	$passThis->siteForms->boundElements['pass2']->form_label	 = "Password";
	$passThis->siteForms->boundElements['pass2']->form_caption	 = "<br />Retype password to confirm.";
	

	$passThis->siteForms->createElement('save_btn',false);
	$passThis->siteForms->boundElements['save_btn']->form_web_type = "submit";
	$passThis->siteForms->boundElements['save_btn']->form_label	 = "";
	$passThis->siteForms->boundElements['save_btn']->form_value = "Save";
}
	
?>

