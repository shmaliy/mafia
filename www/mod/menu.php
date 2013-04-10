<?php
class menu_db
{
    private $_db;
    public $view;
	
    function __construct()
    {
        $this->view = new view();
        $this->_db = $GLOBALS['db'];
	}
	
	public function get_by_alias($alias, $field = '*')
	{
        $select = $this->_db->select()->from('cms_menu', $field)->limit(1);
        $select->where($this->_db->quoteIdentifier('title_alias') . ' = ?', $alias);
        $select->where($this->_db->quoteIdentifier('published') . ' = ?', '1');
        
        if ($field != '*') {
            return $this->_db->fetchOne($select);
        } else {
            return $this->_db->fetchRow($select);
        }
	}
	
	public function get_by_id($id, $field = NULL)
	{
        $select = $this->_db->select()->from('cms_menu', $field)->limit(1);
        $select->where($this->_db->quoteIdentifier('id') . ' = ?', $id);
        $select->where($this->_db->quoteIdentifier('published') . ' = ?', '1');
        
        if ($field != '*') {
            return $this->_db->fetchOne($select);
        } else {
            return $this->_db->fetchRow($select);
        }
	}
	
	public function get_list($parent_id, $limit = NULL)
	{
        $select = $this->_db->select()->from('cms_menu');
        $select->where($this->_db->quoteIdentifier('parent_id') . ' = ?', $parent_id);
        $select->where($this->_db->quoteIdentifier('published') . ' = ?', '1');
        $select->order('ordering');
        
        if (isset($limit)) {
            $select->limit($limit);
        }
        
        return $this->_db->fetchAll($select);
	}
}

class menu_gen extends menu_db
{
	function gen($alias, $level, $url, $suffix)
	{
	    $view = $this->view;
		$menu = $this->get_by_alias($alias);
		if (!empty($menu)) {
		    $elements = $this->get_list($menu['id']);
		    if (!empty($elements)) {
		        foreach ($elements as &$element) {
					// обработка параметра target
		            if ($element['browser_nav'] == 0){ $element['browser_nav'] = ' target="_self"'; }
					elseif ($element['browser_nav'] == 1){ $element['browser_nav'] = ' target="_blank"'; }
					elseif ($element['browser_nav'] == 2){ $element['browser_nav'] = ' target="_parent"'; }
					elseif ($element['browser_nav'] == 3){ $element['browser_nav'] = ' target="_top"'; }
					
					// обработка параметра link
					if (isset($url) && $url == substr($element['link'], 1, strlen($element['link'])) && $element['link'] != '/' && $element['title_alias'] == '') {
					    $element['current'] = 1;
					} else if ($url == '' && $element['link'] == '/' && $element['title_alias'] == '') {
					    $element['current'] = 1;
					} else {
					    $element['current'] = 0;
					}
					
					if ($element['title_alias'] != '') {
					    $element['childs'] = $this->gen($element['title_alias'], $level, $url, $suffix);
					}
		        }
		    }
		    $view->elements = $elements;
		}
		
		$template = TPLDIR . '@menu/' . $alias . $suffix . '.tpl';
    	if (file_exists($template)){
    	    return $view->render($template);
		}else{
    	    return $view->render(TPLDIR . '@menu/default.tpl');
		}
	}
}

class menu extends menu_gen
{
	
}
?>