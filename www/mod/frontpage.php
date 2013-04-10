<?php

class frontpage
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
    private $_user;
    public $view;
    private $_state = false;
    
    public function __construct(){
        $this->_db = $GLOBALS['db'];
		$this->view = new view();
		$this->view->mNames = $this->_month;
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
		
		if (file_exists(TPLDIR . '@static/'.$tpl.'.tpl')) {
		    return $this->view->render(TPLDIR . '@static/'.$tpl.'.tpl');
		} else {
		    return $this->view->render(TPLDIR . '@static/default.tpl');
		}
	}
	
	public function calendar()
	{
	    $db = $this->_db;
	    $view = $this->view;
	    
	    $typed = new typedcontent();
	    $content = new content();
	    
	    $date = $view->date = getdate();
	    $select = $db->select()->from('cms_categories')->limit(1);
	    $select->where($db->quoteIdentifier('title_alias') . ' = ?', 'club');
	    $clubParent = $db->fetchRow($select);
	    
	    if (!empty($clubParent)) {
    	    $select = $db->select()->from('cms_categories', 'id');
    	    $select->where($db->quoteIdentifier('parent_id') . ' = ?', $clubParent['id']);
    	    $clubs = $db->fetchAll($select);	        
	    }
	    
	    if (!empty($clubs)) {
    	    $select = $db->select()->from('cms_categories', array('id'));
    	    
  	        $parentsExpr = array();
    	    foreach ($clubs as $club) {
    	        $parentsExpr[] = $db->quoteIdentifier('parent_id') . ' = ' . $club['id'];
    	    }
        	$select->where(implode(' OR ', $parentsExpr));    	        
        	
        	$select->where($db->quoteIdentifier('title_alias') . ' = ?', 'club_afishi');    	        
    	    $afishiParents = $db->fetchAll($select);	        
	    }
	    
	    if (!empty($afishiParents)) {
    	    $select = $db->select()->from('cms_content', array('image', 'publish_up'));
  	        
    	    $parentsExpr = array();
    	    foreach ($afishiParents as $parent) {
    	        $parentsExpr[] = $db->quoteIdentifier('parent_id') . ' = ' . $parent['id'];
    	    }
        	$select->where(implode(' OR ', $parentsExpr));
        	
            $uTS = isset($_GET['date']) ? $_GET['date'] : mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $uDT = getdate($uTS);
            
            $prevDT['mon'] = $uDT['mon'] == 1 ? 12 : $uDT['mon']-1;
            $prevDT['year'] = $uDT['mon'] == 1 ? $uDT['year']-1 : $uDT['year'];
            $prevTS = mktime(0, 0, 0, $prevDT['mon'], 1, $prevDT['year'])-1;
            
            $nextDT['mon'] = $uDT['mon'] == 12 ? 1 : $uDT['mon']+1;
            $nextDT['year'] = $uDT['mon'] == 12 ? $uDT['year']+1 : $uDT['year'];
            $nextTS = mktime(0, 0, 0, $nextDT['mon'], 1, $nextDT['year'])+1;
            
        	$select->where($db->quoteIdentifier('publish_up') . ' > ?', $prevTS);    	        
        	$select->where($db->quoteIdentifier('publish_up') . ' < ?', $nextTS);
        	$view->afishiAll = $db->fetchAll($select);
	    }
        
	    // frontpage text
	    $view->frontpageCenterText = $typed->get('frontpage_center_text');
	    
	    // news
	    $view->newsList = $content->gen_cat_fp(array('news'), 'default_fp', 3);
	    
	    // filter
	    $this->_state = true;
		$typed = new typedcontent();
        $view->filter = $this->genFilter();
        
	    return $view->render(TPLDIR . '/frontpage.tpl');
	}
	
	public function genFilter()
	{
        $db = $this->_db;
	    $view = $this->view;
	    $content = new content();
		$typed = new typedcontent();
		$view->centerBanner = $typed->get('centerBanner');
	    $return = array();
	    
        if (!isset($this->_user) && isset($_SESSION['auth']['user_id'])) {
            $select = $this->_db->select()->from('cms_users');
            $select->where($this->_db->quoteIdentifier('id') . ' = ?', $_SESSION['auth']['user_id'])->limit(1);
            $this->_user = $this->_db->fetchRow($select);
        }
	    
        if (!empty($this->_user)) {
            $select = $db->select()->from('cms_categories')->limit(1);
            $select->where($db->quoteIdentifier('title_alias') . ' = ?', $this->_user['email']);
            $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
            $view->club = $db->fetchRow($select);
        }
        
	    $filterCat = $content->_find_cat(array('filter'));        
	    $select = $db->select()->from('cms_categories', array('id', 'parent_id', 'title'));
        $select->where($db->quoteIdentifier('parent_id') . ' = ?', $filterCat['id']);
        $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
        $filterCatList = $db->fetchAll($select);        
	    
        if (!empty($filterCatList)) {
            $sql = array();
            $select = $db->select()->from('cms_content');
            foreach ($filterCatList as $parent) {
                $sql[] = $db->quoteIdentifier('parent_id') . ' = ' . $parent['id'];
            }
            $select->where(implode(' OR ', $sql));
            $select->where($db->quoteIdentifier('published') . ' = ?', '1');        
            $filterContList = $db->fetchAll($select);
            
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
        $view->user = $this->_user;
        $view->filter = $return;
        $view->state = $this->_state;
        return $view->render(TPLDIR . '/filter.tpl');
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}