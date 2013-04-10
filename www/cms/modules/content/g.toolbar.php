<?php
	function _toolbar($mode, $id = NULL){
		if ($mode == 'list'){
			$buttons = array(
				'icon' => $this->info['MODULE']['ICON48']['#val'],
				'title' => $this->info['MODULE']['NAME']['#val'],
				'buttons' => array(
					array(BASEDIR.'/images/toolbar/icon-32-publish.png', '��������', "call('$this->name', '_publish', ['true', getcheckbox('ctable_contents')]);", 13),
					array(BASEDIR.'/images/toolbar/icon-32-unpublish.png', '������', "call('$this->name', '_publish', ['false', getcheckbox('ctable_contents')]);", 13),
					array(BASEDIR.'/images/toolbar/icon-32-move.png', '�����������', "call('$this->name', '_move', ['confirm', getcheckbox('ctable_contents')]);", 13),
					array(BASEDIR.'/images/toolbar/icon-32-copy.png', '����������', "call('$this->name', '_copy', ['confirm', getcheckbox('ctable_contents')]);", 12),
					array(BASEDIR.'/images/toolbar/icon-32-trash.png', '�������', "call('$this->name', '_delete', ['confirm', getcheckbox('ctable_contents')]);", 12),
					array(BASEDIR.'/images/toolbar/icon-32-new.png', '�������', "call('$this->name', '_new');", 12)
				)
			);
		}
		elseif ($mode == 'new'){
			$buttons = array(
				'icon' => $this->info['MODULE']['ICON48']['#val'],
				'title' => $this->info['MODULE']['NAME']['#val'],
				'buttons' => array(
					array(BASEDIR.'/images/toolbar/icon-32-save.png', '���������', "call('$this->name', '_save', getform('$this->name'));", 13),
					array(BASEDIR.'/images/toolbar/icon-32-apply.png', '���������', "call('$this->name', '_apply', getform('$this->name'));", 13),
					array(BASEDIR.'/images/toolbar/icon-32-cancel.png', '������', "call('$this->name', '_cancel', 'new');", 13)
				)
			);
		}
		elseif ($mode == 'edit'){
			$buttons = array(
				'icon' => $this->info['MODULE']['ICON48']['#val'],
				'title' => $this->info['MODULE']['NAME']['#val'],
				'buttons' => array(
					array(BASEDIR.'/images/toolbar/icon-32-save.png', '���������', "call('$this->name', '_save', getform('$this->name'));", 13),
					array(BASEDIR.'/images/toolbar/icon-32-apply.png', '���������', "call('$this->name', '_apply', getform('$this->name'));", 13),
					array(BASEDIR.'/images/toolbar/icon-32-lock.png', '�����������', "call('$this->name', '_lock');", 13),
					array(BASEDIR.'/images/toolbar/icon-32-cancel.png', '������', "call('$this->name', '_cancel', '$id');", 13)
				)
			);
		}
		return ($buttons) ? $this->core->toolbar($buttons) : false;
	}
?>