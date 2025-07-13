<?php
class Forum_Userlist
{
	protected  $MainTemplate = "forum/userlist/index.tpl";
	var $_url_params = array();
	var $Node = null;
	var $Form = null;
	var $GroupID = null;
	var $ThemeID = null;
	var $Page = 1;
    var $CountPerPage = 100;
	
	function __construct ()
	{
		$this->AuthManager = CreateObject("Auth_Manager");
		$this->_url_params = $GLOBALS['ForumCore']->_url_params;
		$this->Node = $GLOBALS['ForumCore']->CurrentParam;
		$this->DbManager = $GLOBALS['ForumCore']->DbManager;
		if (isset($_GET['p']) && $_GET['p']>0)
		{
			$this->Page = intval($_GET['p']);
		}
	}
// обработка событий
	public function onEvent_ForumSearchUser($form)
	{
		$this->Form = $form;
	}

	function Prepare(&$ds)
	{
        if (isMobile()) {
            $ds->assign("is_mobile", 'mobile');
        } else {
            $ds->assign("is_mobile", 'not mobile');
        }
        list($null,$userlist,$type) = $this->_url_params;
		$ForumManager = CreateObject("Forum_Manager");
		$_groups = $ForumManager->LoadGroups();	
		if (!$_GET['letter']) {
		    $badUsers = $this->getUsersByKarma('ASC');
		    $goodUsers = $this->getUsersByKarma('DESC');
		    $ds->assign("badUsers",$badUsers);
		    $ds->assign("goodUsers",$goodUsers);
		}
        $page = intval($_GET['page'])==0?1:intval($_GET['page']);
        $ds->assign("page", $page);
        $ds->assign("letter", $_GET['letter']);
		$_userlist = $this->GetUserList(null,@$_GET['letter'],$page);
		$ds->assign("title_part", "ПОЛЬЗОВАТЕЛИ");
		if ($this->AuthManager->User->userID >0)
		{
			$ForumPager = CreateObject("Forum_Pager");
			$_pager_stat = $ForumPager->CheckMessStat($this->AuthManager->User->userID);
			$ds->assign("_pager_info",$_pager_stat);
		}
		$ds->assign("groups",$_groups);
		$ds->assign("_users",$_userlist);
		$ds->assign("_page_totalrows",$this->TotalRows);
		$ds->assign("_page_curpage",$this->Page);
		$ds->assign("_page_countperpage",$this->CountPerPage);
	}
//---------------Вывод шаблона
	public function Display(&$parser)
	{
		$parser->display($this->MainTemplate);
	}
	public function getUsersByKarma($sort)
	{
	    $query = 'SELECT * FROM forum_users ';
	    if ('ASC' == $sort) {
	        $query .=' WHERE danger_level <0 and banned=0 ORDER BY danger_level ASC';
	    }
	    if ('DESC' == $sort) {
	        $query .=' WHERE danger_level >0 and banned=0 ORDER BY danger_level DESC';
	    }
	    $query .=' LIMIT 30';
	    return  $this->DbManager->select($query);
	}
	public function GetUserList ($_userid=null,$_letter=null,$_page=1)
	{

        if ($_letter==='!') {
            $_favorites = $this->DbManager->select(
                "SELECT
                    u.*,
                    inet_ntoa(user_ip) as userIP
                FROM
                    ?# u
                WHERE
                    1=1
                    AND active = 1
                ORDER BY
                    registered DESC
                LIMIT
                    ?d,?d",
                'forum_users',
                (($_page - 1) * $this->CountPerPage),
                $this->CountPerPage
            );
        } elseif ($_letter==='@') {
            $_favorites = $this->DbManager->select(
                'SELECT
                    u.*,
                    inet_ntoa(user_ip) as userIP
                FROM
                    ?# u
                WHERE
                    1=1
                    AND active = 1
                    AND user_name RLIKE  "^[[:punct:]]+"
                ORDER BY
                    registered DESC
                LIMIT
                    ?d,?d',
                'forum_users',
                (($_page - 1) * $this->CountPerPage),
                $this->CountPerPage
            );
        } else {
            $_favorites = $this->DbManager->select(
                                                "SELECT
                                                    u.*,
                                                    inet_ntoa(user_ip) as userIP
                                                FROM
                                                    ?# u
                                                WHERE
                                                    1=1
                                                    AND active = 1
                                                    {AND user_name LIKE ?}
                                                ORDER BY
                                                    user_name ASC
                                                LIMIT
                                                    ?d,?d",
                                                'forum_users',
                                                ($_letter != '')?$_letter."%":DBSIMPLE_SKIP,
                                                (($_page - 1) * $this->CountPerPage),
                                                $this->CountPerPage
            );
        }
		return $_favorites;
	} 
}
