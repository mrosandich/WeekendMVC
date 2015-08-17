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

class cFormList{
	
	var $db;
	var $config;
	var $user;
	
	//List view vars
	var $prefix_item_html		= "<td>";
	var $postfix_item_html		= "</td>";
	var $prefix_itemheader_html	= "<th>";
	var $postfix_itemheader_html="</th>";
	var $prefix_row_html		= "<tr>";
	var $postfix_row_html		= "</tr>";
	var $prefix_wrap_html		= "<table>";
	var $postfix_wrap_html		= "</table>";
	
	var $form_list_items		= array();
	var $table_name				= "";
	var $table_pk_col			= "";
	var $table_action			= "";
	
	var $app_name				= "";
	var $app_page				= "";
	
	
	var $link_app_name			= "";
	var $link_app_page			= "";
	var $link_text				= "";
	var $link_use				= 0;	//0=None,  1=Left side, 2=Right Side

	function __construct($cDBObject,$SiteConfig,$cUser,$AppName, $AppPage){
			$this->db 					= $cDBObject;
			$this->config 				= $SiteConfig;
			$this->user 				= $cUser;
			$this->app_name				= $AppName;
			$this->app_page				= $AppPage;
	}
	
	
	//-----------------------------------------------------------------------------------------------
	//											SETUP DB TABLE		
	//-----------------------------------------------------------------------------------------------
	function setTableName($TableName){
		$this->table_name = $TableName;
	}
	
	function setPKName($PKName){
		$this->table_pk_col = $PKName;
	}
	
	function setTableAction($TableAction){
		$this->table_action = $TableAction;
	}
	
	
	
	//-----------------------------------------------------------------------------------------------
	//											SETUP HTML OUTPUT FORMAT		
	//-----------------------------------------------------------------------------------------------
	function setPrefixItemHTML($InHTML){
		$this->prefix_item_html	= $InHTML;
	}
	function setPostfixItemHTML($InHTML){
		$this->postfix_item_html= $InHTML;
	}
	
	function setPrefixItemHeaderHTML($InHTML){
		$this->prefix_itemheader_html	= $InHTML;
	}
	function setPostfixItemHeaderHTML($InHTML){
		$this->postfix_itemheader_html= $InHTML;
	}
	function setPrefixRowHTML($InHTML){
		$this->prefix_row_html	= $InHTML;
	}
	function setPostfixRowHTML($InHTML){
		$this->postfix_row_html = $InHTML;
	}
	function setPrefixWrapHTML($InHTML){
		$this->prefix_wrap_html	= $InHTML;
	}
	function setPostfixWrapHTML($InHTML){
		$this->postfix_wrap_html = $InHTML;
	}
	
	function setActionlink($AppName="", $PageName="", $LinkText){
		
		$this->link_app_name = $this->app_name;
		if( $AppName != "" ){
			$this->link_app_name = $AppName;
		}
		
		$this->link_app_page = $this->app_page;
		if( $PageName != "" ){
			$this->link_app_page = $PageName;
		}

		$this->link_text = $LinkText;
		
	}
	
	
	function addListCol($ColName, $Label="", $arrayFormat=array()){
		$this->form_list_items[] = new cFormListCol($ColName,$Label,$arrayFormat);
	}
	
	function renderListView(){
		
		$ReturnString = "";
		$ColList = $this->getColList();
		$Statement = "select $ColList from {$this->table_name}";
		$this->db->sql($Statement);
		$this->db->db_fetch_how = PDO::FETCH_ASSOC;
		$result = $this->db->execute();
		
		//gen header row
		
		$ReturnString .= $this->prefix_row_html . "\n";
		if($this->link_use == 1 ){
			$ReturnString .= $this->prefix_itemheader_html . "Action" . $this->postfix_itemheader_html;
		}
		foreach ($this->form_list_items as $arrayidex => $itemobj ){	
			$ReturnString .= $this->prefix_itemheader_html . $itemobj->col_label . $this->postfix_itemheader_html;
		}//end foreach litst item
		if($this->link_use == 2 ){
			$ReturnString .= $this->prefix_itemheader_html . "Action" . $this->postfix_itemheader_html;
		}
		$ReturnString .= $this->postfix_row_html . "\n";
		
		
		
		
		//gen each row
		if( $this->db->getResultCount() > 0 ){
			foreach($result as $db_id => $db_rowval ){
				$ReturnString .= $this->prefix_row_html . "\n";
				if($this->link_use == 1 ){
					$ReturnString .= $this->prefix_item_html . $this->getURL($db_rowval[$this->table_pk_col]) . $this->postfix_item_html;
				}
				foreach ($this->form_list_items as $arrayidex => $itemobj ){	
				
					//since the columns are generated per row we simply pass the DB value to the getDisplayHTML instead of setting it as part of the object
					$ReturnString .= $this->prefix_item_html . $itemobj->getDisplayHTML($db_rowval[$itemobj->col_name]) . $this->postfix_item_html;
				}//end foreach litst item
				if($this->link_use == 2 ){
					$ReturnString .= $this->prefix_item_html .$this->getURL($db_rowval[$this->table_pk_col]) . $this->postfix_item_html;
				}
				$ReturnString .= $this->postfix_row_html . "\n";
				
			}//end foreach result
		}
		
		return $this->prefix_wrap_html . $ReturnString . $this->postfix_wrap_html;
	}
	
	
	function getURL($InID){
		$ReturnString = "<a href=\"/index.php?app_name={$this->link_app_name}&app_page={$this->link_app_page}&app_action=view&{$this->table_pk_col}=$InID\">{$this->link_text}</a>\n";
		return $ReturnString;
	}
	
	
	function getColList(){
		$RetString = "";
		foreach( $this->form_list_items as $key => $itemobj ){
			$RetString .= $itemobj->col_name . ", ";
		}
		
		//if the PrimaryKey is not in the list then add it.
		if( strpos($RetString,$this->table_pk_col) ===false ){
			$RetString .= $this->table_pk_col . ", ";
		}
		$RetString = rtrim($RetString, ", ");
		return $RetString;
	}//end function getColList
	
	
	
		
	
	
	
}//end class cFormList


class cFormListCol{
	var $col_name		= "";
	var $col_label		= "";
	var $col_format		= array();  //FunctionName => params
	var $col_row_offset = 0; // lets you put one record per multiple rows
	
	function __construct($ColName,$ColLabel="",$ColFormat=array(), $RowLayout=1){
		$this->col_name 		= $ColName;
		$this->col_label 		= $ColLabel;
		$this->col_format 		= $ColFormat;
		$this->col_row_offset 	= $RowLayout;
	}
	
	
	function getDisplayHTML($InValue){
		$ReturnString = $InValue;
		if( count( $this->col_format ) > 0 ){
			foreach( $this->col_format as $FunName => $ParamaValues){
				if( $FunName == "formatDate" ){
					$ReturnString = $this->formatDate($ReturnString, $ParamaValues);
				}
			}
		}
		return $ReturnString;
	}
	
	//-----------------------------------------------------------------------------------------------
	//											FORMATTING FUNCTIONS		
	//-----------------------------------------------------------------------------------------------
	function formatDate($InValue,$InFormat){
		$ReturnString = $InValue;
		if( $ReturnString == "" || $ReturnString =="0000-00-00 00:00:00" ){
			$ReturnString="no date";
		}else{
			$ReturnString = date($InFormat,strtotime($InValue));
		}
		
		return $ReturnString;
	}
} //end class cFormListCol









?>