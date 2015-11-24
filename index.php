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
define('SYSLOADED', "Yes Loaded");
session_start();
include("config.php");

include("classes/class_web.php");
include("classes/class_db.php");
include("classes/class_user.php");
include("classes/class_email.php");

include("classes/class_menu_item.php");
include("classes/class_forms.php");
include("classes/class_form_element.php");
include("classes/class_formlist.php");

include("classes/class_app.php");
include("classes/class_page.php");
include("classes/class_pageflow.php");

?>