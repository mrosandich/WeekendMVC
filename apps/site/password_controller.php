<?php
$this->siteForms->setTableName('users');
$this->siteForms->setPKName('id');
$this->siteForms->createElement('id',true,'id',"", "Numeric");

$this->siteForms->createElement('password',true,'',"", "Password");
$this->siteForms->boundElements['password']->auto_populate_db = false;
$this->siteForms->boundElements['password']->auto_populate_post = false;
$this->siteForms->boundElements['password']->addValidation('notEmpty',array());

$this->siteForms->createElement('pass2',false,'',"", "Password");
$this->siteForms->boundElements['pass2']->auto_populate_db = false;
$this->siteForms->boundElements['pass2']->auto_populate_post = false;

$this->siteForms->createElement('save_btn',false);

if( $this->web_helper->current_app_page== "password_update" ){
	$this->siteForms->setTableAction('update');
	
	$GoodData = $this->siteForms->bindAndFilter();
	
	//if passwords match and is not blank then send to sql with value of password MD5 hashed.
	if( $this->siteForms->boundElements['password']->form_value  == $this->siteForms->boundElements['pass2']->form_value){
		$this->siteForms->boundElements['password']->form_value = MD5($this->siteForms->boundElements['password']->form_value);
	}else{
		$this->siteForms->HTMLErrors[$this->web_helper->current_app_page] .= "Password did not match.";
		$GoodData = false;
	}


	
	if( $GoodData == true ){
		$Result = $this->siteForms->doDatabaseUpdate();
		$this->app_user_message = "Password Updated";
		$this->app_user_message_type = "good";
		$this->user->getActiveUserFromDB();
	}else{
		$this->app_user_message = $this->siteForms->HTMLErrors[$this->web_helper->current_app_page];
		$this->app_user_message_type = "warning";
	}
	
	$this->web_helper->current_app_page= "password";
}

if( $this->web_helper->current_app_page== "password" ){
	$this->siteForms->setTableAction('edit');
	//set data elements / columns used.
	$this->siteForms->boundElements['id']->form_web_type = "hidden";
	
	$this->siteForms->boundElements['password']->form_web_type = "password";
	$this->siteForms->boundElements['password']->form_label	 = "Password";

	//set html parts for this col
	$this->siteForms->boundElements['pass2']->form_web_type = "password";
	$this->siteForms->boundElements['pass2']->form_label	 = "Password";
	$this->siteForms->boundElements['pass2']->form_caption	 = "<br />Retype password to confirm.";
	

	$this->siteForms->boundElements['save_btn']->form_web_type = "submit";
	$this->siteForms->boundElements['save_btn']->form_label	 = "";
	$this->siteForms->boundElements['save_btn']->form_value = "Save";
	$this->siteForms->dbLoadSelectTableSingleRow($this->user->user_id); //load the data into the form
	
	//rendering the form, is call in the view
}


?>