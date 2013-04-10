<?php

class content
{
    private $_db;
    private $_route = array();
    private $_month = array(
        1 => '€нвар€',
        'феврал€',
        'марта',
        'апрел€',
        'ма€',
        'июн€',
        'июл€',
        'августа',
        'сент€бр€',
        'окт€бр€',
        'но€бр€',
        'декабр€'
    );
    public $view;
    
    function __construct()
    {
        $this->view = new view();
        $this->view->month = $this->_month;
        $this->_db = $GLOBALS['db'];
        $typed = new typedcontent();
        $this->view->centerBanner = $typed->get('centerBanner');
    }
    
    public function get_cont($id)
    {
        $db = $this->_db;
        $select = $db->select()->from('cms_content')->limit(1);
        $select->where($db->quoteIdentifier('id') . ' = ?', $id);
        $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
        return $db->fetchRow($select);        
    }
    
    public function _find_cat($url, $level = 0, $parent_id = 0)
    {
        $db = $this->_db;
        $select = $db->select()->from('cms_categories', array('id', 'parent_id', 'title', 'title_alias', 'published'))->limit(1);
        $select->where($db->quoteIdentifier('title_alias') . ' = ?', $url[$level]);
        $select->where($db->quoteIdentifier('parent_id') . ' = ?', "$parent_id");
        $select->where($db->quoteIdentifier('published') . ' = ?', '1');
        $cat = $db->fetchRow($select);
        if (!empty($cat)) {
            $this->_route[] = $cat;
            if (count($url) > $level+1) {
                return $this->_find_cat($url, $level+1, $cat['id']);
            } else {
                return $cat;
            }
        } else return false;
    }
    
    public function gen_cat($url, $nameSpace, $rows)
    {
        $db = $this->_db;
        $view = $this->view;
        
        $cat = $this->_find_cat($url);
        if (empty($cat)) {
            return $view->render(TPLDIR . '404.tpl');
        }
        
        $view->rows = $rows;
        $view->page = isset($_GET['page']) ? $_GET['page'] : '1';
        
        $select = $db->select()->from('cms_categories')->limit(1);
        $select->where($db->quoteIdentifier('id') . ' = ?', $cat['id']);
        $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
        $cat = $view->cat = $db->fetchRow($select);
        
        $select = $db->select()->from('cms_categories');
        $select->where($db->quoteIdentifier('parent_id') . ' = ?', $cat['id']);
        $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
        $view->catList = $db->fetchAll($select);        
        
        $select = $db->select()->from('cms_content')->limitPage($view->page, $view->rows);
        $select->where($db->quoteIdentifier('parent_id') . ' = ?', $cat['id']);
        $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
        $select->order('ordering');
        $view->contList = $db->fetchAll($select);        
        
        $select = $db->select()->from('cms_content', 'id');
        $select->where($db->quoteIdentifier('parent_id') . ' = ?', $cat['id']);
        $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
        $view->contListTotal = count($db->fetchAll($select));        
        
        return $view->render(TPLDIR . '@dynamic/' . $nameSpace . '.tpl');
    }
    
    public function gen_cat_fp($url, $nameSpace, $rows)
    {
        $db = $this->_db;
        $view = $this->view;
        
        $cat = $this->_find_cat($url);
        if (empty($cat)) {
            return;// $view->render(TPLDIR . '404.tpl');
        }
        
        $select = $db->select()->from('cms_categories')->limit(1);
        $select->where($db->quoteIdentifier('id') . ' = ?', $cat['id']);
        $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
        $cat = $view->cat = $db->fetchRow($select);
        
        $select = $db->select()->from('cms_categories');
        $select->where($db->quoteIdentifier('parent_id') . ' = ?', $cat['id']);
        $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
        $view->catList = $db->fetchAll($select);        
        
        $select = $db->select()->from('cms_content')->limit($rows);
        $select->where($db->quoteIdentifier('parent_id') . ' = ?', $cat['id']);
        $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
        $view->contList = $db->fetchAll($select);        
        
        $select = $db->select()->from('cms_content', 'id');
        $select->where($db->quoteIdentifier('parent_id') . ' = ?', $cat['id']);
        $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
        $view->contListTotal = count($db->fetchAll($select));        
        
        return $view->render(TPLDIR . '@dynamic/' . $nameSpace . '.tpl');
    }
    
    public function gen_cont($url, $nameSpace)
    {
        $db = $this->_db;
        $view = $this->view;
        
        $el = array_pop($url);        
        
        $cat = $this->_find_cat($url);
        if (empty($cat)) {
            return $view->render(TPLDIR . '404.tpl');
        }
        
        $select = $db->select()->from('cms_content')->limit(1);
        $select->where($db->quoteIdentifier('id') . ' = ?', $el);
        $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
        $view->item = $db->fetchRow($select);
        
        if (!empty($view->item)) {
            return $view->render(TPLDIR . '@dynamic/' . $nameSpace . '_item.tpl');
        } else {
            return $view->render(TPLDIR . '404.tpl');
        }
    }
    
    public function page($url, $nameSpace = 'default', $rows = 10)
    {
        $el = end($url);
        if (!is_numeric($el)) {
            return $this->gen_cat($url, $nameSpace, $rows);
        } else {
            return $this->gen_cont($url, $nameSpace);
        }
    }
}