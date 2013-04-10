<?php
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
?>