<?php
class users
{
	public $tpl;
	public $mailto = 'pavlenko.obs@gmail.com';
	public $dynamic;
    
	private $_db;
    public $view;
		
    function __construct(){
	    $this->tpl = new tpl();
	    $this->dynamic = new content();
        
	    $this->view = new view();
        $this->_db = $GLOBALS['db'];
    }
	
	function get($id, $field = NULL){
		$query = "SELECT * FROM `".$GLOBALS['cms_config_dbprefix']."users` WHERE `id` = $id LIMIT 1";
		$req = @mysql_query($query);
		if ($req && mysql_num_rows($req)==1){
			$data = mysql_fetch_assoc($req);
			return ($field) ? $data[$field] : $data;
		}
		else return false;
	}
	
	function get_byemail($email, $field = NULL){
		$q = "SELECT * FROM `".$GLOBALS['cms_config_dbprefix']."users`";
		$q .= " WHERE `email` = '$email' LIMIT 1";
		$e = @mysql_query($q);
		if ($e && @mysql_num_rows($e)>0){
			$data = mysql_fetch_assoc($e);
			return ($field) ? $data[$field] : $data;
		}else return false;
	}
	
    function get_bymd5($email, $compare_field){
        $compare = "`$compare_field`";
        $q = "SELECT $compare, `id` FROM `".$GLOBALS['cms_config_dbprefix']."users`";
        $e = @mysql_query($q);
        if ($e && @mysql_num_rows($e)>0){
            while ($row = mysql_fetch_assoc($e)) {
                if ( $email == md5($row[$compare_field])) {
                    return $row;
                }
            }
        }
        return false;
    }
	
    function get_authorized($login, $password){
		$password = md5($password);
		$query = "SELECT * FROM `".$GLOBALS['cms_config_dbprefix']."users` WHERE `email` = '$login' AND `password` = '$password' LIMIT 1";
		$req = @mysql_query($query);
		if ($req && @mysql_num_rows($req)){
			return mysql_fetch_assoc($req);
		}else return false;
	}
}
?>