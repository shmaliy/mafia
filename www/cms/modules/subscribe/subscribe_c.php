<?php
class subscribe
{

	function __construct(){
		$this->name = 'subscribe';
		$this->core = new core();
		$this->info = $this->core->_mod_get_info($this->name);
		$this->tbl = $GLOBALS['cms_config_dbprefix'].'subscribe';
		$this->c_db();
		if (!$_SESSION['cms']['mod'][$this->name] && $_SESSION['cms']['authorized'] == 1){
			$_SESSION['cms']['mod'][$this->name] = array('processing' => 0, 'title' => '', 'text'=> '');
		}
	}


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


	function _prep_f($array){
		if (count($array)==2 && !is_array($array[0])){
			$a[$array[0]] = $this->_prep_f($array[1]);
		}else{
			foreach($array as $k=>$v){if(is_array($v)){$a[$v[0]] = $v[1];}}
		}
		return $a;
	}


	function c_db(){
		$query  = "CREATE TABLE IF NOT EXISTS `$this->tbl` ( ";
		$query .= "`id` INT NOT NULL AUTO_INCREMENT, ";
		$query .= "`email` TEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL, ";
		$query .= "`md5` TEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL, ";
		$query .= "PRIMARY KEY (`id`)); ";
		if (!@mysql_query($query)){ /*core::set_error("$this->name::c_db");*/ }
	}


	function get_list(){
		$q = "SELECT * FROM `$this->tbl`";
		$r = @mysql_query($q);
		if ($r && @mysql_num_rows($r)>0){
			while ($row = mysql_fetch_assoc($r)){
				$d[] = $row;
			}
			return $d;
		}else return false;
	}


	function head(){
		$return  = '<link href="'.BASEDIR.'/modules/'.$this->name.'/'.$this->name.'.css.php" rel="stylesheet" type="text/css" />'."\n";
		$return .= '<script type="text/javascript" src="'.BASEDIR.'/js/tiny_mce/tiny_mce.js"></script>'."\n";
		$return .= '<script type="text/javascript" src="'.BASEDIR.'/js/tiny_mce_init.js"></script>'."\n";
		return $return;
	}


	function _auto($data){
		$h  = 'MIME-Version: 1.0' . "\r\n";
		$h .= 'Content-type: text/html; charset="windows-1251"' . "\r\n";
		$h .= 'From: http://mirmafii.com.ua';
		$m = $_SESSION['cms']['mod'][$this->name]['text'].$this->core->tpl->assign("modules/$this->name/tpl/adv.tpl",'{#link#}','http://mirmafii.com.ua/unsubscribe?hash='.md5($data[0][1]));
		if (mail($data[0][1], $_SESSION['cms']['mod'][$this->name]['title'], $m, $h)){
			$return[] = array('assign', 'subscribe_'.$data[0][0].'_stat', 'innerHTML', '<font color="green">ok</font>');
		}else{
			$return[] = array('assign', 'subscribe_'.$data[0][0].'_stat', 'innerHTML', '<font color="red">error</font>');
		}
		if (count($data)>1){
			for ($i=1; $i<count($data); $i++){ $e[] = $data[$i]; }
		}elseif (count($data)>1){
			$e[] = $data[0];
		} else {
		    $_SESSION['cms']['mod'][$this->name]['processing'] = 0;
		    return $return;
		}
		if ($_SESSION['cms']['mod'][$this->name]['processing'] == 1){
			$return[] = array('call', 'auto_send', $e);
			if (count($data)==2){
				$_SESSION['cms']['mod'][$this->name]['processing'] = 0;
			}
		}
		
		return $return;
	}


	function page(){
		return $this->_toolbar().$this->table();
	}


	function _toolbar(){
		$buttons = array(
			'icon' => $this->info['MODULE']['ICON48']['#val'],
			'title' => $this->info['MODULE']['NAME']['#val'],
			'buttons' => array(
				array(BASEDIR.'/images/toolbar/icon-32-send.png', 'Отправить', "call('$this->name', '_send', getform('$this->name'));", 12)
			)
		);
		return ($buttons) ? $this->core->toolbar($buttons) : false;
	}


	function _send($data){
		$data = $this->_prep_f($data);
		$m['title'] = $data[$this->name]['title'];
		$m['text'] = $data[$this->name]['text'];
		
		$error = array();
		if ($m['title'] == ''){ $error[] = 'Введите заголовок сообщения'; }
		if ($m['text'] == ''){ $error[] = 'Введите текст сообщения'; }
		
		if (count($error)==0){
			$list = $this->get_list();
			if ($list){
				foreach ($list as $item){ $e[] = array($item['id'],$item['email']); }
				$_SESSION['cms']['mod'][$this->name]['processing'] = 1;
				$_SESSION['cms']['mod'][$this->name]['title'] = $m['title'];
				$_SESSION['cms']['mod'][$this->name]['text'] = $m['text'];
				$return[] = array('call', 'auto_send', $e);
			}
		}else{
			$return[] = array('call', 'message', implode('<br />', $error));
		}		
		
		return $return;
	}


	function _ts_to_dt($data){ return date('Y-m-d H:i:s', $data); }


	function get($id, $f = NULL){
		$q = "SELECT * FROM `$this->tbl` WHERE `id` = $id LIMIT 1";
		$r = @mysql_query($q);
		if ($r && @mysql_num_rows($r)>0){
			if (isset($f) && $f != ''){ $d = mysql_fetch_assoc($r); return $d[$f]; }
			else { return mysql_fetch_assoc($r); }
		}else return false;
	}

}
?>