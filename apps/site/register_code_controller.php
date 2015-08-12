<?php

function ShowActCodeForm($passThis){
	$passThis->siteForms->setTableName('users');
	$passThis->siteForms->setPKName('id');

	$passThis->siteForms->createElement('username',true,'',"", "UserName");
	$passThis->siteForms->boundElements['username']->addValidation('notEmpty',array());
	$passThis->siteForms->boundElements['username']->addValidation('isValidUserName',array('userlen' => $passThis->config['security_user_allowed_name_min_length'] , 'userchars' => $passThis->config['security_user_allowed_name_characters']) );
	$passThis->siteForms->boundElements['username']->form_web_type = "text";
	$passThis->siteForms->boundElements['username']->form_label	 = "User Name";		
	$passThis->siteForms->boundElements['username']->query_key = "un";
	
	$passThis->siteForms->createElement('activation_guid',true,'',"", "AlphaNumeric");
	$passThis->siteForms->boundElements['activation_guid']->addValidation('notEmpty',array());
	$passThis->siteForms->boundElements['activation_guid']->form_web_type = "text";
	$passThis->siteForms->boundElements['activation_guid']->form_label	 = "Activation Code";	
	$passThis->siteForms->boundElements['activation_guid']->query_key = "ac";
	
	$passThis->siteForms->createElement('save_btn',false);
	$passThis->siteForms->boundElements['save_btn']->form_web_type = "submit";
	$passThis->siteForms->boundElements['save_btn']->form_label	 = "";
	$passThis->siteForms->boundElements['save_btn']->form_value = "Verify";
	$passThis->siteForms->boundElements['save_btn']->bind_to_query = false;
	$passThis->siteForms->setRawQueryBind();
	}

?>