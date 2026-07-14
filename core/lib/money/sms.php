<?php
class Money_Sms
{
	var $Encoding = 'utf8';
	var	$url_restrict = false;
 	var	$limit = 0;
	var	$lang_switcher = true;
	var	$DefaultLang = 'ru';
	var $ProjectID = 'XXXXXXXXXXX';
	function __construct()
	{
		$this->AuthManager = CreateObject("Auth_Manager");
		$this->_url_params = $GLOBALS['ForumCore']->_url_params;
		$this->Node = $GLOBALS['ForumCore']->CurrentParam;
		$this->DbManager = $GLOBALS['ForumCore']->DbManager;
	}
	
	function CheckSmsCode($form)
	{
		$this->Form = $form;
		$language = $this->DefaultLang;
		$result_code = false;
		$result_message = "closed";
		$_key = $this->DbManager->selectcell("SELECT `key` FROM ?# WHERE `key`=?",'sms_keys',$this->Form->Request['sms']['code']);
		if ($_key)
		{
			$result_code = false;
		}
		else
		{
			if (isset($this->Form->Request['sms']['code']) && ereg('^[a-z0-9]{4}-[a-z0-9]{4}$', $this->Form->Request['sms']['code'])) 
			{
				$check_url = 'http://check.smszamok.ru/check/?p='.$this->Form->Request['sms']['code'].'&id='.$this->ProjectID;
				if ($this->url_restrict) {
					$check_url .= "&url_restricted=".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				}
				if ($this->limit > 0) {
					$check_url .= "&limit=".$this->limit;
				}
				$handle = fopen($check_url, "r");
				if ($handle !== FALSE) {
				 	$result_message = fgets($handle, 255);
					$result_code = ($result_message == "true");
					fclose($handle);
					$this->DbManager->query("INSERT INTO ?# SET `key`=?, `expired`=NOW()+interval 20 minute",'sms_keys',$this->Form->Request['sms']['code']);
				} 
				else
				{
					$result_message = "server_busy";
				}
			}
			if (!$result_code) {
				$result_code = file_get_contents(($result_message == "server_busy") ?
				'http://iface.smszamok.ru/client/sorry.php?lng='.$language.'&enc='.$this->Encoding :
				'http://iface.smszamok.ru/client/'.$language.'.iface.'.$this->Encoding.'.php?pid='.$this->ProjectID.'&message='.$result_message.'&ls='.($this->lang_switcher?'1':'0'));
				// халявы нема
			}
		}
		return $result_code;
	}
}