<?php

class afisha
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
    
    public function __construct()
    {
        $this->view = new view();
        $this->_db = $GLOBALS['db'];
		$this->view->mNames = $this->_month;
    }
    
    public function gen()
    {
	    $db = $this->_db;
	    $view = $this->view;
        $users = new users();
        $content = new content();
        $frontpage = new frontpage();
        $clubC = new club();
	    $typed = new typedcontent();
		$view->centerBanner = $typed->get('centerBanner');
	    
        $date = $view->date = isset($_GET['date']) ? getdate($_GET['date']) : getdate();
        
        $select = $db->select();
        $select->from(array('p' => 'cms_categories'), array());
        $select->joinLeft(array('c' => 'cms_categories'), 'c.parent_id = p.id', array('clubId' => 'id', 'clubTitle' => 'title'));
        $select->joinLeft(array('u' => 'cms_users'), 'u.email = c.title_alias', array('region_id' => 'param5', 'city_id' => 'param6'));
        $select->joinLeft(array('ap' => 'cms_categories'), 'ap.parent_id = c.id', array());
        $select->joinLeft(array('a' => 'cms_content'), 'a.parent_id = ap.id');
        $select->joinLeft(array('r' => 'cms_categories'), 'r.id = u.param5', array('region_title' => 'title'));
        $select->joinLeft(array('ct' => 'cms_content'), 'ct.id = u.param6', array('city_title' => 'title'));
        $select->where($db->quoteIdentifier('p.title_alias') . ' = ?', 'club');
        $select->where($db->quoteIdentifier('ap.title_alias') . ' = ?', 'club_afishi');
        
        if (isset($_GET['region']) && $_GET['region'] != '' && $_GET['region'] != '0' && is_numeric($_GET['region'])) {
            $select->where($db->quoteIdentifier('u.param5') . ' = ?', $_GET['region']);
        }
        if (isset($_GET['city']) && $_GET['city'] != '' && $_GET['city'] != '0' && is_numeric($_GET['city'])) {
            $select->where($db->quoteIdentifier('u.param6') . ' = ?', $_GET['city']);
        }
        if (isset($_GET['date'])) {
            $before = mktime(0, 0, 0, $date["mon"], $date["mday"], $date["year"]) + 24*60*60;
            $after = mktime(0, 0, 0, $date["mon"], $date["mday"], $date["year"])-1;
            $select->where($db->quoteIdentifier('a.publish_up') . ' > ?', $after);               
            $select->where($db->quoteIdentifier('a.publish_up') . ' < ?', $before);
        }
        //echo $select;
        $view->afishiAll = $db->fetchAll($select);
	    
	    $view->filter = $frontpage->genFilter();
        return $view->render(TPLDIR . '@club/afishi_list.tpl');
    }
}