<?php
session_start();
include("inc/BOTI.class.php");
$_BOTI = new BOTI;
include("inc/config.inc.php");
include("inc/content.funcs.php");
$_BOTI->db_connect();

?>