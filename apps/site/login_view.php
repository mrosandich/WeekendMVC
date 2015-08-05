<h2>Login</h2>
<?php 
echo $this->pages_array[$this->current_pages_index]->getFormActionHTML();
?>
User Name: <input type="text" name="username" value=""><br />
Password: <input type="password" name="password" value=""><br />
<input type="submit" name="subit" value="Submit"><br />