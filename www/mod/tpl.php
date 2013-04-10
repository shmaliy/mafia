<?php
class tpl{
	function assign($tpl_name, $arr_data = null, $val = null){
		$tpl = @file_get_contents(TPLDIR.$tpl_name);

		if(is_array($arr_data)){
			foreach ($arr_data as $k => $v){
				$tpl = str_replace($k, $v, $tpl);
			}
		}
		elseif(is_string($arr_data)){
			$tpl = str_replace($arr_data, $val, $tpl);
		}
		else{}

		return $tpl;
	}
}

?>