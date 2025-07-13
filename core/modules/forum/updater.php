<?php
class Forum_Updater
{
	protected  $MainTemplate = "forum/update/index.tpl";
	var $_url_params = array();
	var $Node = null;
	var $Form = null;
	var $GroupID = null;
	var $ThemeID = null;
	var $Page = 1;
	function __construct ()
	{
		$this->_url_params = $GLOBALS['ForumCore']->_url_params;
		list($_a,$forum,$this->GroupID,$this->ThemeID) = $this->_url_params;
		$this->Node = $GLOBALS['ForumCore']->CurrentParam;
		$this->DbManager = $GLOBALS['ForumCore']->DbManager;
		if (isset($_GET['p']) && $_GET['p']>0)
		{
			$this->Page = intval($_GET['p']);
		}
	}
	
// поднятие темы вверх
	public function onEvent_ForumUpTheme($form)
	{
		$this->Form = $form;
		$this->DbManager->query("UPDATE ?# SET `updated`=NOW() WHERE themeID=?d","forum_db_themes",$this->Form->Request['themeid']);
	}
	
	public function Prepare(&$ds)
	{
		
	}
	
	public function Display(&$parser)
	{
		$parser->display($this->MainTemplate);
	}
}