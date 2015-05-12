<?php

class BOTI_func_statistics {
	var $content;
	function run() {
		global $_BOTI;
		if($_GET['delete'] == "offline_bots") {
			$_BOTI->query("DELETE FROM BOTI_victims WHERE ConTime <".(time()-604800));
			$this->content .= title("Delete");
			$this->content .= content("Bots successfully deleted!", "success");
		} elseif($_GET['delete'] == "tasks") {
			$_BOTI->query("DELETE FROM BOTI_tasks");
			$_BOTI->query("DELETE FROM BOTI_task_done");
			$this->content .= title("Delete");
			$this->content .= content("Tasks successfully deleted!", "success");
		}
		$this->content .= title("Statistics");
		$query = $_BOTI->query("SELECT (SELECT count(*) FROM BOTI_victims) AS bots,(SELECT count(*) FROM BOTI_victims WHERE ConTime >".(time()-$_BOTI->online).") AS bots_online,(SELECT count(*) FROM BOTI_victims WHERE ConTime >".(time()-86400).") AS bots_online24,(SELECT count(*) FROM BOTI_victims WHERE ConTime >".(time()-604800).") AS bots_online7,(SELECT count(*) FROM BOTI_victims WHERE ConTime >".(time()-$_BOTI->online)." AND taskID != 0) AS bots_busy,(SELECT count(*) FROM BOTI_tasks AS t WHERE t.elapsed > '".time()."' OR (t.elapsed=0 AND (SELECT count(*) FROM BOTI_task_done WHERE taskID=t.taskID)<bots)) AS tasks");
		$ds=mysql_fetch_array($query);
		$table = '<table>';
		$table .= '<tr><td style="text-align:right;width:40%">Total Bots:</td><td><b>'.$ds['bots'].' Bots</b></td></tr>';
		if(!$ds['bots']) $ds['bots'] = 1;
		$table .= '<tr><td style="text-align:right;">Bots Online:</td><td><b>'.$ds['bots_online'].' Bots</b> ('.round($ds['bots_online']/$ds['bots']*100, 2).'%)</td></tr>';
		$table .= '<tr><td style="text-align:right;">Bots Offline:</td><td><b>'.($ds['bots']-$ds['bots_online']).' Bots</b> ('.round(($ds['bots'] ? ($ds['bots']-$ds['bots_online'])/$ds['bots']*100 : 0), 2).'%)</td></tr>';
		$table .= '<tr><td style="text-align:right;">Bots Online (24 hours):</td><td><b>'.$ds['bots_online24'].' Bots</b> ('.round($ds['bots_online24']/$ds['bots']*100, 2).'%)</td></tr>';
		$table .= '<tr><td style="text-align:right;">Bots Online (7 days):</td><td><b>'.$ds['bots_online7'].' Bots</b> ('.round($ds['bots_online7']/$ds['bots']*100, 2).'%)</td></tr>';
		$table .= '<tr><td style="text-align:right;">Busy Bots:</td><td><b>'.$ds['bots_busy'].' Bots</b> ('.round($ds['bots_busy']/$ds['bots']*100, 2).'%)</td></tr>';
		$table .= '<tr><td style="text-align:right;">Active Tasks:</td><td><b>'.$ds['tasks'].' Tasks</b></td></tr>';
		$table .= '</table><div style="text-align:right;padding:10px"><a href="?delete=offline_bots"  onclick="return confirm(\'Do you really want to delete all Bots which are offline for more than one week?\')" class="button"><span>Delete Bots</span></a><a href="?delete=tasks"  onclick="return confirm(\'Do you really want to delete all Tasks?\')"class="button"><span>Delete Tasks</span></a></div>';
		$this->content .= content($table);
	}

}


?>