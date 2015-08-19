<?php
/*
	Author	: Mell Rosandich
	Date	: 6/29/2015
	email	: mell@ourace.com
	website : www.ourace.com
	
	Copyright 2015 Mell Rosandich

	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at

		http://www.apache.org/licenses/LICENSE-2.0

	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.

*/
defined('SYSLOADED') OR die('No direct access allowed.');

class cForms{
	
	var $db;
	var $config;
	var $user;
	
	//extra classes
	var $web_helper;
	

	var $table_name			= ""; //name of the table this is going to bind to.
	var $table_pk_col		= ""; //The primary key col name used for updates, and deletes
	var $table_action		= ""; //select,update,delete,copy(insert same data with new PK)
	var $sql_where_filter	= array();
	
	var $boundElements			= array(); //all the db elements keyed of ColName
	
	//this describes how the elements of the for will be displayed. Could be a TR row, or a span, or divs. You can override each element as well.
	//replaces items within the string on output [id],[labelname],[formelement]
	var $HTMLOutFormat		= "<fieldset id=\"fs_[id]\">\n\t<label>[labelname]</label>\n\t[formelement]\n[formcaption]<span class=\"errortext\">[errormsg]</span></fieldset>"; 
	var $HTMLFormStart		= ""; //this can be overrided. it is set in the app_load of the controller which calls page->getFormActionHTML
	var $HTMLFormEnd		= "</form>";
	var $HTMLErrors			= array();
	
	
	
	
	function __construct($cDBObject,$SiteConfig,$cUser){
		$this->db 					= $cDBObject;
		$this->config 				= $SiteConfig;
		$this->user 				= $cUser;
		$this->web_helper 			= new cWeb();
	}
	
	
	function setTableName($TableName){
		$this->table_name = $TableName;
	}
	
	function setPKName($PKName){
		$this->table_pk_col = $PKName;
	}
	
	function setTableAction($TableAction){
		$this->table_action = $TableAction;
	}
	
	
	
	function createElement($ColName,$IsBound, $PostName="", $Roles="", $Filters=""){
		//If just ColName is provide it will assume post var name is the same.
		//If you want to add roles per field, you can pass an array of the roles required to have access to that field.
		//samples  
		//	$cObject->bindDBPost('name_first');
		//	$cObject->bindDBPost('name_first','fname');
		//	$cObject->bindDBPost('username','uname',array('site_profile_adv_edit') );  //let only users with advanced profile edit role edit their user name.
		//	$cObject->bindDBPost('password','pass','',array('MD5') );  //Add filter or hook to password field that will MD5 hash it.
	
		$this->boundElements[$ColName] = new cFormElement($ColName,$IsBound, $PostName, $Roles, $Filters);
	}
	
	
	
	function clearBind(){
		$this->boundElements = array();
	}
	
	
	
	function setRawPostBind(){
		//Go through each element that was created and find the corresponding $_POST value. Set that value to the object
		foreach ($this->boundElements as $key => $val ){
			if( $val->bind_to_post == true ){
				$val->form_value = $this->web_helper->getFormValue($val->post_name, $val->form_value_default, $val->post_filter_type);
			}
		}
	}
	
	function setRawQueryBind(){
		//Go through each element that was created and find the corresponding $_POST value. Set that value to the object
		foreach ($this->boundElements as $key => $val ){
			if( $val->bind_to_query == true ){
				if($val->query_key ==""){
					$val->form_value = $this->web_helper->getQueryValue($val->post_name, $val->form_value_default, $val->post_filter_type);
				}else{
					$val->form_value = $this->web_helper->getQueryValue($val->query_key, $val->form_value_default, $val->post_filter_type);
				
				}
			
			}
		}
	}
	
	function setCheckRepost(){
		if( $this->web_helper->didPost() ){
			 $this->setRawPostBind();
			
		}
	}
	
	
	

	
	//-----------------------------------------------------------------------------------------------
	//											Validation.		
	//-----------------------------------------------------------------------------------------------
	
	function bindAndFilter(){
		$this->setRawPostBind();
		if( $_SESSION['form_model_uid'] != $this->web_helper->getFormValue("form_model_uid","","AlphaNumeric")){
			//This means the user is trying to submit a form that already has been submitted.
			$this->HTMLErrors[$this->web_helper->current_app_page] = "The form Has Expired or has been already submitted (Don't refresh your browser).";			
		}
		
		foreach ($this->boundElements as $key => $val ){
			$val->selfValidate();
			$selfErrors = $val->HTMLErrors;
			if( count($selfErrors) > 0 ){
				$this->HTMLErrors[$val->col_name] = $val->HTMLErrors; //this is an array of errors for that column
			}
		}
		
		if(count($this->HTMLErrors) > 0){
			if( $this->HTMLErrors[$this->web_helper->current_app_page] == "" ){
				$this->HTMLErrors[$this->web_helper->current_app_page] = "Please review the error below and correct them before submitting";
			}else{
				$this->HTMLErrors[$this->web_helper->current_app_page] .= "Please review the error below and correct them before submitting.";
			}
			return false;
		}else{
			return true;
		}
	}
	
	
	
	//-----------------------------------------------------------------------------------------------
	//											Rendering HTML.		
	//-----------------------------------------------------------------------------------------------
	
	function renderForm(){
		$FormHTMLOut = "";
		foreach ($this->boundElements as $key => $val ){
			$FormHTMLOut .= $this->renderFormItem($key) . "\n";
		}
		return $this->HTMLFormStart . $FormHTMLOut . $this->HTMLFormEnd;
	}
	
	function renderFormItem($KeyName){
		if(DEBUG_ECHO == true){
			if( !array_key_exists($KeyName,$this->boundElements) ){
				echo "Trying to render a form element that was setup correctly - Element doesn't exists:" . $KeyName ;
			}
			if( !method_exists($this->boundElements[$KeyName],'renderElement') ){
				echo "Trying to render a form element that was setup correctly - missing renderElement:" . $KeyName ;
			}
		}
		$form_element_html = $this->boundElements[$KeyName]->renderElement();
			if( $this->boundElements[$KeyName]->is_visible ){
				if( $this->boundElements[$KeyName]->form_web_type ==  "html_space_group" ){
					$current_element =  $this->boundElements[$KeyName]->html_space_group;
				}else{
					$current_element = str_replace('[formelement]',$form_element_html,$this->HTMLOutFormat);
					$current_element = str_replace('[labelname]',$this->boundElements[$KeyName]->form_label,$current_element);
					$current_element = str_replace('[id]',$this->boundElements[$KeyName]->post_name,$current_element);
					$current_element = str_replace('[formcaption]',$this->boundElements[$KeyName]->form_caption,$current_element);
					$current_element = str_replace('[errormsg]',$this->boundElements[$KeyName]->getErrorText(),$current_element);
				}
			}else{
				$current_element=$form_element_html;
			}
			
			return $current_element;
	}
	
	
	
	
	
	
	//-----------------------------------------------------------------------------------------------
	//											SQL Interactions.		
	//-----------------------------------------------------------------------------------------------
	
	
	function dbLoadSelectTableSingleRow($PKValue){
		//populate all the values in the form
		$Statement = "select * from {$this->table_name} where {$this->table_pk_col}='$PKValue' limit 1";
		$this->db->sql($Statement);
		$result = $this->db->execute();
		if( $this->db->getResultCount() > 0 ){
			foreach ($this->boundElements as $key => $val ){
				if( $val->is_bound == true || $val->auto_populate_db == true){
					foreach($result[0] as $item_key => $item_val ){
						if( $key == $item_key ){
							$val->form_value =  $item_val;
						}
					}
				}//end if bound true
			}
		}
	}
	
	
	function doDatabaseUpdate(){
		$ColValList = "";
		foreach ($this->boundElements as $key => $val ){
			//check roles
			if( $val->col_name !=  $this->table_pk_col && $val->is_bound == true ){
				$ColValList .= $val->col_name . "=:" . $val->col_name . ",";
			}
			
		}
		$ColValList 	= rtrim($ColValList,",");
		$SQLStatment = "update " . $this->table_name. " set " . $ColValList . " where " . $this->table_pk_col . "=:" . $this->table_pk_col;
		$this->db->sql($SQLStatment);
		
		foreach ($this->boundElements as $key => $val ){
			//check roles
			if( $val->is_bound == true ){
				$this->db->addParam(":" . $val->col_name ,$val->form_value);
			}
		}
		return $this->db->execute();
	}
	
	function doSQLInsert(){
		
		$ColList = "";
		$ColValues = "";
		foreach ($this->boundElements as $key => $val ){
			//check roles
			if( $val->col_name !=  $this->table_pk_col && $val->is_bound == true ){
				$ColValues 	.= ":" . $val->col_name . ",";
				$ColList 	.= $val->col_name . ",";
			}
		}
		$ColValues 	= rtrim($ColValues,",");
		$ColList 	= rtrim($ColList,",");
		$SQLStatment =  "insert into " . $this->table_name. "(" . $ColList . ")values(" . $ColValues . ")";
		$this->db->sql($SQLStatment);
		//echo $SQLStatment;
		
		foreach ($this->boundElements as $key => $val ){
			//check roles
			if( $val->col_name !=  $this->table_pk_col &&$val->is_bound == true ){
				$this->db->addParam(":" . $val->col_name ,$val->form_value);
				//echo "<br />setting:  {$val->col_name} ={$val->form_value}";
			}
		}
		return $this->db->execute();
	}
	
}//cForms
?>