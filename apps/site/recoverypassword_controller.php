<?php
$this->siteForms->setTableName('users');
$this->siteForms->setPKName('id');


$this->siteForms->createElement('email',true,'',"", "Email");
$this->siteForms->boundElements['email']->addValidation('notEmpty',array());
$this->siteForms->boundElements['email']->addValidation('isEmail',array());
$this->siteForms->boundElements['email']->form_web_type = "text";
$this->siteForms->boundElements['email']->form_label = "Email";
?>