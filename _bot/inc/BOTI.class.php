<?php

class BOTI {
	var $mysql;
	function db_connect() {
		mysql_connect($this->mysql['host'], $this->mysql['user'], $this->mysql['pass']) or die("mysql-error");
		mysql_select_db($this->mysql['db']) or die("db-error");
	}
	function query($sql) {
		$q = mysql_query($sql);
		if(!$q) die(mysql_error());
		return $q;
	}
}

?>