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
	$retString .= "<html>\n";
	$retString .= "<head>\n";
	$retString .= "<title>Registration Email</title>\n";
	$retString .= "</head>\n";
	$retString .= "<body>\n";
	$retString .= "Hello " . $strArray['fullname'] . ",<br />\n";
	$retString .= "To register on " . $strArray['sitename'] . " you will need to activate your account. Please click the link below or enter"; 
	$retString .= "the code on the page you were directed to after you entered your account information.<br />\n";
	$retString .= "<br />\n";
	$retString .= "<table width=\"400\">\n";
	$retString .= "<tr>\n<td style=\"padding:10px;border:1px solid #000;text-align:center;font-size:18\">" . $strArray['actcode'] . "</td>\n";
	$retString .= "<td style=\"padding:10px;border:1px solid #000;text-align:center;font-size:18\">Activation Code</td>\n</tr>\n";
	$retString .= "</table>\n<br />\n<br>\n";
	$retString .= "activation link " . $strArray['actlink'] . "<br />\n";
	$retString .= "</body>\n";
	$retString .= "</html>\n";
	
	return $retString;
}//end function getEmailContent