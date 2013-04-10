<?php
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
?>