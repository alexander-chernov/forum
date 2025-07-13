<?php
class Forum_Pager
{
	protected  $MainTemplate = "forum/personal/pager/default.tpl";
	var $_url_params = array();
	var $Node = null;
	var $Form = null;
	var $GroupID = null;
	var $ThemeID = null;
	var $Page = 1;
	var $PagerPage = 1;
	var $CountPerPage = 10;
    var $spamMailList = array();
    private $hasCaptcha = true;
	
	public function __construct()
	{
		$this->AuthManager = CreateObject("Auth_Manager");
		$this->DbManager = $GLOBALS['ForumCore']->DbManager;
		$this->_url_params = $GLOBALS['ForumCore']->_url_params;
        $this->getPermissionModel ($this->DbManager, $this->AuthManager);
        $this->getForumModel ($this->DbManager, $this->AuthManager);
        $this->Page = (isset($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
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
    
	
	public function onEvent_ForumPagerCreateMess($form)
	{
        $this->hasCaptcha = true;
        $this->Form = $form;
        $_message_info = $this->Form->Request['pagermess'];
        if ($_message_info['content'] == '')
        {
            $this->Error['system'] = "Содержание сообщения не должно быть пустым";
        }

        if ($this->_url_params[3] == '')
        {
            $this->Error['system'] = "Выберите отправителя";
        }
        $valid = 0;
        $allowRefererList = $this->AuthManager->allowRefererList;
        for ($i=0;$i<count($allowRefererList);$i++) {
            if(strstr($_SERVER['HTTP_REFERER'],$allowRefererList[$i]) && !empty($_SERVER['HTTP_REFERER'])){
                $valid = 1;
                break;
            }
        }
        /*
        $allowedDate = time() - strtotime($this->AuthManager->User->registered);
        if ($allowedDate<FRESH_USERS_PAGER_SECOND_LIMIT) {
            $time = FRESH_USERS_PAGER_SECOND_LIMIT - $allowedDate;
            $hours = 0;
            $min = 0;
            $sec = 0;
            if ($time>60) {
                $sec = $time % 60;
                $time = floor($time / 60);
                $min = $time % 60;
                $time = floor($time / 60);
                $hours = $time % 24;
                $time = floor($time / 24);
            }
            $this->Error['system']= "Вы недавно зарегистрировались и пока не можете отправить приватное сообщение. Осталось "
                //." lastlogin=".$this->AuthManager->User->registered
                //." lastlogin_totime=".strtotime($this->AuthManager->User->registered)
                //." time=".time()
                //." time-lastlogin=".(time() - strtotime($this->AuthManager->User->registered))
                //." allowed=".$allowedDate
                //." time=".$time
                ." ".($hours<10?"0":"").$hours . ":" . ($min<10?"0":"").$min . ":" . ($sec<10?"0":"").$sec;
        }
        */
        $spam = false;
        $domain_list = '';
        $this->spamMailList = $this->getSpamList();
        foreach ($this->spamMailList as $domain_name) {
            if (preg_match("/".$domain_name."/i", $this->AuthManager->User->user_email)) {
                $spam = true;
                break;
            }

        }
        if ($this->AuthManager->User->banned!=0) {
            $valid = 0;
            $this->Error['system']= "Вы не можете отправить сообщение. ";
        }
        if ($spam == true) {
            $valid = 0;
            $this->Error['system']= "Вы не можете отправить сообщение. Ошибка. ";
        }
        if ($this->inBlackList($this->AuthManager->User->userID,$this->_url_params[3])) {
            $valid = 0;
            $this->Error['system']= "Вы не можете отправить сообщение. Пользователь занес Вас в черный список.";
        }
        if ($this->inBlackList($this->_url_params[3],$this->AuthManager->User->userID)) {
            $valid = 0;
            $this->Error['system']= "Вы не можете отправить сообщение. Вы занесли пользователя в черный список.";
        }
        if (isset($_SESSION['lastPagerTime']) && (time()-$_SESSION['lastPagerTime'])<USER_PAGER_TIMEOUT_MESS) {
            $valid = 0;
            $this->Error['system']= "Отправляете сообщения слишком часто.";//(USER_PAGER_TIMEOUT_MESS - (time() - $_SESSION['lastPagerTime']));
        }
        if ($this->MessageCompareLastMess($this->AuthManager->User->userID,$_message_info['content'])>USER_PAGER_TIMEOUT_MESS) {
            $valid = 0;
            $this->Error['system']= "Попытки рассылать одинаковые сообщения запрещены";
        }


        if ($valid != 1) {
            if (empty($this->Error['system'])) {
                $this->Error['system'] = "Нельзя отправить";
            }
        }
        if (empty($this->Error)) {
            $_result = $this->MessageSend($this->AuthManager->User->userID,$this->_url_params[3],$_message_info['content']);
            $_SESSION['lastPagerTime'] = time();

            if ($_result>0 && $this->AuthManager->User->danger_level>=0) {
                $this->uploadPagerFiles ($_result);
            }
            $toEmail = $this->DbManager->selectcell('SELECT user_email FROM ?# WHERE userID = ?d','forum_users',$this->_url_params[3]);
            $subscr = $this->DbManager->selectcell('SELECT pager_subscribe FROM ?# WHERE userID = ?d','forum_users',$this->AuthManager->User->userID);

            if (filter_var($toEmail,FILTER_VALIDATE_EMAIL) && $subscr==1) {
                $this->SendMessageByMail($toEmail,$_message_info['content']);
            }
        }
        if (!empty($this->Error)) {

            $this->sendErrors ($this->Error);
        } else {
            $result = array (
                'submitOn' => true,
                'callFunc' => 'addMessage'
            );
            $this->sendJSON ($result);
        }
	}
    public function uploadPagerFiles($messageId)
    {
        include (LIB_DIR . 'PHPThumb/ThumbLib.inc.php');
        $i = 0;
        foreach ($_FILES as $key => $val) {
            if ($i < 4) {
                if (is_file($val[ 'tmp_name' ]) && file_exists($val[ 'tmp_name' ])) {
                    if (! is_dir (HOME_DIR . 'attaches/personal/' . $messageId)) {
                        mkdir (HOME_DIR . 'attaches/personal/' . $messageId);
                    }
                }
                $imgId = microtime (1);
                $imgId = str_replace (',', '', $imgId);
                $imgId = str_replace ('.', '', $imgId);
                $imgId = str_replace (' ', '', $imgId);
                $ext = strtolower (array_pop (explode ('.', $val[ 'name' ])));
                $filename = $imgId . '.' . $ext;
                //$filename = $imgId . '.' . strtolower (array_pop (explode ('.', $val[ 'name' ])));
                $newFile = HOME_DIR . 'attaches/personal/'  . $messageId . '/' . $filename;
                if (copy ($val[ 'tmp_name' ], $newFile)) {
                    if ($ext!='gif') {
                        $thumb = PhpThumbFactory::create ($newFile);
                        $thumb->resize (ORIG_WIDTH, ORIG_HEIGHT);
                        $thumb->save ($newFile);
                        unset ($thumb);
                    }
                    $thumb = PhpThumbFactory::create ($newFile);
                    $thumb->adaptiveResize (THUMB_WIDTH, THUMB_HEIGHT);
                    $thumb->save (HOME_DIR . 'attaches/personal/' . $messageId . '/thumb_' . $filename);
                    $this->DbManager->query ('INSERT INTO ?# SET id=NULL, messageID=?d,filename=?,ext=?,size=?', 'forum_pager_messages_attaches', $messageId, $filename, '', filesize ($newFile));
                    unset ($thumb);
                }
            }
            $i ++;
        }
    }


	public function onEvent_ForumPagerDialogDelete($form)
	{
		$this->Form = $form;
		$me = $this->AuthManager->User->userID;
		$_dialogs = array();
		$_dialogs = $this->Form->Request['userto'];

		foreach ($_dialogs as $_key => $_val)
		{
			$this->deleteUserDialog($me, $_val);
		}
	}
	
	function Prepare(&$ds)
	{
        if (isMobile()) {
            $ds->assign("is_mobile", 'mobile');
        } else {
            $ds->assign("is_mobile", 'not mobile');
        }

        list($null,$pager,$type) = $this->_url_params;
		$ForumManager = CreateObject("Forum_Manager");
		$_groups = $ForumManager->LoadGroups();	
		$ds->assign("groups",$_groups);

        //$this->DbManager->query ("SET NAMES cp1251");
		if ($this->AuthManager->User->userID > 0)
		{
			switch ($type)
			{
                case 'getmess':
                    $mess = $this->CheckMessStat();
                    if (is_array($mess)) {
                        $result = array (
                            'submitOn' => true,
                            'callFunc' => 'updatePager',
                            'newmess' => intval ($mess['new_mess']),
                            'allmess' => intval($mess['current_mess'])
                        );
                        $this->sendJSON ($result);
                    } else {
                        $this->sendErrors (array (
                            'error' => 'ошибка #1'
                        ));
                    }
                    exit();
                    break;
			// диалог с пользователем
			 	case 'dialog':
			 		$_messages = $this->GetPagerMessByUser($this->TotalRows,$this->AuthManager->User->userID,$this->_url_params[3]);
					$this->SetMessagesAsReaded($this->AuthManager->User->userID,$this->_url_params[3]);
					$ds->assign("messages",$_messages);
					$ds->assign("user_info", $this->getFriendStats($this->_url_params[3]));
					$this->MainTemplate = "forum/personal/pager/messages.tpl";
				break;
                case 'img':
                    $result = $this->DbManager->select('
                                    SELECT *
                                    FROM ?# p
                                    LEFT JOIN ?# m ON p.messageID = m.id
                                    WHERE p.id = ?d
                                    AND (m.fromuser = ?d OR m.touser = ?d)
                                    '
                                ,'forum_pager_messages_attaches'
                                ,'forum_users_pager'
                                ,$this->_url_params[3]
                                ,$this->AuthManager->User->userID
                                ,$this->AuthManager->User->userID
                                );
                    //$newFile = HOME_DIR . 'attaches/personal/'  . $messageId . '/' . $filename;
                    $filePut = HOME_DIR . 'attaches/personal/' . $result[0]['messageID'] .'/'. $result[0]['filename'];
                    if (is_file($filePut) && file_exists($filePut)) {
                        $size = filesize($filePut);
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimetype = finfo_file($finfo, $filePut);
                        finfo_close($finfo);
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Expires: 0');
                        header('Pragma: public');
                        header('Content-type: ' . $mimetype);
                        header('Content-Length: ' . $size);
                        //header("Content-Disposition: attachment; filename=\"" . basename($filePut) . "\"");
                        readfile($filePut);
                    }
                    die();
               break;
                case 'thumb_img':
                    $result = $this->DbManager->select('
                                    SELECT *
                                    FROM ?# p
                                    LEFT JOIN ?# m ON p.messageID = m.id
                                    WHERE p.id = ?d
                                    AND (m.fromuser = ?d OR m.touser = ?d)
                                    '
                                ,'forum_pager_messages_attaches'
                                ,'forum_users_pager'
                                ,$this->_url_params[3]
                                ,$this->AuthManager->User->userID
                                ,$this->AuthManager->User->userID
                                );
                    //$newFile = HOME_DIR . 'attaches/personal/'  . $messageId . '/' . $filename;
                    $filePut = HOME_DIR . 'attaches/personal/' . $result[0]['messageID'] .'/thumb_'. $result[0]['filename'];
                    if (is_file($filePut) && file_exists($filePut)) {
                        $size = filesize($filePut);
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimetype = finfo_file($finfo, $filePut);
                        finfo_close($finfo);
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Expires: 0');
                        header('Pragma: public');
                        header('Content-type: ' . $mimetype);
                        header('Content-Length: ' . $size);
                        //header("Content-Disposition: attachment; filename=\"" . basename($filePut) . "\"");
                        readfile($filePut);
                    }
                    die();
               break;
			// диалог с пользователем (окно диалога с формой)
			 	case 'udialog':
			 		$_messages = $this->GetPagerMessByUser($this->TotalRows,$this->AuthManager->User->userID,$this->_url_params[3]);
					$ds->assign("messages",$_messages);
					$ds->assign("user_info", $this->getFriendStats($this->_url_params[3]));
					$this->MainTemplate = "forum/personal/pager/dialog.tpl";
                    if ($this->hasCaptcha) {
                        $ds->assign ('captcha', true);
                    }

				break;	
			// список сообщений.
				default:
					$ds->assign("title_part", "ПЕЙДЖЕР");
					$_userlist = $this->GetMessUserlist(1);
					$ds->assign("pagers",$_userlist);
					$this->MainTemplate = "forum/personal/pager/default.tpl";
			}
			$ds->assign('_pager_info',$this->CheckMessStat());
		}
		else
		{
			header('Location: /register/');
		}
		
		// информация для постраничного вывода.
        $_pages = round($this->TotalRows/$this->CountPerPage);
        if ($_pages < 1)
        {
            $_pages=1;
        }
        else
        {
            $_pages = $_pages+1;
        }
        $ds->assign("_totalrows",$this->TotalRows);
        $ds->assign("_page",$this->Page);
        $ds->assign("_lastpage",$_pages);

		$ds->assign("__errors", $this->Error['Forum_Pager']);
		$ds->assign("_userto",$this->_url_params[3]);
		$ds->assign("_page_countperpage",$this->CountPerPage);
	}
	
	public function deleteUserDialog($me, $user)
	{
		//Delete inbox
		$this->DbManager->query(
			"UPDATE 
				?# 
			SET 
				`del_to`= 1
			WHERE 
				`touser`= ?d 
				AND
				`fromuser` = ?d
			",
			'forum_users_pager',
			$me, $user
		);
		
		//Delete outbox
		$this->DbManager->query(
			"UPDATE 
				?# 
			SET 
				`del_from`= 1
			WHERE 
				`fromuser`= ?d 
				AND
				`touser` = ?d
			",
			'forum_users_pager',
			$me, $user
		);
		
		//Delete dialog
		$this->DbManager->query(
			"DELETE FROM
				?#
			WHERE 
				`userID`= ?d
				AND
				`userlist` = ?d
			",
			'forum_users_pagerlist',
			$me, $user
		);	
	}
	
	public function Display(&$parser)
	{
		$parser->display($this->MainTemplate);
	}
	
	private function getFriendStats($userID)
	{
		if ($userID > 0) {
			$data =  $this->DbManager->selectRow(
				"SELECT 
					`userID`, `user_name`, `lastlogin`
				FROM 
					?# 
				WHERE 
					`userID`= ?d",
				"forum_users",
				$userID
			);	
			return $data;
		}
		return null;
	}
	
	public function CheckMessStat($_user = null)
	{
		$_messages = array();
		if ($_user === null)
		{
			$_user = $GLOBALS['ForumCore']->AuthManager->User->userID;
		}
		$_messages = $this->DbManager->selectRow("-- CACHE: 0h 0m 20s
		                                        SELECT
													SUM(`allmess`) as current_mess,
													SUM(`newmess`) as new_mess
												FROM 
													?# 
												WHERE 
													`userID`=?d 
												",
												"forum_users_pagerlist",
												$_user
							);
        //var_dump($_messages);
        /*
		$_messages['new_mess'] = $this->DbManager->selectcell(
												"SELECT 

												FROM 
													?# 
												WHERE 
													`userID`=?d 
												",
												"forum_users_pagerlist",
												$_user
							);
         *
         */
		return $_messages;					
	}
	public function GetMessUserlist ($_page)
	{
		
		return $this->DbManager->select("-- CACHE: 0h 1m 00s
		    SELECT
		        p.*
		    FROM ?# p
		    WHERE p.userID=?d
		    ORDER BY p.lastmess DESC
		    "
            ,'forum_users_pagerlist'
            ,$this->AuthManager->User->userID);
		
	}
	public function GetPagerMessByUser(&$total_rows,$authorId,$to_user=null)
	{
		if ($to_user)
		{ 
			//$this->DbManager->query("UPDATE ?# SET newmess=0 WHERE userID=?d AND userlist=?d",'forum_users_pagerlist',$authorId,$to_user);
			$this->DbManager->query("UPDATE ?# SET newmess=0 WHERE userID=?d AND userlist=?d",'forum_users_pagerlist',$authorId,$to_user);
            $total_rows = $this->DbManager->selectcell(
                "SELECT
                    count(id)
                FROM
                    ?# p
                WHERE
                    ((p.fromuser=?d AND
                    p.touser = ?d AND p.del_from = 0) OR
                     (p.fromuser=?d AND
                    p.touser = ?d AND p.del_to = 0))


                ",
                'forum_users_pager',
                $authorId,
                $to_user,
                $to_user,
                $authorId
            );
            $result = $this->DbManager->select(
										"SELECT 
											*,
											INET_NTOA(creator_ip) as creator_ip,
											(SELECT count(filename) FROM ?# WHERE messageID = p.id and hidden=0) as hav_file
										FROM 
											?# p 
										WHERE 
											((p.fromuser=?d AND
											p.touser = ?d AND p.del_from = 0) OR  
											 (p.fromuser=?d AND
											p.touser = ?d AND p.del_to = 0))
										ORDER BY p.id DESC
										LIMIT ?d,?d
										",
                                        'forum_pager_messages_attaches',
										'forum_users_pager',
										$authorId,
										$to_user,
										$to_user,
										$authorId,
                                        ($this->Page - 1) * $this->CountPerPage,
                                        $this->CountPerPage
									);
            $files = array ();
            foreach ($result as $key => $val) {
                if ($val[ 'hav_file' ] > 0) {
                    $files = $this->getPagerFilesByMessagesIds ($val[ 'id' ]);
                    $val[ 'files' ] = $files;
                    $val[ 'first_file' ] = $files[ 0 ];
                } else {
                    unset ($val[ 'files' ]);
                    unset ($val[ 'first_file' ]);
                }
                // косяк в базе с массовым апдейтом хекса
                if ($val['id']<4294297) {
                    $val[ 'content' ] = iconv("windows-1251","utf-8",pack('H*', $val[ 'content' ]));
                } else {
                    $val[ 'content' ] = pack('H*', $val[ 'content' ]);
                }


                $result[ $key ] = $val;
            }
            return $result;
		}
		else
		{
			return null;
		}
	}

    public function getPagerFilesByMessagesIds($ids)
    {
        $files = $this->DbManager->select ("SELECT * FROM ?# WHERE messageID IN (?d) AND hidden=0", 'forum_pager_messages_attaches', $ids);
        $result = array ();
        if (! $files) {
            return null;
        }
        foreach ($files as $key => $val) {
            $result[ ] = $val;
        }
        return $result;
    }
    public function inBlackList($from,$to){
        $result = false;
        if ($from > 0 && $to>0) {
            $result_ins = $this->DbManager->selectcell("SELECT COUNT(*) FROM ?# WHERE userId=?d AND ownerId=?d",'forum_users_blacklist',$from,$to);
        }
        if ($result_ins>0) {
            $result = true;
        }
        return $result;
    }


    public function MessageTimeCountSession($from,$minutes=60){
        $seconds = $minutes*60;
        if ($from > 0) {
            if ($_SESSION['lastPagerTime']>=time()-$seconds) {
                return 1;
            }
        }
        return 0;
    }
    public function MessageTimeCount($from,$minutes=60){
        $result_ins = 0;
        if ($from > 0) {
            $result_ins = $this->DbManager->selectcell("SELECT COUNT(id) FROM ?# WHERE fromuser=?d AND isnew=1 AND del_to=0 AND created >= NOW() - INTERVAL ?d MINUTE",'forum_users_pager',$from,$minutes);
        }
        return $result_ins;
    }

    public function MessageCompareLastMess($from,$message){
        $result_ins = 0;
        if ($from > 0) {
            $result_ins = $this->DbManager->selectcell("SELECT COUNT(id) FROM ?# WHERE fromuser = ?d AND isnew = 1 AND del_to = 0 AND content = ? AND created >= NOW() - INTERVAL 180 MINUTE",'forum_users_pager',$from,$message);
        }
        return $result_ins;
    }


	public function MessageSend($from,$to,$message,$mail=0,$_usermail=null)
	{
        $result_ins = 0;
		if ($from > 0) {
            //$message[ 'content' ] = iconv("windows-1251","utf-8",$message[ 'content' ]);

            //$message[ 'content' ] = unpack('H*',iconv("utf-8","windows-1251",$message[ 'content' ]));
			$result_ins = $this->DbManager->query("INSERT INTO ?#
									SET 
										`id`=NULL,
										`fromuser`=?d, 
										`touser`=?d, 
										`isnew`=1, 
										`content`=HEX(?),
										`created`=NOW(), 
										`creator_ip`=INET_ATON(?),
										`del_from` = 0,
										`del_to` = 0
									",
									'forum_users_pager',
									$from,
									$to, 
									$message, 
									$GLOBALS['ForumCore']->AuthManager->RemoteAddr
								);
/*
        $fp = fopen('log.txt', 'a');
        fwrite($fp, "\n\n-----------------\n\n");
        fwrite($fp, var_export($result,true));
        fwrite($fp, "\n\n-----------------\n\n");
        fclose($fp);
*/
				$_newmess = $this->DbManager->selectcell("SELECT COUNT(id) FROM ?# WHERE `fromuser`=?d AND `touser`=?d AND `isnew`=1 AND `del_to`=0",'forum_users_pager',$from,$to);
				$_allmess = $this->DbManager->selectcell("SELECT COUNT(id) FROM ?# WHERE ((`fromuser`=?d AND `touser`=?d) OR (`fromuser`=?d AND `touser`=?d)) AND `del_to`=0 ",'forum_users_pager',$from,$to,$to,$from);
/*
				if ($_allmess>50){
					$_allmess = 50;
				}
*/
				// Обновляем диалог собеседнику
				$_dialogid=$this->DbManager->selectcell("SELECT `userID` FROM ?# WHERE userID=?d AND userlist=?d",'forum_users_pagerlist',$to,$from);
				if ($_dialogid >0)
				{
					$this->DbManager->query(
									"UPDATE 
										?# 
									SET 
										`newmess`=?d, 
										`allmess`=?d,
										`lastmess`=NOW() 
									WHERE 
										`userID`=?d 
										AND `userlist`=?d
									",
									'forum_users_pagerlist',
									$_newmess,
									$_allmess,
									$to,
									$from
								);
				}
				else
				{
					$_userlist = $this->getFriendStats($to);
					// добавляем диалог собеседнику
					$this->DbManager->query(
										"INSERT INTO 
											?# 
										SET 
											`userID`=?d,
											`username`=?,
											`userlist`=?d,
											`newmess`=1, 
											`allmess`=1,
											`lastmess`=NOW() 
										",
										'forum_users_pagerlist',
										$to,
										$this->AuthManager->User->user_name,
										$this->AuthManager->User->userID
									);
				}
				$_mydialogid=$this->DbManager->selectcell("SELECT `userID` FROM ?# WHERE userID=?d AND userlist=?d",'forum_users_pagerlist',$to,$from);
				if ($_mydialogid >0)
				{
					// Обновляем диалог себе
					$this->DbManager->query(
									"UPDATE
										?# 
									SET 
										`allmess`= ?d,
										`lastmess`= NOW() 
									WHERE 
										`userID`= ?d 
										AND userlist = ?d
									",
									'forum_users_pagerlist',
									$_allmess,
									$from,
									$to
								);

				}
				else
				{
				// добавляем диалог себе
					$this->DbManager->query(
										"INSERT INTO 
											?# 
										SET 
											id=NULL,	
											`userID`=?d,
											`username`=?,
											`userlist`=?d,
											`newmess`=0, 
											`allmess`=1,
											`lastmess`=NOW() 
										",
										'forum_users_pagerlist',
										$this->AuthManager->User->userID,
										$_userlist['user_name'],
										$_userlist['userID']
									);
									
				}
		}
        return $result_ins;
	}
	private function SendMessageByMail($_usermail,$message)
	{
		$Mailer = CreateObject("Mail_PHPMailer");
        $Mailer->CharSet = "UTF-8";
    	$Mailer->From = 'noreply@'.SERVER_NAME;      // от кого
        $Mailer->FromName = 'http://'.SERVER_NAME.'/';   // от кого
        $Mailer->AddAddress($_usermail, $_usermail); // кому - адрес, Имя
        $Mailer->Subject = "=?utf-8?b?".base64_encode("Новое приватное сообщение с ".SERVER_NAME)."?=";  // тема письма
        if (mb_strlen($message)>50) {
            $newmess = mb_substr($message,0,50)."...\n";
            $newmess .= "Сообщение полностью Вы можете прочитать на ".SERVER_NAME."\n";
        } else {
            $newmess = $message."\n";
        }
        $newmess .= "\n-------------------------------------\n";
        $newmess .= "С Уважением, Робот http://".SERVER_NAME."\n";
        $Mailer->Body = $newmess;
        @$Mailer->Send();
	    $Mailer->ClearAllRecipients();
	    @$Mailer->ClearAttachments();
	    unset($Mailer);
	}
	public function SetMessagesAsReaded($_user,$_userto)
	{
		$this->DbManager->query("UPDATE ?# SET isnew=0 WHERE `fromuser`=?d AND touser=?d",'forum_users_pager',$_userto,$_user);
	}

    public function sendJSON($jsonText)
    {
        $this->getForumModel ()->sendJSON ($jsonText);
    }

    public function sendErrors($errors)
    {
        $this->getForumModel ()->sendErrors ($errors);
    }
    public function getSpamList()
    {
        return $this->getForumModel ()->spamMailList();
    }


}