<?php
/* 
 * Static pages dispatcher
 */
class Pages_Manager
{
    protected $MainTemplate = "forum/pages/index.tpl";
    var $Page = null;

    function __construct()
    {
        $this->DbManager = $GLOBALS['ForumCore']->DbManager;
		$this->_url_params = $GLOBALS['ForumCore']->_url_params;
		$this->Page = $this->_url_params[2];
    }
	
	 public function Prepare(&$ds)
    {
		$ForumManager = CreateObject("Forum_Manager");
		$_groups = $ForumManager->LoadGroups();	
		$ds->assign("groups",$_groups);
        if (isMobile()) {
            $ds->assign("is_mobile", 'mobile');
        } else {
            $ds->assign("is_mobile", 'not mobile');
        }


        if ($this->AuthManager->User->userID >0)
		{
			$ForumPager = CreateObject("Forum_Pager");
			$_pager_stat = $ForumPager->CheckMessStat($this->AuthManager->User->userID);
			$ds->assign("_pager_info",$_pager_stat);
		}
		
    	$_result = $this->DbManager->selectRow("
			SELECT 
				* 
			FROM 
				?# 
			WHERE
				`page_name` = ?s
				AND `page_active` = 1",
			"forum_pages",
			$this->Page
		);
		
		if (count($_result) > 0) {
			$ds->assign('title_part', $_result['page_caption']);
			$ds->assign('_content', $_result['page_content']);
		}
		else {
			echo '404';
			exit();
		}
	}
	
    public function Display(&$parser)
    {
        $parser->display($this->MainTemplate);
    }
}