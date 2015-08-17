<?php

function ShowUserList($passThis){
		$flist = new cFormList($passThis->db, $passThis->config, $passThis->user, $passThis->current_app_name, $passThis->current_app_page);
		$flist->setTableName("users");
		$flist->setPKName("id");
		$flist->setPrefixWrapHTML("<table id=\"userTable\">");
		$flist->addListCol('name_first', 'First Name');
		$flist->addListCol('name_last', 'Last Name');
		$flist->addListCol('email', 'Email');
		$flist->addListCol('last_login_date', 'Last Login', array("formatDate"=>"m/d/Y @ h:i:s a") );
		$flist->link_use = 1; //left side
		$flist->setActionlink("", "", 'edit');
		
		$passThis->page_content_view = $flist->renderListView();
	}

?>