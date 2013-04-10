<?php

class club_users
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
        'SUCESS_ADDING_ITEM' => 'Участник добавлен',
        'SUCESS_SAVING_ITEM' => 'Участник сохранен',
        'SUCESS_DELETE_ITEM' => 'Участник удален',
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
    
    protected function _usersAjaxResponse()
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
        $this->_ajaxActions[] = '$("create_users_stat").update(' . $messages . ')';
        return implode(';', $this->_ajaxActions);
    }
    
    protected function _usersCheck()
    {
        $select = $this->_db->select()->from('cms_categories', 'id')->limit(1);
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', $this->_club['id']);
        $select->where($this->_db->quoteIdentifier('title_alias') . ' = ?', 'club_users');
        $catId = $this->_db->fetchOne($select);
        if (empty($catId)) {
            $preformattedData = array(
                'parent_id' => $this->_club['id'],
                'title' => 'Участники клуба',
                'title_alias' => 'club_users',
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
    
    protected function _usersGetCat($catId)
    {
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $catId);
        return $this->_db->fetchRow($select);
    }    
    
    protected function _usersGetList($catId, $rows = null)
    {
        $select = $this->_db->select()->from('cms_content');
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', $catId);
        $select->order('param2 DESC');
        if (isset($rows)) {
            $page = isset($_GET['page']) ? $_GET['page'] : '1';        
            $select->limitPage($page, $rows);
        }
        return $this->_db->fetchAll($select);
    }
    
    protected function _usersGetItem($id)
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
    
    public function usersRoute($url)
    {
        if (!isset($this->_club)) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        if (!isset($url[3])) {
            return $this->usersGenAll();
        } else if ($url[3] == 'add') {
            return $this->usersGenEdit();
        } else if (substr(strtolower($url[3]), 0, 4) == 'user') {
            $itemId = substr($url[3], 4);
            if (isset($url[4])) {
                if ($url[4] != 'edit') {
                    return $this->view->render(TPLDIR . '404.tpl');
                }
                return $this->usersGenEdit($itemId);
            }
            return $this->usersGenItem($itemId);
        }
    }
    
    public function usersGenStandalone()
    {
        $frontpage = new frontpage();
        $this->view->filter = $frontpage->genFilter();
        
        $db = $this->_db;
        $view = $this->view;
        
        $select = $db->select();
        $select->from(array('p' => 'cms_categories'), array());
        $select->join(array('c' => 'cms_categories'), 'c.parent_id = p.id', array('clubId' => 'id'));
        $select->join(array('u' => 'cms_users'), 'u.email = c.title_alias', array('region_id' => 'param5', 'city_id' => 'param6'));
        $select->join(array('ap' => 'cms_categories'), 'ap.parent_id = c.id', array());
        $select->join(array('a' => 'cms_content'), 'a.parent_id = ap.id');
        $select->join(array('r' => 'cms_categories'), 'r.id = u.param5', array('region_title' => 'title'));
        $select->join(array('ct' => 'cms_content'), 'ct.parent_id = r.id', array('city_title' => 'title'));
        $select->where($db->quoteIdentifier('p.title_alias') . ' = ?', 'club');
        $select->where($db->quoteIdentifier('ap.title_alias') . ' = ?', 'club_users');
        $select->order('param2 DESC');
        
        if (isset($_GET['region']) && $_GET['region'] != '' && $_GET['region'] != '0' && is_numeric($_GET['region'])) {
            $select->where($db->quoteIdentifier('u.param5') . ' = ?', $_GET['region']);
        }
        if (isset($_GET['city']) && $_GET['city'] != '' && $_GET['city'] != '0' && is_numeric($_GET['city'])) {
            $select->where($db->quoteIdentifier('u.param6') . ' = ?', $_GET['city']);
        }
        
        $this->view->rows = 20;
        $this->view->page = isset($_GET['page']) ? $_GET['page'] : '1';    
        $this->view->contListTotal = count($db->fetchAll($select));
        
        $select->limitPage($this->view->page, $this->view->rows);
        $this->view->list = $db->fetchAll($select);
        
        return $this->view->render(TPLDIR . '@club/club_users_standalone.tpl');
    }
    
    public function usersGenClub()
    {
        $catId = $this->_usersCheck();
        if ($catId === false) {
            return;
        }
        
        if (isset($_GET['psge'])) {
            unset($_GET['page']);
        }
        
        $this->view->cat = $this->_usersGetCat($catId);
        $this->view->list = $this->_usersGetList($catId, 5);
        $this->view->club = $this->_club;
        $this->view->user = $this->_user;
        
        if (empty($this->view->list) && (!isset($this->_user) || $this->_user['email'] != $this->_club['title_alias'])) {
            return;
        }
        
        return $this->view->render(TPLDIR . '@club/club_users_club.tpl');
    }
    
    public function usersGenAll()
    {
        $catId = $this->_usersCheck();
        if ($catId === false) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        $this->view->cat = $this->_usersGetCat($catId);
		//$this->view->rows = 20;
		//$this->view->page = isset($_GET['page']) ? $_GET['page'] : '1';    
		//$this->view->contListTotal = count($this->_usersGetList($catId));
        $this->view->list = $this->_usersGetList($catId);
        $this->view->club = $this->_club;
        $this->view->user = $this->_user;
        
        if (empty($this->view->list) && (!isset($this->_user) || $this->_user['email'] != $this->_club['title_alias'])) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        return $this->view->render(TPLDIR . '@club/club_users_all.tpl');
    }
    
    public function usersGenItem($itemId)
    {
        $catId = $this->_usersCheck();
        if ($catId === false) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        $this->view->cat = $this->_usersGetCat($catId);
        $this->view->club = $this->_club;
        $this->view->user = $this->_user;
        
        $select = $this->_db->select()->from('cms_content')->limit(1);
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', $catId);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $itemId);
        $this->view->item = $this->_db->fetchRow($select);
        
        if (empty($this->view->item)) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        return $this->view->render(TPLDIR . '@club/club_users_item.tpl');
    }
    
    public function usersGenEdit($itemId = null)
    {
        $catId = $this->_usersCheck();
        if ($catId === false) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        $this->view->cat = $this->_usersGetCat($catId);
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
        return $this->view->render(TPLDIR . '@club/club_users_edit.tpl');
    }
    
    public function usersUploadImage($data)
    {
        $this->setUser();
        
        if (empty($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_usersAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || empty($_FILES)) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_usersAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_usersAjaxResponse();
        }
        
        $catId = $this->_usersCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_usersAjaxResponse();
        }
        
        $dirName = 'contents/club' . $this->_club['id'] . '/users';
        if (!file_exists($dirName)) {
            if (!mkdir($dirName, 0777, true)) {
                $this->_ajaxMessages[] = 'ERROR_FILESYSTEM';
                return $this->_usersAjaxResponse();
            }
        }
        $path = $dirName . '/u' . $this->_user['id'] . '_' . md5_file($_FILES['userfile']['tmp_name']) . '.' . substr($_FILES['userfile']['name'], -3);
        
        if (@move_uploaded_file($_FILES['userfile']['tmp_name'], $path)) {
            $this->_ajaxActions[] = 'document.create_users_form.image.value="/' . $path . '"';
            $this->_ajaxActions[] = '$("linkImage").down("img").src="/image.php?i=' . $path . '&t=png&m=fit&w=120&h=120&ca=cen&bg=eaeed7"';
            $this->_ajaxActions[] = '$("linkImage").show()';
            $this->_ajaxMessages[] = 'SUCESS_UPLOAD_FILE';
            return $this->_usersAjaxResponse();
        } else {
            $this->_ajaxMessages[] = 'ERROR_UPLOAD_FILE';
            return $this->_usersAjaxResponse();
        }        
    }
    
    public function usersDeleteImage($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_usersAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || !isset($data['image'])) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_usersAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_usersAjaxResponse();
        }
        
        $catId = $this->_usersCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_usersAjaxResponse();
        }
        
        $fileName = array_pop(explode('/', trim($data['image'], '/')));
        $pathName = 'contents/club' . $this->_club['id'] . '/users/' . $fileName;
        if (!file_exists($pathName)) {
            $this->_ajaxMessages[] = 'ERROR_FILE_NOT_EXISTS';
            return $this->_usersAjaxResponse();
        }
        
        if (!unlink($pathName)) {
            $this->_ajaxMessages[] = 'ERROR_DELETE_FILE';
            return $this->_usersAjaxResponse();
        }
        
        $this->_ajaxActions[] = '$("linkImage").hide()';
        $this->_ajaxActions[] = 'document.create_users_form.image.value=""';
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
        return $this->_usersAjaxResponse();
    }
    
    public function usersSaveItem($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxActions[] = 'document.create_users_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_usersAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId'])) {
            $this->_ajaxActions[] = 'document.create_users_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_usersAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxActions[] = 'document.create_users_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_usersAjaxResponse();
        }
        unset($data['clubId']);
        
        $catId = $this->_usersCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxActions[] = 'document.create_users_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_usersAjaxResponse();
        }
        unset($data['catId']);
        
        if ($data['title'] == '') {
            $this->_ajaxActions[] = 'document.create_users_form.title.setStyle({outline:"2px solid #f00"})';
        }
        if ($data['param1'] == '') {
            $this->_ajaxActions[] = 'document.create_users_form.param1.setStyle({outline:"2px solid #f00"})';
        }
        if ($data['image'] == '') {
            $this->_ajaxActions[] = '$("uploadImage").setStyle({outline:"2px solid #f00"})';
        }
        if ($data['introtext'] == '') {
            $this->_ajaxActions[] = '$("introtext_parent").setStyle({outline:"2px solid #f00"})';
        }
        if (!empty($this->_ajaxActions)) {
            $this->_ajaxActions[] = 'document.create_users_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_REQUIRED_FIELDS';
            return $this->_usersAjaxResponse();
        }
        
        foreach ($data as $k => $v) {
            $data[$k] = iconv('utf-8', 'cp1251', $v);
        }
        $preformattedData = array(
            'parent_id' => $catId,
            'created' => time(),
            'created_by' => '0',
            'publish_up' => time(),
            'publish_down' => '0',
            'published' => '1',
            'checked_out' => '0',
            'checked_out_time' => '0000-00-00 00:00:00',
            'ordering' => '1',
            'hits' => '0',
        );
        if ($data['id'] == '') {
            unset($data['id']);
            if (0 < $this->_db->insert('cms_content', array_merge($preformattedData, $data))) {
                $id = $this->_db->lastInsertId();
                $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/club/club' . $this->_club['id'] . '/users/\"",100)';
                $this->_ajaxMessages[] = 'SUCESS_ADDING_ITEM';
            } else {
                $this->_ajaxActions[] = 'document.create_users_form.enable()';
                $this->_ajaxMessages[] = 'EDDOR_ADDING_ITEM';
            }
        } else {
            $id = $data['id'];
            unset($data['id']);
            if (0 < $this->_db->update('cms_content', array_merge($preformattedData, $data), $this->_db->quoteIdentifier('id') . " = $id")) {
                $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/club/club' . $this->_club['id'] . '/users/\"",100)';
                $this->_ajaxMessages[] = 'SUCESS_SAVING_ITEM';
            } else {
                $this->_ajaxActions[] = 'document.create_users_form.enable()';
                $this->_ajaxMessages[] = 'ERROR_SAVING_ITEM';
            }
        }
        return $this->_usersAjaxResponse();
    }
    
    public function usersDeleteItem($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_usersAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || !isset($data['itemId'])) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_usersAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_usersAjaxResponse();
        }
        
        $catId = $this->_usersCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_usersAjaxResponse();
        }
        
        $item = $this->_usersGetItem($data['itemId']);
        if (empty($item)) {
            $this->_ajaxMessages[] = 'ERROR_ITEM_NOT_FOUND';
            return $this->_usersAjaxResponse();            
        }
        
        @unlink(substr($item['image'], 1));
        
        if (0 < $this->_db->delete('cms_content', $this->_db->quoteIdentifier('id') . ' = ' . $item['id'])) {
            $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/club/club' . $this->_club['id'] . '/users/\"",250)';
            $this->_ajaxMessages[] = 'SUCESS_DELETE_ITEM';
        } else {
            $this->_ajaxMessages[] = 'ERROR_DELETE_ITEM';
        }
        return $this->_usersAjaxResponse();
    }
    
    public function usersRate($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_usersAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || !isset($data['itemId']) || !isset($data['mode']) || !isset($data['count'])) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_usersAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_usersAjaxResponse();
        }
        
        $catId = $this->_usersCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_usersAjaxResponse();
        }
        
        $item = $this->_usersGetItem($data['itemId']);
        if (empty($item)) {
            $this->_ajaxMessages[] = 'ERROR_ITEM_NOT_FOUND';
            return $this->_usersAjaxResponse();            
        }
        
        if ($item['param2'] == '') {
            $rate = 0;
        } else {
            $rate = (int) $item['param2'];
        }
        
        if ($data['mode'] == 'up') {
            $rate += $data['count'];
        }
        
        if ($data['mode'] == 'down' && $rate > 0) {
            $rate -= $data['count'];
        }
        
        if (0 < $this->_db->update('cms_content', array('param2' => "$rate"), $this->_db->quoteIdentifier('id') . ' = ' . $item['id'])) {
            $this->_ajaxActions[] = 'var l = window.location.href; window.location.href = l';
        } else {
            $this->_ajaxActions[] = 'null';
        }
        return $this->_usersAjaxResponse();
    }
    
    public function usersRateToggle($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_usersAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId'])) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_usersAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_usersAjaxResponse();
        }
        
        $catId = $this->_usersCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_usersAjaxResponse();
        }
        
        $item = $this->_usersGetCat($catId);
        
        if ($item['param1'] == 0) {
            $t = 1;
        } else {
            $t = 0;
        }
        
        if (0 < $this->_db->update('cms_categories', array('param1' => "$t"), $this->_db->quoteIdentifier('id') . ' = ' . "$catId")) {
            $this->_ajaxActions[] = 'var l = window.location.href; window.location.href = l';
        } else {
            $this->_ajaxActions[] = 'null';
        }
        return $this->_usersAjaxResponse();
    }
    
    public function usersRateGames($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_usersAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || !isset($data['itemId']) || !isset($data['mode']) || !isset($data['count'])) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_usersAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_usersAjaxResponse();
        }
        
        $catId = $this->_usersCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_usersAjaxResponse();
        }
        
        $item = $this->_usersGetItem($data['itemId']);
        if (empty($item)) {
            $this->_ajaxMessages[] = 'ERROR_ITEM_NOT_FOUND';
            return $this->_usersAjaxResponse();            
        }
        
        if ($item['param3'] == '') {
            $rate = 0;
        } else {
            $rate = (int) $item['param3'];
        }
        
        if ($data['mode'] == 'up') {
            $rate += $data['count'];
        }
        
        if ($data['mode'] == 'down' && $rate > 0) {
            $rate -= $data['count'];
        }
        
        if (0 < $this->_db->update('cms_content', array('param3' => "$rate"), $this->_db->quoteIdentifier('id') . ' = ' . $item['id'])) {
            $this->_ajaxActions[] = 'var l = window.location.href; window.location.href = l';
        } else {
            $this->_ajaxActions[] = 'null';
        }
        return $this->_usersAjaxResponse();
    }
}