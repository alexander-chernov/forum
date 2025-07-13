<?php
interface IAuth
{

    public function AuthByForm();

    //public function ValidateConfirmationCode();
    public function onEvent_CmsAuthUserByForm($form);
}
class Auth_Manager implements IAuth
{
    var $ConfirmationCode = null;
    var $User = null;
    var $AuthDb = null;
    var $allowRefererList = array (
        'forum.site'
    );
    var $Forwarder;
    var $RemoteAddr;
    var $hasCaptcha;


    function __construct()
    {
        $this->AuthDb = CreateObject ("Auth_Db");
        if (isset ($_COOKIE[ 'user_id' ]) && isset ($_COOKIE[ 'hash' ])) {
            $this->AuthFromCookie ($_COOKIE[ 'user_id' ], $_COOKIE[ 'hash' ]);
        } else {
            $_user = CreateObject ("Auth_User");
            $this->SetUser ($_user);
        }
        $this->RemoteAddr = $_SERVER['REMOTE_ADDR'];
        $this->Forwarder = isset($_SERVER[ "HTTP_X_FORWARDED_FOR" ])
                ?
                $_SERVER[ "HTTP_X_FORWARDED_FOR" ]
                :
                (
                    isset($_SERVER[ "HTTP_VIA" ])
                    ?
                    $_SERVER[ "HTTP_VIA" ]
                    :
                    (
                        isset($_SERVER[ "HTTP_FORWARDED_FOR" ])
                        ?
                        $_SERVER[ "HTTP_FORWARDED_FOR" ]
                        :
                        (
                            isset($_SERVER[ "HTTP_X_FORWARDED" ])
                            ?
                            $_SERVER[ "HTTP_X_FORWARDED" ]
                            :
                            (
                                isset($_SERVER[ "HTTP_FORWARDED" ])
                                ?
                                $_SERVER[ "HTTP_FORWARDED" ]
                                :
                                (
                                    isset($_SERVER[ "HTTP_CLIENT_IP" ])
                                    ?
                                    $_SERVER[ "HTTP_CLIENT_IP" ]
                                    :
                                    (
                                        isset($_SERVER[ "HTTP_FORWARDED_FOR_IP" ])
                                        ?
                                        $_SERVER[ "HTTP_FORWARDED_FOR_IP" ]
                                        :
                                        (
                                            isset($_SERVER[ "VIA" ])
                                            ?
                                            $_SERVER[ "VIA" ]
                                            :
                                            (
                                                isset($_SERVER[ "X_FORWARDED_FOR" ])
                                                ?
                                                $_SERVER[ "X_FORWARDED_FOR" ]
                                                :
                                                (
                                                    isset($_SERVER[ "X_FORWARDED" ])
                                                    ?
                                                    $_SERVER[ "X_FORWARDED" ]
                                                    :
                                                    (
                                                        isset($_SERVER[ "FORWARDED" ])
                                                        ?
                                                        $_SERVER[ "FORWARDED" ]
                                                        :
                                                        (
                                                            isset($_SERVER[ "CLIENT_IP" ])
                                                            ?
                                                            $_SERVER[ "CLIENT_IP" ]
                                                            :
                                                            (
                                                                isset($_SERVER[ "FORWARDED_FOR_IP" ])
                                                                ?
                                                                $_SERVER[ "FORWARDED_FOR_IP" ]
                                                                :
                                                                (
                                                                    isset($_SERVER[ "HTTP_PROXY_CONNECTION" ])
                                                                    ?
                                                                    $_SERVER[ "HTTP_PROXY_CONNECTION" ]
                                                                    :
                                                                    (
                                                                        isset($_SERVER[ "USERAGENT_VIA" ])
                                                                        ?
                                                                        $_SERVER[ "USERAGENT_VIA" ]
                                                                        :
                                                                        (
                                                                            isset($_SERVER[ "PROXY_CONNECTION" ])
                                                                            ?
                                                                            $_SERVER[ "PROXY_CONNECTION" ]
                                                                            :
                                                                            (
                                                                                isset($_SERVER[ "XPROXY_CONNECTION" ])
                                                                                ?
                                                                                $_SERVER[ "XPROXY_CONNECTION" ]
                                                                                :
                                                                                (
                                                                                    isset($_SERVER[ "HTTP_PC_REMOTE_ADDR" ])
                                                                                    ?
                                                                                    $_SERVER[ "HTTP_PC_REMOTE_ADDR" ]
                                                                                    :
                                                                                    ''
                                                                                )
                                                                            )
                                                                        )
                                                                    )
                                                                )
                                                            )
                                                        )
                                                    )
                                                )
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                );
        preg_match_all('/\d+\.\d+\.\d+\.\d+/', $this->Forwarder, $nodesArray);
        if (is_array($nodesArray[0])) {
            $this->Forwarder = implode(',',$nodesArray[0]);
        } else {
            $this->Forwarder = $nodesArray[0];
        }
    }

    //-------------------------регистрация пользователя-----------------------//	
    public function onEvent_CmsCreateUserByForm($form)
    {
        $this->hasCaptcha = true;
        $this->Form = $form;
        $this->AuthDb = CreateObject ("Auth_Db");
        $this->Form->Request[ 'user' ][ 'user_name' ] = preg_replace ("/\s{1,}/ims", " ", trim ($this->Form->Request[ 'user' ][ 'user_name' ]));
        $_user_info = $this->Form->Request[ 'user' ];
        
        //check characters
        $rusflag = false;
        $enflag = false;
        $name = iconv ("utf-8", "windows-1251", $_user_info[ 'user_name' ]);
        for ($i = 0; $i < strlen ($name); $i ++) {
            $code = ord ($name[ $i ]);
            if ($code >= 97 && $code <= 122)
                $enflag = true;
            elseif ($code >= 224 && $code <= 255)
                $rusflag = true;
            
            if ($rusflag && $enflag) {
                $GLOBALS[ 'ForumCore' ]->Errors[ 'Auth_Manager' ][ 'user_name' ] = "Недопустимо перемешивание русских и английских символов";
                break;
            }
        }
        
        # проверка на имя пользователя
        //print_r($_user_info);
        if (strlen (trim ($_user_info[ 'user_name' ])) == 0) {
            $GLOBALS[ 'ForumCore' ]->Errors[ 'Auth_Manager' ][ 'user_name' ] = "Вы не указали имя пользователя.";
        } elseif ($this->AuthDb->FindUserByName ($_user_info[ 'user_name' ])) {
            $GLOBALS[ 'ForumCore' ]->Errors[ 'Auth_Manager' ][ 'user_name' ] = "Такой пользователь уже существует.";
        }
        
        # проверка на пароль
        if ($_user_info[ 'user_password' ] != $_user_info[ 'user_password_confirm' ] || $_user_info[ 'user_password' ] == '') {
            $GLOBALS[ 'ForumCore' ]->Errors[ 'Auth_Manager' ][ 'user_password' ] = "Пароль и его подтверждение не совпадают. Проверьте корректность ввода.";
        }
        # проверка почтового ящика
        $_user_info[ 'user_email' ] = trim($_user_info[ 'user_email' ]);
        if ($_user_info[ 'user_email' ] == '') {
            $GLOBALS[ 'ForumCore' ]->Errors[ 'Auth_Manager' ][ 'user_email' ] = "Вы не указали e-mail.";
        } elseif ($this->AuthDb->FindUserByEmail ($_user_info[ 'user_email' ])) {
            $GLOBALS[ 'ForumCore' ]->Errors[ 'Auth_Manager' ][ 'user_email' ] = "Пользователь с таким email уже существует.";
        }
        if ($_user_info[ 'rules_agreement' ] != 'on') {
            $GLOBALS[ 'ForumCore' ]->Errors[ 'Auth_Manager' ][ 'rules_agreement' ] = "Для продолжения необходимо подтвердить прочтение правил и согласие их соблюдать.";
        }

        if ($_user_info[ 'fz152_agreement' ] != 'on') {
            $GLOBALS[ 'ForumCore' ]->Errors[ 'Auth_Manager' ][ 'fz152_agreement' ] = "Для продолжения необходимо согласиться на обработку персональных данных.";
        }
        
        if ($GLOBALS[ 'ForumCore' ]->_url_params[ 1 ] == 'register' && $GLOBALS[ 'ForumCore' ]->_url_params[ 2 ] == 'bysms') {
            $_valid_code = $this->AuthDb->GetRegisterSMSCode ($form->Request[ 'smscode' ]);
            if (! $_valid_code) {
                $GLOBALS[ 'ForumCore' ]->Errors[ 'Auth_Manager' ][ 'smscode' ] = "Не введен код протекции, либо введен неверный код.";
            }
        }
        $decrypted = decrypt($_SESSION[ '_thread' ],ENCRYPT_KEY,ENCRYPT_IV,ENCRYPT_BIT_CHECK);

        $sql = 'SELECT count(*) FROM forum_users WHERE ifnull(reg_ip,0)<>0 AND reg_ip='.ip2long($_SERVER['REMOTE_ADDR']).' AND registered > NOW() - INTERVAL 1 DAY';
        $countReg = $GLOBALS[ 'ForumCore' ]->DbManager->selectcell($sql);
        if ($countReg>0){
            $GLOBALS[ 'ForumCore' ]->Errors[ 'Auth_Manager' ][ 'user_name' ] = 'Повторная регистрация недопускается.';
        }

        if (! is_array ($GLOBALS[ 'ForumCore' ]->Errors[ 'Auth_Manager' ])) {
            unset ($_user_info[ 'user_password_confirm' ]);
            unset ($_user_info[ 'rules_agreement' ]);
            unset ($_user_info[ 'imageString' ]);
            unset($_SESSION[ '_thread' ]);
            unset($_SESSION[ 'captcha' ]);

            if ($_user_info[ 'fz152_agreement' ]=='on') {
                $_user_info[ 'fz152_agreement' ] = 1;
            }
            $_user_info[ 'user_ip' ] = ip2long($_SERVER['REMOTE_ADDR']);
            $_user_info[ 'reg_ip' ] = ip2long($_SERVER['REMOTE_ADDR']);

            $_id = $this->AuthDb->SaveUser ($_user_info);
            $this->AuthDb->UpdateCounter ();
            $GLOBALS[ 'ForumCore' ]->Infos[ 'Auth_Manager' ][ 'register' ] = "Пользователь успешно зарегистрирован!<br/>На вашу почту отправлено письмо для подтвержения регистрации!";
            $AuthDb = CreateObject ("Auth_Db");
            $_code = md5 (base64_encode ($_id . '_#_#_' . $_user_info[ 'user_email' ]));
            $GLOBALS[ 'ForumCore' ]->DbManager->query ("INSERT INTO `forum_users_confirm` SET `userID` = ?d, `code` = ?s", $_id, $_code);
            
            $message = "Для завершения регистрации перейдите по ссылке http://".SERVER_NAME."/register/?event=confirmcode&_code=" . $_code . "\n\n";
            $message .= "-------------------------------------\n";
            $message .= "С Уважением, Робот http://".SERVER_NAME."\n";

            //var_dump($message);

            $Mailer = CreateObject ("Mail_PHPMailer");
            
            $Mailer->From = 'noreply@'.SERVER_NAME; // от кого
            $Mailer->FromName = 'http://'.SERVER_NAME.'/'; // от кого
            $Mailer->ContentType = "text/plain";
            $Mailer->CharSet = 'UTF-8';
            
            $Mailer->AddAddress ($_user_info[ 'user_email' ], $_user_info[ 'user_name' ]); // кому - адрес, Имя
            $Mailer->Subject = "Подтвержение регистрации на сайте ".SERVER_NAME.""; // тема письма
            $Mailer->Body = $message;
            
            @$Mailer->Send ();
            $Mailer->ClearAllRecipients ();
            @$Mailer->ClearAttachments ();
            
            $_form = $this->Form;
            $_form->Request = array ();
            $_form->Request[ 'user_name' ] = $_user_info[ 'user_name' ];
            $_form->Request[ 'user_password' ] = $_user_info[ 'user_password' ];
            //$this->onEvent_CmsAuthUserByForm($_form);
            if (isset ($_form->Request[ 'smscode' ]) && $_form->Request[ 'smscode' ] != '') {
                $this->AuthDb->UpdateSmsRegistration ($_form->Request[ 'smscode' ], $this->User->userID);
            }
        }
    }

    //-------------------------авторизация пользователя из формы---------------//
    public function onEvent_CmsAuthUserByForm($form)
    {
        $this->hasCaptcha = true;
        $this->AuthDb = CreateObject ("Auth_Db");
        $this->User = null;
        $_login = $form->Request[ 'user_name' ];
        $_password = $form->Request[ 'user_password' ];
        $_captcha = $form->Request[ 'imageString' ];
        if (isMobile()) {
            $is_mobile='mobile';
        } else {
            $is_mobile='not mobile';
        }


        if ($_captcha) {
            if ($_login && $_password) {
                $decrypted = decrypt($_SESSION[ '_thread' ],ENCRYPT_KEY,ENCRYPT_IV,ENCRYPT_BIT_CHECK);

                if ($_captcha != $decrypted) {
                    //$GLOBALS[ 'ForumCore' ]->Protector->renderBanned ('notactive.tpl',array('is_mobile' => $is_mobile,'result_header'=>'Вы указали неверный проверочный код'));
                    $GLOBALS[ 'ForumCore' ]->SetError ("Auth_Manager", 'Вы указали неверный проверочный код');

                } else {
                    unset($_user);
                    $_userTmp = $this->AuthDb->FindUserByLogin($_login);
                    if ((int) $_userTmp[ 'active' ] == 0) {
                        $redirectUrl = '/forum/noactive/';
                    } else {
                        if (!empty($_userTmp[ 'user_password' ]) && $_userTmp[ 'user_password' ]===md5($_password)) {
                            $_user = $_userTmp;
                        }
                    }
                    //			print_r($_user);
                    if (isset ($_user[ 'userID' ]) && $_user[ 'userID' ] > 0 && $_user[ 'active' ] > 0 ) {
                        //Удаление из кеша если происходит новая авторизация
                        $ix = sprintf("forumAuth_FindUserById_%d", $_user['userID']);
                        xcache_unset($ix);
                        $this->CreateUserFromArray ($_user);
                        $this->SaveToCookie ();
                        $this->User->confirmcode = $this->CreateConfirmationCode ();
                        $this->AuthDb->SaveConfirmationCode ($this->User->confirmcode, $this->User->userID);

                        unset($_SESSION[ '_thread' ]);
                        unset($_SESSION[ 'captcha' ]);
                        setcookie('_thread', '', time() - 6000, '/', $_SERVER['SERVER_NAME']);
                        setcookie('_thread', '', time() - 6000, '/', '.' . $_SERVER['SERVER_NAME']);

                        if ($_user['fz152_agreement']==0) {
                            //header ('Location: /personal/#fz152');
                            $redirectUrl = '/personal/#fz152';
                        } else {
                            $redirectUrl = '/forum/';
                        }
                    } else {
                        if ($GLOBALS[ 'ForumCore' ]->authBanned) {
//                            $GLOBALS[ 'ForumCore' ]->Protector->renderBanned ('auth.tpl',array('is_mobile' => $is_mobile));
                            //$GLOBALS[ 'ForumCore' ]->SetError ("Auth_Manager", 'Вы указали неверный проверочный код');
                            $redirectUrl = '/forum/auth/';
                        } else {
                            $GLOBALS[ 'ForumCore' ]->SetError ("Auth_Manager", 'Вы указали неправильный пароль');
                            //$_user = CreateObject ("Auth_User");
                            //$this->SetUser ($_user);
                            //$GLOBALS[ 'ForumCore' ]->Protector->renderBanned ('notactive.tpl',array('is_mobile' => $is_mobile,'result_header'=>'Где-то возникла ошибка'));
                            //header ('Location: ' . $_SERVER[ 'HTTP_REFERER' ]);
                            //$redirectUrl = $_SERVER[ 'HTTP_REFERER' ];
                        }
                    }
                }
            } else {
                //$GLOBALS[ 'ForumCore' ]->Protector->renderBanned ('notactive.tpl',array('is_mobile' => $is_mobile,'result_header'=>'Вы не ввели логин или пароль'));
                $GLOBALS[ 'ForumCore' ]->SetError ("Auth_Manager", 'Вы не ввели логин или пароль');
            }
        } else {
            //$GLOBALS[ 'ForumCore' ]->Protector->renderBanned ('notactive.tpl',array('is_mobile' => $is_mobile,'result_header'=>'Вы не указали проверочный код'));
            $GLOBALS[ 'ForumCore' ]->SetError ("Auth_Manager", 'Вы не указали проверочный код');
        }
        if ($GLOBALS[ 'ForumCore' ]->Errors['Auth_Manager']) {
            $response = array (
                'errors' => $GLOBALS[ 'ForumCore' ]->Errors['Auth_Manager'],
                'submitOn' => false
            );
        } else {
            $response = array (
                'submitOn' => true,
                'callFunc' => 'onAuthResultForm',
                'userID'=> $this->User->userID,
                'userName'=> $this->User->user_name,
                'redirectUrl'=> $redirectUrl
            );
        }

        die (json_encode($response));

    }

    //-----------------------выход пользователя--------------------//	
    public function onEvent_CmsUserLogout($form)
    {

        //Удаление из кеша если пользователь отлогинивается
        $ix = sprintf("forumAuth_FindUserById_%d", $this->User->userID);
        xcache_unset($ix);
        
        setcookie ('user_id', '', time () - 10, '/', '.' . SERVER_NAME, 0, 0);
        setcookie ('hash', '', time () - 10, '/', '.' . SERVER_NAME, 0, 0);
        $this->User = CreateObject ("Auth_Guest");
        if ($GLOBALS[ 'ForumCore' ]->_url_params[ 1 ] != 'forum') {
            header ("Location: /forum/");
        } else {
            header ('Location: ./');
        }
    }

    //-----------------------авторизация из куков-----------------------------//
    private function AuthFromCookie($_UserId, $_Hash)
    {
        $_result = false;
        
        if ($_UserId && is_numeric ($_UserId)) {
            $_cookie_user = $this->AuthDb->FindUserById ($_UserId);
            if (is_array ($_cookie_user) && $this->ValidateConfirmationCode ($_cookie_user)) {
                $_dt = date_parse ($_cookie_user[ 'lastlogin' ]);
                $_time = @mktime ($_dt[ 'hour' ], $_dt[ 'minute' ], $_dt[ 'second' ], $_dt[ 'month' ], $_dt[ 'day' ], $_dt[ 'year' ]);
                //
                if ((time () - $_time) > (5 * 60)) {
                    $GLOBALS[ 'ForumCore' ]->DbManager->query ("UPDATE `forum_users` SET `lastlogin` = NOW(),`user_ip` = ?d, WHERE `userID` = ?d", ip2long($_SERVER['REMOTE_ADDR']), $_cookie_user[ "userID" ]);
                }
                $_result = $this->SetUser ($this->CreateUserFromArray ($_cookie_user));
                $this->CheckOnlineUser ($_cookie_user);
            } else {
                setcookie ('user_id', '0', time (), '/', '.' . SERVER_NAME, 0, 0);
                setcookie ('hash', '', time (), '/', '.' . SERVER_NAME, 0, 0);
            }
        }
        return $_result;
    }

    function ValidateConfirmationCode($_user)
    {
        //$_code = sprintf ('%s%u%s', $_user[ 'user_email' ], !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'localhost', !empty($this->Forwarder)?$this->Forwarder:'no proxy');
        $_code = sprintf ('%s%u%s', $_user[ 'user_email' ], isset ($_SERVER[ 'REMOTE_ADDR' ]) ? $_SERVER[ 'REMOTE_ADDR' ] : 'localhost', isset ($_SERVER[ "HTTP_X_FORWARDED_FOR" ]) ? $_SERVER[ "HTTP_X_FORWARDED_FOR" ] : 'no proxy');
        return ($_user[ 'confirmcode' ] == md5 ($_code) && md5 ($_code) == $_COOKIE[ 'hash' ]);
    }

    public function CheckOnlineUser($_cookie_user)
    {
        $_online_user = $this->AuthDb->FindUserOnline ($_cookie_user[ "userID" ]);
        if ($_online_user) {
            $this->AuthDb->UpdateUserOnline ($_cookie_user[ "userID" ]);
        } else {
            $this->AuthDb->SetUserOnline ($_cookie_user);
        }
    }

    //----------------создание пользователя из массива---------------------------------------//
    private function CreateUserFromArray($_user_array)
    {
        foreach ($_user_array as $_key => $_val) {
            $this->User->$_key = $_val;
        }
        $this->User->Allow = ! $this->User->banned;
    }

    public function CreateUserArray($_user_object)
    {
        $_result = array ();
        if (is_array ($_user_object) || is_object ($_user_object)) {
            foreach ($_user_object as $key => $val) {
                $_result[ $key ] = $val;
            }
        }
        return $_result;
    }

    private function CreateConfirmationCode()
    {
        //$_code = sprintf ('%s%u%s', $this->User->user_email, !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'localhost', !empty($this->Forwarder)?$this->Forwarder:'no proxy');
        $_code = sprintf ('%s%u%s', $this->User->user_email, isset ($_SERVER[ 'REMOTE_ADDR' ]) ? $_SERVER[ 'REMOTE_ADDR' ] : 'localhost', isset ($_SERVER[ "HTTP_X_FORWARDED_FOR" ]) ? $_SERVER[ "HTTP_X_FORWARDED_FOR" ] : 'no proxy');
        setcookie ('hash', md5 ($_code), 0, '/', '.' . SERVER_NAME, 0, 0);
        $this->ConfirmationCode = md5 ($_code);
        return $this->ConfirmationCode;
    }

    private function SetUser($_user)
    {
        $_result = false;
        if ($_user) {
            $this->User = $_user;
            if ($this->User->userID) {
                $this->SaveToCookie ();
            }
            $_result = true;
            $this->Authorized = true;
        }
        return $_result;
    }

    private function SaveToCookie()
    {
        setcookie ('user_id', $this->User->userID, 0, '/', '.' . SERVER_NAME, 0, 0);
        setcookie ('user_name', $this->User->user_name, 0, '/', '.' . SERVER_NAME, 0, 0);
    }

    public function AuthByForm()
    {

    }

    public function PasswdGen($number)
    {
        $arr = array (
            'a', 
            'b', 
            'c', 
            'd', 
            'e', 
            'f', 
            
            'g', 
            'h', 
            'i', 
            'j', 
            'k', 
            'l', 
            
            'm', 
            'n', 
            'o', 
            'p', 
            'r', 
            's', 
            
            't', 
            'u', 
            'v', 
            'x', 
            'y', 
            'z', 
            
            'A', 
            'B', 
            'C', 
            'D', 
            'E', 
            'F', 
            
            'G', 
            'H', 
            'I', 
            'J', 
            'K', 
            'L', 
            
            'M', 
            'N', 
            'O', 
            'P', 
            'R', 
            'S', 
            
            'T', 
            'U', 
            'V', 
            'X', 
            'Y', 
            'Z', 
            
            '1', 
            '2', 
            '3', 
            '4', 
            '5', 
            '6', 
            
            '7', 
            '8', 
            '9', 
            '0'
        );
        // Генерируем пароль
        $pass = "";
        for ($i = 0; $i < $number; $i ++) {
            $index = rand (0, count ($arr) - 1);
            $pass .= $arr[ $index ];

        }
        //var_dump($pass);
        return $pass;
    }

    public function getUsersByIds($ids)
    {
        if (empty ($ids)) {
            return array ();
        }
        
        return $this->AuthDb->getUsersByIds ($ids);
    }
    public function searchUserByName($key)
    {
        return $this->AuthDb->searchUserByName($key);
    }
    public function searchOldUserByName($key)
    {
        return $this->AuthDb->searchOldUserByName($key);
    }
    public function setUserPassword($user,$passgen) {
        $user_id = $this->AuthDb->FindUserByName ($user);
        $user_info = $this->AuthDb->FindUserById ($user_id);
        $this->clearPager($user_id);
        if ($passgen==1) {
            $user_info['user_password'] = $this->User->user_password;
            $newpass = 0;
        } else {
            $_num = rand(4,9);
            $newpass = $this->PasswdGen($_num);
            $user_info['user_password'] = md5($newpass);
        }
        $user_info['user_email'] = $this->User->user_email;
        $user_info['registered'] = date("Y-m-d H:i:s");
        $user_info['active'] = 1;
        $user_info['confirmcode'] = $newpass;
        //Отключаем возможность смены пароля с целью недопущения покупки ников
        //$_id = $this->AuthDb->SaveUser ($user_info,2);
        $result = $newpass;
        return $result;
    }

    public function clearPager($userID) {
        $GLOBALS[ 'ForumCore' ]->DbManager->query ("DELETE FROM ?# WHERE userID = ?d", 'forum_users_pagerlist', $userID);
        $GLOBALS[ 'ForumCore' ]->DbManager->query ("UPDATE ?# SET del_from = 1 WHERE fromuser = ?d", 'forum_users_pager', $userID);
    }

}
