<?php
class Forum_Banned
{
	var $MainTemplate = 'forum/banned/auth.tpl';
    var $spamMailList = array();
    private $permissionModel = null;
    private $forumModel = null;


	function __construct ()
	{

		$this->_url_params = $GLOBALS['ForumCore']->_url_params;
		$this->Node = $GLOBALS['ForumCore']->CurrentParam;
		$this->DbManager = $GLOBALS['ForumCore']->DbManager;
        $this->AuthManager = CreateObject ("Auth_Manager");
        $this->getPermissionModel ($this->DbManager, $this->AuthManager);
        $this->getForumModel ($this->DbManager, $this->AuthManager);
	}
    public function getPermissionModel($dbManager = null, $authManager = null)
    {
        if (! $this->permissionModel) {
            require_once (MODULE_DIR . 'forum/permissoin.class.php');
            $this->permissionModel = new Permission ($dbManager, $authManager);
        }
        return $this->permissionModel;
    }

    public function getForumModel($dbManager = null, $authManager = null)
    {
        if (! $this->forumModel) {
            require_once (MODULE_DIR . 'forum/forum.class.php');
            $this->forumModel = new ForumModel ($dbManager, $authManager);
            $this->forumModel->CountPerPage = $this->CountPerPage;
        }
        return $this->forumModel;
    }

	public function Prepare($ds)
	{
        if (isMobile()) {
            $ds->assign("is_mobile", 'mobile');
        } else {
            $ds->assign("is_mobile", 'not mobile');
        }
        list($null,$banned,$check) = $this->_url_params;
		if ($check == 'checkipnick')
		{
			if (ereg ("([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3}).([0-9]{1,3})", $_GET['request'])) 
			{
			   $_result = $this->DbManager->selectrow("
											SELECT 
												INET_NTOA(b.`init_ip`) as `init_ip`,
												INET_NTOA(b.`end_ip`) as `end_ip`,
												b.`comments`, 
												b.`ban_type`, 
												r.`caption` as `rule`,
												b.`banned_time`,
												DATE_ADD(b.`banned_time`, INTERVAL b.`banned_period` SECOND) date_end
											FROM 
												?# b 
												LEFT JOIN ?# r ON (b.ruleID = r.ruleID) 
											WHERE 
												INET_ATON(?) BETWEEN b.`init_ip` AND b.`end_ip` 
												AND b.`banned`>0",
											"forum_networks_banned",
			   								"forum_rules",
											$_GET['request']
										);
				$this->MainTemplate = 'forum/banned/checkip.tpl';
			} 
			else 
			{
				
			   $_result = $this->DbManager->selectrow("
			   										SELECT 
			   											b.*,
			   											u.user_name,  
			   											r.caption as rule 
		   											FROM 
		   												?# u 
		   												LEFT JOIN ?# b ON (b.userID=u.userID)
	   													LEFT JOIN ?# r ON (b.ruleID=r.ruleID)
	   												WHERE 
	   													u.user_name=? 
	   													AND b.banned=1",
   													'forum_users',
			   										'forum_banned_users',
			   										'forum_rules',
			   										$_GET['request']
		   										);
												
		   		$_result['ban_end'] = strtotime($_result['when_banned']) + $_result['ban_period'];
		   		$_result['ban_end'] = date("Y-m-d h:i:s", $_result['ban_end']);
		   		
				$this->MainTemplate = 'forum/banned/checknick.tpl';
			}
			
		}
		if ($check == 'recovery' && $banned == 'auth')
		{
            $this->MainTemplate = 'forum/auth/recoverypw.tpl';
            $this->AuthDb = CreateObject ("Auth_Db");
            $key = 'War and Pease';
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);


            if ((filter_var($_GET['email'], FILTER_VALIDATE_EMAIL) == $_GET['email']) && (!isset($_GET['verifycode']))) {
                $_users = $this->DbManager->select("SELECT userID, user_name, confirmcode FROM ?# WHERE user_email=?",'forum_users',trim($_GET['email']));
                if (count($_users)>0) {
                    foreach ($_users as $k => $_user) {

                        if ($_user['user_name'] != '') {

                            $_user_password = uniqid();
                            $confirmcode = $this->random_string();

                            $_user_name = $_user['user_name'];
                            $_result = $_GET['email'];
                            $message = "Добрый день, '".$_user_name."'!"."\n";
                            $message .= "Вы запросили функцию восстановления пароля на форуме.\n";
                            $message .= "Для того, чтоб начать процедуру смены пароля откройте следующую ссылку:\n\n";
                            $crypt_url = date('Y-m-d H:i').' '.$_GET['email'];
                            $crypttext = urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $crypt_url, MCRYPT_MODE_ECB, $iv)));
                            $message .= "http://".SERVER_NAME."/auth/recovery/?email=".$_GET['email']."&verifycode=".$crypttext."&confirmcode=".$confirmcode."\n\n";
                            $message .= "Если Вы не вызывали функцию восстановления пароля и не желаете менять пароль, проигнорируйте данное письмо.\n";
                            $message .= "-------------------------------------\n";
                            $message .= "С Уважением, Робот http://".SERVER_NAME."\n";

                            $this->DbManager->query('UPDATE ?# SET confirmcode=? WHERE userID=?d','forum_users',$confirmcode,$_user['userID']);

                            $Mailer = CreateObject("Mail_PHPMailer");
                            $Mailer->From = 'noreply@'.SERVER_NAME;      // от кого
                            $Mailer->FromName = SERVER_NAME.' robot';   // от кого
                            $Mailer->AddAddress($_result, iconv("utf-8", "windows-1251", $_user_name)); // кому - адрес, Имя
                            $Mailer->Subject = iconv("utf-8", "windows-1251", "Приватное сообщение с ".SERVER_NAME);  // тема письма
                            $Mailer->Body = iconv("utf-8", "windows-1251", $message);

                            $spam = false;
                            $this->spamMailList = $this->getSpamList();

                            foreach ($this->spamMailList as $domain) {
                                if (preg_match("/".$domain."/i", $_result)) {
                                    $spam = true;
                                    break;
                                }
                            }
                            if ($spam == false) {
                                @$Mailer->Send();
                            }

                            //@$Mailer->Send();
                            $Mailer->ClearAllRecipients();
                            @$Mailer->ClearAttachments();
                            unset($Mailer);
                            $_result = 'Инструкции по смене пароля были высланы Вам на почту';
                        }
                    }
                }
                else
                {
                    $_result = 'Невозможно найти подходящего пользователя.';
                }

            } elseif ((filter_var($_GET['email'], FILTER_VALIDATE_EMAIL) == $_GET['email']) && (isset($_GET['verifycode']))) {
                $this->MainTemplate = "forum/registration/getconfirm.tpl";
                if (!empty($_GET['confirmcode'])) {
                    $_userOne = $this->DbManager->selectRow("SELECT userID, user_name FROM ?# WHERE user_email=? AND confirmcode=?",'forum_users',trim($_GET['email']),trim(urlencode($_GET['confirmcode'])));
                }
                if (count($_userOne)>0) {
                    $_user = $_userOne;
                    if ($_user['user_name'] != '') {
                        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($_GET['verifycode']), MCRYPT_MODE_ECB, $iv);

                        list($date,$time,$check_email) = explode(' ',$decrypttext);
                        //var_dump($check_email);
                        if (trim($check_email) == trim($_GET['email'])) {
                            $_user_password = uniqid();
                            $this->DbManager->query("UPDATE ?# SET active=1, confirmcode='', user_password=? WHERE userID=?",'forum_users',md5($_user_password),$_user['userID']);
                            $message = "Добрый день, '".$_user['user_name']."'!"."\n\n\n";
                            $message .= "Вы запросили функцию восстановления пароля на форуме.\n\n";
                            $message .= "Ваш новый пароль: ".$_user_password."\n\n\n";
                            $message .= "-------------------------------------\n";
                            $message .= "С Уважением, Робот http://".SERVER_NAME."\n";

                            $Mailer = CreateObject("Mail_PHPMailer");
                            $Mailer->From = 'noreply@'.SERVER_NAME;      // от кого
                            $Mailer->FromName = SERVER_NAME.' robot';   // от кого
                            $Mailer->AddAddress(trim($check_email), iconv("utf-8", "windows-1251", trim($_user['user_name']))); // кому - адрес, Имя
                            $Mailer->Subject = iconv("utf-8", "windows-1251", "Приватное сообщение с ".SERVER_NAME);  // тема письма
                            $Mailer->Body = iconv("utf-8", "windows-1251", $message);

                            $spam = false;
                            $this->spamMailList = $this->getSpamList();
                            foreach ($this->spamMailList as $domain) {
                                if (preg_match("/".$domain."/i", $check_email)) {
                                    $spam = true;
                                    break;
                                }
                            }
                            if ($spam == false) {
                                @$Mailer->Send();
                            }

                            //@$Mailer->Send();
                            $Mailer->ClearAllRecipients();
                            @$Mailer->ClearAttachments();
                            unset($Mailer);
                        } else {
                            $error[] = 'Неверный код. ';
                        }
                    }
                } else {
                    $_users = $this->DbManager->select("SELECT userID,user_name FROM ?# WHERE user_email=?",'forum_users',trim($_GET['email']));
                    if (count($_users)>0) {
                        foreach ($_users as $k => $_user) {
                            if ($_user['user_name'] != '') {
                                $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($_GET['verifycode']), MCRYPT_MODE_ECB, $iv);
                                list($date,$time,$check_email) = explode(' ',$decrypttext);
                                if (trim($check_email) == trim($_GET['email'])) {
                                    $_user_password = uniqid();
                                    $this->DbManager->query("UPDATE ?# SET active=1, confirmcode='', user_password=? WHERE userID=?",'forum_users',md5($_user_password),$_user['userID']);
                                    $message = "Добрый день, '".$_user['user_name']."'!"."\n";
                                    $message .= "Вы запросили функцию восстановления пароля на форуме.\n";
                                    $message .= "Ваш новый пароль: ".$_user_password."\n\n";
                                    $message .= "-------------------------------------\n";
                                    $message .= "С Уважением, Робот http://".SERVER_NAME."\n";

                                    $Mailer = CreateObject("Mail_PHPMailer");
                                    $Mailer->From = 'noreply@'.SERVER_NAME;      // от кого
                                    $Mailer->FromName = SERVER_NAME.' robot';   // от кого
                                    $Mailer->AddAddress(trim($check_email), iconv("utf-8", "windows-1251", trim($_user['user_name']))); // кому - адрес, Имя
                                    $Mailer->Subject = iconv("utf-8", "windows-1251", "Приватное сообщение с ".SERVER_NAME);  // тема письма
                                    $Mailer->Body = iconv("utf-8", "windows-1251", $message);

                                    $spam = false;
                                    $this->spamMailList = $this->getSpamList();
                                    foreach ($this->spamMailList as $domain) {
                                        if (preg_match("/".$domain."/i", $check_email)) {
                                            $spam = true;
                                            break;
                                        }
                                    }
                                    if ($spam == false) {
                                        @$Mailer->Send();
                                    }

                                    //@$Mailer->Send();
                                    $Mailer->ClearAllRecipients();
                                    @$Mailer->ClearAttachments();
                                    unset($Mailer);
                                } else {
                                    $error[] = 'Неверный код. ';
                                }
                            }
                        }
                    } else {
                        $error[] = 'Неверный пользователь.';
                    }
                }
                if (count($error)>0) {
                    $ds->assign('_err', $error);
                } else {
                    $ds->assign('_msg', 'На Вашу почту отправлен новый автоматически сгенерированный пароль.');
                }
                $ds->assign('_frm', '1');
                $this->MainTemplate = "forum/registration/getconfirm.tpl";
            } else {
                $_result = 'Email неверен';
            }

		}

        $ds->assign("result",$_result);
	}
	//---------------Вывод шаблона
	public function Display(&$parser)
	{
		$parser->display($this->MainTemplate);
	}

    function random_string($l = 10){
        $c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxwz0123456789";
        for(;$l > 0;$l--) $s .= $c{rand(0,strlen($c))};
        return str_shuffle($s);
    }
    public function getSpamList()
    {
        return $this->getForumModel ()->spamMailList();
    }


}