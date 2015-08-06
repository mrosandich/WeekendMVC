<?php
$this->siteForms->setTableName('users');
$this->siteForms->setPKName('id');
$this->siteForms->createElement('id',true,'id',"", "Numeric");

$this->siteForms->createElement('username',false,'',"", "");

$this->siteForms->createElement('name_first',true,'',"", "AlphaNumeric");
$this->siteForms->createElement('name_last',true,'',"", "AlphaNumeric");

$this->siteForms->createElement('email',true,'',"", "Email");
$this->siteForms->boundElements['email']->addValidation('notEmpty',array());
$this->siteForms->boundElements['email']->addValidation('isEmail',array());
$this->siteForms->boundElements['email']->form_web_type = "text";
$this->siteForms->boundElements['email']->form_label = "Email";


$this->siteForms->createElement('save_btn',false);

if( $this->web_helper->current_app_page== "profile_update" ){
	$this->siteForms->setTableAction('update');
	$GoodData = $this->siteForms->bindAndFilter();
	
	
	//print_r($this->siteForms->HTMLErrors);
	
	if( $GoodData == true ){
		$Result = $this->siteForms->doDatabaseUpdate();
		//$this->siteForms->clearBind();
		$this->app_user_message = "Profile updated";
		$this->app_user_message_type = "good";
		$this->user->getActiveUserFromDB();
	}else{
		$this->app_user_message = $this->siteForms->HTMLErrors[$this->web_helper->current_app_page];
		$this->app_user_message_type = "warning";
	}
	
	$this->web_helper->current_app_page= "profile";
}

if( $this->web_helper->current_app_page== "profile" ){
	$this->siteForms->setTableAction('edit');
	//set data elements / columns used.
	$this->siteForms->boundElements['id']->form_web_type = "hidden";
	
	$this->siteForms->boundElements['username']->form_web_type = "static";
	$this->siteForms->boundElements['username']->form_label	 = "username";

	
	//set html parts for this col
	$this->siteForms->boundElements['name_first']->form_web_type = "text";
	$this->siteForms->boundElements['name_first']->form_label	 = "First Name";
	
	$this->siteForms->boundElements['name_last']->form_web_type = "text";
	$this->siteForms->boundElements['name_last']->form_label = "Last Name";
		

	$this->siteForms->boundElements['save_btn']->form_web_type = "submit";
	$this->siteForms->boundElements['save_btn']->form_label	 = "Click Save";
	$this->siteForms->boundElements['save_btn']->form_value = "Save";
	$this->siteForms->dbLoadSelectTableSingleRow($this->user->user_id); //load the data into the form
	$this->siteForms->setCheckRepost();
	//rendering the form, is call in the view
}


?>