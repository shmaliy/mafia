<?php
	function c_db(){
		$query  = "CREATE TABLE IF NOT EXISTS `$this->tbl` ( ";
		$query .= "`id` INT NOT NULL AUTO_INCREMENT, ";
		$query .= "`email` TEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL, ";
		$query .= "`md5` TEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL, ";
		$query .= "PRIMARY KEY (`id`)); ";
		if (!@mysql_query($query)){ /*core::set_error("$this->name::c_db");*/ }
	}
?>