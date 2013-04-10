<?php
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
?>