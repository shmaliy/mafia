<?php
class typedcontent
{
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
    
    public function __construct(){
        $this->_db = $GLOBALS['db'];
		$this->view = new view();
        
        $this->view->centerBanner = $this->get('centerBanner');
    }
	
	function get($alias, $field = '*'){
        $select = $this->_db->select()->from('cms_content', $field)->limit(1);
        $select->where($this->_db->quoteIdentifier('title_alias') . ' = ?', $alias);
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', '0');
        $select->where($this->_db->quoteIdentifier('published') . ' = ?', '1');
        
        if ($field != '*') {
            return $this->_db->fetchOne($select);
        } else {
            return $this->_db->fetchRow($select);
        }
	}
	
	function gen($alias){
	    $item = $this->get($alias);
		$tpl = $alias;
		
		if (empty($item)) {
		    return $this->view->render(TPLDIR . '404.tpl');
		}		
		$this->view->item = $item;
		if (file_exists(TPLDIR . '@static/'.$tpl.'.tpl')) {
		    return $this->view->render(TPLDIR . '@static/'.$tpl.'.tpl');
		} else {
		    return $this->view->render(TPLDIR . '@static/default.tpl');
		}
	}
    
    protected $_ajaxActions = array();
    protected $_ajaxMessages = array();    
	
    protected function _ajaxResponse()
    {
        foreach ($this->_ajaxMessages as &$message) {
            if (substr($message, 0, 5) == 'ERROR') {
                $message = '"<div class=\"ststus ststus_error\">' . substr($message, 6) . '</div>"';
            } else if (substr($message, 0, 7) == 'WARNING') {
                $message = '"<div class=\"ststus ststus_warning\">' . substr($message, 8) . '</div>"';
            } else {
                $message = '"<div class=\"ststus ststus_sucess\">' . $message . '</div>"';
            }
        }
        
        $messages = implode('+"<br />"+', $this->_ajaxMessages);
        $this->_ajaxActions[] = '$("callback_stat").update(' . $messages . ')';
        return implode(';', $this->_ajaxActions);
    }
	
    public function callback($data)
    {
        if ($data['title'] == '') {
            $this->_ajaxActions[] = 'document.callback_form.title.setStyle({outline:"2px solid #f00"})';
        }
        if ($data['email'] == '') {
            $this->_ajaxActions[] = 'document.callback_form.email.setStyle({outline:"2px solid #f00"})';
        }
        if ($data['message'] == '') {
            $this->_ajaxActions[] = 'document.callback_form.message.setStyle({outline:"2px solid #f00"})';
        }
        if (!empty($this->_ajaxActions)) {
            $this->_ajaxActions[] = 'document.callback_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_Заполните поля отмеченые красным';
            return $this->_ajaxResponse();
        }
        
        $link = 'http://' . $_SERVER['HTTP_HOST'] . '/club/confirm?hash=' . md5($data['email']);
        $headers = 'MIME-Version: 1.0' . "\r\n"
                 . 'Content-type: text/html; charset="windows-1251"' . "\r\n"
                 . 'Reply-To: ' . $data['email'] . "\r\n"
                 . 'From: ' . $data['email'] . "\r\n";
        if (@mail('mirmafii@list.ru', $data['title'], $data['message'], $headers)) {
            $this->_ajaxMessages[] = 'Сообщение отправлено';
            $this->_ajaxActions[] = 'document.callback_form.reset()';
            $this->_ajaxActions[] = 'document.callback_form.enable()';
        } else {
            $this->_ajaxActions[] = 'document.callback_form.enable()';
            $this->_ajaxMessages[] = 'ERROR_Ошибка отправки сообщения';
        }                
        return $this->_ajaxResponse();
    }
}