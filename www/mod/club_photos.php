<?php

class club_photos
{
    public $_const = array(
        'ERROR_SESSION_EXPIRED' => '������ �������, ���������� ������������� ������',
        'ERROR_POST_DATA' => '������ ��������� ������',
        'ERROR_EVENT_DENIED' => '�������� ���������',
        'ERROR_DATABASE' => '������ ��',
        'ERROR_FILESYSTEM' => '������ �������� �������',
        'ERROR_UPLOAD_FILE' => '������ ��������',
        'ERROR_FILE_NOT_EXISTS' => '����� �� ����������',
        'ERROR_FILE_EXISTS' => '���� ��� ��������',
        'ERROR_DELETE_FILE' => '������ ��������',
        'ERROR_REQUIRED_FIELDS' => '��������� ���� ��������� �������',
        'EDDOR_ADDING_ITEM' => '������ ����������',
        'ERROR_SAVING_ITEM' => '������ ����������',
        'ERROR_ITEM_NOT_FOUND' => '������� �� ������',
        'ERROR_DELETE_ITEM' => '������ �������� �� ��',
        'SUCESS_UPLOAD_FILE' => '���� ������� ��������',
        'SUCESS_DELETE_FILE' => '���� ������� ������',
        'SUCESS_ADDING_ITEM' => '���������� ��������',
        'SUCESS_SAVING_ITEM' => '���������� ��������',
        'SUCESS_DELETE_ITEM' => '���������� ������',
    );
    
    private $_user = null;
    private $_club;
    private $_db;
    private $_month = array(
        1 => '������',
        '�������',
        '����',
        '������',
        '���',
        '����',
        '����',
        '������',
        '��������',
        '�������',
        '������',
        '�������'
    );
    protected $_ajaxActions = array();
    protected $_ajaxMessages = array();    
    public $view;
    
    protected function _photosAjaxResponse()
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
        $this->_ajaxActions[] = '$("create_photos_stat").update(' . $messages . ')';
        return implode(';', $this->_ajaxActions);
    }
    
    protected function _photosCheck()
    {
        $select = $this->_db->select()->from('cms_categories', 'id')->limit(1);
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', $this->_club['id']);
        $select->where($this->_db->quoteIdentifier('title_alias') . ' = ?', 'club_photos');
        $catId = $this->_db->fetchOne($select);
        if (empty($catId)) {
            $preformattedData = array(
                'parent_id' => $this->_club['id'],
                'title' => '����������� �����',
                'title_alias' => 'club_photos',
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
    
    protected function _photosGetCat($catId)
    {
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $catId);
        return $this->_db->fetchRow($select);
    }    
    
    protected function _photosGetList($catId, $rows = null)
    {
        $select = $this->_db->select()->from('cms_content');
        if (isset($rows)) {
            $page = isset($_GET['page']) ? $_GET['page'] : '1';        
            $select->limitPage($page, $rows);
        }
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', $catId);
        return $this->_db->fetchAll($select);
    }
    
    protected function _photosGetItem($id)
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
    
    public function photosRoute($url)
    {
        if (!isset($this->_club)) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        if (!isset($url[3])) {
            return $this->photosGenAll();
        } else if ($url[3] == 'add') {
            return $this->photosGenEdit();
        } else if (substr(strtolower($url[3]), 0, 5) == 'photo') {
            $itemId = substr($url[3], 5);
            if (isset($url[4])) {
                if ($url[4] != 'edit') {
                    return $this->view->render(TPLDIR . '404.tpl');
                }
                return $this->photosGenEdit($itemId);
            }
            return $this->photosGenItem($itemId);
        }
    }
    public function photosGenClub()
    {
        $catId = $this->_photosCheck();
        if ($catId === false) {
            return;
        }
        
        if (isset($_GET['psge'])) {
            unset($_GET['page']);
        }
        
        $this->view->cat = $this->_photosGetCat($catId);
        $this->view->list = $this->_photosGetList($catId, 4);
        $this->view->club = $this->_club;
        $this->view->user = $this->_user;
        
        if (empty($this->view->list) && (!isset($this->_user) || $this->_user['email'] != $this->_club['title_alias'])) {
            return;
        }
        
        return $this->view->render(TPLDIR . '@club/club_photos_club.tpl');
    }
    
    public function photosGenAll()
    {
        $catId = $this->_photosCheck();
        if ($catId === false) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        $this->view->cat = $this->_photosGetCat($catId);
        $this->view->list = $this->_photosGetList($catId, 16);
        $this->view->club = $this->_club;
        $this->view->user = $this->_user;
        
        if (empty($this->view->list) && (!isset($this->_user) || $this->_user['email'] != $this->_club['title_alias'])) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        return $this->view->render(TPLDIR . '@club/club_photos_all.tpl');
    }
    
    public function photosGenItem($itemId)
    {
        $catId = $this->_photosCheck();
        if ($catId === false) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        $this->view->cat = $this->_photosGetCat($catId);
        $this->view->club = $this->_club;
        $this->view->user = $this->_user;
        
        $select = $this->_db->select()->from('cms_content')->limit(1);
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', $catId);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $itemId);
        $this->view->item = $this->_db->fetchRow($select);
        
        if (empty($this->view->item)) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        return $this->view->render(TPLDIR . '@club/club_photos_item.tpl');
    }
    
    public function photosGenEdit($itemId = null)
    {
        $catId = $this->_photosCheck();
        if ($catId === false) {
            return $this->view->render(TPLDIR . '404.tpl');
        }
        
        $this->view->cat = $this->_photosGetCat($catId);
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
        return $this->view->render(TPLDIR . '@club/club_photos_edit.tpl');
    }
    
    public function photosUploadImage($data)
    {
        $this->setUser();
        
        if (empty($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_photosAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || empty($_FILES)) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_photosAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_photosAjaxResponse();
        }
        
        $catId = $this->_photosCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_photosAjaxResponse();
        }
        
        $dirName = 'contents/club' . $this->_club['id'] . '/photos';
        if (!file_exists($dirName)) {
            if (!mkdir($dirName, 0777, true)) {
                $this->_ajaxMessages[] = 'ERROR_FILESYSTEM';
                return $this->_photosAjaxResponse();
            }
        }
        $path = $dirName . '/u' . $this->_user['id'] . '_' . md5_file($_FILES['userfile']['tmp_name']) . '.' . substr($_FILES['userfile']['name'], -3);
        
        if (@move_uploaded_file($_FILES['userfile']['tmp_name'], $path)) {
            $this->_ajaxActions[] = 'document.create_photos_form.image.value="/' . $path . '"';
            $this->_ajaxActions[] = '$("linkImage").down("img").src="/image.php?i=' . $path . '&t=png&m=fit&w=120&h=120&ca=cen&bg=eaeed7"';
            $this->_ajaxActions[] = '$("linkImage").show()';
            $this->_ajaxMessages[] = 'SUCESS_UPLOAD_FILE';
            return $this->_photosAjaxResponse();
        } else {
            $this->_ajaxMessages[] = 'ERROR_UPLOAD_FILE';
            return $this->_photosAjaxResponse();
        }        
    }
    
    public function photosUploadImages($data)
    {
        $this->setUser();
        
        if (empty($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_photosAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || !isset($data['itemId']) || empty($_FILES)) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_photosAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_photosAjaxResponse();
        }
        
        $catId = $this->_photosCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_photosAjaxResponse();
        }
        
        $dirName = 'contents/club' . $this->_club['id'] . '/photos/photo' . $data['itemId'];
        if (!file_exists($dirName)) {
            if (!mkdir($dirName, 0777, true)) {
                $this->_ajaxMessages[] = 'ERROR_FILESYSTEM';
                return $this->_photosAjaxResponse();
            }
        }
        $path = $dirName . '/u' . $this->_user['id'] . '_' . md5_file($_FILES['userfile']['tmp_name']) . '.' . substr($_FILES['userfile']['name'], -3);
        
        if (@move_uploaded_file($_FILES['userfile']['tmp_name'], $path)) {
            $this->_ajaxActions[] = 'updateImages("/' . $path . '")';
            $this->_ajaxMessages[] = 'SUCESS_UPLOAD_FILE';
            return $this->_photosAjaxResponse();
        } else {
            $this->_ajaxMessages[] = 'ERROR_UPLOAD_FILE';
            return $this->_photosAjaxResponse();
        }        
    }
    
    public function photosDeleteImage($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_photosAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || !isset($data['image'])) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_photosAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_photosAjaxResponse();
        }
        
        $catId = $this->_photosCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_photosAjaxResponse();
        }
        
        $fileName = array_pop(explode('/', trim($data['image'], '/')));
        $pathName = 'contents/club' . $this->_club['id'] . '/photos/' . $fileName;
        if (!file_exists($pathName)) {
            $this->_ajaxMessages[] = 'ERROR_FILE_NOT_EXISTS';
            return $this->_photosAjaxResponse();
        }
        
        if (!unlink($pathName)) {
            $this->_ajaxMessages[] = 'ERROR_DELETE_FILE';
            return $this->_photosAjaxResponse();
        }
        
        $this->_ajaxActions[] = '$("linkImage").hide()';
        $this->_ajaxActions[] = 'document.create_photos_form.image.value=""';
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
        return $this->_photosAjaxResponse();
    }
    
    
    //TODO: uncomplete
    public function photosDeleteImages($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_photosAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || !isset($data['itemId']) || !isset($data['image'])) {
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_photosAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_photosAjaxResponse();
        }
        
        $catId = $this->_photosCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_photosAjaxResponse();
        }
        $fileName = array_pop(explode('/', trim($data['image'], '/')));
        $pathName = 'contents/club' . $this->_club['id'] . '/photos/photo' . $data['itemId'] . '/' . $fileName;
        if (!file_exists($pathName)) {
            $this->_ajaxMessages[] = 'ERROR_FILE_NOT_EXISTS';
            return $this->_photosAjaxResponse();
        }
        
        $item = $this->_photosGetItem($data['itemId']);
        if (empty($item)) {
            $this->_ajaxMessages[] = 'ERROR_ITEM_NOT_FOUND';
            return $this->_photosAjaxResponse();
        }
        $images = explode('|', $item['images']);
        $images2 = array();
        $images3 = '';
        foreach ($images as $image) {
            if ($image != $data['image']) {
                $images2[] = $image;
            }
        }
        $images3 = count($images2)>0 ? implode('|', $images2) : '';
        
        if (!unlink($pathName)) {
            $this->_ajaxMessages[] = 'ERROR_DELETE_FILE';
            return $this->_photosAjaxResponse();
        }
        
        $preformattedData = array(
            'images' => $images3,
            'publish_up' => time()
        );
        
        if (0 == $this->_db->update('cms_content', $preformattedData, $this->_db->quoteIdentifier('id') . " = " . $data['itemId'])) {
            $this->_ajaxMessages[] = 'ERROR_DELETE_ITEM';
            return $this->_photosAjaxResponse();
        }
        $this->_ajaxActions[] = 'state=club_photos.SUCESS_DELETE_FILE';
        $this->_ajaxActions[] = 'updateImages()';
        $this->_ajaxMessages[] = 'SUCESS_DELETE_FILE';
        return $this->_photosAjaxResponse();
    }
    
    public function photosSaveItem($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxActions[] = 'document.create_photos_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_photosAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId'])) {
            $this->_ajaxActions[] = 'document.create_photos_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_photosAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxActions[] = 'document.create_photos_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_photosAjaxResponse();
        }
        unset($data['clubId']);
        
        $catId = $this->_photosCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxActions[] = 'document.create_photos_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_photosAjaxResponse();
        }
        unset($data['catId']);
        
        if ($data['title'] == '') {
            $this->_ajaxActions[] = 'document.create_photos_form.title.setStyle({outline:"2px solid #f00"})';
        }
        if ($data['image'] == '') {
            $this->_ajaxActions[] = '$("uploadImage").setStyle({outline:"2px solid #f00"})';
        }
        if ($data['id'] != '' && $data['images'] == '') {
            $this->_ajaxActions[] = '$("uploadImages").setStyle({outline:"2px solid #f00"})';
        }
        if (!empty($this->_ajaxActions)) {
            $this->_ajaxActions[] = 'document.create_photos_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_REQUIRED_FIELDS';
            return $this->_photosAjaxResponse();
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
                $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/club/club' . $this->_club['id'] . '/photos/photo' . $id . '/edit\"",100)';
                $this->_ajaxMessages[] = 'SUCESS_ADDING_ITEM';
            } else {
                $this->_ajaxActions[] = 'document.create_photos_form.enable()';
                $this->_ajaxMessages[] = 'EDDOR_ADDING_ITEM';
            }
        } else {
            $id = $data['id'];
            unset($data['id']);
            if (0 < $this->_db->update('cms_content', array_merge($preformattedData, $data), $this->_db->quoteIdentifier('id') . " = $id")) {
                $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/club/club' . $this->_club['id'] . '/photos/\"",100)';
                $this->_ajaxMessages[] = 'SUCESS_SAVING_ITEM';
            } else {
                $this->_ajaxActions[] = 'document.create_photos_form.enable()';
                $this->_ajaxMessages[] = 'ERROR_SAVING_ITEM';
            }
        }
        return $this->_photosAjaxResponse();
    }
    
    public function photosDeleteItem($data)
    {
        $this->setUser();
        
        if (!isset($this->_user)) {
            $this->_ajaxMessages[] = 'ERROR_SESSION_EXPIRED';
            return $this->_photosAjaxResponse();
        }
        
        if (!isset($data['clubId']) || !isset($data['catId']) || !isset($data['itemId'])) {
            return $this->_photosAjaxResponse();
        }
        
        $select = $this->_db->select()->from('cms_categories')->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $data['clubId']);
        $this->_club = $this->_db->fetchRow($select);
        
        if (empty($this->_club) || $this->_club['title_alias'] != $this->_user['email']) {
            $this->_ajaxMessages[] = 'ERROR_EVENT_DENIED';
            return $this->_photosAjaxResponse();
        }
        
        $catId = $this->_photosCheck();
        if ($catId === false || $catId != $data['catId']) {
            $this->_ajaxMessages[] = 'ERROR_DATABASE';
            return $this->_photosAjaxResponse();
        }
        
        $item = $this->_photosGetItem($data['itemId']);
        if (empty($item)) {
            $this->_ajaxMessages[] = 'ERROR_ITEM_NOT_FOUND';
            return $this->_photosAjaxResponse();            
        }
        
        @unlink(substr($item['image'], 1));
        
        if (0 < $this->_db->delete('cms_content', $this->_db->quoteIdentifier('id') . ' = ' . $item['id'])) {
            $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/club/club' . $this->_club['id'] . '/photos/\"",250)';
            $this->_ajaxMessages[] = 'SUCESS_DELETE_ITEM';
        } else {
            $this->_ajaxMessages[] = 'ERROR_DELETE_ITEM';
        }
        return $this->_photosAjaxResponse();
    }
}