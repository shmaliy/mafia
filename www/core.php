<?php

set_include_path(
    get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib'
);

include_once 'config.php';

function init(){
	if (is_dir('mod') && $dir = opendir('mod')){
		while (false !== ($file = readdir($dir))){
			if ($file != "." && $file != ".."){ $m[] = $file; }
		}
		closedir($dir);
	}else{
		die('<font color="red"><b>Папка с модулями отсутствует или нет доступа</b></font>');
	}
	
	if (count($m)==0){
		die('<font color="red"><b>Папка с модулями пуста</b></font>');
	}else{
		$f = fopen('includes.php', 'w+');
		$str = "<?php\n";
		asort($m);
		foreach ($m as $v){ $str .= "require_once 'mod/$v';\n"; }
		$str .= "?>";
	    fputs ($f, $str);
	  	fclose ($f);
	}
}

init();
require_once 'includes.php';

function generate_password($number){
    $arr = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','s','t','u','v','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','R','S','T','U','V','X','Y','Z','1','2','3','4','5','6','7','8','9','0');
    $pass = "";
    for($i = 0; $i < $number; $i++){
        $index = rand(0, count($arr) - 1);
        $pass .= $arr[$index];
    }
    return $pass;
}

function gen_navigator($current, $total, $on_page){
	$tpl = new tpl();
	if ($total > $on_page){
		$r = $on_page;
		$c = $current;
		$t = $total;
		$pages = ($r != 0) ? ($t/$r > floor($t/$r)) ? floor($t/$r)+1 : $t/$r : 1;
		for($i=1; $i<=$pages; $i++){
			$o['{#n#}'] = "$i";
			if ($pages > 11){
				if ($i<4 || $i>$pages-3 || $i == $c-1 || $i == $c+1 || $i == $c || ($i == floor($pages/2) || $i == floor($pages/2)+1 || $i == floor($pages/2)-1) && ($c<6 || $c>$pages-5)){
					if ($i == $c){ $out['{#pages#}'] .= $tpl->assign('navigator_c.tpl', $o); }
					else{ $out['{#pages#}'] .= $tpl->assign('navigator_p.tpl', $o); }
				}else{
					$out['{#pages#}'] .= ' ';
				}
			}else{
				if ($i == $c){ $out['{#pages#}'] .= $tpl->assign('navigator_c.tpl', $o); }
				else{ $out['{#pages#}'] .= $tpl->assign('navigator_p.tpl', $o); }
			}
			if ($c == 1) { $out['{#s#}'] = 'class="c"'; $out['{#s_link#}'] = ''; }
			else{ $out['{#s#}'] = ''; $out['{#s_link#}'] = 'href="javascript:page(1);"'; }
			if ($c == $pages) { $out['{#e#}'] = 'class="c"'; $out['{#e_link#}'] = ''; }
			else{ $out['{#e#}'] = ''; $out['{#e_link#}'] = 'href="javascript:page('."$pages".');"'; }
		}
		$out['{#start#}'] = ($c==1) ? '' : 'href="javascript:page(1);"';
		$out['{#end#}'] = ($c==$pages) ? '' : 'href="javascript:page('.$pages.');"';
		return $tpl->assign('navigator.tpl', $out);
	}
}

function _isint($str){ return ($str/2 == 0) ? false : true; }

function router($url_query){
	$dynamic = new dynamic();
	$typedcontent = new typedcontent();
	$tpl = new tpl();
	$out = '<a href="/">Главная</a> / ';
	for($i=0; $i<count($url_query); $i++){
		$j = $i+1;
		$link = $url_query;
		$link = array_slice($link, 0, $j);
		$link = '/'.implode('/', $link);
		if ($i<count($url_query)-1){
			$out .= '<a href="'.$link.'">';
			if (strstr($url_query[$i],'.stc')){
				$alias = str_replace('.stc', '', $url_query[$i]);
				$title = $typedcontent->get($alias, 'title');
			}else{
				$title = $dynamic->get_cat($url_query[$i], null, 'title');
			}
			$out .= $title.'</a>';
		}else{
			if (strstr($url_query[$i],'.stc')){
				$alias = str_replace('.stc', '', $url_query[$i]);
				$out .= '<a>'.$typedcontent->get($alias, 'title').'</a>';
			}else{
				if (is_numeric($url_query[$i])){
					$out .= '<a>'.$dynamic->get_cont($url_query[$i], 'title').'</a>';
				}else{$out .= '<a>'.$dynamic->get_cat($url_query[$i], NULL, 'title').'</a>';}
			}			
		}
		if ($i<count($url_query)-1){ $out .= ' / '; }
	}
	return $tpl->assign('router.tpl', '{#router#}', $out);
}

function page(){
	global $url_query, $theme_name, $uri;
	$GLOBALS['site_title'] = array();
	$frontpage = new frontpage();
	$typedcontent = new typedcontent();
	$content = new content();
	$users = new users();
	$menu = new menu();
	$club = new club();
	$afisha = new afisha();
	$news = new club_news();
	$admin = new club_admin();
	$view = new view();
	$cusers = new club_users();
	
	$view->topMenu = $menu->gen('main', 0, $uri, '');
	$view->leftMenu = $menu->gen('left', 0, $uri, '');
	$view->adminBlock = $admin->adminGenAuth();
	$view->right = $news->newsGenStandaloneFp();
	$view->leftBanner = $typedcontent->get('leftBanner');
	$view->rightBanner = $typedcontent->get('rightBanner');
	
	if (!$url_query[0]){	    
		$view->center = $frontpage->calendar();
	}else{
		if (strstr($url_query[0],'.stc')){
			$tpl_name = str_replace('.stc', '', $url_query[0]);
			$view->center = $typedcontent->gen($tpl_name, $tpl_name);
		}else{
		    if ($url_query[0] == 'search') {
		        $view->center = $view->render(TPLDIR . '/searchResults.tpl');
		    } else if ($url_query[0] == 'shop') {
		        $view->center = $view->render(TPLDIR . '/shop.tpl');
		    } else if ($url_query[0] == 'club') {
		        $view->center = $club->route($url_query);
		    } else if ($url_query[0] == 'afishi') {
		        $view->center = $afisha->gen();
            } else if ($url_query[0] == 'rating') {
                $view->center = $cusers->usersGenStandalone();
		    } else {
                $ns = ($url_query[0] == 'publications') ? $url_query[0] : 'default';
			    $view->center = $content->page($url_query, $ns, 10);
		    }
		}
	}
	$GLOBALS['site_title'][] = 'Mafia Dnepr League';
	$view->siteTitle = implode(' :: ', $GLOBALS['site_title']);
	return $view->render(TPLDIR . '/layout.tpl');
}

$ua = $_SERVER['HTTP_USER_AGENT'];
$tpl = new tpl();
if (stripos($ua, 'MSIE 6.0')!==false && stripos($ua, 'MSIE 8.0')===false && stripos($ua, 'MSIE 7.0')===false){
	echo $tpl->assign('ie6.tpl', '{#theme_name#}', $theme_name);
}else{
	echo page();
}

