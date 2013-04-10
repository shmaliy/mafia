<?php
if (!defined(BASEDIR)){
	$bd = str_replace("\\", '/', getcwd());
	$bd = str_replace($_SERVER['DOCUMENT_ROOT'], '', $bd);
	define(BASEDIR, str_replace('/modules/subscribe', '', $bd));
}
header("Cache-Control: no-cache, must-revalidate");
header("Content-type: text/css"); 
?>
<style media="screen">
.icon-16-subscribe { background-image: url('<?=BASEDIR;?>/modules/subscribe/icon-16-subscribe.png'); }
.icon-48-subscribe { background-image: url('<?=BASEDIR;?>/modules/subscribe/icon-48-subscribe.png'); }
</style>
