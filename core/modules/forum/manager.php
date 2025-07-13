<?php
class Forum_Manager
{
    protected $MainTemplate = "forum/index.tpl";
    var $_url_params = array();
    var $Node = null;
    var $Form = null;
    var $GroupID = null;
    var $ThemeID = null;
    var $reserveID = null;
    var $Page = 1;
    var $CountPerPage = PER_PAGE;
    var $StringCut = 2000;
    var $key = 'SuperStrongPasswordEncryptionWord';
    var $iv = '12345678';
    private $permissionModel = null;
    private $forumModel = null;
    private $hasCaptcha = false;
    private $isGroupAdministrator = false;
    private $isGroupOwner = false;
    var $Errors = array();
    var $spamMailList = array();


    function __construct()
    {
        $this->_url_params = $GLOBALS['ForumCore']->_url_params;
        list ($_a, $forum, $this->GroupID, $this->ThemeID) = $this->_url_params;
        $this->Node = $GLOBALS['ForumCore']->CurrentParam;
        $this->DbManager = $GLOBALS['ForumCore']->DbManager;
        $this->AuthManager = CreateObject("Auth_Manager");
        if (isset ($_GET['p']) && $_GET['p'] > 0) {
            $this->Page = intval($_GET['p']);
        }

        if (!$_SESSION['captcha'] && $this->AuthManager->User->userID == 0) {
            $this->hasCaptcha = true;
        }
        $this->getPermissionModel($this->DbManager, $this->AuthManager);
        $this->getForumModel($this->DbManager, $this->AuthManager);

        //var_dump($this->AuthManager->RemoteAddr);
        //var_dump($this->AuthManager->Forwarder);
        /*
                if ($_SERVER['REQUEST_METHOD']=='POST') {

                    $_res = $this->DbManager->query('INSERT INTO ?#
                                                SET ip = ?
                                                ,grey_ip = ?
                                                ,serialized=?s
                                                '
                        ,'forum_db_post_log'
                        ,$this->AuthManager->RemoteAddr
                        ,$this->AuthManager->Forwarder
                        ,serialize($_POST)."\n".serialize($_SERVER)."\n".serialize($_GET)."\n".serialize($_COOKIE)."\n".serialize($_SESSION)
                    );
                }
        */
    }

    public function getPermissionModel($dbManager = null, $authManager = null)
    {
        if (!$this->permissionModel) {
            require_once(MODULE_DIR . 'forum/permissoin.class.php');
            $this->permissionModel = new Permission ($dbManager, $authManager);
        }
        return $this->permissionModel;
    }

    public function getForumModel($dbManager = null, $authManager = null)
    {
        if (!$this->forumModel) {
            require_once(MODULE_DIR . 'forum/forum.class.php');
            $this->forumModel = new ForumModel ($dbManager, $authManager);
            $this->forumModel->CountPerPage = $this->CountPerPage;
        }
        return $this->forumModel;
    }

    // обработка событий
    // увеличиваем уровень негатива сообщения
    public function onEvent_ForumSetRedZone($form)
    {
        $this->Form = $form;
        // берем информацию о пользователе. например ИП, refferer, авторизованность
        $_user_info = $this->GetAuthorInfo($GLOBALS['ForumCore']->AuthManager->User->user_name);
        //TODO: сделать проверку на валидность данных пользователя.
        /*
         * ип пользователя не забанен
         * пользователь пришел со страницы форума
         * пользователь пишет с браузера
         * содержание сообщения не противоречит правилам
         */
        if (true) {
            $this->SetMessageRedZone($this->Form->Request['message']);
        }
    }

    // Поиск темы по форуму.
    public function onEvent_ForumSearchTheme($form)
    {
        $this->Form = $form;
        if (strlen($this->Form->Request['skey']) > 4) {
            //$_search_key = mysql_escape_string($this->Form->Request['skey']);
            //$this->DbManager->query("INSERT INTO `forum_db_temp_search` SELECT null, `themeID`, `groupID`, `caption`, `author`, `authorID`, '".$_COOKIE['search_id']."', NOW(), '".$_search_key."' as `searchstr` FROM `forum_db_themes` WHERE `caption` LIKE '%".$_search_key."%'");
            $GLOBALS['ForumCore']->Search = CreateObject("Search_Manager");
            $data = $GLOBALS['ForumCore']->Search->performQuery($this->Form->Request['skey']);
        } else {
        }
    }

    public function onEvent_AddQuestion($form)
    {
        if ($this->AuthManager->User->userID) {
            if ((int)$form->Request['_data']['mod'] == 0) {
                $this->Errors[] = 'Не выбран модератор';
            }
            if (trim($form->Request['_data']['question']) == '') {
                $this->Errors[] = 'Не задан вопрос';
            }
            if (count($this->Errors) == 0) {
                $_sql = "SELECT COUNT(*) as cnt FROM ?# WHERE `userID` = ?d AND `is_answer` = 1";

                $_ismod = $this->DbManager->selectrow($_sql, "forum_users_rights", $form->Request['_data']['mod']);

                if ($_ismod['cnt'] > 0) {
                    $_sql = "SELECT * FROM ?# WHERE `userID` = ?d";

                    $_sql = "SELECT COUNT(*) as cnt FROM ?# WHERE `userID` = ?d AND `userlist` = ?d";

                    $_list = $this->DbManager->selectrow($_sql, "forum_users_pagerlist", $form->Request['_data']['mod'], $this->AuthManager->User->userID);

                    if ($_list['cnt'] == 0) {
                        $_sql = "INSERT INTO `forum_users_pagerlist` SET
										`userID` = ?d,
										`username` = ?s,
										`avatar` = '',
										`userlist` = ?d,
										`newmess` = '1',
										`allmess` = '1',
										`lastmess` = '" . date("Y-m-d H:i:s") . "'";
                        //mysql_query($_sql);
                        $this->DbManager->query($_sql, $form->Request['_data']['mod'], $this->AuthManager->User->user_name, $this->AuthManager->User->userID);
                    } else {
                        $_sql = "UPDATE `forum_users_pagerlist` SET
										`newmess` = (`newmess` + 1),
										`allmess` = (`allmess` + 1),
										`lastmess` = '" . date("Y-m-d H:i:s") . "'
										WHERE
										`userID` = ?d
										AND
										`userlist` = ?d LIMIT 1";
                        //mysql_query($_sql);
                        $this->DbManager->query($_sql, $form->Request['_data']['mod'], $this->AuthManager->User->userID);
                    }

                    $_sql = "INSERT INTO `forum_users_pager` SET
									`touser` = ?d,
									`fromuser` = ?d,
									`isnew` = '1',
									`created` = '" . date("Y-m-d H:i:s") . "',
									`content` = HEX(?s),
									`creator_ip` = INET_ATON(?)";

                    $this->DbManager->query($_sql, $form->Request['_data']['mod'], $this->AuthManager->User->userID, "* FEEDBACK *\r\n" . trim($form->Request['_data']['question']), $this->AuthManager->RemoteAddr);
                }
                header('Location: /forum/sendquestion/?_msg=ok');
                exit ();
            }
        }
    }

    public function validateMessageForm($form)
    {
        $result = true;
        if (!$this->validateFiles()) {
            $this->Errors[] = 'Невозможно загрузить файлы. Неверный формат';
            $result = false;
        }
        if ($GLOBALS['ForumCore']->Protector->_read_only == 1) {
            $this->Errors[] = 'Вам нельзя добавлять сообщения';
            $result = false;
        } elseif ($this->GetThemeParam('is_locked', 'integer') == 1) {
            $this->Errors[] = 'Тема закрыта администратором';
            $result = false;
        } elseif (($this->GetThemeGroupParam('is_mat', 'integer') == 1 && $this->AuthManager->User->userID > 0 && $this->AuthManager->User->danger_level <= USER_MAT_LEVEL)) {
            $this->Errors[] = 'Вам нельзя добавлять сообщения в матоязычные. У Вас слишком низкая карма';
            $result = false;
        } elseif (($this->GetThemeGroupParam('is_mat', 'integer') == 0 && $this->AuthManager->User->userID > 0 && $this->AuthManager->User->danger_level <= USER_LEVEL)) {
            $this->Errors[] = 'Вам нельзя добавлять сообщения. У Вас низкая карма';
            $result = false;
        }

        if ($this->AuthManager->User->userID == 0) {
            if (ANONYMOUS_MESSAGES_LIMIT_DAY > 0) {
                if ($this->getMessagesCountByIp($this->Form->Request['message']['groupID']) > ANONYMOUS_MESSAGES_LIMIT_DAY) {
                    $this->Errors[] = 'Превышено ограничение на количество анонимных сообщений.';
                    $result = false;
                }
            } else {
                $this->Errors[] = 'Превышено ограничение на количество анонимных сообщений.';
                $result = false;
            }
        }
        return $result;
    }

    //создание сообщения	
    public function onEvent_ForumCreateMessage($form)
    {
        if ($this->validateMessageForm($form)) {
            /*
                        $fp = fopen('log.txt', 'a');
                        fwrite($fp, "\n\n-----------------\n\n");
                        fwrite($fp, "\n\n---STEP1---\n\n");
                        fwrite($fp, var_export($_COOKIE,true));
                        fwrite($fp, "\n\n-----------------\n\n");
                        fwrite($fp, var_export($this->Errors,true));
                        fwrite($fp, "\n\n-----------------\n\n");
                        fclose($fp);
            */

            if ($_SERVER['SERVER_NAME'] == 'forum.rde.ru') {
                $_updated = $this->DbManager->selectcell("SELECT TIMESTAMPDIFF(second,updated,now()) FROM forum_db_themes WHERE themeID = ?d", $this->ThemeID);
            } else {
                $redis = new Redis();
                $redis->pconnect('192.168.122.11');
                $_updated = time() - $redis->get(sprintf("theme_updated:%d", $this->ThemeID));
                if ($_updated <= 0) {
                    $_updated = $this->DbManager->selectcell("SELECT TIMESTAMPDIFF(second,updated,now()) FROM forum_db_themes WHERE themeID = ?d", $this->ThemeID);
                }
            }

            //$_updated = $this->DbManager->selectcell ("SELECT TIMESTAMPDIFF(second,updated,now()) FROM forum_db_themes WHERE themeID = ?d", $this->ThemeID);

            if (intval($_updated) > TIMEOUT_THEME_UPDATE) {
                $this->hasCaptcha = true;
            }
            if ($this->AuthManager->User->userID == 0) {
                $this->hasCaptcha = true;
            }
            $isThemeOwner = $this->getPermissionModel()->isThemeOwner($this->ThemeID, $this->AuthManager->User->userID);
            $currentGroup = $this->getGroupInfo($this->GroupID);
            $isGroupOwner = $this->getPermissionModel()->isGroupOwner($currentGroup, $this->AuthManager->User->userID);
            $isGroupAdministrator = $this->getPermissionModel()->isGroupModerator($this->GroupID, $this->AuthManager->User->userID);
            if ($isGroupAdministrator || $isGroupOwner || $this->AuthManager->User->is_admin || $isThemeOwner) {
                $this->hasCaptcha = false;
            }


            //постим
            $this->Form = $form;

            $decrypted = decrypt($_SESSION['_thread'], ENCRYPT_KEY, ENCRYPT_IV, ENCRYPT_BIT_CHECK);

            if ($this->hasCaptcha) {
                if (empty ($this->Form->Request['imageString']) || $this->Form->Request['imageString'] != $decrypted) {
                    $this->Errors['imageString'] = 'ОШИБКА: Неверный код подтверждения';
                    $this->sendErrors($this->Errors);
                    $_SESSION['captcha'] = false;
                    $this->hasCaptcha = false;
                } else {
                    $_SESSION['captcha'] = true;
                    $this->hasCaptcha = true;
                }
            }
            $groupParams = $this->getForumModel()->getGroupInfo($this->GroupID);
            $this->spamMailList = $this->getSpamList();
            foreach ($this->spamMailList as $domain) {
                if (preg_match("/" . $domain . "/i", $this->AuthManager->User->user_email)) {
                    $this->Errors['system'] = 'Ваш аккаунт не активирован. Для активации перейдите по ссылке: <a href="/register/getconfirm">выслать код активации</a>';
                    break;
                }
            }

            if (!$this->getPermissionModel()->isGroupOpen($groupParams)) {
                $this->Errors[] = 'Доступ в раздел закрыт';
            }

            /*
            if (!$this->Errors) {
                $allowedDate = time() - strtotime($this->AuthManager->User->registered);
                if ($allowedDate<FRESH_USERS_SECOND_LIMIT) {
                    $time = FRESH_USERS_SECOND_LIMIT - $allowedDate;
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

                    $this->Errors[ 'system' ] = "Вы недавно зарегистрировались и пока не можете написать сообщение. Осталось "
                        //." lastlogin=".$this->AuthManager->User->registered
                        //." lastlogin_totime=".strtotime($this->AuthManager->User->registered)
                        //." time=".time()
                        //." time-lastlogin=".(time() - strtotime($this->AuthManager->User->registered))
                        //." allowed=".$allowedDate
                        //." time=".$time
                        ." ".($hours<10?"0":"").$hours . ":" . ($min<10?"0":"").$min . ":" . ($sec<10?"0":"").$sec;
                }
            }
            */

            if (!$this->Errors) {
                $_user_info = $this->GetAuthorInfo($this->Form->Request['_message']['author']);
                if (($groupParams['deny_guest'] == 1)
                    && $this->AuthManager->User->user_name != $this->Form->Request['_message']['author']
                    && !empty($this->Form->Request['_message']['author'])
                ) {
                    $this->Errors[] = 'В данной теме пользоваться анонимными никами нельзя';
                }

                //проверка разницы времени обновления темы и текущим временем,
                //если разница больше OLD_THEME, значит тема старая и надо +1
                // в сессию у параметра old_theme_count и записать olt_teme_updated_time
                //как только параметр old_theme_count из сессии превышет OLD_THEME_COUNT, 
                //то проверять время последнего добавления в старую тему.
                //Как только прошло больше чем OLD_THEME_TIMEOUT секунд, в тему добавлять можно.
                if ($_SERVER['SERVER_NAME'] == 'forum.rde.ru') {
                    $_updated = $this->DbManager->selectcell("SELECT TIMESTAMPDIFF(second,updated,now()) FROM forum_db_themes WHERE themeID = ?d", $this->ThemeID);
                } else {
                    $redis = new Redis();
                    $redis->pconnect('192.168.122.11');
                    $_updated = time() - $redis->get(sprintf("theme_updated:%d", $this->ThemeID));
                    if ($_updated <= 0) {
                        $_updated = $this->DbManager->selectcell("SELECT TIMESTAMPDIFF(second,updated,now()) FROM forum_db_themes WHERE themeID = ?d", $this->ThemeID);
                    }
                }
                $this->Form->Request['_message']['content'] = trim($this->Form->Request['_message']['content']);
                //$_updated = $this->DbManager->selectcell ('SELECT TIMESTAMPDIFF(second,updated,now()) FROM forum_db_themes WHERE themeID = ?d', $this->ThemeID);
                //$_SESSION['old_theme_count'] = 0;
                $timeout = 60 * 60 * 3; //60 секудн * 60 минут * 3 часа


                if (!isset($_SESSION['old_theme_count'])) {
                    $_SESSION['old_theme_count'] = 0;
                }
                if (!isset($_SESSION['olt_teme_updated_time'])) {
                    $_SESSION['olt_theme_updated_time'] = 0;
                }
                //если с момента прошлого сообщения прошло больше 3 часов
                if ((time() - $_SESSION['olt_theme_updated_time']) >= $timeout) {
                    //сессия со счетчиком обнуляется
                    $_SESSION['old_theme_count'] = 0;
                }

                //если счетчик больше OLD_THEME_COUNT (поднимаем много старых тем)
                if ($_SESSION['old_theme_count'] > OLD_THEME_COUNT) {
                    //вычислям время последнего обновления старой темы
                    $last_old_theme_update = time() - $_SESSION['olt_theme_updated_time'];

                } else {
                    $last_old_theme_update = time();
                }
                //$this->Errors[ 'system' ] = $last_old_theme_update;
                //если время последнего обновления старой темы >
                if ($last_old_theme_update < OLD_THEME_TIMEOUT) {
                    //добавлять сообщения в старую тему нельзя
                    $this->Errors['system'] = 'ОШИБКА: Чтоб поднять еще одну старую тему Вам нужно подождать ' . (OLD_THEME_TIMEOUT - $last_old_theme_update) . ' сек. ';
                } else {
                    //если разница времени обновления темы и текущим моментом больше OLD_THEME (тема старая)
                    if ($_updated > OLD_THEME) {
                        //увеличиваем счетчик
                        $_SESSION['old_theme_count']++;
                        //и сохраняем время последнего обновления темы
                        $_SESSION['olt_theme_updated_time'] = time();
                    }
                }


                //var_dump($_updated);


                if (strlen(trim($this->Form->Request['_message']['content'])) < 3) {
                    $this->Errors['system'] = 'ОШИБКА: Сообщение не может быть короче 3х символов.' . ($_user_info['fz152_agreement'] <> 0 ? 1 : 2);
                    //} elseif ($_user_info['fz152_agreement']==0) {
                    //    $this->Errors[ 'system' ] = 'ОШИБКА: Вы не можете писать на форуме, пока на согласитесь с условиями обработки персональных данных в разделе Мой паспорт.';
                } else {
                    if (!$_user_info) {
                        $this->Errors['system'] = 'ОШИБКА: Нет прав для отправки сообщения. Попробуйте выйти и зайти заново. ';
                    } elseif (!$this->Errors) {

                        if (empty($this->Form->Request['_message']['author']) && empty($_COOKIE['_cookie_name'])) {
                            $this->Form->Request['_message']['author'] = $_user_info['author'];
                        }
                        if (!empty($this->Form->Request['_message']['author'])) {
                            setcookie('_cookie_name', htmlspecialchars($this->Form->Request['_message']['author']), 0, '/', '.' . SERVER_NAME, 0, 0);
                        }
                        if (empty($this->Form->Request['_message']['author']) && !empty($_COOKIE['_cookie_name'])) {
                            $this->Form->Request['_message']['author'] = $_COOKIE['_cookie_name'];
                        }

                        //$_user_message = $this->Form->Request[ '_message' ];
                        if ($GLOBALS['ForumCore']->Protector->PostFilter($this->Form->Request['_message'])
                            && $this->ThemeID > 0
                        ) {


                            $_themeInfo = 0;
                            $sql = "SELECT themeID FROM ?# WHERE themeID = ?d AND ifnull(top_end,NOW()) >= NOW() AND (hottop = 1)";
                            $_themeInfoHot = $this->DbManager->selectcell($sql, 'forum_db_themes', $this->ThemeID);
                            $sql = "SELECT themeID FROM ?# WHERE themeID = ?d AND ifnull(top_end,NOW()) >= NOW() AND (is_top = 1)";
                            $_themeInfoTop = $this->DbManager->selectcell($sql, 'forum_db_themes', $this->ThemeID);
                            //$this->Errors[ 'system' ] = var_export($_themeInfo,true);
                            if (isset($this->Form->Request['_message']['top'])) {
                                if ($_themeInfoTop == 0
                                    && $this->Form->Request['_message']['top'] == 1
                                    && $this->AuthManager->User->userID > 0
                                    && $this->AuthManager->User->user_balance >= TOP_PRICE
                                ) {
                                    $this->Form->Request['_message']['top'] = 1;
                                } else {
                                    if ($_themeInfoTop <> 0) {
                                        $this->Errors['system'] = 'Ошибка при использовании опции поднятия темы в ТОП: тема уже в топе';
                                    } elseif ($this->AuthManager->User->user_balance < TOP_PRICE) {
                                        $this->Errors['system'] = 'Ошибка при использовании опции поднятия темы в ТОП: не достаточно денег';
                                    } elseif ($this->AuthManager->User->userID <= 0) {
                                        $this->Errors['system'] = 'Ошибка при использовании опции поднятия темы в ТОП: ошибка с идентификацией пользователя ';
                                    } else {
                                        $this->Errors['system'] = 'Ошибка при использовании опции поднятия темы в ТОП ';
                                    }
                                }
                            }
                            if (isset($this->Form->Request['_message']['top30'])) {
                                if ($_themeInfoHot == 0
                                    && $this->Form->Request['_message']['top30'] == 1
                                    && $this->AuthManager->User->userID > 0
                                    && $this->AuthManager->User->user_balance >= TOP30_PRICE
                                    && $this->getHotThemesCount() <= HOTTOP_THEMES
                                ) {
                                    $this->Form->Request['_message']['top30'] = 1;
                                } else {
                                    $this->Errors['system'] = 'Ошибка при использовании опции поднятия темы в Горячее ';
                                    if ($_themeInfoHot <> 0) {
                                        $this->Errors['system'] = 'Ошибка при использовании опции поднятия темы в Горячее: тема уже в горячем';
                                    } elseif ($this->AuthManager->User->user_balance < TOP30_PRICE) {
                                        $this->Errors['system'] = 'Ошибка при использовании опции поднятия темы в Горячее: не достаточно денег';
                                    } elseif ($this->getHotThemesCount() > HOTTOP_THEMES) {
                                        $this->Errors['system'] = 'Ошибка при использовании опции поднятия темы в Горячее: слошком много тем в горячем';
                                    } elseif ($this->AuthManager->User->userID <= 0) {
                                        $this->Errors['system'] = 'Ошибка при использовании опции поднятия темы в Горячее: ошибка с идентификацией пользователя ';
                                    } else {
                                        $this->Errors['system'] = 'Ошибка при использовании опции поднятия темы в Горячее ';
                                    }

                                }
                            }
                            $user_message_break = false;
                            $messages1 = $this->GetLastMessagesInTheme();
                            $messages2 = $this->GetLastMessagesByUserID();
                            $message = $this->stripSpaces(strip_tags($this->Form->Request['_message']['content']));
                            $messages = array_merge($messages1, $messages2);
                            for ($i = 0; $i < count($messages); $i++) {
                                similar_text($messages[$i], $message, $percent);
                                if ($percent > SIMILAR_PERCENT) {
                                    $user_message_break = true;
                                }
                            }
                            if (!$user_message_break) {
                                if ($this->Form->Request['_message']['content'] != $_COOKIE['lastmess'] && $this->Form->Request['_message']['content'] != '') {
                                    if (count($this->Errors) == 0) {
                                        $messageId = $this->AddForumMessage($this->Form->Request['_message'], $_user_info);
                                        setcookie('_thread', '', time() - 6000, '/', $_SERVER['SERVER_NAME']);
                                        setcookie('_thread', '', time() - 6000, '/', '.' . $_SERVER['SERVER_NAME']);
                                        unset($_SESSION['_thread']);
                                        unset($_SESSION['captcha']);
                                        //$this->LoadMessagesToCache($this->ThemeID, $messageId);
                                    }
                                    if (!$messageId && empty($this->Errors['system'])) {
                                        $this->Errors['system'] = 'ОШИБКА: Ошибка добавления сообщения';
                                    } else {
                                        $this->updateMessagesCountByIp($this->Form->Request['_message']['groupID']);
                                        $this->uploadFiles($this->ThemeID, $messageId);
                                        setcookie('lastmess', $this->Form->Request['_message']['content'], 0, '/', '.' . SERVER_NAME, 0, 0);
                                        //$_SESSION['thread'] = $this->ThemeID;
                                        //$_SESSION['updated'] = $_updated;
                                        if ($this->Form->Request['_message']['top']) {
                                            if ($this->AuthManager->User->user_balance >= TOP_PRICE) {
                                                $this->AuthManager->User->user_balance = $this->AuthManager->User->user_balance - TOP_PRICE;
                                                $this->billingLog(TOP_PRICE, 'Операция закрепления темы ' . $this->ThemeID . ' на сумму ' . TOP_PRICE . 'р.');
                                            } else {
                                                $this->Errors['system'] = 'ОШИБКА: Ошибка снятия средств';
                                            }
                                        }
                                        if ($this->Form->Request['_message']['top30']) {
                                            if ($this->AuthManager->User->user_balance >= TOP30_PRICE) {
                                                $this->AuthManager->User->user_balance = $this->AuthManager->User->user_balance - TOP30_PRICE;
                                                $this->billingLog(TOP30_PRICE, 'Операция закрепления темы ' . $this->ThemeID . ' на сумму ' . TOP30_PRICE . 'р.');
                                            } else {
                                                $this->Errors['system'] = 'ОШИБКА: Ошибка снятия средств';
                                            }
                                        }
                                        unset ($this->AuthManager->User->Allow);
                                        $use_pass = 2; //не менять пароль
                                        $this->AuthManager->AuthDb->SaveUser($this->AuthManager->CreateUserArray($this->AuthManager->User), $use_pass);
                                    }
                                } else {
                                    $this->Errors['system'] = 'ОШИБКА: Флуд запрещен';
                                }
                            } else {
                                $this->Errors['system'] = 'ОШИБКА: Сообщение подобно предыдущим. Измените его';
                            }
                        } else {
                            $this->Errors['system'] = 'ОШИБКА: Сообщение/автор/заголовок содержат запрещённые слова: "' . $_SESSION['badword'] . '"';
                        }
                    }
                }


            }
        }
        /*
                $fp = fopen('log.txt', 'a');
                fwrite($fp, "\n\n-----------------\n\n");
                fwrite($fp, "\n\n---STEP_LAST---\n\n");
                fwrite($fp, var_export($_COOKIE,true));
                fwrite($fp, "\n\n-----------------\n\n");
                fwrite($fp, var_export($this->Errors,true));
                fwrite($fp, "\n\n-----------------\n\n");
                fclose($fp);
        */
        if ($this->Errors) {
            $this->sendErrors($this->Errors);
        } else {
            $result = array(
                'submitOn' => true,
                'callFunc' => 'addMessage',
                'author' => $this->Form->Request['_message']['author'],
                'authorID' => $_user_info['authorID']
            );
            $this->sendJSON($result);
        }
    }

    public function stripSpaces($str)
    {
        return preg_replace(array('/\s{2,}/', '/[\t\n]/'), '', $str);
    }


    private function makeServiceLink($objid, $args)
    {
        $services = $args['services'];
        $skipcheck = false;
        if (isset ($args['skipcheck']))
            $skipcheck = $args['skipcheck'];

        $packageid = 0;
        if (isset ($args['packageid']))
            $packageid = $args['packageid'];

        if (!is_array($services))
            $services = array();

        //echo '<pre>' . print_r($objid, true) . print_r($packageid, true) . print_r($services, true) . print_r($skipcheck, true) . '</pre>';
        $cSerives = CreateObject('Money_Commercialservice', array(
            'DbManager' => $this->DbManager
        ));
        $cSerives->makeServiceLink($objid, $packageid, $this->AuthManager->User->userID, $services, $skipcheck);
    }

    public function onEvent_ForumDeleteMessageInMyTheme($form)
    {
        if ($this->AuthManager->User->userID > 0 && (int)$form->Request['_msgId'] > 0) {
            $_msgId = (int)$form->Request['_msgId'];
            if ($this->AuthManager->User->user_balance >= HIDE_MESSAGE_COST) {
                $_passport_info = $this->DbManager->selectrow("SELECT * FROM ?# WHERE userID=?d", 'forum_users', $this->AuthManager->User->userID);
                $result_balance = $_passport_info['user_balance'] - HIDE_MESSAGE_COST;
                $this->DbManager->query("UPDATE ?# SET
                                                user_balance = ?d
                                                WHERE
                                                    userID = ?d", 'forum_users', $result_balance, $this->AuthManager->User->userID);
                $this->AuthManager->User->user_balance = $result_balance;
                //$this->billingLog(HIDE_MESSAGE_COST, 'Скрытие сообщения в собственной теме на сумму ' . HIDE_MESSAGE_COST . 'р.');

                $ix = sprintf("forumAuth_FindUserById_%d", $this->AuthManager->User->userID);
                if (xcache_isset($ix)) {
                    $_user = unserialize(xcache_get($ix));
                    $_user['user_balance'] = $this->AuthManager->User->user_balance;
                    xcache_set($ix, serialize($_user), 1200);
                }
                $_cnt = $this->DbManager->query("UPDATE ?#
                            SET `hidden` = 1
                            WHERE `messageID` = ?d
                            AND `hidden` = 0", "forum_messages_attaches", $_msgId);
                $_cnt = $this->DbManager->query("UPDATE ?#
                            SET `hidden` = 1
                            WHERE `messageID` = ?d
                            AND `hidden` = 0", "forum_db_messages", $_msgId, $this->AuthManager->User->userID);
                $this->messageHideLog($_msgId);
                if (!$_cnt) {
                    $this->Errors['system'] = 'Update error';
                }
            } else {
                $this->Errors['system'] = 'Стоимость ' . HIDE_MESSAGE_COST . 'р.';
            }
        }
        if ($this->Errors) {
            $this->sendErrors($this->Errors);
        } else {
            $result = array(
                'submitOn' => true,
                'callFunc' => 'hideMessage'
            );
            $this->sendJSON($result);
        }
    }

    public function onEvent_ForumHideAjaxMessage($form)
    {

        if ($this->AuthManager->User->userID > 0 && (int)$form->Request['_msgId'] > 0) {
            $_msgId = (int)$form->Request['_msgId'];
            $messageInfo = $this->DbManager->selectrow('SELECT * FROM ?# m WHERE messageID = ?d', 'forum_db_messages', $_msgId);
            $lastMess = $this->GetRealLastMessTheme($messageInfo['themeID']);

            $isThemeOwner = $this->getPermissionModel()->isThemeOwner($messageInfo['themeID'], $this->AuthManager->User->userID);
            $currentGroup = $this->getGroupInfo($messageInfo['groupID']);
            $isGroupOwner = $this->getPermissionModel()->isGroupOwner($currentGroup, $this->AuthManager->User->userID);
            $isGroupAdministrator = $this->getPermissionModel()->isGroupModerator($messageInfo['groupID'], $this->AuthManager->User->userID);

            if ($isGroupAdministrator || $isGroupOwner || $this->AuthManager->User->is_admin) {
                $_cnt = $this->DbManager->query("UPDATE ?#
                            SET `hidden` = 1
                            WHERE `messageID` = ?d
                            AND `hidden` = 0", "forum_messages_attaches", $_msgId);
                $_cnt = $this->DbManager->query("UPDATE ?#
                            SET `hidden` = 1
                            WHERE `messageID` = ?d
                            AND `hidden` = 0", "forum_db_messages", $_msgId);
                if ($_msgId == $lastMess['messageID']) {
                    //$lastMess = $this->GetRealLastMessTheme($messageInfo['themeID']);
                    $_cnt = $this->DbManager->query("UPDATE ?#
                                SET fact_date = ?,updated = ?
                                WHERE `themeID` = ?d
                                ", "forum_db_themes", $lastMess['created'], $lastMess['created'], $messageInfo['themeID']);

                }
                $this->messageHideLog($_msgId);
            } else {
                $_cnt = false;
            }


            if (!$_cnt) {
                $this->Errors['system'] = 'Update error';
            }
        } else {
            $this->Errors['system'] = 'Auth error';
        }
        if ($this->Errors) {
            $this->sendErrors($this->Errors);
        } else {
            $result = array(
                'submitOn' => true,
                'callFunc' => 'hideMessage'
            );
            $this->sendJSON($result);
        }
    }

    public function onEvent_ForumHideAjaxUserMessages($form)
    {
        if ($this->AuthManager->User->userID > 0 && (int)$form->Request['_uId'] > 0) {
            $_uId = (int)$form->Request['_uId'];
            if ($this->AuthManager->User->is_admin) {
                $ret4 = $this->DbManager->query('UPDATE ?#
                SET hidden=1
                    ,hidden_time=now()
                    ,is_locked=1
                WHERE authorID = ?d
                AND hidden=0
                AND created > (NOW() - INTERVAL 1 MONTH )
                '
                //and TO_DAYS(NOW()) - TO_DAYS(created)<=7
                , 'forum_db_themes', $_uId);
                $ret1 = $this->DbManager->query('UPDATE ?# m
                SET hidden=1
                WHERE authorID = ?d
                AND created > (NOW() - INTERVAL 1 MONTH )
                and hidden=0'
                    //and TO_DAYS(NOW()) - TO_DAYS(created)<=7
                    , 'forum_db_messages', $_uId);
                $ret2 = $this->DbManager->query('UPDATE ?# m SET del_from=1,del_to=1,isnew=0 WHERE fromuser = ?d', 'forum_users_pager', $_uId);
                $ret3 = $this->DbManager->query('DELETE FROM ?# m WHERE userlist = ?d', 'forum_users_pagerlist', $_uId);
            } else {
                $this->Errors['system'] = 'Auth error';
            }
        } else {
            $this->Errors['system'] = 'Param error';
        }
        if ($this->Errors) {
            $this->sendErrors($this->Errors);
        } else {
            $result = array(
                'submitOn' => true,
                'callFunc' => 'hideMessage'
            );
            $this->sendJSON($result);
        }
    }

    public function onEvent_ForumBanAjaxUserForever($form)
    {
        if ($this->AuthManager->User->userID > 0 && (int)$form->Request['_uId'] > 0 && $this->AuthManager->User->is_admin) {
            $_uId = (int)$form->Request['_uId'];
            $address = $this->DbManager->selectCell('SELECT user_ip FROM ?# WHERE userID = ?d', 'forum_users', $_uId);
            if ($this->AuthManager->User->is_admin) {

                $ret1 = $this->DbManager->query('UPDATE ?# SET banned=1 WHERE userID = ?d', 'forum_users', $_uId);

                $ret2 = $this->DbManager->query('REPLACE INTO
						?#(userID, user_ip, banned, adminID, when_banned, ban_period, ruleID, admin_comment, is_confirmed)
					VALUES(?d, ?s, 1, ?d, NOW(), ?d, ?d, ?s, 1)',
                    "forum_banned_users",
                    $_uId,
                    $address,
                    $this->AuthManager->User->userID,
                    300000000,
                    5,
                    'Нарушение правил');
            } else {
                $this->Errors['system'] = 'Auth error';
            }
        } else {
            $this->Errors['system'] = 'Param error';
        }
        if ($this->Errors) {
            $this->sendErrors($this->Errors);
        } else {
            $result = array(
                'submitOn' => true,
                'callFunc' => 'hideMessage'

            );
            $this->sendJSON($result);
        }
    }

    public function onEvent_ForumAjaxUserClearKarma($form)
    {
        if ($this->AuthManager->User->userID > 0 && (int)$form->Request['_uId'] > 0 && $this->AuthManager->User->is_admin) {
            $_uId = (int)$form->Request['_uId'];
            if ($this->AuthManager->User->is_admin) {

                $ret1 = $this->DbManager->query('UPDATE ?# SET danger_level=0 WHERE userID = ?d', 'forum_users', $_uId);
            } else {
                $this->Errors['system'] = 'Auth error';
            }
        } else {
            $this->Errors['system'] = 'Param error';
        }
        if ($this->Errors) {
            $this->sendErrors($this->Errors);
        } else {
            $result = array(
                'submitOn' => true,
                'callFunc' => 'hideMessage'

            );
            $this->sendJSON($result);
        }
    }

    public function onEvent_ForumHideAjaxMessageAll($form)
    {
        if ($this->AuthManager->User->userID > 0 && (int)$form->Request['_msgId'] > 0) {
            $_msgId = (int)$form->Request['_msgId'];
            $messageInfo = $this->DbManager->selectrow("SELECT * FROM ?# m WHERE messageID = ?d", 'forum_db_messages', $_msgId);
            $currentGroup = $this->getGroupInfo($messageInfo['groupID']);
            $isGroupOwner = $this->getPermissionModel()->isGroupOwner($currentGroup, $this->AuthManager->User->userID);
            $isGroupAdministrator = $this->getPermissionModel()->isGroupModerator($messageInfo['groupID'], $this->AuthManager->User->userID);


            if (($isGroupAdministrator || $isGroupOwner || $this->AuthManager->User->is_admin) && ($messageInfo['authorID'] > 0)) {
                /*
                foreach ($messages as $messageItem) {
                    $lastMess = $this->GetRealLastMessTheme($messageItem['themeID']);

                    $_cnt = $this->DbManager->query ("UPDATE ?#
                                    SET `hidden` = 1
                                    WHERE `messageID` = ?d
                                    AND `hidden` = 0", "forum_messages_attaches", $messageItem['messageID']);
                    $_cnt = $this->DbManager->query ("UPDATE ?#
                                    SET `hidden` = 1
                                    WHERE `messageID` = ?d
                                    AND `hidden` = 0", "forum_db_messages", $messageItem['messageID']);
                    $this->messageHideLog($messageItem['messageID']);
                    if ($messageItem['messageID'] == $lastMess['messageID']) {
                        //$lastMess = $this->GetRealLastMessTheme($messageItem['themeID']);
                        $_cnt = $this->DbManager->query ("UPDATE ?#
                                        SET fact_date = ?,updated = ?
                                        WHERE `themeID` = ?d
                                        ", "forum_db_themes", $lastMess['created'],$lastMess['created'], $messageItem['themeID']);
                    }
                    if (! $_cnt) {
                        $this->Errors[ 'system' ] = 'Update error';
                    }
                }
                */
                $ret4 = $this->DbManager->query('UPDATE ?#
                SET hidden=1
                ,hidden_time=now()
                ,is_locked=1
                WHERE authorID = ?d
                AND hidden=0
                AND created > (NOW() - INTERVAL 1 MONTH )
                '
                    //and TO_DAYS(NOW()) - TO_DAYS(created)<=7
                    , 'forum_db_themes', $messageInfo['authorID']);
                $ret1 = $this->DbManager->query('UPDATE ?# m
                SET hidden=1
                WHERE authorID = ?d
                and hidden=0
                AND created > (NOW() - INTERVAL 1 MONTH )
                '
                //and TO_DAYS(NOW()) - TO_DAYS(created)<=7
                    , 'forum_db_messages', $messageInfo['authorID']);
                $ret2 = $this->DbManager->query('UPDATE ?# m SET del_from=1,del_to=1,isnew=0 WHERE fromuser = ?d', 'forum_users_pager', $messageInfo['authorID']);
                $ret3 = $this->DbManager->query('DELETE FROM ?# m WHERE userlist = ?d', 'forum_users_pagerlist', $messageInfo['authorID']);

            } else {
                $this->Errors['system'] = 'Auth error';
            }
        } else {
            $this->Errors['system'] = 'Auth error';
        }
        if ($this->Errors) {
            $this->sendErrors($this->Errors);
        } else {
            $result = array(
                'submitOn' => true,
                'callFunc' => 'hideMessage'
            );
            $this->sendJSON($result);
        }
    }


    public function onEvent_ForumHideAjaxTheme($form)
    {
        if ($this->AuthManager->User->userID > 0 && (int)$form->Request['_thId'] > 0) {
            $_thId = (int)$form->Request['_thId'];
            $themeInfo = $this->DbManager->selectrow("-- CACHE: 1h 0m 0s
                SELECT * FROM ?# WHERE themeID = ?d", 'forum_db_themes', $_thId);

            $currentGroup = $this->getGroupInfo($themeInfo['groupID']);
            $isGroupOwner = $this->getPermissionModel()->isGroupOwner($currentGroup, $this->AuthManager->User->userID);
            $isGroupAdministrator = $this->getPermissionModel()->isGroupModerator($themeInfo['groupID'], $this->AuthManager->User->userID);
            if ($isGroupAdministrator || $isGroupOwner || $this->AuthManager->User->is_admin) {
                $_cnt = $this->DbManager->query("UPDATE ?#
                                SET hidden = 1, is_locked=1
                                WHERE `themeID` = ?d
                                AND `hidden` = 0", "forum_db_themes", $_thId);
                $_cnt = $this->DbManager->query("UPDATE ?#
                                SET `hidden` = 1
                                WHERE `themeID` = ?d
                                AND `hidden` = 0", "forum_messages_attaches", $_thId);
                $_cnt = $this->DbManager->query("UPDATE ?#
                                SET `hidden` = 1
                                WHERE `themeID` = ?d
                                AND `hidden` = 0", "forum_db_messages", $_thId);
                $this->themeHideLog($_thId);
            } else {
                $this->Errors['system'][] = 'Auth error';
            }
        } else {
            $this->Errors['system'][] = 'Auth error';
        }
        if ($this->Errors) {
            $this->sendErrors($this->Errors);
        } else {
            $result = array(
                'submitOn' => true,
                'callFunc' => 'hideMessage'
            );
            $this->sendJSON($result);
        }
    }

    public function onEvent_ForumTopDayTheme($form)
    {
        if ($this->AuthManager->User->userID > 0
            && (int)$form->Request['_thId'] > 0
        ) {
            if ($this->AuthManager->User->user_balance >= TOP_DAY_PRICE) {


                $_thId = (int)$form->Request['_thId'];
                $sql = "SELECT groupID FROM ?# WHERE themeID = ?d";
                $_GroupId = $this->DbManager->selectcell($sql, 'forum_db_themes', $_thId);
                $sql = "SELECT themeID FROM ?# WHERE themeID = ?d AND ifnull(top_end,NOW()) >= NOW() AND (is_top = 1)";
                $_themeInfoTop = $this->DbManager->selectcell($sql, 'forum_db_themes', $_thId);
                if ($_themeInfoTop <> 0) {
                    $this->Errors['system'][] = 'Тема уже в топе.';
                } else {
                    $this->AuthManager->User->user_balance = $this->AuthManager->User->user_balance - TOP_DAY_PRICE;
                    $this->DbManager->query("UPDATE ?# SET
                                                user_balance = ?d
                                                WHERE
                                                    userID = ?d", 'forum_users', $this->AuthManager->User->user_balance, $this->AuthManager->User->userID);
                    $this->billingLog(TOP_DAY_PRICE, 'Поднятие темы в топ на сумму ' . TOP_DAY_PRICE . 'р.');

                    $ix = sprintf("forumAuth_FindUserById_%d", $this->AuthManager->User->userID);
                    if (xcache_isset($ix)) {
                        $_user = unserialize(xcache_get($ix));
                        $_user['user_balance'] = $this->AuthManager->User->user_balance;
                        xcache_set($ix, serialize($_user), 1200);
                    }
                    $this->DbManager->query("UPDATE
                                        ?#
                                    SET
                                        `updated`=?s,
                                        `is_top`=?d,
                                        `top_end`=NOW()+interval ?d DAY,
                                        `updated_by`=?,
                                        `fact_date`=?s,
                                        `updated_by_id`=?d
                                    WHERE
                                        `themeID`=?d",
                        'forum_db_themes',
                        date("Y-m-d H:i:s"),
                        1,
                        1,
                        $this->AuthManager->User->user_name,
                        date("Y-m-d H:i:s"),
                        $this->AuthManager->User->userID,
                        $_thId);
                    $this->getForumModel()->updateGroupThemes($TotalRows, $_GroupId, $this->Page);
                }
            } else {
                $this->Errors['system'][] = 'Эта процедура стоит: ' . TOP_DAY_PRICE . 'р. ';
            }
        } else {
            $this->Errors['system'][] = 'Проблема #1';
        }
        if ($this->Errors) {
            $this->sendErrors($this->Errors);
        } else {
            $result = array(
                'submitOn' => true
            );
            $this->sendJSON($result);
        }
    }

    public function onEvent_ForumTop30DayTheme($form)
    {
        if ($this->AuthManager->User->userID > 0
            && (int)$form->Request['_thId'] > 0
        ) {
            if ($this->AuthManager->User->user_balance >= TOP30_DAY_PRICE) {
                $_thId = (int)$form->Request['_thId'];
                $sql = "SELECT groupID FROM ?# WHERE themeID = ?d";
                $_GroupId = $this->DbManager->selectcell($sql, 'forum_db_themes', $_thId);
                $sql = "SELECT themeID FROM ?# WHERE themeID = ?d AND ifnull(top_end,NOW()) >= NOW() AND (hottop = 1)";
                $_themeInfoHot = $this->DbManager->selectcell($sql, 'forum_db_themes', $_thId);
                if ($_themeInfoHot <> 0) {
                    $this->Errors['system'][] = 'Тема уже в топе. ';
                } else {
                    $this->AuthManager->User->user_balance = $this->AuthManager->User->user_balance - TOP30_DAY_PRICE;
                    $this->DbManager->query("UPDATE ?# SET
                                            user_balance = ?d
                                            WHERE
                                                userID = ?d", 'forum_users', $this->AuthManager->User->user_balance, $this->AuthManager->User->userID);
                    $this->billingLog(TOP30_DAY_PRICE, 'Поднятие темы в топ на сумму ' . TOP30_DAY_PRICE . 'р.');

                    $ix = sprintf("forumAuth_FindUserById_%d", $this->AuthManager->User->userID);
                    if (xcache_isset($ix)) {
                        $_user = unserialize(xcache_get($ix));
                        $_user['user_balance'] = $this->AuthManager->User->user_balance;
                        xcache_set($ix, serialize($_user), 1200);
                    }
                    $this->DbManager->query("UPDATE
									?#
								SET
									`updated`=?s,
                                    `hottop`=?d,
                                    `top_end`=NOW()+interval ?d DAY,
									`updated_by`=?,
									`fact_date`=?s,
									`updated_by_id`=?d
								WHERE
									`themeID`=?d",
                        'forum_db_themes',
                        date("Y-m-d H:i:s"),
                        1,
                        1,
                        $this->AuthManager->User->user_name,
                        date("Y-m-d H:i:s"),
                        $this->AuthManager->User->userID,
                        $_thId);
                    $this->getForumModel()->updateGroupThemes($TotalRows, $_GroupId, $this->Page);
                }

            } else {
                $this->Errors['system'][] = 'Эта процедура стоит: ' . TOP30_DAY_PRICE . 'р. ';
            }

        } else {
            $this->Errors['system'][] = 'Проблема #1';
        }
        if ($this->Errors) {
            $this->sendErrors($this->Errors);
        } else {
            $result = array(
                'submitOn' => true
            );
            $this->sendJSON($result);
        }
    }

    public function onEvent_ForumHideAjaxPicture($form)
    {
        if ($this->AuthManager->User->userID > 0 && (int)$form->Request['_msgId'] > 0) {
            $_msgId = (int)$form->Request['_msgId'];
            $_cnt = $this->DbManager->query("UPDATE ?#
                            SET `hidden` = 1
                            WHERE `id` = ?d
                            AND `hidden` = 0", "forum_messages_attaches", $_msgId);
            if (!$_cnt) {
                $this->Errors['system'] = 'Update error';
            }
        } else {
            $this->Errors['system'] = 'Auth error';
        }
        if ($this->Errors) {
            $this->sendErrors($this->Errors);
        } else {
            $result = array(
                'submitOn' => true,
                'callFunc' => 'hideMessage'
            );
            $this->sendJSON($result);
        }

    }

    public function onEvent_ForumAddBlackList($form)
    {
        if ($this->AuthManager->User->userID > 0 && (int)$form->Request['_messId'] > 0) {
            $_messId = (int)$form->Request['_messId'];
            //проверка имени автора сообщения и его реального имени
            $_messageUser = $this->DbManager->selectrow('SELECT u.user_name, m.authorID, m.author
                FROM ?# m
                LEFT JOIN ?# u ON m.authorID = u.userID
                WHERE m.messageID = ?d', 'forum_db_messages', 'forum_users', $_messId);
            if ($_messageUser['user_name'] == $_messageUser['author']) {
                $_authorId = $_messageUser['authorID'];
                $hidden = 0;
            } else {
                if ($this->AuthManager->User->user_balance >= EDIT_HIDE_ANONIM_COST) {
                    $_authorId = $_messageUser['authorID'];
                    $hidden = 1;
                    $_passport_info = $this->DbManager->selectrow("SELECT * FROM ?# WHERE userID=?d", 'forum_users', $this->AuthManager->User->userID);
                    $result_balance = $_passport_info['user_balance'] - EDIT_HIDE_ANONIM_COST;
                    $this->DbManager->query("UPDATE ?# SET
                                                user_balance = ?d
                                                WHERE
                                                    userID = ?d", 'forum_users', $result_balance, $this->AuthManager->User->userID);
                    $this->AuthManager->User->user_balance = $result_balance;
                    //$this->billingLog(EDIT_HIDE_ANONIM_COST, 'Операция добавления анонимов в черный список на сумму ' . EDIT_HIDE_ANONIM_COST . 'р.');

                    $ix = sprintf("forumAuth_FindUserById_%d", $this->AuthManager->User->userID);
                    if (xcache_isset($ix)) {
                        $_user = unserialize(xcache_get($ix));
                        $_user['user_balance'] = $this->AuthManager->User->user_balance;
                        xcache_set($ix, serialize($_user), 1200);
                    }
                }
            }
            if ($_authorId > 0) {
                $_cnt = $this->DbManager->query("INSERT INTO ?# (ownerId,userId,hidden)
                            VALUES (?d,?d,?d)
                            ", "forum_users_blacklist", $this->AuthManager->User->userID, $_authorId, $hidden);
            } else {
                $this->Errors['system'] = 'Balance error';
            }
            if (!$_cnt) {
                $this->Errors['system'] = 'Update error';
            }
        } else {
            $this->Errors['system'] = 'Auth error';
        }
        if ($this->Errors) {
            $this->sendErrors($this->Errors);
        } else {
            $result = array(
                'submitOn' => true,
                'callFunc' => 'hideMessage'
            );
            $this->sendJSON($result);
        }

    }


    public function onEvent_ForumCreateCommercialTheme($form)
    {
        if ($this->AuthManager->User->userID == 0 || COMMERCIAL_ON == 0) {
            header('Location: /forum/');
            exit ();
        }
        $merchant = CreateObject('Money_Tariffication', array(
            'DbManager' => $this->DbManager
        ));
        if (isset ($form->Request['_package']) && (int)$form->Request['_package'] > 0) {
            $form->Request['_package'] = (int)$form->Request['_package'];
            $merchant = CreateObject('Money_Tariffication', array(
                'DbManager' => $this->DbManager
            ));
            $_obj = $merchant->getMakeCheckPackage($form->Request['_package'], $this->AuthManager->User->userID, 'addcommercialtheme');

            if (is_array($_obj) && count($_obj) > 0) {
                if (isset ($_obj['keyservices']['uptheme'])) {
                    $_topCnt = $this->DbManager->selectcell("SELECT COUNT(*)
											FROM ?#
											WHERE 
											`groupID` = ?d
											AND `is_top` = 1 AND `hidden` = 0
											", "forum_db_themes", $this->GroupID);

                    if ((int)$_topCnt > 4) {
                        $this->Errors[] = 'Вы не можете поднять в ТОП, все места заняты';
                    }
                }
            } else {
                $this->Errors[] = 'Вы должны купить пакет';
            }

            if (count($this->Errors) == 0) {
                $_callback = 'makeServiceLink';
                $this->onEvent_ForumCreateTheme($form, $_callback, array(
                    'services' => $_obj,
                    'skipcheck' => true
                ));
            }
        } else {
            $this->Errors[] = 'Вы не выбрали пакет';
        }

        $this->_themeParam = array(
            'packageid' => (int)$form->Request['_package'],
            'caption' => $form->Request['message']['caption'],
            'content' => $form->Request['message']['content'],
            'author' => $form->Request['message']['author']
        );
    }

    // cоздание темы
    public function onEvent_ForumCreateTheme($form, $_callback = '', $argscallback = array())
    {
        $this->hasCaptcha = true;
        if (!$this->validateFiles()) {
            $this->Errors[] = 'Невозможно загрузить файлы. неверный формат';
        }
        $allowedDate = time() - strtotime($this->AuthManager->User->registered);
        /*
        if ($allowedDate<FRESH_USERS_SECOND_LIMIT) {
            $time = FRESH_USERS_SECOND_LIMIT - $allowedDate;
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
            $this->Error['system']= "Вы недавно зарегистрировались и пока не можете создать тему. Осталось "
                ." ".($hours<10?"0":"").$hours . ":" . ($min<10?"0":"").$min . ":" . ($sec<10?"0":"").$sec;
        }
        $this->spamMailList = $this->getSpamList();
        foreach ($this->spamMailList as $domain) {
            if (preg_match("/".$domain."/i", $this->AuthManager->User->user_email)) {
                $GLOBALS[ 'ForumCore' ]->Protector->_read_only = 1;
                break;
            }
        }
        */
        $_current_group = $this->getForumModel()->getGroupInfo($this->GroupID);
        if (!$this->getPermissionModel()->isGroupOpen($_current_group)) {
            $this->Errors[] = 'Доступ в раздел закрыт';
        }

        if ($GLOBALS['ForumCore']->Protector->_read_only == 1) {
            $this->Errors[] = 'Вам нельзя добавлять темы';
        } elseif ($this->AuthManager->User->userID > 0 && ($this->AuthManager->User->danger_level < USER_LEVEL || $this->AuthManager->User->danger_level < USER_MAT_LEVEL)) {
            $this->Errors[] = 'Вам нельзя добавлять темы. У Вас низкая карма';
        } elseif (
            $this->AuthManager->User->userID == 0 && ($this->getMessagesCountByIp($this->Form->Request['_message']['groupID']) > ANONYMOUS_MESSAGES_LIMIT_DAY || ANONYMOUS_MESSAGES_LIMIT_DAY == 0)
        ) {
            $this->Errors[] = 'Превышено ограничение на количество анонимных сообщений.';
        } elseif ($this->AuthManager->User->fz152_agreement <> 1) {
            $this->Errors[] = 'Вы не можете писать на форуме, пока на согласитесь с условиями обработки персональных данных в разделе Мой паспорт.';
        } else {
            $this->Form = $form;

            /*
            //$this->Form->Request['message']['content'] = substr($this->Form->Request['message']['content'], 0, $this->StringCut);
            $cipher = mcrypt_module_open (MCRYPT_BLOWFISH, '', 'cbc', '');
            
            mcrypt_generic_init ($cipher, $this->key, $this->iv);
            $decrypted = substr (mdecrypt_generic ($cipher, base64_decode ($_COOKIE[ '_thread' ])), 0, 5);
            mcrypt_generic_deinit ($cipher);
            *
            */


            $decrypted = decrypt($_SESSION['_thread'], ENCRYPT_KEY, ENCRYPT_IV, ENCRYPT_BIT_CHECK);

            if ($this->hasCaptcha) {
                if (empty ($this->Form->Request['imageString']) || $this->Form->Request['imageString'] != $decrypted) {
                    $this->Errors[] = 'Неверный код подтверждения';
                    $_SESSION['captcha'] = false;
                    return;
                } else {
                    $_SESSION['captcha'] = true;
                    $this->hasCaptcha = true;
                }
            }
            $_user_info = $this->GetAuthorInfo($this->Form->Request['_message']['author']);

            if (!$_user_info) {
                $this->Errors[] = 'Ошибка добавления темы. Проблема с авторизацией';
                return;
            }
            $_user_message = $this->Form->Request['_message'];
            //setcookie ('_cookie_name', $this->Form->Request[ '_message' ][ 'author' ], 0, '/', '.' . SERVER_NAME, 0, 0);
            if ($GLOBALS['ForumCore']->Protector->PostFilter($_user_message) && $this->GroupID > 0) {
                $groupParams = $this->getGroupInfo($this->GroupID);
                if (($groupParams['deny_guest'] == 1) && $this->AuthManager->User->user_name != $this->Form->Request['_message']['author'] && !empty($this->Form->Request['_message']['author'])) {
                    $this->Errors[] = 'В данной теме пользоваться анонимными никами нельзя';
                }

                if (isset($_user_message['top'])) {
                    if ($_user_message['top'] == 1
                        && $this->AuthManager->User->userID > 0
                        && $this->AuthManager->User->user_balance >= TOP_PRICE
                    ) {
                        $_user_message['top'] = 1;
                        if (isset($_user_message['close_theme'])) {
                            $_user_message['close_theme'] = 1;
                        }
                    } else {
                        $this->Errors[] = 'Ошибка при использовании опции поднятия темы в ТОП.';
                        return;
                    }

                }
                if (isset($_user_message['top30'])) {
                    if (
                        $_user_message['top30'] == 1
                        && $this->AuthManager->User->userID > 0
                        && $this->AuthManager->User->user_balance >= TOP30_PRICE
                        && $this->getHotThemesCount() <= HOTTOP_THEMES
                    ) {
                        $_user_message['top30'] = 1;
                        if (isset($_user_message['close_theme'])) {
                            $_user_message['close_theme'] = 1;
                        }
                    } else {
                        $this->Errors[] = 'Ошибка при использовании опции поднятия темы в Горячее.';
                        return;
                    }
                }

                if (strlen(trim($this->Form->Request['_message']['content'])) < 3) {
                    $this->Errors[] = 'Сообщение не может быть короче 3х символов';
                }
                if ($this->AuthManager->User->userID == 0) {
                    if (ANONYMOUS_MESSAGES_LIMIT_DAY > 0) {
                        if ($this->getMessagesCountByIp($this->GroupID) > ANONYMOUS_MESSAGES_LIMIT_DAY) {
                            $this->Errors[] = 'Превышено ограничение на количество анонимных сообщений.';
                        }
                    } else {
                        $this->Errors[] = 'Превышено ограничение на количество анонимных сообщений.';
                    }
                }
                $messages = $this->GetLastMessagesByUserID();
                $message = $this->stripSpaces(strip_tags($this->Form->Request['_message']['content']));
                $user_message_break = false;
                for ($i = 0; $i < count($messages); $i++) {
                    $str1 = $messages[$i];
                    $str2 = $message;
                    similar_text($str1, $str2, $percent);
                    if ($percent > SIMILAR_PERCENT) {
                        $user_message_break = true;
                    }
                }
                if ($user_message_break) {
                    $this->Errors[] = 'Ошибка. Флуд запрещен.';
                }

                if (count($this->Errors) == 0) {
                    $this->ThemeID = $this->AddForumTheme($_user_message, $_user_info);
                    $this->getForumModel()->updateGroupThemes($TotalRows, $this->GroupID);
                }
                if ($this->ThemeID > 0 && $_user_message != '') {
                    $_user_message['top'] = 0;
                    $_user_message['top30'] = 0;
                    $messageId = $this->AddForumMessage($_user_message, $_user_info);
                    unset($_SESSION['_thread']);
                    unset($_SESSION['captcha']);
                    setcookie('_thread', '', time() - 6000, '/', $_SERVER['SERVER_NAME']);
                    setcookie('_thread', '', time() - 6000, '/', '.' . $_SERVER['SERVER_NAME']);
                    $this->updateMessagesCountByIp($this->GroupID);
                    $this->uploadFiles($this->ThemeID, $messageId);
                    if ($_user_message['top']) {
                        $this->AuthManager->User->user_balance = $this->AuthManager->User->user_balance - TOP_PRICE;
                        $this->billingLog(TOP_PRICE, 'Операция закрепления темы ' . $this->ThemeID . ' на сумму ' . TOP_PRICE . 'р.');
                    }
                    if ($_user_message['top30']) {
                        $this->AuthManager->User->user_balance = $this->AuthManager->User->user_balance - TOP30_PRICE;
                        $this->billingLog(TOP30_PRICE, 'Операция закрепления темы ' . $this->ThemeID . ' на сумму ' . TOP30_PRICE . 'р.');
                    }
                    unset ($this->AuthManager->User->Allow);

                    $use_pass = 2; //не менять пароль
                    $this->AuthManager->AuthDb->SaveUser($this->AuthManager->CreateUserArray($this->AuthManager->User), $use_pass);
                    if ($_callback != '' && method_exists($this, $_callback)) {
                        $this->$_callback ($this->ThemeID, $argscallback);
                    }
                    setcookie('_cookie_name', $_COOKIE['_cookie_name'], 0, '/', '.' . SERVER_NAME, 0, 0);
                    setcookie('_cookie_name', $_COOKIE['_cookie_name'], 0, '/forum/' . $this->GroupID . '/' . $this->ThemeID . '/', '.' . SERVER_NAME, 0, 0);
                    header('Location: /forum/' . $this->GroupID . '/' . $this->ThemeID . '/');
                    exit ();
                }
            } else {
                //$this->Errors[ ] = 'Сообщение/автор/заголовок содержат запрещённые слова';
                $this->Errors[] = 'Сообщение/автор/заголовок содержат запрещённые слова: "' . $_SESSION['badword'] . '"';
            }
        }
    }

    //-------------------------------------------------------------------------------------//


    /* Жалоба на сообщение ajax */
    private function complaintMessage()
    {
        $userID = $this->AuthManager->User->userID;
        $messageID = (int)$_GET['messageID'];
        $ruleID = (int)$_GET['ruleID'];

        if (!($ruleID >= 1 && $ruleID <= 12))
            return 'rule';

        //Update danger level on message
        $allowed = true; //todo checker


        $data = $this->DbManager->query("-- CACHE: 0h 0m 10s
				SELECT
					*
				FROM
					?#
				WHERE
					`userID` = ?d
					AND`messageID` = ?d
				", "forum_users_complaint", $userID, $messageID);

        if (count($data))
            return 'repeat';

        if ($allowed) {
            $this->DbManager->query("
				UPDATE
					?#
				SET
					`danger_level` = `danger_level` + 1
				WHERE
					`messageID` = ?d
				", "forum_db_messages", $messageID);

            if ($this->DbManager->error) {
                //Debug($this->DbManager->error);
                return 'error';
            }

            $this->DbManager->query("
				INSERT INTO
					?#(userID, ruleID, messageID)
				VALUES
					(?d, ?d, ?d)
				", "forum_users_complaint", $userID, $ruleID, $messageID);

            if ($this->DbManager->error) {
                //Debug($this->DbManager->error);
                return 'error';
            }

            //Get messageID IPs
            $ips = $this->DbManager->selectRow("-- CACHE: 3h 0m 0s
				SELECT
					`author_ip`, `author_ip_grey`
				FROM
					?#
				WHERE
					`messageID` = ?d
				", "forum_db_messages", $messageID);

            if ($this->DbManager->error) {
                //Debug($this->DbManager->error);
                return 'error';
            }

            $userIP = (int)$ips["author_ip"];
            $userIPGrey = (string)$ips["author_ip_grey"];

            $this->DbManager->query("
				INSERT INTO
					?#(author_ip, author_ip_grey, counter)
				VALUES
					(?d, ?s, ?d)
				ON DUPLICATE KEY UPDATE
					counter = counter + 1
				", "forum_users_complaint_ip", $userIP, $userIPGrey, 1);

            if ($this->DbManager->error) {
                //Debug($this->DbManager->error);
                return 'error';
            }

        }

        return 'success';
    }

    private function setRating($messageId, $rating)
    {

        if (isset($_SESSION['lastRatingTime'])) {
            $newRatingTime = time() - $_SESSION['lastRatingTime'];
            if ($newRatingTime > 60) {
                $action_enable = true;
            } else {
                $action_enable = false;
            }
        } else {
            $action_enable = true;
        }
        if ($this->AuthManager->User->banned != 0) {
            $action_enable = false;
        }
        //danger_level это карма у пользователя
        //rating это карма у сообщения
        $ignored_user = array('3359');
        $ip = $this->AuthManager->RemoteAddr;
        if (empty($ip)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $rating = ($rating > 0) ? 1 : -1;
        if (!$this->AuthManager->User->userID) {
            $this->sendErrors(array(
                'rating' => 'Доступно только зарегистрированным'
            ));
        }
        if ($action_enable) {
            //карма юзера
            $userKarma = $this->DbManager->selectcell('SELECT danger_level FROM ?# WHERE userID=?d', 'forum_users', $this->AuthManager->User->userID);
            $query = "SELECT COUNT(*) FROM ?# WHERE ip = INET_ATON(?) AND created='" . date('Y-m-d') . "'";
            //сколько за сегодня голосовали с этого ip
            $userCount = $this->DbManager->selectcell($query, 'forum_db_rating_log', $ip);
            $count = 0;

            //если карма больше чем разрешенная карма для голосования
            if ($userKarma >= USER_VOTE_LEVEL) {
                //если сегодня пользователь голосовал меньше чем разрешено в день или пользователь в списке разрешенных (игнорируемых)
                if ($userCount <= USER_DAILY_KARMA || in_array($this->AuthManager->User->userID, $ignored_user)) {
                    $query = '
                    SELECT COUNT(*)
                    FROM ?#
                    WHERE `messageId`=?d
                    AND ip = INET_ATON(?)';
                    //сколько голосовали за это сообщение с текущего ip
                    $count = $this->DbManager->selectcell($query, 'forum_db_rating_log', $messageId, $ip);
                    if ($count > 0) {
                        $this->sendErrors(array(
                            'rating' => 'Повтор'
                        ));
                    } else {
                        //инфо об авторе и рейтинге сообщения
                        $info = $this->DbManager->selectRow('
                                SELECT authorID, rating
                                FROM ?#
                                WHERE messageID=?d', 'forum_db_messages', $messageId);
                        //нет ли пользователя, который голосует, у автора сообщения в черном списке
                        $inBlackList = $this->DbManager->selectcell('SELECT count(ownerId) FROM ?# WHERE userId=?d AND ownerId=?d', 'forum_users_blacklist', $this->AuthManager->User->userID, $info['authorID']);

                        if ($inBlackList == 0) {
                            //если автор не голосует сам за себя?
                            if ($info['authorID'] != $this->AuthManager->User->userID) {
                                //если автор у сообщения есть
                                if ($info['authorID'] > 0) {
                                    $this->DbManager->query('INSERT
                                            INTO ?#
                                            SET userId=?d,
                                            authorID=?d,
                                            messageId=?d,
                                            created=NOW(),
                                            rating=?d,
                                            ip=INET_ATON(?)'
                                        , 'forum_db_rating_log'
                                        , $this->AuthManager->User->userID
                                        , $info['authorID']
                                        , $messageId
                                        , $rating
                                        , $ip);

                                    $lastid = $this->DbManager->selectcell('SELECT LAST_INSERT_ID( )');
                                    if ($lastid > 0) {
                                        $system_info = "\nPOST:" . var_export($_POST, true) . "\n";
                                        $system_info .= "\nGET:" . var_export($_GET, true) . "\n";
                                        $system_info .= "\nSERVER:" . var_export($_SERVER, true) . "\n";
                                        $system_info .= "\nSESSION:" . var_export($_SESSION, true) . "\n";

                                        //$this->getForumModel()->billingLog(0, 'Операция смена кармы: ' . $system_info);

                                        if ($rating < 0) {
                                            $this->DbManager->query('UPDATE ?# SET rating=rating-1 WHERE messageID=?d', 'forum_db_messages', $messageId);
                                            $info['rating'] = $info['rating'] - 1;
                                            $this->DbManager->query('UPDATE ?# SET danger_level = ifnull(danger_level,0)-1 WHERE userID=?d', 'forum_users', $info['authorID']);
                                            $ix = sprintf("forumAuth_FindUserById_%d", $info['authorID']);
                                            $_user = array();
                                            if (xcache_isset($ix)) {
                                                $_user = unserialize(xcache_get($ix));
                                                $_user['danger_level'] = $_user['danger_level'] - 1;
                                                xcache_set($ix, serialize($_user), 1200);
                                            }
                                        } else {
                                            $this->DbManager->query('UPDATE ?# SET rating=rating+1 WHERE messageID=?d', 'forum_db_messages', $messageId);
                                            $info['rating'] = $info['rating'] + 1;
                                            $this->DbManager->query('UPDATE ?# SET danger_level = ifnull(danger_level,0)+1 WHERE userID=?d', 'forum_users', $info['authorID']);
                                            $ix = sprintf("forumAuth_FindUserById_%d", $info['authorID']);
                                            $_user = array();
                                            if (xcache_isset($ix)) {
                                                $_user = unserialize(xcache_get($ix));
                                                $_user['danger_level'] = $_user['danger_level'] + 1;
                                                xcache_set($ix, serialize($_user), 1200);
                                            }
                                        }
                                        $this->DbManager->query('UPDATE ?# SET danger_level = ifnull(danger_level,0)-1 WHERE userID=?d', 'forum_users', $this->AuthManager->User->userID);


                                        $_SESSION['lastRatingTime'] = time();
                                        $result = array(
                                            'submitOn' => true,
                                            'callFunc' => 'addRating',
                                            'rating' => intval($info['rating']),
                                            'half_hide' => MESSAGE_HALFHIDE_RATING,
                                            'hide' => MESSAGE_HIDE_RATING
                                        );
                                        $this->sendJSON($result);

                                    } else {
                                        $this->sendErrors(array(
                                            'rating' => 'Ошибка #5'
                                        ));
                                    }
                                    //} else {
                                    //    $this->DbManager->query('INSERT INTO ?# SET userId=?d, messageId=?d, created=NOW(), rating=?,ip=INET_ATON(?)','forum_db_rating_log',$this->AuthManager->User->userID,$messageId,$rating,$ip);
                                } else {
                                    $this->sendErrors(array(
                                        'rating' => 'Анонимное сообщение'
                                    ));
                                }
                            } else {
                                $this->sendErrors(array(
                                    'rating' => 'Нельзя самому за себя'
                                ));
                            }
                        } else {
                            $this->sendErrors(array(
                                'rating' => 'Пользователь внес вас в черный список '
                            ));
                        }
                    }
                } else {
                    $this->sendErrors(array(
                        'rating' => 'Суточный лимит израсходован'
                    ));
                }
            } else {
                $this->sendErrors(array(
                    'rating' => 'Слишком низкая карма'
                ));
            }
        } else {
            $this->sendErrors(array(
                'rating' => 'Действие невозможно'
            ));
        }
    }

    private function showRating($messageId, $ds)
    {
        $query = '-- CACHE: 0h 1m 0s
        SELECT log.rating, u.user_name, u.userID
        FROM forum_db_rating_log log
        LEFT JOIN forum_users u ON (log.userId=u.userID)
        WHERE log.messageId=?d';
        $rating_list = $this->DbManager->select($query, $messageId);
        $ds->assign('rating_list', $rating_list);
        $this->MainTemplate = "forum/messages/showrating.tpl";
    }

    public function editMessage($messageId)
    {
        $message = $this->LoadMessageById($messageId);
        $postTime = time() - strtotime($message['created']);
        if ($message['authorID'] != $this->AuthManager->User->userID) {
            $errors['author'] = 'Вы можете редактировать только свои сообщения.';
        }
        if (0 == $this->AuthManager->User->userID) {
            $errors['author'] = 'Редактировать свои сообщения могут только авторизированные пользователи.';
        }
        if ($postTime > EDIT_MESSAGE_TIME_LIMIT) {
            $errors['posttime'] = 'Редактировать свои сообщения возможно только в первые 10 минут.';
        }
        if ($this->AuthManager->User->user_balance < EDIT_MESSAGE_COST) {
            $errors['balance'] = 'Для редактирования сообщения Ваш баланс должен быть более ' . EDIT_MESSAGE_COST . ' руб.';
        }
        if ($_GET['type'] == 'cancle') {
            $this->sendJSON($message);
        }
        if ($errors) {
            $this->sendJSON(array(
                'errors' => $errors
            ));
        }

        if ($_POST['content']) {
            $this->DbManager->query('UPDATE forum_db_messages SET content=? WHERE messageID=?d', $_POST['content'], $messageId);
            $this->DbManager->query('UPDATE forum_users SET user_balance=user_balance-?f WHERE userID=?d', EDIT_MESSAGE_COST, $this->AuthManager->User->userID);
            //$this->billingLog(EDIT_MESSAGE_COST, 'Операция редактирования сообщения ' . $messageId . ' на сумму ' . EDIT_MESSAGE_COST . 'р.');

            $ix = sprintf("forumAuth_FindUserById_%d", $this->AuthManager->User->userID);
            if (xcache_isset($ix)) {
                $_user = unserialize(xcache_get($ix));
                $_user['user_balance'] = $_user['user_balance'] - EDIT_MESSAGE_COST;
                xcache_set($ix, serialize($_user), 1200);
            }

            $message = $this->LoadMessageById($messageId);
            $message['user_balance'] = $this->AuthManager->User->user_balance - EDIT_MESSAGE_COST;
        }
        $this->sendJSON($message);

    }

    private function loadMessage($messageId, &$ds)
    {
        $message = $this->LoadMessageById($messageId);
        if (!$message) {
            die (header('Location: /forum/'));
        }

        $_current_group = $this->getForumModel()->getGroupInfo($message['groupID']);
        if (!$_current_group['groupID']) {
            die (header('Location: /forum/'));
        }
        /*
        if (! $this->getPermissionModel ()->isGroupOpen ($_current_group)) {
            $ds->assign ('group_info', $_current_group);
            $this->MainTemplate = 'forum/access/denied.tpl';
            return;
        }
        */
        $current_theme = $this->getForumModel()->getCurrentTheme($message['themeID']);
        if (!$current_theme || $current_theme['hidden'] == 1) {
            die (header("Location: /forum/groups/"));
        }
        if (isset ($_GET['action'])) {
            switch ($_GET['action']) {
                case 'setRedZone' :
                    $this->SetMessageRedZone($messageId);
                    break;
                case 'setRate' :
                    $this->setRating($messageId, $_GET['rate']);
                    break;
                case 'showRate' :
                    $this->showRating($messageId, $ds);
                    return;
                    break;
                case 'edit' :
                    $this->editMessage($messageId);
                    break;
                default :
                    exit ();
                    break;
            }
        } else {
            $this->isGroupOwner = $this->getPermissionModel()->isGroupOwner($_current_group, $this->AuthManager->User->userID);
            $this->isGroupAdministrator = $this->getPermissionModel()->isGroupModerator($_current_group['groupID'], $this->AuthManager->User->userID);
            $canManage = ($this->isGroupAdministrator || $this->isGroupOwner);
            $userRights = $this->getPermissionModel()->getThemeRightsByUserId($message['themeID'], $this->AuthManager->User->userID);
            if (!$canManage) {
                if ($current_theme['optRead'] == ALLOW_READ_THEME_AUTHUSER && $this->AuthManager->User->userID == 0) {
                    $this->MainTemplate = "forum/themes/acces_deny.tpl";
                    $ds->assign("title_part", $_current_group['caption'] . " | " . $current_theme['caption'] . ' - ДОСТУП ЗАПРЕЩЕН');
                    $ds->assign("current_theme", $current_theme);
                    $ds->assign("current_group", $_current_group);
                    return;
                } elseif ($current_theme['optRead'] == ALLOW_READ_THEME_VALIDUSER && $userRights['optRead'] == 0) {
                    $this->MainTemplate = "forum/themes/acces_deny.tpl";
                    $ds->assign("current_theme", $current_theme);
                    $ds->assign("current_group", $_current_group);
                    $ds->assign("title_part", $_current_group['caption'] . " | " . $current_theme['caption'] . ' - ДОСТУП ЗАПРЕЩЕН');
                    return;
                }
            }
        }
        $ds->assign("current_theme", $current_theme);
        $ds->assign("current_group", $_current_group);
        $ds->assign("messages", $message);
        $ds->assign("page", $this->Page);
        $this->MainTemplate = "forum/messages/onemess.tpl";
    }

    // ПОДГОТОВКА ДАННЫХ К ОТОБРАЖЕНИЮ В ШАБЛОНЕ
    public function Prepare(&$ds)
    {
        $ds->assign("_per_page", $this->CountPerPage);
        if (isMobile()) {
            $ds->assign("is_mobile", 'mobile');
            $is_mobile = 'mobile';
        } else {
            $ds->assign("is_mobile", 'not mobile');
            $is_mobile = 'not mobile';
        }
        if ($this->hasCaptcha) {
            $ds->assign('captcha', true);
        }
        $TotalRows = 0;
        /*
                if ($_SERVER['REQUEST_METHOD']=='POST') {
                    $_res = $this->DbManager->query('INSERT INTO ?#
                                                SET ip = ?
                                                ,grey_ip = ?
                                                ,serialized=?s
                                                '
                        ,'forum_db_post_log'
                        ,$_SERVER['REMOTE_ADDR']
                        ,$_SERVER['HTTP_X_FORWARDED_FOR']
                        ,serialize($_POST)."\n".serialize($_SERVER)."\n".serialize($_GET)."\n".serialize($_COOKIE)."\n".serialize($_SESSION)
                    );
                }
         *
         */
        /*
        $ips = array('90.188.105.216','90.188.111.246');
        $varcmp = false;
        for ($i=0; $i<count($ips);$i++) {
            if (strstr($_SERVER['REMOTE_ADDR'],$ips[$i])) {
                $varcmp = true;
            }
            if (strstr($_SERVER['HTTP_X_FORWARDED_FOR'],$ips[$i])) {
                $varcmp = true;
            }
        }
        //$varcmp = strstr($_SERVER['REMOTE_ADDR'],$ip);
        //var_dump($varcmp);
        $orig_names = array ('вальный','понтпонт');
        //$orig_name = 'вальный';
        $varname = false;
        for ($i=0; $i<count($orig_names);$i++) {
            if (strstr($this->AuthManager->User->user_name,$orig_names[$i])) {
                $varname = true;
            }
        }

        //var_dump($this->AuthManager->User->user_name);
        //$varname = strstr($this->AuthManager->User->user_name,$orig_name);
        //var_dump($varname);

        if ($varcmp || $varname) {
            $_res = $this->DbManager->query('INSERT INTO ?#
                                        SET userID=?d
                                        ,ip = INET_ATON(?)
                                        ,real_ip = INET_ATON(?)
                                        ,server_var=?s
                                        '
                ,'forum_users_marker'
                ,$this->AuthManager->User->userID
                ,$_SERVER['REMOTE_ADDR']
                ,$_SERVER['HTTP_X_FORWARDED_FOR']
                ,serialize($_SERVER)."\n".serialize($_POST)."\n".serialize($_GET)."\n".serialize($_COOKIE)
            );

            $message = "Бот атака (уведомление)";

            $message = "<br>\n".$_SERVER['HTTP_USER_AGENT'];
            $message = "<br>\n".$_SERVER['REMOTE_ADDR'];
            $message = "<br>\n".$_SERVER['HTTP_X_FORWARDED_FOR'];
            $message = "<br>\n".$_SERVER['HTTP_VIA'];
            $message = "<br>\n".$this->AuthManager->User->userID;
            $message = "<br>\n".var_expoert($_SERVER,true);
            $message = "<br>\n".var_expoert($_POST,true);
            $message = "<br>\n".var_expoert($_GET,true);
            $message = "<br>\n".var_expoert($_COOKIE,true);
            $message = "<br>\n".var_expoert($_SESSION,true);

            $Mailer = CreateObject("Mail_PHPMailer");

            $Mailer->From = 'noreply@forum.site';      // от кого
            $Mailer->FromName = 'http://forum.site/';   // от кого
            $Mailer->ContentType = "text/html";
            $Mailer->CharSet = 'UTF-8';

            $Mailer->AddAddress('chernov-aa@rde.ru'); // кому - адрес, Имя
            $Mailer->Subject = "Бот атака (уведомление)";  // тема письма
            $Mailer->Body = $message;

            @$Mailer->Send();
            $Mailer->ClearAllRecipients();
            @$Mailer->ClearAttachments();

        }
        */
        list ($_a, $forum, $this->GroupID, $this->ThemeID, $this->reserveID) = $this->_url_params;
        unset ($_a);
        $_groups = $this->getForumModel()->LoadGroups();
        $ds->assign("groups", $_groups);
        if ($forum == 'mess') {
            //под GroupID подразумевается в данном случае идентифекатор сообщения
            $this->loadMessage($this->GroupID, $ds);
        } elseif ($forum == 'themesettings') {
            $this->loadThemeSettings($ds);

        } elseif ($forum == "forum") {
            if ((int)$this->GroupID > 0) {
                if (empty($this->ThemeID)) {
                    $ds->assign('isheader', 1);
                }
                $_current_group = $this->getGroupInfo($this->GroupID);
                $this->isGroupOwner = $this->getPermissionModel()->isGroupOwner($_current_group, $this->AuthManager->User->userID);
                $this->isGroupAdministrator = $this->getPermissionModel()->isGroupModerator($this->GroupID, $this->AuthManager->User->userID);


                //$themeAccess = $this->getPermissionModel ()->getThemeAccessUsers ($this->ThemeID);
                //$ds->assign ('themeAccess', $themeAccess);


                //var_dump($themeAccess);
                //добавить:
                //проверку на возможность пользователю читать тему
                //проверку на возможность писать в тему
                //тип запрета или разрешения в глобальных настройках темы
                //список пользователя в настройках темы, учитывать тип настроек в forum_settings_themes_userAccess, брать из глобальных настроек темы


                /*
                if (! $this->getPermissionModel ()->isGroupOpen ($_current_group)) {
                    $ds->assign ('group_info', $_current_group);
                    $this->MainTemplate = 'forum/access/denied.tpl';
                    return;
                }
                */
                if (!$_current_group['groupID']) {
                    header('Location: /forum/');
                    exit ();
                }
                $ds->assign('isGroupAdministrator', ($this->isGroupAdministrator || $this->isGroupOwner));
                $ds->assign('isGroupOwner', $this->isGroupOwner);
                if ((int)$this->ThemeID > 0) {
                    $this->isThemeOwner = $this->getPermissionModel()->isThemeOwner($this->ThemeID, $this->AuthManager->User->userID);
                    $ds->assign('isThemeOwner', $this->isThemeOwner);
                }
                $ds->assign("current_group", $_current_group);

            }

            if ($this->GroupID == 'top') {
                $ds->assign("title_part", " ТОП ОБЩЕНИЯ");
                $page = (isset($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
                $_top50 = $this->getForumModel()->LoadTop($TotalRows, $page, $this->CountPerPage);
                $ds->assign("themes", $_top50);
                $ds->assign("is_top", 1);
                $ds->assign("isLast", ($page * $this->CountPerPage >= $TotalRows) ? 1 : 0);
                $ds->assign('page', $page);
                $this->MainTemplate = "forum/top50/index.tpl";
            } elseif ($this->GroupID == 'hot') {
                $ds->assign("title_part", " ПОПУЛЯРНОЕ");
                $_top50 = $this->getForumModel()->LoadHotRead($TotalRows, $this->Page);
                $ds->assign("themes", $_top50);
                $ds->assign("is_top", 1);
                $this->MainTemplate = "forum/top50/index.tpl";

            } elseif ($this->GroupID == 'good') {
                $ds->assign("title_part", " ЛУЧШЕЕ");
                $_top50 = $this->getForumModel()->LoadThemesByRating($TotalRows, $this->Page, 'good');
                $ds->assign("themes", $_top50);
                $ds->assign("is_top", 1);
                $this->MainTemplate = "forum/goodbad/index.tpl";

            } elseif ($this->GroupID == 'noactive') {
                $GLOBALS['ForumCore']->Protector->renderBanned('notactive.tpl', array('is_mobile' => $is_mobile));
            } elseif ($this->GroupID == 'auth') {
                $GLOBALS['ForumCore']->Protector->renderBanned('auth.tpl', array('is_mobile' => $is_mobile));
            } elseif ($this->GroupID == 'bad') {
                $ds->assign("title_part", " ХУДШЕЕ");
                $_top50 = $this->getForumModel()->LoadThemesByRating($TotalRows, $this->Page, 'bad');
                $ds->assign("themes", $_top50);
                $ds->assign("is_top", 1);
                $this->MainTemplate = "forum/goodbad/index.tpl";

            } elseif ($this->GroupID == 'sendquestion') {
                if ($this->AuthManager->User->userID == 0) {
                    header('Location: /forum/');
                    exit ();
                }
                $ds->assign('obj', $_POST['_data']);
                $ds->assign('sendmsg', $_GET['_msg']);
                $ds->assign("title_part", " ОБРАТНАЯ СВЯЗЬ");
                $this->MainTemplate = "forum/sendquestion/index.tpl";
                $moderates = $this->DbManager->select("SELECT
                        *, `userID` AS ARRAY_KEYS
                    FROM
                        ?#
                    WHERE
                        `is_answer` = 1
                    ", "forum_users_rights");
                if (count($moderates) > 0) {
                    $moderates_names = $this->DbManager->select("SELECT
                            *, `userID` AS ARRAY_KEYS
                        FROM
                            ?#
                        WHERE
                            `userID` IN (?a)
                        ", "forum_users", array_keys($moderates));

                    $ds->assign("moderates", $moderates);
                    $ds->assign("moderates_names", $moderates_names);
                }
            } elseif ($this->GroupID == 'search') {
                $ds->assign("title_part", " ПОИСК");
                $this->MainTemplate = "forum/search/index.tpl";
            } elseif ($this->GroupID == 'groups') {
                $ds->assign("title_part", " ГРУППЫ ТЕМ");
                $this->MainTemplate = "forum/groups/index.tpl";
                /*
            } elseif ($this->GroupID = 'week') {
                $ds->assign ("title_part", " ТОП ПРОСМОТРОВ ЗА НЕДЕЛЮ");
                $page = (isset($_GET['page']) && $_GET['page'] >0)?$_GET['page']:1;
                $_top50 = $this->getForumModel ()->LoadThemesByHits ($TotalRows,7,$page, $this->CountPerPage);
                $ds->assign ("themes", $_top50);
                $ds->assign ("is_top", 1);
                $ds->assign("isLast",($page*$this->CountPerPage>=$TotalRows)?1:0);
                $ds->assign('page',$page);
                $this->MainTemplate = "forum/top50/index.tpl";
                $ds->assign("isLast",($page*$this->CountPerPage>=$TotalRows)?1:0);
                $ds->assign('page',$page);
                */
            } elseif ($this->GroupID == '') {
                $page = (isset($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
                if ($_GET['ajx'] == 1) {
                    if (strlen($this->AuthManager->User->ignore_groups) > 0)
                        $igroups = split(",", $this->AuthManager->User->ignore_groups);
                    else
                        $igroups = null;

                    $_top50 = $this->getForumModel()->LoadHot($TotalRows, $page, $this->CountPerPage, $igroups);


                    $ds->assign("themes", $_top50);
                    $ds->assign("_nav_hack", 1);
                    $ds->assign("_top_hot", 1);
                    $ds->assign("isLast", ($page * $this->CountPerPage >= $TotalRows) ? 1 : 0);
                    $ds->assign('page', $page);
                    $this->MainTemplate = "forum/top50/ajax_index.tpl";

                } else {
                    $ds->assign("title_part", "TOP 50 ГОРЯЧИХ ТЕМ");
                    if (strlen($this->AuthManager->User->ignore_groups) > 0)
                        $igroups = split(",", $this->AuthManager->User->ignore_groups);
                    else
                        $igroups = null;
                    $_top50 = $this->getForumModel()->LoadHot($TotalRows, $page, $this->CountPerPage, $igroups);
                    $ds->assign("themes", $_top50);
                    $ds->assign("_nav_hack", 1);
                    $ds->assign("_top_hot", 1);
                    $ds->assign("isLast", ($page * $this->CountPerPage >= $TotalRows) ? 1 : 0);
                    //var_dump($page);
                    //var_dump($this->CountPerPage);
                    //var_dump($TotalRows);
                    //die();

                    $ds->assign('page', $page);
                    $this->MainTemplate = "forum/top50/index.tpl";
                }
                //echo '<br>page='.$page.'<br>';
                //echo '<br>TotalRows='.$TotalRows.'<br>';
                //echo '<br>CountPerPage='.$this->CountPerPage.'<br>';
                //echo '<br>isLast='.(($page*$this->CountPerPage>=$TotalRows)?1:0).'<br>';
            } elseif ($this->Node == 'complaint') {
                $ds->assign("_completed", $this->complaintMessage());
                $this->MainTemplate = "forum/ahah/complaint.tpl";
            } else {
                if ((int)$this->ThemeID > 0) {
                    $this->preDataSetLoadTheme($ds, $_current_group, $TotalRows);
                    //var_dump($TotalRows);
                } elseif ((int)$this->GroupID > 0 && $this->ThemeID == 'settings') {
                    if ($this->isGroupAdministrator || $this->isGroupOwner) {
                        $this->MainTemplate = "forum/themes/group_settings.tpl";
                        $this->loadGroupSettings($_current_group, $ds);
                    } else {
                        header("Location: /forum/groups/");
                        exit ();
                    }
                } elseif ((int)$this->GroupID > 0) {
                    if (!$this->getPermissionModel()->isGroupOpen($_current_group)) {
                        $ds->assign('group_info', $_current_group);
                        //$this->MainTemplate = 'forum/access/denied.tpl';
                        $_addthemestpl = "";
                    } else {
                        $_addthemestpl = "forum/themes/create.tpl";
                    }
                    $ds->assign("addthemestpl", $_addthemestpl);
                    $_themes = $this->getForumModel()->LoadThemes($TotalRows, $this->GroupID, $this->Page, ($this->isGroupAdministrator || $this->isGroupOwner));
                    $ds->assign('isGroupAdministrator', ($this->isGroupAdministrator || $this->isGroupOwner));
                    $ds->assign('isGroupOwner', $this->isGroupOwner);
                    $TotalRows = $_current_group['themes'];
                    $ds->assign("themes", $_themes);
                    $this->MainTemplate = "forum/themes/index.tpl";
                    $ds->assign("title_part", $_current_group['caption']);
                    $ds->assign('captcha', true);
                    $this->hasCaptcha = true;
                }
            }
            if ($this->AuthManager->User->userID > 0) {
                $_favorites = $this->DbManager->select('SELECT `themeID` AS ARRAY_KEY, `userID` FROM `forum_db_favorites` WHERE userID=?d', $this->AuthManager->User->userID);
                $ForumPager = CreateObject("Forum_Pager");
                $_pager_stat = $ForumPager->CheckMessStat($this->AuthManager->User->userID);
                $ds->assign("_pager_info", $_pager_stat);
                $ds->assign("_logged_in", 1);
                $ds->assign('_fav', $_favorites);

            }

            if (!defined('_SAPE_USER')) {
                define('_SAPE_USER', 'e8603782e9b201f903b7f5a1cb9bfa30');
            }
            require_once(realpath($_SERVER['DOCUMENT_ROOT'] . '/' . _SAPE_USER . '/sape.php'));
            $sape = new SAPE_client();

            if ($this->MainTemplate == 'forum/index.tpl') {
                header('Location: /forum/');
                exit ();
            }
            $ds->assign("__total_rows", @$TotalRows);
            $ds->assign("__page", $this->Page);
            $ds->assign("__pagePrev", $this->Page - 1);
            $ds->assign("__pageNext", $this->Page + 1);
            $ds->assign("__pages", ceil(($TotalRows + 1) / $this->CountPerPage));
            $ds->assign("__countperpage", $this->CountPerPage);

            $ds->assign("__rules", $this->getForumModel()->LoadRules());
            $ds->assign("__errors", $this->Errors);
            //$ds->assign ("__sape", var_export($sape,true));
            $ds->assign("__sape", $sape->return_links());
        } else {
            header('Location: /forum/');
        }
    }

    private function preDataSetLoadTheme(&$ds, $_current_group, &$TotalRows)
    {
        $canManage = ($this->isGroupAdministrator || $this->isGroupOwner || $this->isThemeOwner || $this->AuthManager->User->is_admin);
        $current_theme = $this->getForumModel()->getCurrentTheme($this->ThemeID);

        // Тема скрыта или перемещенная 
        if (($current_theme['hidden'] == 1 && !$canManage) || (isset ($this->GroupID) && $this->GroupID != $current_theme['groupID'])) {
            header("Location: /forum/groups/");
            exit ();
        }
        $userRights = $this->getPermissionModel()->getThemeRightsByUserId($this->ThemeID, $this->AuthManager->User->userID);

        $theme_access = true;
        if (!$canManage) {

            if ($userRights['access_type'] == ALLOW_READ_THEME_AUTHUSER && $this->AuthManager->User->userID == 0) { //если разрешено всем кроме анонимов
                $theme_access = false;
            } elseif ($userRights['access_type'] == ALLOW_READ_THEME_VALIDUSER && $userRights['optRead'] == 0) { //если разрешено всем из списка
                $theme_access = false;
            } elseif ($userRights['access_type'] == DISALLOW_READ_THEME_VALIDUSER && $userRights['optRead'] == 1) { //если разрешено всем кроме пользователей из списка
                $theme_access = false;
            } elseif ($userRights['access_type'] == ALLOW_READ_FOR_ALL_THEME_VALIDUSER) { //если разрешено всем кроме пользователей из списка
                $theme_access = false;
            } else { //если разрешено всем
                $theme_access = true;
            }
            $ds->assign("theme_access", $theme_access);
            /*
            if ($current_theme[ 'optRead' ] == ALLOW_READ_THEME_AUTHUSER && $this->AuthManager->User->userID == 0) {
                $this->MainTemplate = "forum/themes/acces_deny.tpl";
                $ds->assign ("title_part", $_current_group[ 'caption' ] . " | " . $current_theme[ 'caption' ] . ' - ДОСТУП ЗАПРЕЩЕН');
                $ds->assign ("current_theme", $current_theme);
                return;
            } elseif ($current_theme[ 'optRead' ] == ALLOW_READ_THEME_VALIDUSER && $userRights[ 'optRead' ] == 0) {
                //$this->MainTemplate = "forum/themes/acces_deny.tpl";
                //$ds->assign ("current_theme", $current_theme);
                $ds->assign ("title_part", $_current_group[ 'caption' ] . " | " . $current_theme[ 'caption' ] . ' - ДОСТУП ЗАПРЕЩЕН');
                return;
            } elseif ($current_theme[ 'optRead' ] == ALLOW_READ_THEME_ALLUSER) {
                $userRights[ 'optRead' ] = 1;
            }
            if (($current_theme[ 'optWrite' ] == ALLOW_WRITE_THEME_ALLUSER) ||
                ($current_theme[ 'optWrite' ] == ALLOW_WRITE_THEME_AUTHUSER && $this->AuthManager->User->userID > 0)) {
                $userRights[ 'optWrite' ] = 1;
            }
            */
        } else {
            $ds->assign("theme_access", $theme_access);
        }
        if ($_GET['ajx'] == 1 && $_GET['lastmsg'] > 0) {
            if ($_GET['c'] == 1) {
                //$this->LoadMessagesToCache($this->ThemeID, $_GET[ 'lastmsg' ]);
            }
            $_messages = $this->LoadMessagesFromId($this->ThemeID, $_GET['lastmsg'], $this->Page);
            $this->MainTemplate = "forum/messages/ajx_index.tpl";
            $ds->assign("messages", $_messages);
            $ds->assign("__countperpage", $this->CountPerPage);
            $firstId = $this->GetRealLastMess($this->ThemeID);
            //$firstId = $lastMess[ 'messageID' ];
            $ds->assign('lastId', $firstId);
        } else {

            $this->themeAddHit($this->ThemeID);
            $ds->assign("title_part", $_current_group['caption'] . " | " . $current_theme['caption']);
            $ds->assign("theme_id", $this->ThemeID);
            $_messages = $this->getForumModel()->LoadMessages($TotalRows, $this->ThemeID, $this->Page, null, $canManage);
            //$TotalRows = $current_theme[ 'messages' ];
            $ds->assign("current_theme", $current_theme);
            $ds->assign('isGroupAdministrator', $canManage);
            $ds->assign('isGroupOwner', $this->isGroupOwner);
            $_current_group = $this->getGroupInfo($this->GroupID);
            $ds->assign('GroupInfo', $_current_group);
            $groupAccess = $this->getPermissionModel()->isGroupOpen($_current_group);
            $ds->assign('isGroupOpen', $groupAccess);


            $ds->assign("messages", $_messages);
            $ds->assign("page", $this->Page);
            $ds->assign("__countperpage", $this->CountPerPage);
            $lastMess = $this->GetRealLastMessTheme($this->ThemeID);

            if ($_SERVER['SERVER_NAME'] == 'forum.rde.ru') {
                $_updated = $this->DbManager->selectcell("SELECT TIMESTAMPDIFF(second,updated,now()) FROM forum_db_themes WHERE themeID = ?d", $this->ThemeID);
            } else {
                $redis = new Redis();
                $redis->pconnect('192.168.122.11');
                $_updated = time() - $redis->get(sprintf("theme_updated:%d", $this->ThemeID));
                if ($_updated <= 0) {
                    $_updated = $this->DbManager->selectcell("SELECT TIMESTAMPDIFF(second,updated,now()) FROM forum_db_themes WHERE themeID = ?d", $this->ThemeID);
                }
            }

            //$firstId = end ($_messages);
            //$lastId = reset ($_messages);
            //$LastMessEditInterval = $lastMess[ 'editInterval' ];
            //var_dump($lastMess[ 'messageID' ]);
            //var_dump($lastId[ 'editInterval' ]);
            //var_dump($lastMess['editInterval']);
            //var_dump(TIMEOUT_THEME_UPDATE);
            //var_dump($LastMessEditInterval);
            if ($_updated > TIMEOUT_THEME_UPDATE) {
                $ds->assign('captcha', true);
                $this->hasCaptcha = true;
            }
            if ($this->AuthManager->User->userID == 0) {
                $ds->assign('captcha', true);
                $this->hasCaptcha = true;
            }
            $firstId = $lastMess['messageID'];
            $ds->assign('lastId', $firstId);

            $this->MainTemplate = "forum/messages/index.tpl";
        }
        $ds->assign('userRights', $userRights);
    }

    private function groupSettingsActions($groupInfo)
    {
        if ('removemoderator' == $_POST['action'] && $this->AuthManager->User->userID == $groupInfo['ownerId']) {
            $result = $this->getPermissionModel()->removeModerator($groupInfo['groupID'], $_POST['userId']);
            die (json_encode(array(
                'success' => 1
            )));
        } elseif ('addmoderator' == $_POST['action']) {
            if ($this->AuthManager->User->userID == $groupInfo['ownerId']) {
                $result = $this->getPermissionModel()->addGroupModerator($groupInfo['groupID'], $_POST['moderator']);
                if ($result['errors']) {
                    $this->Errors = array_merge($this->Errors, $result['errors']);
                }
            } else {
                $this->Errors[] = 'У Вас нет права добавлять модераторов раздела';
            }
        } elseif ('saveSettings' == $_POST['action']) {
            if ($this->AuthManager->User->userID == $groupInfo['ownerId']) {
                $groupInfo['is_mat'] = $_POST['is_mat'];
                $groupInfo['deny_guest'] = $_POST['deny_guest'];
                $groupInfo['deny_user'] = $_POST['deny_user'];
                $groupInfo['deny_all'] = $_POST['deny_all'];
                $groupInfo = $this->updateGroup($groupInfo);
            } else {
                $this->Errors[] = 'У Вас нет права изменять настройки раздела';
            }
        } elseif ($this->isGroupAdministrator) {
            if ('removeuser' == $_POST['action']) {
                if ($_POST['list'] == 'blacklist') {
                    $result = $this->getPermissionModel()->removeBlackUser($groupInfo['groupID'], $_POST['userId']);
                } elseif ($_POST['list'] == 'whitelist') {
                    $result = $this->getPermissionModel()->removeWhiteUser($groupInfo['groupID'], $_POST['userId']);
                } elseif ($_POST['list'] == 'ip') {
                    $result = $this->getPermissionModel()->removeBlackIp($groupInfo['groupID'], $_POST['userId']);
                }
                die (json_encode(array(
                    'success' => 1
                )));
            }
            if ('addBlackUser' == $_POST['action']) {
                $result = $this->getPermissionModel()->addBlackUser($groupInfo['groupID'], $_POST['user']);
                if ($result['errors']) {
                    $this->Errors = array_merge($this->Errors, $result['errors']);
                }
            }
            if ('addWhiteUser' == $_POST['action']) {
                $result = $this->getPermissionModel()->addWhiteUser($groupInfo['groupID'], $_POST['user']);
                if ($result['errors']) {
                    $this->Errors = array_merge($this->Errors, $result['errors']);
                }
            }
            if ('addBlackIp' == $_POST['action']) {
                $result = $this->getPermissionModel()->addBlackIp($groupInfo['groupID'], $_POST['ip']);
                if ($result['errors']) {
                    $this->Errors = array_merge($this->Errors, $result['errors']);
                }
            }
        } else {
            $this->Errors[] = 'У Вас нет права для данного действия';
        }
    }

    public function loadGroupSettings($groupInfo, $ds)
    {
        if (isset ($_POST['action'])) {
            $this->groupSettingsActions($groupInfo);
        }
        if (isset ($_GET['q'])) {
            echo(implode("\n", $this->AuthManager->searchUserByName($_GET['q'])));
            exit ();
        }
        $groupModerators = $this->getPermissionModel()->getGroupModerators($groupInfo['groupID']);
        $blackList = $this->getPermissionModel()->getBlackListUsers($groupInfo['groupID']);
        $whiteList = $this->getPermissionModel()->getWhiteListUsers($groupInfo['groupID']);
        $ipList = $this->getPermissionModel()->getIpList($groupInfo['groupID']);
        $ds->assign('blackList', $blackList);
        $ds->assign('whiteList', $whiteList);
        $ds->assign('ipList', $ipList);
        $ds->assign('groupModerators', $groupModerators);
        $ds->assign('group_info', $groupInfo);
        $ds->assign("__errors", $this->Errors);
    }

    private function loadThemeSettings(&$ds)
    {
        $this->MainTemplate = 'forum/themes/theme_settings.tpl';
        $errors = 0;
        $isGroupAdministrator = 0;
        $isGroupOwner = 0;
        $currentGroup = $this->getGroupInfo($this->GroupID);
        if (!$currentGroup) {
            $errors = 1;
        } else {
            //echo $isGroupOwner = $this->getPermissionModel ()->isGroupOwner ($_current_group, $this->AuthManager->User->userID);
            $isThemeOwner = $this->getPermissionModel()->isThemeOwner($this->ThemeID, $this->AuthManager->User->userID);
            $isGroupOwner = $this->getPermissionModel()->isGroupOwner($currentGroup, $this->AuthManager->User->userID);
            $isGroupAdministrator = $this->getPermissionModel()->isGroupModerator($this->GroupID, $this->AuthManager->User->userID);
            $currentTheme = $this->getForumModel()->getCurrentTheme($this->ThemeID);
            if (!$currentTheme) {
                $errors = 1;
            }
        }

        if ($errors || !($isGroupAdministrator || $isGroupOwner || $isThemeOwner) || $this->AuthManager->User->userID == 0) {
            die (header('Location: /forum/'));
        }
        $_groups = $this->getForumModel()->LoadGroups();
        if (isset ($_GET['q'])) {
            echo(implode("\n", $this->AuthManager->searchUserByName($_GET['q'])));
            exit ();
        }
        if ($_POST['action']) {
            switch ($_POST['action']) {
                case 'editTheme' :
                    if ($isThemeOwner) {
                        if ($this->AuthManager->User->user_balance >= EDIT_THEME_COST) {
                            $_passport_info = $this->DbManager->selectrow("SELECT * FROM ?# WHERE userID=?d", 'forum_users', $this->AuthManager->User->userID);
                            $result_balance = $_passport_info['user_balance'] - EDIT_THEME_COST;
                            if ($_passport_info['danger_level'] > 10) {
                                $this->DbManager->query("UPDATE ?# SET
                                                    user_balance = ?d
                                                    ,danger_level = 0
                                                    WHERE
                                                        userID = ?d", 'forum_users', $result_balance, $this->AuthManager->User->userID);
                            } else {
                                $this->DbManager->query("UPDATE ?# SET
                                                    user_balance = ?d
                                                    WHERE
                                                        userID = ?d", 'forum_users', $result_balance, $this->AuthManager->User->userID);
                            }
                            $this->AuthManager->User->user_balance = $result_balance;
                            //$this->billingLog(EDIT_THEME_COST, 'Операция редактирования темы ' . $this->ThemeID . ' на сумму ' . EDIT_THEME_COST . 'р.');

                            $ix = sprintf("forumAuth_FindUserById_%d", $this->AuthManager->User->userID);
                            if (xcache_isset($ix)) {
                                $_user = unserialize(xcache_get($ix));
                                $_user['user_balance'] = $this->AuthManager->User->user_balance;
                                xcache_set($ix, serialize($_user), 1200);
                            }
                            $this->getForumModel()->editTheme($this->ThemeID, $_POST['theme']);
                        }
                    }
                    if ($isGroupAdministrator || $isGroupOwner) {
                        $this->getForumModel()->editTheme($this->ThemeID, $_POST['theme']);
                    }
                    $currentTheme = $this->getForumModel()->getCurrentTheme($this->ThemeID);
                    break;
                case 'editThemeRights' :
                    $request = $_POST['theme'];
                    unset($request['caption']);
                    unset($request['author']);
                    if ($isGroupAdministrator || $isGroupOwner || $isThemeOwner) {
                        $this->getForumModel()->editTheme($this->ThemeID, $request);
                    }
                    $currentTheme = $this->getForumModel()->getCurrentTheme($this->ThemeID);
                    break;
                case 'removeuser' :
                    $this->getPermissionModel()->removeThemeUserAccess($this->ThemeID, $_POST['userId']);
                    die (json_encode(array(
                        'success' => 1
                    )));
                    break;
                case 'addUser' :
                    $restul = $this->getPermissionModel()->addThemeUserAccess($this->ThemeID, $_POST['user'], (bool)$_POST['optRead'], (bool)$_POST['optWrite']);
                    break;
            }
        } else {

        }

        $themeAccess = $this->getPermissionModel()->getThemeAccessUsers($this->ThemeID);
        //добавить:
        //проверку на возможность пользователю читать тему
        //проверку на возможность писать в тему
        //тип запрета или разрешения в глобальных настройках темы
        //список пользователя в настройках темы, учитывать тип настроек в forum_settings_themes_userAccess, брать из глобальных настроек темы

        $ds->assign('isGroupAdministrator', ($isGroupAdministrator || $isGroupOwner));
        $ds->assign('themeAccess', $themeAccess);
        $ds->assign('isGroupOwner', $isGroupOwner);
        $ds->assign('isThemeOwner', $isThemeOwner);
        $ds->assign("groups", $_groups);
        $ds->assign('theme_info', $currentTheme);
        $ds->assign('group_info', $currentGroup);

    }

    //---------------Вывод шаблона
    public function Display(&$parser)
    {
        $parser->display($this->MainTemplate);
    }

    //---------------загрузка сообщений в теме
    public function LoadMessages(& $TotalRows, $_theme_id, $page = 1, $timestamp = null, $isManager = false)
    {
        return $this->getForumModel()->LoadMessages($TotalRows, $_theme_id, $page, $timestamp, $isManager);
    }

    private function LoadMessageById($messageId)
    {
        return $this->getForumModel()->LoadMessageById($messageId);
    }

    //ajax-загрузка сообщений в теме
    public function LoadMessagesFromId($themeID, $messageID, $page = 1)
    {
        return $this->getForumModel()->LoadMessagesFromId($themeID, $messageID, $page);
    }

    //update сообщений в теме to cache
    public function LoadMessagesToCache($themeID, $messageID)
    {
        return $this->getForumModel()->LoadMessagesFromIdToCache($themeID, $messageID);
    }

    public function GetAuthorInfo($name)
    {
        return $this->getForumModel()->GetAuthorInfo($name);
    }

    //непосредственное добавление сообщения в теме.
    public function AddForumMessage($_message_info, $_user_info)
    {
        $country = apache_note("GEOIP_COUNTRY_CODE");
        //запрет писать в политику не русским
        if ($this->GroupID == 24 && $country != 'RU') {
            $this->Errors[] = 'Действие невозможно';
            return false;
        } else {
            if ($_user_info['authorID']!=3359 && $_message_info['author']=='Франк') {
                $this->Errors[] = 'Действие невозможно';
                return false;
            }  else {
                $_message_info['content'] = trim($_message_info['content']);
                if (strlen(trim($_message_info['content'])) > 0) {
                    $_message_info['caption'] = preg_replace('/([^\s]{110})[^\s]+/', '$1...', $_message_info['caption']);
                    $messageId = $this->DbManager->query("INSERT INTO
										?#
									SET
										 `themeID`=?d,
										 `groupID`=?d,
										 `caption`=?,
										 `content`=?,
										 `author`=?,
										 `authorID`=?,
										 `realname`=?s,
										 `author_ip`=INET_ATON(?),
										 `author_ip_grey`=?,
										 `country_code`=?,
										 `danger_level`='0',
										 `created`=NOW(),
										 `is_moderated` = '0',
										 `is_deleted` = '0'
								", 'forum_db_messages',
                        $this->ThemeID,
                        $this->GroupID,
                        trim($_message_info['caption']),
                        trim($_message_info['content']),
                        ($_user_info['author'] != '') ? $_user_info['author'] : 'Анонимно' . rand(10000, 99999),
                        $_user_info['authorID'],
                        $_user_info['realname'],
                        $_user_info['author_ip'],
                        $_user_info['author_ip_grey'],
                        $country);
                    //var_dump($messageId);
                    $this->DbManager->query("UPDATE
									?#
								SET
									{`updated`=?s,}
                                    {`is_top`=?d, }
                                    {`top_end`=NOW()+interval ?d DAY, }
                                    {`hottop`=?d, }
									`messages`=`messages`+1,
									`updated_by`=?,
									`fact_date`=?s,
									`updated_by_id`=?d
								WHERE
									`themeID`=?d",
                        'forum_db_themes',
                        (((int)$cmp != 1) ? date("Y-m-d H:i:s") : DBSIMPLE_SKIP),
                        ($_message_info['top']) ? 1 : DBSIMPLE_SKIP,
                        ($_message_info['top'] || $_message_info['top30']) ? 7 : DBSIMPLE_SKIP,
                        ($_message_info['top30']) ? 1 : DBSIMPLE_SKIP,
                        $_user_info['author'],
                        date("Y-m-d H:i:s"),
                        $_user_info['authorID'],
                        $this->ThemeID);
                    //Redis optimize BEGIN
                    if ($_SERVER['SERVER_NAME'] != 'forum.rde.ru') {
                        $redis = new Redis();
                        $redis->pconnect('192.168.122.11');
                        $key = sprintf("theme_updated:%d", $this->ThemeID);
                        $redis->set($key, time());
                    }
                    //Redis optimize END

                    //Update counter
                    $this->DbManager->query("UPDATE
									?#
								SET
									`value` = `value` + 1
								WHERE
									`name`= ?s", 'forum_stat', 'messages_count');
                    return $messageId;
                } else {
                    $this->Errors[] = 'Вы не ввели сообщение';
                    return false;
                }
            }
        }
    }

    //добавление темы в группу
    public function AddForumTheme($_theme_info, $_user_info)
    {
        $country = apache_note("GEOIP_COUNTRY_CODE");
        //запрет писать в политику не русским
        if ($this->GroupID == 24 && $country != 'RU') {
            $this->Errors[] = 'Действие невозможно';
            return false;
        } else {
            $_theme_info['caption'] = trim($_theme_info['caption']);
            if (strlen(trim($_theme_info['caption'])) > 2) {
                $_theme_info['caption'] = preg_replace('/([^\s]{110})[^\s]+/', '$1...', $_theme_info['caption']);
                if ($_theme_info['caption'] == mb_strtoupper($_theme_info['caption'], 'UTF-8')) {
                    $_theme_info['caption'] = mb_strtolower($_theme_info['caption'], "UTF-8");
                }
                $_result = $this->DbManager->query("INSERT INTO
                            ?#
                        SET
                            `groupID`=?d,
                            `caption`=?,
                            `author`=?,
                            `authorID`=?,
                            `realname`=?s,
                            `author_ip`=INET_ATON(?),
                            `updated_by`=?,
                            `updated_by_id`=?,
                            `author_ip_grey`=?,
                            {`is_top`=?d, }
                            {`top_end`=NOW()+interval ?d DAY, }
                            {`hottop`=?d, }
                            `is_locked` = ?d,
                            `hidden`='0',
                            `is_vote`='0',
                            `rateID`='0',
                            `moderated`='0',
                            `messages`='0',
                            `danger_level`='0',
                            `autoup`='0',
                            `updated`=NOW(),
                            `created`=NOW()
                        ", 'forum_db_themes'
                    , $this->GroupID
                    , trim($_theme_info['caption'])
                    , ($_user_info['author'] != '') ? $_user_info['author'] : 'Анонимно' . rand(10000, 99999)
                    , $_user_info['authorID']
                    , $_user_info['realname']
                    , $_user_info['author_ip']
                    , $_user_info['author']
                    , $_user_info['authorID']
                    , $_user_info['author_ip_grey']
                    , ($_theme_info['top']) ? 1 : DBSIMPLE_SKIP, ($_theme_info['top'] || $_theme_info['top30']) ? 7 : DBSIMPLE_SKIP, ($_theme_info['top30']) ? 1 : DBSIMPLE_SKIP
                    , ($_theme_info['close_theme'] == 1) ? 1 : 0
                );

                if ($_result) {
                    $this->DbManager->query("UPDATE `forum_db_groups` SET `themes`=(`themes`+1) WHERE `groupID`=?d", $this->GroupID);

                    //Update counter
                    $this->DbManager->query("UPDATE
                                            ?#
                                        SET
                                            `value` = `value` + 1
                                        WHERE
                                            `name`= ?s", 'forum_stat', 'themes_count');
                }
                return $_result;
            } else {
                $this->Errors[] = 'Вы не ввели заголовок темы';
                return null;
            }
        }
    }

    private function GetLastMessagesByUserID()
    {
        $temp = $this->DbManager->selectcol("-- CACHE: 0h 0m 10s
                SELECT content FROM `forum_db_messages` WHERE authorID = ?d AND hidden=0 ORDER BY messageID DESC LIMIT 0,10", $this->AuthManager->User->userID);
        $messages = array();
        foreach ($temp as $message) {
            $messages[] = $this->stripSpaces($message);
        }

        return $messages;
    }

    private function GetLastMessagesInTheme()
    {
        $temp = $this->DbManager->selectcol("-- CACHE: 0h 0m 10s
	    SELECT content FROM `forum_db_messages` WHERE themeID = ?d and hidden=0 ORDER BY messageID DESC LIMIT 0,10", $this->ThemeID);
        $messages = array();
        foreach ($temp as $message) {
            $messages[] = $this->stripSpaces($message);
        }
        return $messages;
    }

    //забираем параметр темы
    private function GetThemeParam($param_name, $param_type = 'string')
    {
        $temp = $this->DbManager->selectcell("-- CACHE: 0h 1m 0s
			SELECT ?# FROM `forum_db_themes` WHERE `themeID` = ?d", $param_name, $this->ThemeID);
        settype($temp, $param_type);
        return $temp;
    }

    private function GetRealLastMessTheme($themeID = 0)
    {
        $ix = sprintf("forumGetLastMessTheme_t%d", $themeID);
        if (!xcache_isset($ix)) {
            if ($themeID > 0) {
                $maxMessID = $this->DbManager->selectcell("-- CACHE: 0h 10m 0s
                SELECT MAX(messageID) as maxMessID FROM ?# m WHERE `themeID` = ?d and hidden=0", 'forum_db_messages', $themeID);
                if ($maxMessID > 0) {
                    $temp = $this->DbManager->selectrow("-- CACHE: 0h 10m 0s
                SELECT m.*,(TIMESTAMPDIFF(second,m.created,now())) as editInterval FROM ?# m WHERE `themeID` = ?d and messageID = ?d", 'forum_db_messages', $themeID, $maxMessID);
                } else {
                    $temp = false;
                }
            } else {
                $temp = false;
            }
            xcache_set($ix, serialize($temp), 600);
        } else {
            $temp = unserialize(xcache_get($ix));
        }
        return $temp;
    }

    private function GetRealLastMess($themeID = 0)
    {
        $ix = sprintf("forumGetRealLastMess_t%d", $themeID);
        if (!xcache_isset($ix)) {
            if ($themeID > 0) {
                $maxMessID = $this->DbManager->selectcell("-- CACHE: 0h 10m 00s
                SELECT MAX(messageID) as maxMessID FROM ?# m WHERE themeID = ?d and hidden=0", 'forum_db_messages', $themeID);
            } else {
                $maxMessID = false;
            }
            xcache_set($ix, serialize($maxMessID), 60);
        } else {
            $maxMessID = unserialize(xcache_get($ix));
        }
        return $maxMessID;
    }


    private function GetThemeGroupParam($param_name, $param_type = 'string')
    {
        $temp = $this->DbManager->selectcell("-- CACHE: 0h 0m 30s
                SELECT ?# FROM forum_db_groups WHERE groupID = ?d", $param_name, $this->GroupID);
        settype($temp, $param_type);
        return $temp;
    }

    public function SetMessageRedZone($_id)
    {
        if (!$this->AuthManager->User->userID) {
            $this->sendErrors(array(
                'result' => 'Доступно только зарегистрированным'
            ));
        }
        $this->getForumModel()->SetMessageRedZone($_id);
        $result = array(
            'submitOn' => true,
            'result' => 'Успешно'
        );
        $this->sendJSON($result);

    }

    public function SetThemeRedZone($_id)
    {
        $this->getForumModel()->SetThemeRedZone($_id);
    }

    public function getGroupInfo($groupID)
    {
        return $this->getForumModel()->getGroupInfo($groupID);
    }

    public function getHotThemesCount()
    {
        return $this->getForumModel()->getHotThemesCount();
    }

    public function sendJSON($jsonText)
    {
        $this->getForumModel()->sendJSON($jsonText);
    }

    public function sendErrors($errors)
    {
        $this->getForumModel()->sendErrors($errors);
    }

    function cookie_parse($line)
    {
        return $this->getForumModel()->cookie_parse($line);
    }

    public function getFilesByMessagesIds($ids)
    {
        return $this->getForumModel()->getFilesByMessagesIds($ids);
    }

    private function uploadFiles($themeId, $messageId)
    {
        $this->getForumModel()->uploadFiles($themeId, $messageId);
    }

    public function validateFiles()
    {
        return $this->getForumModel()->validateFiles();
    }

    private function themeAddHit($themeId)
    {
        $this->getForumModel()->themeAddHit($themeId);
    }

    private function getMessagesCountByIp($groupId)
    {
        return $this->getForumModel()->getMessagesCountByIp($groupId);
    }

    private function updateMessagesCountByIp($groupId)
    {
        $this->getForumModel()->updateMessagesCountByIp($groupId);
    }

    private function updateGroup($groupInfo)
    {
        return $this->getForumModel()->updateGroup($groupInfo);
    }

    public function LoadGroups()
    {
        return $this->getForumModel()->LoadGroups();
    }

    private function getLastThemeInterval($themeId)
    {

    }

    private function themeHideLog($themeId)
    {
        $this->getForumModel()->themeHideLog($themeId);
    }

    private function messageHideLog($messageId)
    {
        $this->getForumModel()->messageHideLog($messageId);
    }

    private function billingLog($amount, $desc)
    {
        $this->getForumModel()->billingLog($amount, $desc);
    }

    public function getSpamList()
    {
        return $this->getForumModel()->spamMailList();
    }


}