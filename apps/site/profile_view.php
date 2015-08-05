<h2>Profile</h2>
<p>
<?php
echo $this->renderForm();
?>
Organizations:<br />
<ul>
<?php
if( count($this->user->user_locations) == 0){
	echo "none"; 
}else{
	foreach($this->user->user_locations as $key => $val){
		echo"<li>" . $this->user->user_locations[$key] . "</li>";
	}
}

?>
</ul>
</p>