<?php
interface Protector
{
	public function FloodProtector ();
	public function KillBannedUsers();
	public function PostFilter($message);
}

class System_Protector implements Protector
{
	var $DbManager;
	var $_ip_gray = null;
	var $_banned_ip = null;
	var $_read_only = 0;
	
	var $Locale = array(
		1 => 'Полный бан',
		10 => 'Авторизация',
		11 => 'Только чтение',
		12 => 'Бан за флуд',
	);
	
	function __construct()
	{
		$this->DbManager = $GLOBALS['ForumCore']->DbManager;
        //$this->stopForumSpam();
		$this->KillBannedUsers();
		$this->FloodProtector();

        
        $this->_ip_gray = $GLOBALS['ForumCore']->AuthManager->RemoteAddr;
		/*
		$this->DbManager->query(
							"
								INSERT 
									INTO `forum_networks_log_daily` 
								SET 
									`ip`=INET_ATON(?s), 
									`ip_gray`=INET_ATON(?s),
									`putdate`=NOW(), 
									`id`=NULL, 
									`requesturl`=?s, 
									`agent`=?s",
									$_SERVER['REMOTE_ADDR'],
									$this->_ip_gray,
									$GLOBALS['REQUEST_URI'],
									$_SERVER['HTTP_USER_AGENT']
							);
		$this->FloodProtector();
		*/
	}


    public function stopForumSpam() {


//if ($_SERVER['REMOTE_ADDR'] == '10.0.0.52') {
//    phpinfo();die();
//}

        if ($_POST['event'] == 'cmsauthuserbyform') {
            return;
        }
        
        if (intval($GLOBALS['ForumCore']->AuthManager->User->userID) == 0) {
            //$data = $this->DbManager->query("select count(*) as cnt from stopforumspam where ip = ?", ip2long($GLOBALS['ForumCore']->AuthManager->RemoteAddr));
            if (isset($data[0])) {
                if ($data[0]['cnt'] > 0) {
                    $this->renderBanned('auth.tpl');
                }
            }
        }
    }
	
	public function FloodProtector ()
	{
		$_hits = $this->DbManager->selectcell("SELECT COUNT(*) FROM `forum_networks_log_daily` WHERE `ip`=INET_ATON('".$GLOBALS['ForumCore']->AuthManager->RemoteAddr."') AND `ip_gray`=INET_ATON('".$this->_ip_gray."') AND `putdate` >= NOW() - INTERVAL 5 MINUTE AND `putdate` <= NOW()");
		if ($_hits >= PROTECT_FLOOD_HITS)
		{
			echo "flood";
			exit();
		}
	}
	
	public function KillBannedUsers()
	{
		/*
		 * Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2
		 * Opera/9.50 (Windows NT 5.1; U; ru)
		 * Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/530.5 (KHTML, like Gecko) Chrome/2.0.172.39 Safari/530.5
		 * Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)
		 */
		$_user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (
			!(
			(stristr($_user_agent, 'Windows') && stristr($_user_agent, 'MSIE')) 
			||(stristr($_user_agent, 'Mozilla') && stristr($_user_agent, 'Chrome')) 
			||(stristr($_user_agent, 'Mozilla') && stristr($_user_agent, 'Firefox'))
			||(stristr($_user_agent, 'Opera') && stristr($_user_agent, 'Windows'))
			||(stristr($_user_agent, 'Navigator') && stristr($_user_agent, 'Windows'))
			)  
		)
		{
		//	echo "not allowed";
		//	exit();
		}
		$_banned_ip = $GLOBALS['ForumCore']->AuthManager->RemoteAddr;

        $_banned_ip_grey = $GLOBALS['ForumCore']->AuthManager->Forwarder;
        //$_banned_ip_grey = '90.188.98.238, 88.204.29.238';
        $grey_ips = array ();
        $tmp_grey_ips = array();

        if (substr_count($_banned_ip_grey,',')>0) {
            $tmp_grey_ips = explode(',',$_banned_ip_grey);
            for ($i=0;$i<count($tmp_grey_ips);$i++){
                if (filter_var(trim($tmp_grey_ips[$i]),FILTER_VALIDATE_IP)) {
                    $grey_ips[] = trim($tmp_grey_ips[$i]);
                }
            }
        } else {
            $grey_ips[] = $_banned_ip_grey;
        }
        if (filter_var($_banned_ip,FILTER_VALIDATE_IP)) {
            $grey_ips[] = $_banned_ip;
        }

		$this->_read_only = 0;
		$_result = null;
        $filter = '';
        for ($i=0;$i<count($grey_ips);$i++){
            if ($i>0){
                $filter .= " OR INET_ATON('".$grey_ips[$i]."') BETWEEN init_ip AND end_ip ";
            } else {
                $filter = " ( INET_ATON('".$grey_ips[$i]."') BETWEEN init_ip AND end_ip ";
            }
        }
        $filter .= ')';

        $_result = $this->DbManager->selectRow(
                                            "
                                            SELECT
                                                *
                                            FROM
                                                ?#
                                            WHERE
                                                ".$filter."
                                                AND `banned` = 1",
                                            "forum_networks_banned",
                                            $_banned_ip
                                        );


		if  (is_array($_result) && count($_result) >0)
		{
			if ($_result['ban_type'] == BAN_AUTH_ONLY )
			{
				$GLOBALS['ForumCore']->authBanned = true;
				if (strtolower($GLOBALS['_REQUEST']['event']) != 'cmscreateuserbyform' && strtolower($_POST['event']) != 'cmsauthuserbyform' && !isset($_COOKIE['user_id']) && $GLOBALS['ForumCore']->_url_params[1] != 'register')
				{
					$this->renderBanned('auth.tpl');
				}
				elseif (strtolower($_POST['event']) == 'cmscreateuserbyform' && ($GLOBALS['ForumCore']->_url_params[1] != 'register') )
				{
					$this->renderBanned('auth.tpl');
				}
			}
			elseif ($_result['ban_type'] == BAN_READ_ONLY) {
				$this->_read_only = 1;
				$this->ban_type = 'ip';
				$this->ban_element = $_banned_ip;
			}
			elseif ($GLOBALS['ForumCore']->_url_params[1] != 'banned')	//full ban
			{
				//Вычисляем дату разбанивания
				$banned_to = date("d.m.Y h:i", strtotime($_result['banned_time']) + $_result['banned_period']);
				$this->renderBanned('fullban.tpl', array('banned_to' => $banned_to, 'comment' => $_result['comments']));
			}
		}
		
		//бан по нику (ридонли) проверка
		if (isset($GLOBALS['ForumCore']->AuthManager->User->userID) && !$this->_read_only) {
			$_result = $this->DbManager->selectRow(
				"
				SELECT 
					*
				FROM 
					?# b
				WHERE 
					`banned` = 1 
					AND `userID` = ?d
				",
				"forum_banned_users",
				 $GLOBALS['ForumCore']->AuthManager->User->userID
			);
			if (count($_result) > 0) {
				$this->_read_only = 1;
				$this->ban_type = 'nick';
				
				$this->ban_element = $this->DbManager->selectCell(
				"SELECT 
					`user_name`
				FROM 
					?# 
				WHERE 
					`userID` = ?d
				",
				"forum_users",
				$GLOBALS['ForumCore']->AuthManager->User->userID		
				);
			}
		}
	}
	
	//Создаёт смарти-объект, рендерит шаблон и завершает работу
	public function renderBanned($tpl_name, $params = null) {
		$Smarty = CreateObject("System_Template");
		if (is_array($params)) {
			foreach ($params as $key => $value) $Smarty->assign($key, $value);
		}
		$Smarty->display('forum/banned/'.$tpl_name);
		exit();
	}
	
	public function PostFilter ($message)
	{
		$TextFilter = CreateObject("Forum_Filter");
		if (strlen(iconv("UTF-8", "CP1251", $message['caption'])) > THEME_LEGHT) {
            return false;
        }

		if (mb_strlen(iconv("UTF-8", "CP1251", $message['author'])) > AUTHOR_LEGHT) {
            return false;
        }
        //если на вход массив параметров (тест, автор, заголовок)
		if (is_array($message))
		{
            //сработал ли антимат?
            if ($TextFilter->CheckBadWords($message)) {
                //возвращаем херовый результат
                return false;
            } else {
                //возвращаем хороший результат
                return true;
            }

		}
	}
}