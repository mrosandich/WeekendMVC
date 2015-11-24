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
function getEmailContent($strArray){
	
	$retString 	= "";
	$retString .= "<html>";
	$retString .= "<head>";
	$retString .= "<title>Password Recovery</title>";
	$retString .= "</head>";
	$retString .= "<body>";
	$retString .= "Hello " . $strArray['fullname'] . ",<br />";
	$retString .= "To reset your password  on " . $strArray['sitename'] . " you will need to follow the link in this email and set a new password"; 
	$retString .= "<br />";
	$retString .= "<table width=\"400\">";
	$retString .= "<tr><td style=\"padding:10px;border:1px solid #000;text-align:center;font-size:18\">" . $strArray['reset_guid'] . "</td>";
	$retString .= "<td style=\"padding:10px;border:1px solid #000;text-align:center;font-size:18\">Reset Code</td></tr>";
	
	$retString .= "<tr><td style=\"padding:10px;border:1px solid #000;text-align:center;font-size:18\">" . $strArray['username'] . "</td>";
	$retString .= "<td style=\"padding:10px;border:1px solid #000;text-align:center;font-size:18\">Username</td></tr>";
	
	$retString .= "</table><br /><br />";
	$retString .= "reset account link " . $strArray['actlink'] . "<br />";
	$retString .= "</body>";
	$retString .= "</html>";
	
	return $retString;
}//end function getEmailContent
?>