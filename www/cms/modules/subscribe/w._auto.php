<?php
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
?>