<?php

class club
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
        'SUCESS_ADDING_ITEM' => 'Клуб добавлен',
        'SUCESS_SAVING_ITEM' => 'Клуб сохранен',
        'SUCESS_DELETE_ITEM' => 'Клуб удален',
    ); 

    private $_user = null;
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
    public $view;
    
    protected function _clubAjaxResponse()
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
        $this->_ajaxActions[] = '$("create_club_stat").update(' . $messages . ')';
        return implode(';', $this->_ajaxActions);
    }
    
    protected function _clubCheck()
    {
        $select = $this->_db->select()->from('cms_categories', 'id')->limit(1);
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', '0');
        $select->where($this->_db->quoteIdentifier('title_alias') . ' = ?', 'club');
        $catId = $this->_db->fetchOne($select);
        if (empty($catId)) {
            $preformattedData = array(
                'parent_id' => '0',
                'title' => 'Клубы',
                'title_alias' => 'club',
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
    
    protected function _clubGetCat()
    {
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', '0');
        $select->where($this->_db->quoteIdentifier('title_alias') . ' = ?', 'club');
        return $this->_db->fetchRow($select);
    }    
    
    public function _clubGetItem($id)
    {
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', (string) $id);
        return $this->_db->fetchRow($select);
    }    
    
    protected function _clubGetList($catId, $rows = null)
    {
        $select = $this->_db->select()->from(array('c' => 'cms_categories'));
        $select->join(array('u' => 'cms_users'), 'c.title_alias = u.email', array('region' => 'param5', 'city' => 'param6'));
        if (isset($rows)) {
            $page = isset($_GET['page']) ? $_GET['page'] : '1';        
            $select->limitPage($page, $rows);
        }
        $select->where($this->_db->quoteIdentifier('c.parent_id') . ' = ?', $catId);
        if (isset($_GET['region']) && $_GET['region'] != '' && $_GET['region'] != '0' && is_numeric($_GET['region'])) {
            $select->where($this->_db->quoteIdentifier('u.param5') . ' = ?', $_GET['region']);
        }
        if (isset($_GET['city']) && $_GET['city'] != '' && $_GET['city'] != '0' && is_numeric($_GET['city'])) {
            $select->where($this->_db->quoteIdentifier('u.param6') . ' = ?', $_GET['city']);
        }
        
        return $this->_db->fetchAll($select);
    }
    
    public function __construct()
    {
        $this->_db = $GLOBALS['db'];
        $this->view = new view();
        $this->view->constants = $this->_const;
        $this->view->month = $this->_month;
        
        // онфо о пользователе
        if (isset($_SESSION['auth']['user_id'])) {
            $select = $this->_db->select()->from('cms_users');
            $select->where($this->_db->quoteIdentifier('id') . ' = ?', $_SESSION['auth']['user_id'])->limit(1);
            $this->view->user = $this->_db->fetchRow($select);
        }
        
        $typed = new typedcontent();
        $this->view->centerBanner = $typed->get('centerBanner');
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
    
    public function route($url)
    {
        $admin = new club_admin();
        if (!isset($url[1])) {
            return $this->clubGenAll();
        } else if ($url[1] == 'add') {
            return $this->clubGenEdit();
        } else if ($url[1] == 'passrepair') {
            return $admin->adminGenPass();
        } else if ($url[1] == 'confirm') {
            return $admin->adminGenConfirm();
        } else if ($url[1] == 'register') {
            if (!empty($this->view->user)) {
                header("Location: http://" . $_SERVER["HTTP_HOST"] . '/club/admin');
                return $this->view->render(TPLDIR . '404.tpl');
            }            
            return $admin->adminGenEdit();
        } else if ($url[1] == 'admin') {
            if (empty($this->view->user)) {
                header("Location: http://" . $_SERVER["HTTP_HOST"] . '/club/register');
                return $this->view->render(TPLDIR . '404.tpl');
            }            
            return $admin->adminGenEdit();
        } else if (substr(strtolower($url[1]), 0, 4) == 'club') {
            $itemId = substr($url[1], 4);
            $club = $this->_clubGetItem($itemId);
            if (!isset($url[2])) {
                return $this->clubGenItem($itemId);
            } else if ($url[2] == 'edit') {
                return $this->clubGenEdit($itemId);
            } else if ($url[2] == 'users') {
                $users = new club_users();
                $users->setClub($club);
                $users->setUser($this->view->user);
                return $users->usersRoute($url);
            } else if ($url[2] == 'news') {
                $news = new club_news();
                $news->setClub($club);
                $news->setUser($this->view->user);
                return $news->newsRoute($url);
            } else if ($url[2] == 'afishi') {
                $afishi = new club_afishi();
                $afishi->setClub($club);
                $afishi->setUser($this->view->user);
                return $afishi->afishiRoute($url);
            } else if ($url[2] == 'photos') {
                $photos = new club_photos();
                $photos->setClub($club);
                $photos->setUser($this->view->user);
                return $photos->photosRoute($url);
            } else if ($url[2] == 'videos') {
                $videos = new club_videos();
                $videos->setClub($club);
                $videos->setUser($this->view->user);
                return $videos->videosRoute($url);
            }
        }
    }
    
    public function clubGenItem($itemId)
    {
        $content = new content();
        $users = new users();
        $catId = $this->_clubCheck();
        if ($catId === false) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        $this->view->club = $this->_clubGetItem($itemId);
        if (empty($this->view->club)) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        $this->view->admin = $users->get_byemail($this->view->club['title_alias']);
        if (!empty($this->view->admin)) {
            $city = $content->get_cont($this->view->admin['param6']);
            $this->view->admin['param6'] = $city['title'];
        }
        
                
        // новости клуба
        $news = new club_news();
        $news->setClub($this->view->club);
        $news->setUser($this->view->user);
        $this->view->news = $news->newsGenClub();
        
        // участники клуба
        $users = new club_users();
        $users->setClub($this->view->club);
        $users->setUser($this->view->user);
        $this->view->users = $users->usersGenClub();
        
        // афиши клуба
        $afishi = new club_afishi();
        $afishi->setClub($this->view->club);
        $afishi->setUser($this->view->user);
        $this->view->afishi = $afishi->afishiGenClub();
        
        // фото клуба
        $photos = new club_photos();
        $photos->setClub($this->view->club);
        $photos->setUser($this->view->user);
        $this->view->photos = $photos->photosGenClub();
        
        // видео клуба
        $videos = new club_videos();
        $videos->setClub($this->view->club);
        $videos->setUser($this->view->user);
        $this->view->videos = $videos->videosGenClub();
        
        return $this->view->render(TPLDIR . '@club/club_e_item.tpl');
    }
    
    public function clubGenAll()
    {
        $users = new users();
        $content = new content();
        $frontpage = new frontpage();
        $catId = $this->_clubCheck();
        if ($catId === false) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        $this->view->cat = $this->_clubGetCat($catId);
        $this->view->list = $this->_clubGetList($catId, 16);
        $this->view->user = $this->_user;
        $this->view->filter = $frontpage->genFilter();
        
        if (empty($this->view->list)) {
            //return $this->view->render(TPLDIR . '404.tpl');
        }
        foreach ($this->view->list as &$item) {
            $owner = $users->get_byemail($item['title_alias']);
            if (!empty($owner)) {
                $item['city'] = $content->get_cont($owner['param6']);
            }
        }
        
        return $this->view->render(TPLDIR . '@club/club_e_all.tpl');
    }
    
    public function clubGenEdit($itemId = null)
    {
        $this->_clubCheck();
        $this->setUser();
        $this->view->user = $this->_user;
        
        if (!isset($this->_user)) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        if (isset($itemId)) {
            $this->view->item = $this->_clubGetItem($itemId);
            
            if (empty($this->view->item) || $this->_user['email'] != $this->view->item['title_alias']) {
                return $this->view->render(TPLDIR . '404.tpl');
            }
        }
        return $this->view->render(TPLDIR . '@club/club_e_edit.tpl');
    }
    
    public function clubSaveItem($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxActions[] = 'document.create_club_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_clubAjaxResponse();
        }
        
        $catId = $this->_clubCheck();
        if ($catId === false) {
            $this->_ajaxActions[] = 'document.create_club_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_clubAjaxResponse();
        }
        
        if ($data['id'] != '') {
            $item = $this->_clubGetItem($data['id']);
            if (empty($item)) {
                $this->_ajaxActions[] = 'document.create_club_form.enable()';
                $this->_ajaxMessages[] = 'ERROR_ITEM_NOT_FOUND';
                return $this->_clubAjaxResponse();
            }
        }
        
        if (!empty($item) && $item['title_alias'] != $this->_user['email']) {
            $this->_ajaxActions[] = 'document.create_club_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_clubAjaxResponse();
        }
        
        if ($data['title'] == '') {
            $this->_ajaxActions[] = 'document.create_club_form.title.setStyle({outline:"2px solid #f00"})';
        }
        if ($data['id'] != '' && $data['image'] == '') {
            $this->_ajaxActions[] = '$("uploadImage").setStyle({outline:"2px solid #f00"})';
        }
        if ($data['description'] == '') {
            $this->_ajaxActions[] = '$("description_parent").setStyle({outline:"2px solid #f00"})'; //fot mce editor field
        }
        if (!empty($this->_ajaxActions)) {
            $this->_ajaxActions[] = 'document.create_club_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_REQUIRED_FIELDS';
            return $this->_clubAjaxResponse();
        }
        
        foreach ($data as $k => $v) {
            $data[$k] = iconv('utf-8', 'cp1251', $v);
        }
        $preformattedData = array(
            'parent_id' => $catId,
            'title_alias' => $this->view->user['email'],
            'published' => '1',
            'checked_out' => '0',
            'checked_out_time' => '0000-00-00 00:00:00',
            'ordering' => '1',
        );
        if ($data['id'] == '') {
            unset($data['id']);
            if (0 < $this->_db->insert('cms_categories', array_merge($preformattedData, $data))) {
                $id = $this->_db->lastInsertId();
                $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/club/club' . $id . '/edit\"",100)';
                $this->_ajaxMessages[] = 'SUCESS_ADDING_ITEM';
            } else {
                $this->_ajaxActions[] = 'document.create_club_form.enable()';
                $this->_ajaxMessages[] = 'EDDOR_ADDING_ITEM';
            }
        } else {
            $id = $data['id'];
            unset($data['id']);
            if (0 < $this->_db->update('cms_categories', array_merge($preformattedData, $data), $this->_db->quoteIdentifier('id') . " = $id")) {
                $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/club/club' . $id . '\"",100)';
                $this->_ajaxMessages[] = 'SUCESS_SAVING_ITEM';
            } else {
                $this->_ajaxActions[] = 'document.create_club_form.enable()';
                $this->_ajaxMessages[] = 'ERROR_SAVING_ITEM';
            }
        }
        return $this->_clubAjaxResponse();
    }
    
    public function clubUploadImage($data)
    {
        $this->setUser();
        
        if (empty($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_clubAjaxResponse();
        }
        
        if (!isset($data['itemId']) || $data['itemId'] == '' || empty($_FILES)) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_clubAjaxResponse();
        }
        
        $catId = $this->_clubCheck();
        if ($catId === false) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_clubAjaxResponse();
        }
        
        $item = $this->_clubGetItem($data['itemId']);
        if (empty($item)){
            $this->_ajaxMessages[] = 'ERROR_ITEM_NOT_FOUND';
            return $this->_clubAjaxResponse();
        }        
        
        if ($item['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_clubAjaxResponse();
        }
        
        $dirName = 'contents/club' . $item['id'];
        if (!file_exists($dirName)) {
            if (!mkdir($dirName, 0777, true)) {
                $this->_ajaxMessages[] = 'ERROR_FILESYSTEM';
                return $this->_clubAjaxResponse();
            }
        }
        $path = $dirName . '/u' . $this->_user['id'] . '_' . md5_file($_FILES['userfile']['tmp_name']) . '.' . substr($_FILES['userfile']['name'], -3);
        
        if (@move_uploaded_file($_FILES['userfile']['tmp_name'], $path)) {
            $this->_ajaxActions[] = 'document.create_club_form.image.value="/' . $path . '"';
            $this->_ajaxActions[] = '$("linkImage").down("img").src="/image.php?i=' . $path . '&t=png&m=fit&w=120&h=120&ca=cen&bg=eaeed7"';
            $this->_ajaxActions[] = '$("linkImage").show()';
            $this->_ajaxMessages[] = 'SUCESS_UPLOAD_FILE';
            return $this->_clubAjaxResponse();
        } else {
            $this->_ajaxMessages[] = 'ERROR_UPLOAD_FILE';
            return $this->_clubAjaxResponse();
        }        
    }
    
    public function clubDeleteImage($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_clubAjaxResponse();
        }
        
        if (!isset($data['itemId']) || !isset($data['image'])) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_clubAjaxResponse();
        }
        
        $catId = $this->_clubCheck();
        if ($catId === false) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_clubAjaxResponse();
        }
        
        $item = $this->_clubGetItem($data['itemId']);
        if (empty($item)){
            $this->_ajaxMessages[] = 'ERROR_ITEM_NOT_FOUND';
            return $this->_clubAjaxResponse();
        }        
        
        if ($item['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_clubAjaxResponse();
        }
                
        $fileName = array_pop(explode('/', trim($data['image'], '/')));
        $pathName = 'contents/club' . $item['id'] . '/' . $fileName;
        if (!file_exists($pathName)) {
            $this->_ajaxMessages[] = 'ERROR_FILE_NOT_EXISTS';
            return $this->_clubAjaxResponse();
        }
        
        if (!unlink($pathName)) {
            $this->_ajaxMessages[] = 'ERROR_DELETE_FILE';
            return $this->_clubAjaxResponse();
        }
        
        $this->_ajaxActions[] = '$("linkImage").hide()';
        $this->_ajaxActions[] = 'document.create_club_form.image.value=""';
        $this->_ajaxActions[] = '$("linkImage").down("img").src=""';
        $this->_ajaxMessages[] = 'SUCESS_DELETE_FILE';
        if (!empty($data['itemId'])) {
            $preformattedData = array(
                'images' => '',
                'title_alias' => $this->_user['email']
            );
            if (0 == $this->_db->update('cms_content', $preformattedData, $this->_db->quoteIdentifier('id') . " = " . $data['itemId'])) {
                $this->_ajaxMessages[] = 'ERROR_DELETE_ITEM';
            }            
        }
        return $this->_clubAjaxResponse();
    }
}