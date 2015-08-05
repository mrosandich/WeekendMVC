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
<?php echo $this->menu_content . "\n"; ?>
<hr/>
<?php
	if( $this->app_user_message !="" ){
		echo "<div class=\"message_user {$this->app_user_message_type}\">\n";
		echo $this->app_user_message;
		echo "</div>\n";
	}
?>
<?php echo $this->page_content . "\n"; ?>
</body>
</html>