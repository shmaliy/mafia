<?php
	function table(){
		$_SESSION['cms']['menudisable'] = 0;
		$list = $this->get_list();
		if ($list){
			foreach ($list as $i){
				foreach ($i as $f => $v){ $r["{#$f#}"] = $v; }
				$r['{#email#}'] = $i['email'];
				$o['{#items#}'] .= $this->core->tpl->assign("modules/$this->name/tpl/table_row.tpl", $r);
			}
		}else { $o['{#items#}'] = '<tr><td colspan="3">Пусто</td></tr>'; }
		$o['{#basedir#}'] = BASEDIR;
		$o['{#name#}'] = $this->name;
		return $this->core->tpl->assign("modules/$this->name/tpl/table.tpl", $o);
	}
?>