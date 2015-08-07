<?php
if( $this->web_helper->current_app_page== "register" || $this->web_helper->current_app_page == "register_do" ){
	$this->siteForms->setTableName('users');
	$this->siteForms->setPKName('id');

	$this->siteForms->createElement('username',true,'',"", "UserName");
	$this->siteForms->boundElements['username']->addValidation('valueNotInDBTable',array('table'=>'users','col'=>'username'));
	$this->siteForms->boundElements['username']->addValidation('notEmpty',array());
	$this->siteForms->boundElements['username']->db = $this->db;
	$this->siteForms->boundElements['username']->addValidation('isValidUserName',array('userlen' => $this->config['security_user_allowed_name_min_length'] , 'userchars' => $this->config['security_user_allowed_name_characters']) );
												

	$this->siteForms->createElement('password',true,'',"", "Password");
	$this->siteForms->boundElements['password']->auto_populate_db = false;
	$this->siteForms->boundElements['password']->auto_populate_post = false;
	$this->siteForms->boundElements['password']->addValidation('notEmpty',array());
	$this->siteForms->boundElements['password']->addValidation('isValidPassword',
												array(
													'len' => $this->config['security_password_min_length'] , 
													'cntup' => $this->config['security_password_required_uppercase'], 
													'cntlw' => $this->config['security_password_required_lowercase'], 
													'cntspc' => $this->config['security_password_required_specialcharacters'], 
													'cntnum' => $this->config['security_password_required_numbers'], 
													'spchar' => $this->config['security_password_specialcharacters']
												)
												);



	$this->siteForms->createElement('pass2',false,'',"", "Password");
	$this->siteForms->boundElements['pass2']->auto_populate_db = false;
	$this->siteForms->boundElements['pass2']->auto_populate_post = false;
	$this->siteForms->boundElements['pass2']->addValidation('otherFormElementSameValue',array('formname'=>'password','formlabel'=>'Password'));


	$this->siteForms->createElement('name_first',true,'',"", "AlphaNumeric");
	$this->siteForms->createElement('name_last',true,'',"", "AlphaNumeric");

	$this->siteForms->createElement('email',true,'',"", "Email");
	$this->siteForms->boundElements['email']->addValidation('notEmpty',array());
	$this->siteForms->boundElements['email']->addValidation('isEmail',array());
	$this->siteForms->boundElements['email']->form_web_type = "text";
	$this->siteForms->boundElements['email']->form_label = "Email";



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

	//if config recaptcha enable lets show the form.
	if($this->config['app_site_register_require_recaptcha'] == 1 ){
		
	}
	
	//if config enterprise id enable lets show the form element.
	if($this->config['app_site_register_require_enterprise_code'] == 1 ){
		$this->siteForms->createElement('enterprise_code',true,'',"", "AlphaNumeric");
		$this->siteForms->boundElements['enterprise_code']->addValidation('valueIsInDBTable',array('table'=>'enterprise','col'=>'enterprise_invite_code'));
		$this->siteForms->boundElements['enterprise_code']->addValidation('notEmpty',array());
		$this->siteForms->boundElements['enterprise_code']->db = $this->db;
		$this->siteForms->boundElements['enterprise_code']->auto_populate_db = false;
		$this->siteForms->boundElements['enterprise_code']->form_web_type = "text";
		$this->siteForms->boundElements['enterprise_code']->form_label	 = "Enterprise Code";
	}
	
	$this->siteForms->createElement('save_btn',false);
}

if( $this->web_helper->current_app_page== "register_do" ){
	$this->siteForms->setTableAction('new');
	
	$GoodData = $this->siteForms->bindAndFilter();
	
	//if passwords match and is not blank then send to sql with value of password MD5 hashed.
	if( $this->siteForms->boundElements['password']->form_value  == $this->siteForms->boundElements['pass2']->form_value){
		$this->siteForms->boundElements['password']->form_value = MD5($this->siteForms->boundElements['password']->form_value);
	}else{
		$this->siteForms->HTMLErrors[$this->web_helper->current_app_page] .= "Password did not match.";
		$GoodData = false;
	}
	
	
	if( $GoodData == true ){
		
		
		
		$Result = $this->siteForms->doSQLInsert();
		$this->app_user_message = "Account Created";
		$this->app_user_message_type = "good";
		$this->user->getActiveUserFromDB();
		$this->web_helper->current_app_page= "register_complete";
	}else{
		$this->app_user_message = $this->siteForms->HTMLErrors[$this->web_helper->current_app_page];
		$this->app_user_message_type = "warning";
	}
	
	
}

if( $this->web_helper->current_app_page== "register" || $this->web_helper->current_app_page == "register_do"){
	$this->siteForms->setTableAction('edit');
	//set data elements / columns used.

	
	$this->siteForms->boundElements['username']->form_web_type = "text";
	$this->siteForms->boundElements['username']->form_label	 = "username";

	$this->siteForms->boundElements['password']->form_web_type = "password";
	$this->siteForms->boundElements['password']->form_label	 = "Password";

	//set html parts for this col
	$this->siteForms->boundElements['pass2']->form_web_type = "password";
	$this->siteForms->boundElements['pass2']->form_label	 = "Password";
	$this->siteForms->boundElements['pass2']->form_caption	 = "<br />Retype password to confirm.";
	
	//set html parts for this col
	$this->siteForms->boundElements['name_first']->form_web_type = "text";
	$this->siteForms->boundElements['name_first']->form_label	 = "First Name";
	
	$this->siteForms->boundElements['name_last']->form_web_type = "text";
	$this->siteForms->boundElements['name_last']->form_label = "Last Name";
		

	
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

}


?>