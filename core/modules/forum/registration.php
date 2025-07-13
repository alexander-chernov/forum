<?php
interface Registration
{
	public function Prepare($ds);
	public function Display($ds);
	
	
}
class Forum_Registration implements Registration
{
	protected  $MainTemplate = "forum/registration/index.tpl";
	var $_url_params = array();
	var $Node = null;
	var $Form = null;
	var $_err = array();
	var $_msg = array();
	var $_act = '';
	var $AuthManager = null;
	var $DbManager = null;
    var $spamMailList = array();


	function __construct ()
	{
		$this->_url_params = $GLOBALS['ForumCore']->_url_params;
		$this->DbManager = $GLOBALS['ForumCore']->DbManager;
		$this->AuthManager = $GLOBALS['ForumCore']->AuthManager;
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

/*
	public function onEvent_restorePasswd($form) {
        var_dump($form->Request[]);
        $this->_msg[] = 'Ссылка на активацию выслана';
    }
 * 
 */
    public function onEvent_sendConfirm($form)
	{
		$_mail = $form->Request['email'];
		$_res = $this->DbManager->selectrow("SELECT * FROM `forum_users` WHERE `user_email` = ?s", $_mail);
		if (count($_res)>0){
			$_code = $this->DbManager->selectrow("SELECT * FROM `forum_users_confirm` WHERE `userID` = ?d", $_res['userID']);
		}
		if (count($_res)>0){
			if (count($_code)==0){
				$_code_txt = md5(base64_encode($_res['userID'] . '_#_#_' . $_res['user_email']));
				$_code = $this->DbManager->query("INSERT INTO `forum_users_confirm` 
														SET `userID` = ?d, `code` = ?s", $_res['userID'], $_code_txt);
			}else{
				$_code_txt = $_code['code'];
			}

			$message = "Для активации аккаунта перейдите по ссылке http://".SERVER_NAME."/register/?event=confirmcode&_code=" . $_code_txt . "\n\n";
            $message .= "-------------------------------------\n";
            $message .= "С Уважением, Робот http://".SERVER_NAME."\n";
            
			$Mailer = CreateObject("Mail_PHPMailer");

			$Mailer->From = 'noreply@'.SERVER_NAME.'';      // от кого
	        $Mailer->FromName = 'http://'.SERVER_NAME.'/';   // от кого
			$Mailer->ContentType = "text/plain";
			$Mailer->CharSet = 'UTF-8';

	        $Mailer->AddAddress($_res['user_email'], $_res['user_name']); // кому - адрес, Имя
	        $Mailer->Subject = "Подтвержение регистрации на сайте ".SERVER_NAME."";  // тема письма
	        $Mailer->Body = $message;
            $spam = false;
            $this->spamMailList = $this->getSpamList();
            foreach ($this->spamMailList as $domain) {
                if (preg_match("/".$domain."/i", $_res['user_email'])) {
                    $spam = true;
                    break;
                }
            }
            if ($spam == false) {
                @$Mailer->Send();
            }
	        $Mailer->ClearAllRecipients();
		    @$Mailer->ClearAttachments();

			$this->_msg[] = 'Ссылка на активацию выслана';
		}else{
			$this->_err[] = 'Пользователя с такой почтой не существует';
		}
	}
	public function onEvent_ConfirmCode($form)
	{
		$this->_act = 'confirmaccount';
		$_code = $form->Request['_code'];
		if (trim($_code) != ''){
			$_res = $this->DbManager->selectrow("SELECT * FROM `forum_users_confirm` WHERE `code` = ?s LIMIT 0,1", $_code);
            //var_dump($_res);
            if ($_res['userID']>0) {
                $_user = $this->DbManager->selectrow("SELECT userID,user_email,user_name FROM `forum_users` WHERE userID = ?d", $_res['userID']);
                //var_dump($_user);
            }

			if (count($_res)>0){
				$_res_ban = $this->DbManager->selectrow("SELECT * FROM `forum_banned_users` WHERE
															`userID` = ?d 
															AND `banned` = 1
															AND
															((
															`ban_period` > 0
															AND
															(DATE_ADD(`when_banned`, INTERVAL `ban_period` SECOND) > NOW())
															)
															OR
															`ban_period` = 0)
															 LIMIT 0,1", $_user['userID']);
				if (count($_res_ban) == 0){
                    //дополнительные правила активации
                    //if (!preg_match('hush',$_user['user_email'])){
                    //    $_res = $this->DbManager->query("INSERT INTO ?# SET userID=?d, ip = INET_NTOA(?), real_ip = INET_NTOA(?)", $_res['userID']);
                    //}
                    $this->DbManager->query("UPDATE `forum_users` SET `active` = 1 WHERE `userID` = ?d LIMIT 1", $_user['userID']);
                    $_res = $this->DbManager->query("DELETE FROM `forum_users_confirm` WHERE `userID` = ?d", $_user['userID']);
                    $this->_msg[] = 'Пользователь '.$_user['user_name'].' активирован. Краткая инструкция отправлена по e-mail.';
                    $message = "Пользователь активирован, теперь Вы можете войти на форум под своим логином и паролем.\n\n";
                    $message .= "Перед началом работы прочитайте правила http://".SERVER_NAME."/pages/rules/\n";
                    $message .= "-------------------------------------\n";
                    $message .= "С Уважением, Робот http://".SERVER_NAME."\n";

                    //$message .= "В случае, если Вы после активации не смогли войти, воспользуйтесь функцией вспомнить пароль, доступной по ссылке http://".SERVER_NAME."/register/?event=confirmcode&_code=".$_code."#\n";                    
                    $Mailer = CreateObject ("Mail_PHPMailer");


                    $Mailer->From = 'noreply@'.SERVER_NAME; // от кого
                    $Mailer->FromName = 'http://'.SERVER_NAME.'/'; // от кого
                    $Mailer->ContentType = "text/plain";
                    $Mailer->CharSet = 'UTF-8';

                    $Mailer->AddAddress ($_user[ 'user_email' ], $_user[ 'user_name' ]); // кому - адрес, Имя
                    $Mailer->Subject = "Подтвержение регистрации на сайте ".SERVER_NAME.""; // тема письма
                    $Mailer->Body = $message;

                    $spam = false;
                    $this->spamMailList = $this->getSpamList();
                    foreach ($this->spamMailList as $domain) {
                        if (preg_match("/".$domain."/i", $_res['user_email'])) {
                            $spam = true;
                            break;
                        }
                    }
                    if ($spam == false) {
                        @$Mailer->Send();
                    }
                    $Mailer->ClearAllRecipients ();
                    @$Mailer->ClearAttachments ();

				}else{
					$this->_err[] = 'Пользователь не может быть активирован, так как забанен.';
				}
			}else{
				$this->_err[] = 'Неверный код активации';
			}
		}else{
			$this->_err[] = 'Код активации пустой';
		}
		
	}

	public function Prepare($ds)
	{
		$this->DbManager = $GLOBALS['ForumCore']->DbManager;
		$this->AuthManager = $GLOBALS['ForumCore']->AuthManager;
        if (isMobile()) {
            $ds->assign("is_mobile", 'mobile');
        } else {
            $ds->assign("is_mobile", 'not mobile');
        }
        if ($this->_url_params[2] == 'getconfirm'){
			$this->MainTemplate = "forum/registration/getconfirm.tpl";
			$ds->assign('_err', $this->_err);
			$ds->assign('_msg', $this->_msg);
            $ds->assign('_frm', '0');
		}
        elseif ($this->_url_params[2] == 'restorepass'){
            $this->MainTemplate = "forum/registration/restorepasswd.tpl";
            $ds->assign('_err', $this->_err);
            $ds->assign('_msg', $this->_msg);
            $ds->assign('_frm', '0');
        }
		/*elseif ($GLOBALS['ForumCore']->_url_params[1] == 'register' && $GLOBALS['ForumCore']->_url_params[2] == 'bysms' && $GLOBALS['ForumCore']->_url_params[3] == 'set')
		{
			echo "Spasibo. Vash parol - ";
			$_num = rand(4,9);
			echo $_pass = $this->AuthManager->PasswdGen($_num);
			$this->DbManager->query(
									"INSERT INTO 
										?# 
									SET 
										`ID`=null, 
										`userID`=0, 
										`user_phone`=?, 
										`created`=NOW(), 
										`is_test`=?, 
										`smscode`=? ,
										`cost`=? ",
										'sms_auth_keys',
									$_GET['user_id'],
									$_GET['test'],
									$_pass,
									$_GET['cost_rur']
			);
			exit();
		}*/
		if($this->_act == 'confirmaccount')
		{
			$this->MainTemplate = "forum/registration/active.tpl";
			$ds->assign('_err', $this->_err);
			$ds->assign('_msg', $this->_msg);
		}
		elseif ($this->AuthManager->User->userID == 0) {
			$ForumManager = CreateObject("Forum_Manager");
			$_groups = $ForumManager->LoadGroups();	
			$form = CreateObject("Form_Manager");
			$ds->assign("_request",$form->Request['user']);
			$ds->assign("groups",$_groups);
			$ds->assign("title_part", "РЕГИСТРАЦИЯ");
			$ds->assign("_auth_errors",$GLOBALS['ForumCore']->Errors['Auth_Manager']);
			$ds->assign("_auth_infos",$GLOBALS['ForumCore']->Infos['Auth_Manager']);
            $ds->assign ('captcha', true);
		}
		else
		{
			header("Location: /forum/");
		}
	}
	
	public function Display($parser)
	{
		$parser->display($this->MainTemplate);
	}

    public function getSpamList()
    {
        return $this->getForumModel ()->spamMailList();
    }

}