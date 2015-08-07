<!DOCTYPE html>
<head>
<title><?php echo $this->pages_array[ $SelectedPageIndex ]->page_title;?></title>
<link rel="stylesheet" type="text/css" href="css/site_app.css">
<?php
	for($x=0;$x<count($this->pages_array[ $SelectedPageIndex ]->page_css);$x++){
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$this->pages_array[ $SelectedPageIndex ]->page_css[$x]}\">\n";
	}
	for($x=0;$x<count($this->pages_array[ $SelectedPageIndex ]->page_js);$x++){
		echo  "<script type=\"text/javascript\" src=\"{$this->pages_array[ $SelectedPageIndex ]->page_js[$x]}\"></script>\n";
	}
?>
</head>
<body>
<div id="page">
<div id="topmenu">
<?php 
	//sample using the html blob. see below in the submenu for using the array
	echo $this->menu_content . "\n"; 
?>
</div><!-- end id:topmenu -->
<?php
	if( count($this->menu_sub_items_template) > 0 ){
		
		echo "<div id=\"submenu\">\n";
		for($x=0;$x<count($this->menu_sub_items_template);$x++){
			$menuactstate = "inactive";
			if( $this->menu_sub_items_template[$x]->linkactive == "1"){
				$menuactstate = "active";
			}
			echo "<a class=\"menu_link $menuactstate\" href=\"{$this->menu_sub_items_template[$x]->linkurl}\" />";
			echo $this->menu_sub_items_template[$x]->linktext;
			echo "</a>";
			if( ($x+1) < count($this->menu_sub_items_template) ){
				echo "&nbsp;|&nbsp;";
			}
			
		}
		echo "</div><!-- end id:submenu -->\n";
	}

	if( $this->app_user_message !="" ){
		echo "<div class=\"message_user {$this->app_user_message_type}\">\n";
		echo $this->app_user_message;
		echo "</div>\n";
	}
?>
<div id="content">
<?php echo $this->page_content . "\n"; ?>
</div><!-- end id:content -->

</div><!-- end id:page -->
</body>
</html>