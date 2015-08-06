<?php
$this->siteForms->setTableName('users');
$this->siteForms->setPKName('id');
$this->siteForms->createElement('id',true,'id',"", "Numeric");

$this->siteForms->createElement('recovery_q1',true,'',"", "AlphaNumeric");
$this->siteForms->boundElements['recovery_q1']->addValidation('notEmpty',array());
$this->siteForms->createElement('recover_an1_enc',true,'',"", "AlphaNumeric");
$this->siteForms->boundElements['recover_an1_enc']->addValidation('notEmpty',array());


$this->siteForms->createElement('recovery_q2',true,'',"", "AlphaNumeric");
$this->siteForms->boundElements['recovery_q2']->addValidation('notEmpty',array());
$this->siteForms->createElement('recover_an2_enc',true,'',"", "AlphaNumeric");
$this->siteForms->boundElements['recover_an2_enc']->addValidation('notEmpty',array());


$this->siteForms->createElement('recovery_q3',true,'',"", "AlphaNumeric");
$this->siteForms->boundElements['recovery_q3']->addValidation('notEmpty',array());
$this->siteForms->createElement('recover_an3_enc',true,'',"", "AlphaNumeric");
$this->siteForms->boundElements['recover_an3_enc']->addValidation('notEmpty',array());


$this->siteForms->createElement('save_btn',false);

if( $this->web_helper->current_app_page== "security_update" ){
	$this->siteForms->setTableAction('update');
	
	$GoodData = $this->siteForms->bindAndFilter();
	
	if( $GoodData == true ){
		$Result = $this->siteForms->doDatabaseUpdate();
		$this->app_user_message = "Security Updated";
		$this->app_user_message_type = "good";
		$this->user->getActiveUserFromDB();
	}else{
		$this->app_user_message = $this->siteForms->HTMLErrors[$this->web_helper->current_app_page];
		$this->app_user_message_type = "warning";
	}
	
	$this->web_helper->current_app_page= "security";
}

if( $this->web_helper->current_app_page== "security" ){
	$this->siteForms->setTableAction('edit');
	//set data elements / columns used.
	$this->siteForms->boundElements['id']->form_web_type = "hidden";
	

	$this->siteForms->boundElements['recovery_q1']->form_web_type = "select";
	$this->siteForms->boundElements['recovery_q1']->form_label = "Recovery Question 1";
	$this->siteForms->boundElements['recover_an1_enc']->form_web_type = "text";
	$this->siteForms->boundElements['recover_an1_enc']->form_label = "Answer to question 1";


	$this->siteForms->boundElements['recovery_q2']->form_web_type = "select";
	$this->siteForms->boundElements['recovery_q2']->form_label = "Recovery Question 2";
	$this->siteForms->boundElements['recover_an2_enc']->form_web_type = "text";
	$this->siteForms->boundElements['recover_an2_enc']->form_label = "Answer to question 2";

	$this->siteForms->boundElements['recovery_q3']->form_web_type = "select";
	$this->siteForms->boundElements['recovery_q3']->form_label = "Recovery Question 3";
	$this->siteForms->boundElements['recover_an3_enc']->form_web_type = "text";
	$this->siteForms->boundElements['recover_an3_enc']->form_label = "Answer to question 3";


	$this->siteForms->boundElements['recovery_q1']->form_cntrl_values = array("what's you pets name?"=>"what's you pets name?","What city were you born in"=>"What city were you born in?");
	$this->siteForms->boundElements['recovery_q2']->form_cntrl_values = array("what's you mom's name?"=>"what's you mom's name?","What city is your favorite"=>"What city is your favorite?");
	$this->siteForms->boundElements['recovery_q3']->form_cntrl_values = array("what's you dad's name?"=>"what's you dad's name?","What is your favorite color"=>"What is your favorite color?");
	
	
	$this->siteForms->boundElements['save_btn']->form_web_type = "submit";
	$this->siteForms->boundElements['save_btn']->form_label	 = "";
	$this->siteForms->boundElements['save_btn']->form_value = "Save";
	$this->siteForms->dbLoadSelectTableSingleRow($this->user->user_id); //load the data into the form
	
	//rendering the form, is call in the view
}


?>