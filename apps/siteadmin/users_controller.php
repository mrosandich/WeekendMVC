<?php

function ShowUserList($passThis){
		$flist = new cFormList($passThis->db, $passThis->config, $passThis->user, $passThis->current_app_name, $passThis->current_app_page);
		$flist->setTableName("users");
		$flist->setPKName("id");
		$flist->setPrefixWrapHTML("<table id=\"userTable\">");
		$flist->addListCol('name_first', 'First Name');
		$flist->addListCol('name_last', 'Last Name');
		$flist->addListCol('email', 'Email');
		$flist->addListCol('username', 'User Name');
		$flist->addListCol('last_login_date', 'Last Login', array("formatDate"=>"m/d/Y @ h:i:s a") );
		$flist->link_use = 1; //left side
		$flist->setActionlink("", "", 'edit');
		
		$passThis->page_content_view = $flist->renderListView();
}

function SaveUser($passThis){
	$Result = $passThis->siteForms->doDatabaseUpdate();
	//$this->siteForms->clearBind();
	$passThis->app_user_message = "User Profile updated";
	$passThis->app_user_message_type = "good";
}

function LoadUser($passThis){
	$passThis->siteForms->setTableName('users');
	$passThis->siteForms->setPKName('id');
	$passThis->siteForms->setTableAction('edit');
	
	$passThis->siteForms->createElement('id',true,'id',"", "Numeric");
	$passThis->siteForms->boundElements['id']->form_web_type = "hidden";

	$passThis->siteForms->createElement('username',true,'',"", "UserName");
	$passThis->siteForms->boundElements['username']->form_web_type = "text";
	$passThis->siteForms->boundElements['username']->form_label	 = "username";

	$passThis->siteForms->createElement('name_first',true,'',"", "AlphaNumeric");
	$passThis->siteForms->boundElements['name_first']->form_web_type = "text";
	$passThis->siteForms->boundElements['name_first']->form_label	 = "First Name";
	
	$passThis->siteForms->createElement('name_last',true,'',"", "AlphaNumeric");
	$passThis->siteForms->boundElements['name_last']->form_web_type = "text";
	$passThis->siteForms->boundElements['name_last']->form_label = "Last Name";

	
	$passThis->siteForms->createElement('email',true,'',"", "Email");
	$passThis->siteForms->boundElements['email']->addValidation('notEmpty',array());
	$passThis->siteForms->boundElements['email']->addValidation('isEmail',array());
	$passThis->siteForms->boundElements['email']->form_web_type = "text";
	$passThis->siteForms->boundElements['email']->form_label = "Email";
	
	$passThis->siteForms->createElement('spacer1',false,'',"", "");
	$passThis->siteForms->boundElements['spacer1']->form_web_type = "html_space_group";
	
	
	$passThis->siteForms->createElement('is_activated',true,'',"", "AlphaNumeric");
	$passThis->siteForms->boundElements['is_activated']->form_web_type = "checkbox";
	$passThis->siteForms->boundElements['is_activated']->form_label = "is activated";
	$passThis->siteForms->boundElements['is_activated']->form_value_default = "1";
	
	$passThis->siteForms->createElement('approval_state',true,'',"", "AlphaNumeric");
	$passThis->siteForms->boundElements['approval_state']->form_web_type = "select";
	$passThis->siteForms->boundElements['approval_state']->form_label = "Approval State";
	$passThis->siteForms->boundElements['approval_state']->form_cntrl_values = array(0=>"Not Approved", 1 =>"Approved", 2=>"Denied", 3=>"Suspended");
	
	
	$passThis->siteForms->createElement('is_locked',true,'',"", "AlphaNumeric");
	$passThis->siteForms->boundElements['is_locked']->form_web_type = "checkbox";
	$passThis->siteForms->boundElements['is_locked']->form_label = "is locked";
	$passThis->siteForms->boundElements['is_locked']->form_value_default = "1";
	
	$passThis->siteForms->createElement('locked_message',true,'',"", "AlphaNumeric");
	$passThis->siteForms->boundElements['locked_message']->form_web_type = "textarea";
	$passThis->siteForms->boundElements['locked_message']->form_label = "Locked Message";
	
	
	
	$passThis->siteForms->createElement('save_btn',false);
	$passThis->siteForms->boundElements['save_btn']->form_web_type = "submit";
	$passThis->siteForms->boundElements['save_btn']->form_label	 = "";
	$passThis->siteForms->boundElements['save_btn']->form_value = "Update user informtion";
	$passThis->siteForms->boundElements['save_btn']->bind_to_query = false;
	
	if($passThis->web_helper->didPost() == true){
		$passThis->siteForms->dbLoadSelectTableSingleRow($passThis->web_helper->getFormValue("id","","Numeric")); //load the data into the form
	}else{
		$passThis->siteForms->dbLoadSelectTableSingleRow($passThis->web_helper->getQueryValue("id","","Numeric")); //load the data into the form
	}
	
	$passThis->siteForms->setCheckRepost();
	
}

function ShowUserForm($passThis){
	$passThis->page_content_view = $passThis->renderForm();
}
?>