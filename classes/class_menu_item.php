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

class cMenuItem{
	
	var $linkurl	= "";
	var $linktext	= "";
	var $linkimage	= "";
	var $linkgroup	= "";
	var $linkactive	= "";
	
	function __construct($strURL,$strText, $strImage, $strGroup, $intActive){
		
		$this->linkurl			= $strURL;
		$this->linktext			= $strText;
		$this->linkimage		= $strImage;
		$this->linkgroup		= $strGroup;
		$this->linkactive		= $intActive;
	}
	
}
?>