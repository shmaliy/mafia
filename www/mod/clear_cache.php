<?php
function clear_cache(){
	$dir = 'contents_cache';
	$files = array();
	if(is_dir($dir)){
		if ($dh = opendir($dir)){
			while (($file = readdir($dh)) !== false){
				if ($file != "." && $file != ".."){
					if(!is_dir($dir."/".$file)){ $files[] = $file; }
				}
			}
			closedir($dh);
		}
	}
	if (count($files)>0){
		foreach ($files as $f){
			$fdatetime = explode('_', substr($f,0,19));
			$ftime = explode(':', $fdatetime[1]);
			$fdate = explode('-', $fdatetime[0]);
			$fmktime = mktime($ftime[0], $ftime[1], $ftime[0], $fdate[1], $fdate[2], $fdate[0]);
			if ($fmktime < (time() - 3600)){
				unlink("$dir/$f");
			}
			//echo $ftime;
		}
	}
}
@clear_cache();
?>