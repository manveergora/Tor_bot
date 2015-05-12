<?php

class BOTI_func_logout {
	function run() {
		$_SESSION['loggedin'] = false;
		header("Location: index.php");
		die();
	}

}

?>