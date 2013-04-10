<?php

class Subscribe
{
    private $db;
    public function __construct()
    {
        $this->db = new PDO('mysql:host=db2.adamant.ua;dbname=mafia_maf', 'mafia_admin', ')l@~7_TDvPge');
    }
    
    public function submit($data)
    {
        $sql = "SELECT * FROM `cms_subscribe` WHERE `email` = '" . $data['email'] . "'";
        $stmt = $this->db->query($sql);
        if (count($stmt->fetchAll()) > 0) {
            return 'this.status.update("Ваш E-mail уже добавлен");' . $stmt->rowCount();
        }
        
        $sql = "INSERT INTO `cms_subscribe` ("
             . "`email`,"
             . "`md5`"
             . ") VALUES ("    
             . "'" . $data['email'] . "',"    
             . "'" . md5($data['email']) . "'"
             . ")";
         $this->db->beginTransaction();
         $stmt = $this->db->query($sql);
         if ($stmt->rowCount() > 0) {
             $this->db->commit();
             return 'this.status.update("Ваш E-mail добавлен")';
         } else {
             $this->db->rollBack();
             return 'this.status.update("ошибка")';
         }
    }
}


function ajax()
{
    if (!empty($_POST) && !isset($_POST['function']) && $_POST['function'] != ''){
        return;
    }
    
    $data = $_POST;
    
    $encoding = 'UTF-8';
    if (isset($data['encoding']) && $data['encoding'] != '') {
        $encoding = $data['encoding'];
        unset($data['encoding']);
    }
    header('content-type: text/plain; charset="' . $encoding . '"');
    
    if (stripos($_POST['function'], '::') !== false){
        $function = explode('::', $_POST['function']);
        if (!class_exists($function[0], false)) {
            echo 'this.status.update("class not found")';
            exit();
        }
        if (!method_exists($function[0], $function[1])) {
            echo 'this.status.update("method not found")';
            exit();
        }
    }elseif(stripos($_POST['function'], '->') !== false){
        $function = explode('->', $_POST['function']);
        if (!class_exists($function[0], false)) {
            echo 'this.status.update("class not found")';
            exit();
        }
        if (!method_exists($function[0], $function[1])) {
            echo 'this.status.update("method not found")';
            exit();
        }
        $function[0] = new $function[0];
    }else{
        $function = $_POST['function'];
        if (!function_exists($function)) {
            echo 'this.status.update("function not found")';
            exit();
        }
    }
    unset($data['function']);
    echo call_user_func($function, $data);
    exit();
}
ajax();
