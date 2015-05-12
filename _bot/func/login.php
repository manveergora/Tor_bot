<?php

class BOTI_func_login {
	var $content;
	function run() {
		global $_BOTI;
		if($_POST['login']) {
			if($_POST['password'] == $_BOTI->admin_password) {
				$_SESSION['admin_password'] = $_BOTI->admin_password;
				$_SESSION['loggedin'] = true;
				header("Location: index.php");
				die();
			} else $this->content .= content("Wrong Password!", "error");
		}
		$this->content .=content('<form method="post">
<div style="text-align:center;margin-top:10px">Password: &nbsp; <input type="password" name="password" /> &nbsp; <input type="submit" name="login" value="Login" /></div></td>
</form>');
	}

}

?>