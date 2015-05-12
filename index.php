<?php
include("_bot/run.php");
$_BOTI->tpl = file_get_contents("_bot/design.tpl");

if(!$_SESSION['loggedin'] || $_SESSION['admin_password'] != $_BOTI->admin_password) {
	include("_bot/func/login.php");
	$_BOTI->func = new BOTI_func_login;
	$_BOTI->func->run();
	echo str_replace(array('{content}', '{navigation}'), array($_BOTI->func->content, 'Please login'), $_BOTI->tpl);
	die();
}

include("_bot/func/statisics.php");
include("_bot/func/list.php");
include("_bot/func/tasks.php");
include("_bot/func/logout.php");

$navigation = array(
	'Statistics'=>'?action=statistics',
	'Bots'=>'?action=list',
	'Tasks'=>'?action=tasks',
	'Logout'=>'?action=logout',
);
$nav = '';
foreach($navigation as $name=>$link) $nav .= '<a href="'.$link.'">'.$name.'</a>';

if($_GET['action'] == "tasks") $_BOTI->func = new BOTI_func_tasks;
elseif($_GET['action'] == "list") $_BOTI->func = new BOTI_func_list;
elseif($_GET['action'] == "logout") $_BOTI->func = new BOTI_func_logout;
else $_BOTI->func = new BOTI_func_statistics;

$_BOTI->func->run();

echo str_replace(array('{content}', '{navigation}'), array($_BOTI->func->content, $nav), $_BOTI->tpl);

?>