<?php
	function _send($data){
		$data = $this->_prep_f($data);
		$m['title'] = $data[$this->name]['title'];
		$m['text'] = $data[$this->name]['text'];
		
		$error = array();
		if ($m['title'] == ''){ $error[] = '¬ведите заголовок сообщени€'; }
		if ($m['text'] == ''){ $error[] = '¬ведите текст сообщени€'; }
		
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
?>