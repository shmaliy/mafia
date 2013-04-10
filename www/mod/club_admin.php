<?php

class club_admin
{
    public $_const = array(
        'ERROR_SESSION_EXPIRED' => '������ �������, ���������� ������������� ������',
        'ERROR_POST_DATA' => '������ ��������� ������',
        'ERROR_EVENT_DENIED' => '�������� ���������',
        'ERROR_DATABASE' => '������ ��',
        'ERROR_USER_EXISTS' => '������������ � ����� email ��� ����������',
        'ERROR_USER_BLOCKED' => '��� ������ ������������',
        'ERROR_DELETE_FILE' => '������ ��������',
        'ERROR_REQUIRED_FIELDS' => '��������� ���� ��������� �������',
        'ERROR_ADDING_ITEM' => '������ �����������',
        'ERROR_SEND_MESSAGE' => '������ �������� ������,<br />�������� ����������� ������ �����',
        'ERROR_SAVING_ITEM' => '������ ����������',
        'ERROR_USER_NOT_FOUND' => '������������ � ����� E-mail �� ���������������',
        'ERROR_AUTHORIZE' => '������� ������������ �����/������',
        'ERROR_REPAIR_PASS' => '������ �������������� ������',
        'SUCESS_AUTHORIZE' => '����������� ������ �������',
        'SUCESS_REPAIR_PASS' => '�� ���� ����������� ����� ���������� ������ � ����� �������',
        'SUCESS_ADDING_ITEM' => '����������� ������ �������<br />�� ���� ����������� ����� ���������� ������ � ��������������',
        'SUCESS_SAVING_ITEM' => '���������� ���������',
    );
    
    private $_user = null;
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
    
    protected function _adminAjaxResponse($div = 'club_admin_stat')
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
        $this->_ajaxActions[] = '$("' . $div . '").update(' . $messages . ')';
        return implode(';', $this->_ajaxActions);
    }
    
    
    public function __construct()
    {
        $this->view = new view();
        $this->_db = $GLOBALS['db'];
        $this->view->constants = $this->_const;
        $this->view->month = $this->_month;
        $this->setUser();
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
    
    public function adminGenAuth()
    {
        $this->setUser();
        if ($this->_user){
            $select = $this->_db->select()->from('cms_categories')->limit(1);
            $select->where($this->_db->quoteIdentifier('title_alias') . ' = ?', $this->_user['email']);
            $select->where($this->_db->quoteIdentifier('published') . ' = ?', '1');        
            $this->view->club = $this->_db->fetchRow($select);
            
            return $this->view->render(TPLDIR . '@club/club_admin_authorized.tpl');
        }else{
            return $this->view->render(TPLDIR . '@club/club_admin_non_authorized.tpl');
        }
    }
    
    public function adminAuthorization($data)
    {
        $users = new users();
        
        if ($data['email'] == '') {
            $this->_ajaxActions[] = 'document.auth.email.setStyle({outline:"2px solid #f00"})';
        }
        if ($data['password'] == '') {
            $this->_ajaxActions[] = 'document.auth.password.setStyle({outline:"2px solid #f00"})';
        }
        if (!empty($this->_ajaxActions)) {
            $this->_ajaxMessages[] = 'ERROR_REQUIRED_FIELDS';
            return $this->_adminAjaxResponse("auth_stat");
        }
                
        $user = $users->get_authorized($data['email'], $data['password']);
        if ($user){
            if ($user['block'] == '0'){
                $_SESSION['auth']['authorized'] = 1;
                $_SESSION['auth']['user_id'] = $user['id'];
                $this->_ajaxActions[] = 'setTimeout("window.location.href=\"' . $data['location'] . '\"",500)';
                $this->_ajaxMessages[] = 'SUCESS_AUTHORIZE';
            }else{
                unset($_SESSION['auth']);
                $this->_ajaxMessages[] = 'ERROR_USER_BLOCKED';
            }
        }else{
            unset($_SESSION['auth']);
            $this->_ajaxMessages[] = 'ERROR_AUTHORIZE';
        }
        return $this->_adminAjaxResponse("auth_stat");
    }
    
    public function adminLogout($data){
        unset($_SESSION['auth']);       
        return 'window.location.href="' . $data['location'] . '";';
    }
    
    public function adminGenPass()
    {
        return $this->view->render(TPLDIR . '@club/club_admin_passrepair.tpl');
    }
    
    public function adminRepairPass($data)
    {
        $users = new users();
        if ($data['email'] == '') {
            $this->_ajaxActions[] = 'document.password_repair_form.email.setStyle({outline:"2px solid #f00"})';
        }
        if (!empty($this->_ajaxActions)) {
            $this->_ajaxMessages[] = 'ERROR_REQUIRED_FIELDS';
            return $this->_adminAjaxResponse();
        }
        $user = $users->get_byemail($data['email']);
        if (empty($user)) {
            $this->_ajaxMessages[] = 'ERROR_USER_NOT_FOUND';
            return $this->_adminAjaxResponse();
        }
        
        $link = 'http://' . $_SERVER['HTTP_HOST'];
        $password = generate_password(10);
        if (0 < $this->_db->update('cms_users', array('password' => md5($password)), $this->_db->quoteIdentifier('id') . " = " . $user['id'])) {
            $message = "����� ������ ��� ����� <a href=\"$link\" target=\"blank\">$link<a>: $password";
            $headers = 'MIME-Version: 1.0' . "\r\n"
                     . 'Content-type: text/html; charset="windows-1251"' . "\r\n";
            if (@mail($email, "�������������� ������", $message, $headers)) {
                $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/\"",500)';
                $this->_ajaxMessages[] = 'SUCESS_REPAIR_PASS';
            } else {
                $this->_ajaxMessages[] = 'ERROR_SEND_MESSAGE';
            }
        } else {
            $this->_ajaxMessages[] = 'ERROR_REPAIR_PASS';
        }
        return $this->_adminAjaxResponse();
    }
    
    public function adminGenConfirm()
    {
        $users = new users();
        if (!isset($_GET['hash'])) {
            header("Location: http://" . $_SERVER["HTTP_HOST"]);
        }
        
        $user = $users->get_bymd5($_GET['hash'], 'email');
        if (empty($user)) {
            $this->view->status = $this->view->render(TPLDIR . 'message_error.tpl', array('message' => '������������ � ����� E-mail �� ������'));
            return $this->view->render(TPLDIR . '@club/club_admin_confirm.tpl');
        }
        
        $user = $users->get($user['id']);
        if ($user['param10'] != '0') {
            $this->view->status = $this->view->render(TPLDIR . 'message_warning.tpl', array('message' => '�� ��� ����������� �����������'));
            return $this->view->render(TPLDIR . '@club/club_admin_confirm.tpl');
        }
        
        if (0 < $this->_db->update('cms_users', array('param10' => '1'), $this->_db->quoteIdentifier('id') . " = " . $user['id'])) {
            $this->view->status = $this->view->render(TPLDIR . 'message_sucess.tpl', array('message' => '�� ����������� �����������'));
            return $this->view->render(TPLDIR . '@club/club_admin_confirm.tpl');
        } else {
            $this->view->status = $this->view->render(TPLDIR . 'message_error.tpl', array('message' => '������ �������������'));
            return $this->view->render(TPLDIR . '@club/club_admin_confirm.tpl');
        }
    }
    
    public function adminGenEdit()
    {
        $content = new content();
        $filterCat = $content->_find_cat(array('filter'));        
        $select = $this->_db->select()->from('cms_categories', array('id', 'parent_id', 'title'));
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', $filterCat['id']);
        $select->where($this->_db->quoteIdentifier('published') . ' = ?', '1');        
        $filterCatList = $this->_db->fetchAll($select);        
        
        if (!empty($filterCatList)) {
            $sql = array();
            $select = $this->_db->select()->from('cms_content');
            foreach ($filterCatList as $parent) {
                $sql[] = $this->_db->quoteIdentifier('parent_id') . ' = ' . $parent['id'];
            }
            $select->where(implode(' OR ', $sql));
            $select->where($this->_db->quoteIdentifier('published') . ' = ?', '1');        
            $filterContList = $this->_db->fetchAll($select);
            
            foreach ($filterCatList as &$parent) {
                $return[$parent['id']]['title'] = $parent['title'];
                if (!empty($filterContList)) {
                    $return[$parent['id']]['childs'] = array();
                    foreach ($filterContList as $item) {
                        if ($parent['id'] == $item['parent_id']) {
                            $return[$parent['id']]['childs'][$item['id']] = $item['title'];
                        }
                    }
                }
            }            
        }
        $this->view->filter = $return;
        $this->view->item = $this->_user;
        return $this->view->render(TPLDIR . '@club/club_admin_edit.tpl');
    }
    
    public function adminSaveItem($data)
    {
        $users = new users();
        $this->setUser();
        
        if (!isset($data['userId']) || $data['userId'] == '' && !isset($data['email'])) {
            $this->_ajaxActions[] = 'document.create_admin_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_POST_DATA';
            return $this->_adminAjaxResponse();
        }
        
        if (isset($data['email']) && $data['email'] != '') {
            $user = $users->get_byemail($data['email']);
            if (!empty($user)) {
                $this->_ajaxActions[] = 'document.create_admin_form.enable()';
                $this->_ajaxActions[] = 'document.create_admin_form.email.setStyle({outline:"2px solid #f00"})';
                $this->_ajaxMessages[] = 'ERROR_USER_EXISTS';
                return $this->_adminAjaxResponse();
            }
        }
        if (isset($data['email']) && $data['email'] == '') {
            $this->_ajaxActions[] = 'document.create_admin_form.email.setStyle({outline:"2px solid #f00"})';
        }
        if (isset($data['password']) && $data['password'] == '') {
            $this->_ajaxActions[] = 'document.create_admin_form.password.setStyle({outline:"2px solid #f00"})';
        }
        // �������
        if ($data['param1'] == '') {
            $this->_ajaxActions[] = 'document.create_admin_form.param1.setStyle({outline:"2px solid #f00"})';
        }
        // ���
        if ($data['param2'] == '') {
            $this->_ajaxActions[] = 'document.create_admin_form.param2.setStyle({outline:"2px solid #f00"})';
        }
        // ��������
        /*if ($data['param3'] == '') {
            $this->_ajaxActions[] = 'document.create_admin_form.param3.setStyle({outline:"2px solid #f00"})';
        }*/
		$data['param3'] = '';
        // �������
        if ($data['param4'] == '') {
            $this->_ajaxActions[] = 'document.create_admin_form.param4.setStyle({outline:"2px solid #f00"})';
        }
        // �������
        if ($data['param5'] == '0' || $data['param5'] == '') {
            $this->_ajaxActions[] = 'document.create_admin_form.param5.setStyle({outline:"2px solid #f00"})';
        }
        // �����
        if ($data['param6'] == '0' || $data['param6'] == '') {
            $this->_ajaxActions[] = 'document.create_admin_form.param6.setStyle({outline:"2px solid #f00"})';
        }
        // �����
        if ($data['param7'] == '') {
            $this->_ajaxActions[] = 'document.create_admin_form.param7.setStyle({outline:"2px solid #f00"})';
        }
        // ����
        //if ($data['param8'] == '') {
        //    $this->_ajaxActions[] = 'document.create_admin_form.param8.setStyle({outline:"2px solid #f00"})';
        //}
        if (!empty($this->_ajaxActions)) {
            $this->_ajaxActions[] = 'document.create_admin_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_REQUIRED_FIELDS';
            return $this->_adminAjaxResponse();
        }
        
        $data['param9'] = date("Y-m-d", mktime(0, 0, 0, $data['d_month'], $data['d_day'], $data['d_year']));
        $id = $data['userId'];
        unset($data['userId']);
        unset($data['d_year']);
        unset($data['d_month']);
        unset($data['d_day']);
        //return 'ok';        
        
        foreach ($data as $k => $v) {
            $data[$k] = iconv('utf-8', 'cp1251', $v);
        }
        $preformattedData = array(
            'checked_out' => '0',
            'checked_out_time' => '0000-00-00 00:00:00',
            'usertype' => '23',
            'block' => '1',
            'register_date' => date('Y-m-d H:i:s'),
            'lastvizit_date' => '0000-00-00 00:00:00'
        );
        if (isset($data['email'])) {
            $data['login'] = $data['email'];
        }
        if (isset($data['password'])) {
            $data['password'] = md5($data['password']);
        }
		unset($data['_']);
        if ($id == '') {
            $data['param10'] = '0';
            if (0 < $this->_db->insert('cms_users', array_merge($preformattedData, $data))) {
                $id = $this->_db->lastInsertId();
                $link = 'http://' . $_SERVER['HTTP_HOST'] . '/club/confirm?hash=' . md5($data['email']);
                $message = "������ ����. �� ���� ��� ������������� �� ������ ������� "
                         . "������� ���� \"�����\" <a href=\"www.mirmafii.com.ua\" target=\"blank\">www.MirMafii.com.ua<a><br /><br />"
                         . "��������� �� ������ ��� ������������� �����������"
                         . "<a href=\"$link\" target=\"blank\">$link<a><br /><br />"
                         . "���� �� �� ��������� ���� �� �������, ������ �� ���������� �� ������ ������.<br /><br />"
                         . "��� ������� �� ������ ������ �� ������ <a href=\"mailto:mirmafii@list.ru\" target=\"blank\">MirMafii@list.ru<a>";
                $headers = 'MIME-Version: 1.0' . "\r\n"
                         . 'Content-type: text/html; charset="windows-1251"' . "\r\n"
                         . 'Reply-To: �������� <reminder@' . $_SERVER['HTTP_HOST'] . '>' . "\r\n"
                         . 'From: �������� <reminder@' . $_SERVER['HTTP_HOST'] . '>' . "\r\n";
                if (@mail($data['email'], "������������� �����������", $message, $headers)) {
                    $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/\"",2000)';
                    $this->_ajaxMessages[] = 'SUCESS_ADDING_ITEM';
                } else {
                    $this->_ajaxActions[] = 'document.create_admin_form.enable()';
                    $this->_ajaxMessages[] = 'ERROR_SEND_MESSAGE';
                }                
            } else {
                $this->_ajaxActions[] = 'document.create_admin_form.enable()';
                $this->_ajaxMessages[] = 'EDDOR_ADDING_ITEM';
            }
        } else {
            unset($preformattedData['register_date']);
            unset($preformattedData['usertype']);
            unset($preformattedData['block']);
            if (0 < $this->_db->update('cms_users', array_merge($preformattedData, $data), $this->_db->quoteIdentifier('id') . " = $id")) {
                $this->_ajaxActions[] = 'setTimeout("window.location.href=\"/\"",2000)';
                $this->_ajaxMessages[] = 'SUCESS_SAVING_ITEM';
            } else {
                $this->_ajaxActions[] = 'document.create_admin_form.enable()';
				$this->_ajaxActions[] = 'console.log("' . var_export(array_merge($preformattedData, $data), true) . '")';
                $this->_ajaxMessages[] = 'ERROR_SAVING_ITEM';
            }
        }
        return $this->_adminAjaxResponse();
    }
}