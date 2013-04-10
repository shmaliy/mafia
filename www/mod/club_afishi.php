<?php

class club_afishi
{
    public $_const = array(
        'ERROR_SESSION_EXPIRED' => 'Сессия истекла, пожалуйста авторизуйтесь заново',
        'ERROR_POST_DATA' => 'Ошибка переданых данных',
        'ERROR_EVENT_DENIED' => 'Действие запрещено',
        'ERROR_DATABASE' => 'Ошибка БД',
        'ERROR_FILESYSTEM' => 'Ошибка файловой системы',
        'ERROR_UPLOAD_FILE' => 'Ошибка загрузки',
        'ERROR_FILE_NOT_EXISTS' => 'Файла не существует',
        'ERROR_DELETE_FILE' => 'Ошибка удаления',
        'ERROR_REQUIRED_FIELDS' => 'Заполните поля отмеченые красным',
        'EDDOR_ADDING_ITEM' => 'Ошибка добавления',
        'ERROR_SAVING_ITEM' => 'Ошибка сохранения',
        'ERROR_ITEM_NOT_FOUND' => 'Элемент не найден',
        'ERROR_DELETE_ITEM' => 'Ошибка удаления из БД',
        'SUCESS_UPLOAD_FILE' => 'Файл успешно загружен',
        'SUCESS_DELETE_FILE' => 'Файл успешно удален',
        'SUCESS_ADDING_ITEM' => 'Афиша добавлена',
        'SUCESS_SAVING_ITEM' => 'Афиша сохранена',
        'SUCESS_DELETE_ITEM' => 'Афиша удалена',
        'ERROR_MESSAGE' => 'Ошибка заказа',
        'SUCESS_MESSAGE' => 'Ваш заказ отправлен. Ожидайте звонка от гангстеров!',
    );
    
    private $_user = null;
    private $_club;
    private $_db;
    private $_month = array(
        1 => 'Январь',
        'Февраль',
        'Март',
        'Апрель',
        'Май',
        'Июнь',
        'Июль',
        'Август',
        'Сентябрь',
        'Октябрь',
        'Ноябрь',
        'Декабрь'
    );
    protected $_ajaxActions = array();
    protected $_ajaxMessages = array();    
    public $view;
    
    protected function _afishiAjaxResponse()
    {
        foreach ($this->_ajaxMessages as &$message) {
            if (substr($message, 0, 5) == 'ERROR') {
                $message = '"<div class=\"ststus ststus_error\">"+' . __CLASS__ . '.' . $message . '+"</div>"';
            } else if (substr($message, 0, 7) == 'WARNING') {
                $message = '"<div class=\"ststus ststus_warning\">"+' . __CLASS__ . '.' . $message . '+"</div>"';
            } else {
                $message = '"<div class=\"ststus ststus_sucess\">"+' . __CLASS__ . '.' . $message . '+"</div>"';
            }
        }
        
        $messages = implode('+"<br />"+', $this->_ajaxMessages);
        $this->_ajaxActions[] = '$("create_afishi_stat").update(' . $messages . ')';
        return implode(';', $this->_ajaxActions);
    }
    
    protected function _afishiCheck()
    {
        $select = $this->_db->select()->from('cms_categories', 'id')->limit(1);
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', $this->_club['id']);
        $select->where($this->_db->quoteIdentifier('title_alias') . ' = ?', 'club_afishi');
        $catId = $this->_db->fetchOne($select);
        if (empty($catId)) {
            $preformattedData = array(
                'parent_id' => $this->_club['id'],
                'title' => 'Афиши клуба',
                'title_alias' => 'club_afishi',
                'published' => '1',
                'checked_out' => '0',
                'checked_out_time' => '0000-00-00 00:00:00',
                'ordering' => '1',
            );            
            if (0 < $this->_db->insert('cms_categories', $preformattedData)) {
                return $this->_db->lastInsertId();
            } else return false;
        }
        return $catId;        
    }    
    
    protected function _afishiGetCat($catId)
    {
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $catId);
        return $this->_db->fetchRow($select);
    }    
    
    protected function _afishiGetList($catId, $rows = null)
    {
        $select = $this->_db->select()->from('cms_content');
        if (isset($rows)) {
            $page = isset($_GET['page']) ? $_GET['page'] : '1';        
            $select->limitPage($page, $rows);
        }
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', $catId);
        $select->order('publish_up DESC');
        return $this->_db->fetchAll($select);
    }
    
    protected function _afishiGetItem($id)
    {
        $select = $this->_db->select()->from('cms_content')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', (string) $id);
        return $this->_db->fetchRow($select);
    }    
    
    public function __construct()
    {
        $this->view = new view();
        $this->_db = $GLOBALS['db'];
        $this->view->constants = $this->_const;
        $this->view->month = $this->_month;
        $typed = new typedcontent();
        $this->view->centerBanner = $typed->get('centerBanner');
    }
    
    public function setClub($club = null)
    {
        if (!is_null($club)) {
            $this->_club = $club;
        }
    }
    
    public function setUser($user = null)
    {
        if (!isset($user)) {
            $this->_user = $user;
        }
        if (!isset($this->_user) && isset($_SESSION['auth']['user_id'])) {
            $select = $this->_db->select()->from('cms_users');
            $select->where($this->_db->quoteIdentifier('id') . ' = ?', $_SESSION['auth']['user_id'])->limit(1);
            $this->_user = $this->_db->fetchRow($select);
        }
    }
    
    public function afishiRoute($url)
    {
        if (!isset($this->_club)) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        if (!isset($url[3])) {
            return $this->afishiGenAll();
        } else if ($url[3] == 'add') {
            return $this->afishiGenEdit();
        } else if (substr(strtolower($url[3]), 0, 6) == 'afisha') {
            $itemId = substr($url[3], 6);
            if (isset($url[4])) {
                if ($url[4] != 'edit') {
                    return $this->view->render(TPLDIR . '404.tpl');
                }
                return $this->afishiGenEdit($itemId);
            }
            return $this->afishiGenItem($itemId);
        }
    }
    public function afishiGenClub()
    {
        $catId = $this->_afishiCheck();
        if ($catId === false) {
            return;
        }
        
        if (isset($_GET['psge'])) {
            unset($_GET['page']);
        }
        
        $this->view->cat = $this->_afishiGetCat($catId);
        $this->view->list = $this->_afishiGetList($catId, 4);
        $this->view->club = $this->_club;
        $this->view->user = $this->_user;
        
        if (empty($this->view->list) && (!isset($this->_user) || $this->_user['email'] != $this->_club['title_alias'])) {
            return;
        }
        
        return $this->view->render(TPLDIR . '@club/club_afishi_club.tpl');
    }
    
    public function afishiGenAll()
    {
        $catId = $this->_afishiCheck();
        if ($catId === false) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        $this->view->cat = $this->_afishiGetCat($catId);
        $this->view->list = $this->_afishiGetList($catId, 16);
        $this->view->club = $this->_club;
        $this->view->user = $this->_user;
        
        if (empty($this->view->list) && (!isset($this->_user) || $this->_user['email'] != $this->_club['title_alias'])) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        return $this->view->render(TPLDIR . '@club/club_afishi_all.tpl');
    }
    
    public function afishiGenItem($itemId)
    {
        $users = new users();
        $content = new content();
        $typed = new typedcontent();
        $catId = $this->_afishiCheck();
        if ($catId === false) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        $owner = $users->get_byemail($this->_club['title_alias']);
        $this->view->city = $content->get_cont($owner['param6']);
        
        
        $this->view->cat = $this->_afishiGetCat($catId);
        $this->view->club = $this->_club;
        $this->view->user = $this->_user;
        
        $select = $this->_db->select()->from('cms_content')->limit(1);
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', $catId);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $itemId);
        $this->view->item = $this->_db->fetchRow($select);
        $this->view->vktext = $typed->get('i_go');
        
        if (empty($this->view->item)) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        return $this->view->render(TPLDIR . '@club/club_afishi_item.tpl');
    }
    
    public function afishiGenEdit($itemId = null)
    {
        $catId = $this->_afishiCheck();
        if ($catId === false) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        $this->view->cat = $this->_afishiGetCat($catId);
        $this->view->club = $this->_club;
        $this->view->user = $this->_user;
        
        if (!isset($this->_user) || $this->_user['email'] != $this->_club['title_alias']) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        if (isset($itemId)) {
            $select = $this->_db->select()->from('cms_content')->limit(1);
            $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', $catId);
            $select->where($this->_db->quoteIdentifier('id') . ' = ?', $itemId);
            $this->view->item = $this->_db->fetchRow($select);
            
            if (empty($this->view->item)) {
                return $this->view->render(TPLDIR . '404.tpl');
            }
        }
        return $this->view->render(TPLDIR . '@club/club_afishi_edit.tpl');
    }
    
    public function afishiUploadImage($data)
    {
        $this->setUser();
        
        if (empty($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_afishiAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || empty($_FILES)) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_afishiAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_afishiAjaxResponse();
        }
        
        $catId = $this->_afishiCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_afishiAjaxResponse();
        }
        
        $dirName = 'contents/club' . $this->_club['id'] . '/afishi';
        if (!file_exists($dirName)) {
            if (!mkdir($dirName, 0777, true)) {
                $this->_ajaxMessages[] = 'ERROR_FILESYSTEM';
                return $this->_afishiAjaxResponse();
            }
        }
        $path = $dirName . '/u' . $this->_user['id'] . '_' . md5_file($_FILES['userfile']['tmp_name']) . '.' . substr($_FILES['userfile']['name'], -3);
        
        if (@move_uploaded_file($_FILES['userfile']['tmp_name'], $path)) {
            $this->_ajaxActions[] = 'document.create_afishi_form.image.value="/' . $path . '"';
            $this->_ajaxActions[] = '$("linkImage").down("img").src="/image.php?i=' . $path . '&t=png&m=fit&w=120&h=120&ca=cen&bg=eaeed7"';
            $this->_ajaxActions[] = '$("linkImage").show()';
            $this->_ajaxMessages[] = 'SUCESS_UPLOAD_FILE';
            return $this->_afishiAjaxResponse();
        } else {
            $this->_ajaxMessages[] = 'ERROR_UPLOAD_FILE';
            return $this->_afishiAjaxResponse();
        }        
    }
    
    public function afishiDeleteImage($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_afishiAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || !isset($data['image'])) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_afishiAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_afishiAjaxResponse();
        }
        
        $catId = $this->_afishiCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_afishiAjaxResponse();
        }
        
        $fileName = array_pop(explode('/', trim($data['image'], '/')));
        $pathName = 'contents/club' . $this->_club['id'] . '/afishi/' . $fileName;
        if (!file_exists($pathName)) {
            $this->_ajaxMessages[] = 'ERROR_FILE_NOT_EXISTS';
            return $this->_afishiAjaxResponse();
        }
        
        if (!unlink($pathName)) {
            $this->_ajaxMessages[] = 'ERROR_DELETE_FILE';
            return $this->_afishiAjaxResponse();
        }
        
        $this->_ajaxActions[] = '$("linkImage").hide()';
        $this->_ajaxActions[] = 'document.create_afishi_form.image.value=""';
        $this->_ajaxActions[] = '$("linkImage").down("img").src=""';
        $this->_ajaxMessages[] = 'SUCESS_DELETE_FILE';
        if (!empty($data['itemId'])) {
            $preformattedData = array(
                'images' => '',
                'publish_up' => time()
            );
            if (0 == $this->_db->update('cms_content', $preformattedData, $this->_db->quoteIdentifier('id') . " = " . $data['itemId'])) {
                $this->_ajaxMessages[] = 'ERROR_DELETE_ITEM';
            }            
        }
        return $this->_afishiAjaxResponse();
    }
    
    public function afishiSaveItem($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxActions[] = 'document.create_afishi_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_afishiAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId'])) {
            $this->_ajaxActions[] = 'document.create_afishi_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_afishiAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxActions[] = 'document.create_afishi_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_afishiAjaxResponse();
        }
        unset($data['clubId']);
        
        $catId = $this->_afishiCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxActions[] = 'document.create_afishi_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_afishiAjaxResponse();
        }
        unset($data['catId']);
        
        if ($data['title'] == '') {
            $this->_ajaxActions[] = 'document.create_afishi_form.title.setStyle({outline:"2px solid #f00"})';
        }
        if ($data['image'] == '') {
            $this->_ajaxActions[] = '$("uploadImage").setStyle({outline:"2px solid #f00"})';
        }
        if ($data['introtext'] == '') {
            $this->_ajaxActions[] = '$("introtext_parent").setStyle({outline:"2px solid #f00"})'; //fot mce editor field
        }
        if (!empty($this->_ajaxActions)) {
            $this->_ajaxActions[] = 'document.create_afishi_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_REQUIRED_FIELDS';
            return $this->_afishiAjaxResponse();
        }
        
        foreach ($data as $k => $v) {
            $data[$k] = iconv('utf-8', 'cp1251', $v);
        }
        $preformattedData = array(
            'parent_id' => $catId,
            'created' => time(),
            'created_by' => '0',
            'publish_up' => mktime($data['d_hour'], $data['d_minute'], 0, $data['d_month'], $data['d_day'], $data['d_year']),
            'publish_down' => '0',
            'published' => '1',
            'checked_out' => '0',
            'checked_out_time' => '0000-00-00 00:00:00',
            'ordering' => '1',
            'hits' => '0',
        );
        unset($data['d_month']);
        unset($data['d_day']);
        unset($data['d_year']);
        unset($data['d_hour']);
        unset($data['d_minute']);
        if ($data['id'] == '') {
            unset($data['id']);
            if (0 < $this->_db->insert('cms_content', array_merge($preformattedData, $data))) {
                $id = $this->_db->lastInsertId();
                $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/club/club' . $this->_club['id'] . '/afishi/\"",100)';
                $this->_ajaxMessages[] = 'SUCESS_ADDING_ITEM';
            } else {
                $this->_ajaxActions[] = 'document.create_afishi_form.enable()';
                $this->_ajaxMessages[] = 'EDDOR_ADDING_ITEM';
            }
        } else {
            $id = $data['id'];
            unset($data['id']);
            if (0 < $this->_db->update('cms_content', array_merge($preformattedData, $data), $this->_db->quoteIdentifier('id') . " = $id")) {
                $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/club/club' . $this->_club['id'] . '/afishi/\"",100)';
                $this->_ajaxMessages[] = 'SUCESS_SAVING_ITEM';
            } else {
                $this->_ajaxActions[] = 'document.create_afishi_form.enable()';
                $this->_ajaxMessages[] = 'ERROR_SAVING_ITEM';
            }
        }
        return $this->_afishiAjaxResponse();
    }
    
    public function afishiDeleteItem($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_afishiAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || !isset($data['itemId'])) {
            return $this->_afishiAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_afishiAjaxResponse();
        }
        
        $catId = $this->_afishiCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_afishiAjaxResponse();
        }
        
        $item = $this->_afishiGetItem($data['itemId']);
        if (empty($item)) {
            $this->_ajaxMessages[] = 'ERROR_ITEM_NOT_FOUND';
            return $this->_afishiAjaxResponse();            
        }
        
        @unlink(substr($item['image'], 1));
        
        if (0 < $this->_db->delete('cms_content', $this->_db->quoteIdentifier('id') . ' = ' . $item['id'])) {
            $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/club/club' . $this->_club['id'] . '/afishi/\"",250)';
            $this->_ajaxMessages[] = 'SUCESS_DELETE_ITEM';
        } else {
            $this->_ajaxMessages[] = 'ERROR_DELETE_ITEM';
        }
        return $this->_afishiAjaxResponse();
    }
    
    public function zakazItem($data)
    {        
        if (!isset($data['clubId']) || !isset($data['string'])) {
            return $this->_afishiAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if ($data['fio'] == '') {
            $this->_ajaxActions[] = 'document.zakaz_stolov_form.fio.setStyle({outline:"2px solid #f00"})';
        }
        if ($data['email'] == '') {
            $this->_ajaxActions[] = 'document.zakaz_stolov_form.email.setStyle({outline:"2px solid #f00"})';
        }
        if ($data['phone'] == '') {
            $this->_ajaxActions[] = 'document.zakaz_stolov_form.phone.setStyle({outline:"2px solid #f00"})'; //fot mce editor field
        }
        if (!empty($this->_ajaxActions)) {
            $this->_ajaxActions[] = 'document.zakaz_stolov_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_REQUIRED_FIELDS';
            return $this->_afishiAjaxResponse();
        }
        foreach ($data as $k => $v) {
            $data[$k] = iconv('utf-8', 'cp1251', $v);
        }
        
        $string = "Заказ места на игру: " . $data['string'];
        $headers = 'MIME-Version: 1.0' . "\r\n"
                 . 'Content-type: text/html; charset="windows-1251"' . "\r\n"
                 . 'Reply-To: ' . $data['email'] . "\r\n"
                 . 'From: ' . $data['email'] . "\r\n";
        $message = "<h3>$string</h3>";
        $message .= "<table>";
        $message .= "<tr><td>Имя</td><td>" . $data['fio'] . "</td></tr>";
        $message .= "<tr><td>Эл. почта</td><td>" . $data['email'] . "</td></tr>";
        $message .= "<tr><td>Телефон</td><td>" . $data['phone'] . "</td></tr>";
        $message .= "<tr><td>Кол-во людей</td><td>" . $data['count'] . "</td></tr>";
        $message .= "</table>";
        $to = $this->_club['title_alias'];
        
        $string2 = "Вы подписались на игру: " . $data['string'];
        $headers2 = 'MIME-Version: 1.0' . "\r\n"
                 . 'Content-type: text/html; charset="windows-1251"' . "\r\n"
                 . 'Reply-To: МирМафии' . "\r\n"
                 . 'From: МирМафии' . "\r\n";
        $message2 = "<h3>$string2</h3>";
        $message2 .= "<table>";
        $message2 .= "<tr><td>Имя</td><td>" . $data['fio'] . "</td></tr>";
        $message2 .= "<tr><td>Эл. почта</td><td>" . $data['email'] . "</td></tr>";
        $message2 .= "<tr><td>Телефон</td><td>" . $data['phone'] . "</td></tr>";
        $message2 .= "<tr><td>Кол-во людей</td><td>" . $data['count'] . "</td></tr>";
        $message2 .= "</table>";
        $to2 = $data['email'];
        
        if (@mail($to, $string, $message, $headers) && @mail($to2, $string2, $message2, $headers2)) {
            $this->_ajaxMessages[] = 'SUCESS_MESSAGE';
            $this->_ajaxActions[] = 'document.zakaz_stolov_form.reset()';
            $this->_ajaxActions[] = 'document.zakaz_stolov_form.enable()';
        } else {
            $this->_ajaxActions[] = 'document.zakaz_stolov_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_MESSAGE';
        }                
        return $this->_afishiAjaxResponse();
    }
}