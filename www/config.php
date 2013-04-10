<?php
//Error_Reporting(E_ALL);
ini_set('magic_quotes_gpc', 'off');
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

if (!empty($_POST) && isset($_POST['PHPSESSID'])) {
    session_id($_POST['PHPSESSID']);
    unset($_POST['PHPSESSID']);
}

session_start();
// Подключение БД
include_once 'cms/db.php';
@mysql_connect($cms_config_host, $cms_config_user, $cms_config_password);
@mysql_select_db($cms_config_db);
@mysql_query('SET NAMES cp1251');

require_once 'Zend/Db.php';
$db = Zend_Db::factory('Pdo_Mysql', array(
    'host'     => $cms_config_host,
    'username' => $cms_config_user,
    'password' => $cms_config_password,
    'dbname'   => $cms_config_db,
    'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES cp1251')
));

// Часовой пояс
date_default_timezone_set('Europe/Helsinki');
$theme_name = "default";

// Путь к темплейтам (нужен для модуля tpl)
if (!@defined(TPLDIR)){ @define(TPLDIR, "theme/$theme_name/tpl/"); }

// ПАРСИНГ СТРОКИ АДРЕСА
$url_query = array();
$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$_SESSION['url'] = $url;
$uri = trim(parse_url($url, PHP_URL_PATH), '/');
$url_query = explode("/", $uri);

// Переменные сессии
if (!$_SESSION['filter']['tm']){ $_SESSION['filter']['tm'] = 'no'; }
if (!$_SESSION['page'] or $_SESSION['url'] != $_SESSION['location'])
{ $_SESSION['page'] = 1; }
