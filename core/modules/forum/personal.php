<?php

class Forum_Personal
{
    protected $MainTemplate = "forum/personal/index.tpl";
    var $_url_params = array ();
    var $Node = null;
    var $Form = null;
    var $GroupID = null;
    var $ThemeID = null;
    var $Page = 1;
    
    var $error = null;
    var $msg = null;
    
    /* Crop configuration */
    var $upload_path = "upload/"; // The path to where the image will be saved
    var $max_file = "107520"; // Approx 1MB
    var $max_width = "500"; // Max width allowed for the large image
    var $max_height = "500"; // Max width allowed for the large image
    var $thumb_width = "70"; // Width of thumbnail image
    var $thumb_height = "70"; // Height of thumbnail image
    var $CountPerPage = 70;
    private $forumModel = null;
    var $large_image = null;
    var $thumb_image = null;
    private $permissionModel = null;
    
    function __construct()
    {
        $this->AuthManager = CreateObject ("Auth_Manager");
        $this->_url_params = $GLOBALS[ 'ForumCore' ]->_url_params;
        $this->Node = $GLOBALS[ 'ForumCore' ]->CurrentParam;
        $this->DbManager = $GLOBALS[ 'ForumCore' ]->DbManager;
        $this->Form = CreateObject ('Form_Manager');
        if (isset ($_GET[ 'p' ]) && $_GET[ 'p' ] > 0) {
            $this->Page = intval ($_GET[ 'p' ]);
        }
    }

    public function getForumModel($dbManager = null, $authManager = null)
    {
        if (! $this->forumModel) {
            require_once (MODULE_DIR . 'forum/forum.class.php');
            $this->forumModel = new ForumModel ($this->DbManager, $this->AuthManager);
            $this->forumModel->CountPerPage = $this->CountPerPage;
        }
        return $this->forumModel;
    }
    public function getPermissionModel()
    {
        if (! $this->permissionModel) {
            require_once (MODULE_DIR . 'forum/permissoin.class.php');
            $this->permissionModel = new Permission ($this->DbManager, $this->AuthManager);
        }
        return $this->permissionModel;
    }
    // обработка событий
    public function uploadAvatar()
    {

        include (LIB_DIR . 'PHPThumb/ThumbLib.inc.php');
       
        if (! empty ($_FILES[ "image" ][ "name" ])) {
            //Get the file information  
            //$userfile_name = $_FILES[ "image" ][ "name" ];
            $userfile_tmp = $_FILES[ "image" ][ "tmp_name" ];
            //$userfile_size = $_FILES[ "image" ][ "size" ];
            $filename = basename ($_FILES[ "image" ][ "name" ]);
            $file_ext = strtolower (array_pop (explode ('.', $filename)));
            $this->large_image = HOME_DIR. $this->upload_path . "resized-" . $this->AuthManager->User->userID . ".".$file_ext;
            $this->thumb_image = HOME_DIR. $this->upload_path . "avatar-" . $this->AuthManager->User->userID . ".".$file_ext;
            if (file_exists ($this->thumb_image))
                unlink ($this->thumb_image);
            if (file_exists ($this->large_image))
                unlink ($this->large_image);

            if (copy ($userfile_tmp, $this->large_image)) {
                $thumb = PhpThumbFactory::create ($this->large_image);
                $thumb->resize($this->max_width,$this->max_height);
                $thumb->save ($this->large_image);
                chmod ($this->large_image, 0777);
                unset($thumb);
                $thumb = PhpThumbFactory::create ($this->large_image);
                $thumb->adaptiveResize ($this->thumb_width, $this->thumb_height);
                $thumb->save ($this->thumb_image);
                chmod ($this->thumb_image, 0777);
                unset ($thumb);
            }
/*

            
            //Only process if the file is a JPG and below the allowed limit  
            if ((! empty ($_FILES[ "image" ])) && ($_FILES[ "image" ][ "error" ] == 0)) {
                if (($file_ext != "JPG") || ($userfile_size > $this->max_file)) {
                    $this->error = "Разрешены только JPEG-изображения до 100 KB";
                }
            } else {
                $this->error = "Выберите JPEG-изображение для загрузки";
            }
            
            //Everything is ok, so we can upload the image.  
            if (! isset ($this->error)) {
                if (isset ($_FILES[ "image" ][ "name" ])) {
                    move_uploaded_file ($userfile_tmp, $this->large_image);
                    chmod ($this->large_image, 0777);
                    
                    $width = $this->getWidth ($this->large_image);
                    //if ($width > $this->max_width) {
                    //    $this->error = "Разрешены только JPEG-изображения до 500px в ширину";
                    //} else {
                    $height = $this->getHeight ($this->large_image);
                        
                        $uploaded = $this->resizeImage ($this->large_image, $width, $height, 1);
                        
                        $scale = $this->thumb_width / $width;
                        $this->resizeThumbnailImage ($this->thumb_image, $this->large_image, $width, $width, 0, 0, $scale);
                    //}
                
     //unlink($this->large_image);  
                }
            }
 *
 */
        }
    }

    private function resizeImage($image, $width, $height, $scale)
    {
        $newImageWidth = ceil ($width * $scale);
        $newImageHeight = ceil ($height * $scale);
        $newImage = imagecreatetruecolor ($newImageWidth, $newImageHeight);
        $source = imagecreatefromjpeg ($image);
        imagecopyresampled ($newImage, $source, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $width, $height);
        imagejpeg ($newImage, $image, 90);
        chmod ($image, 0777);
        return $image;
    }

    private function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale)
    {
        $newImageWidth = ceil ($width * $scale);
        $newImageHeight = ceil ($height * $scale);
        $newImage = imagecreatetruecolor ($newImageWidth, $newImageHeight);
        $source = imagecreatefromjpeg ($image);
        imagecopyresampled ($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
        imagejpeg ($newImage, $thumb_image_name, 90);
        chmod ($thumb_image_name, 0777);
        return $thumb_image_name;
    }

    private function getHeight($image)
    {
        $sizes = getimagesize ($image);
        $height = $sizes[ 1 ];
        return $height;
    }

    private function getWidth($image)
    {
        $sizes = getimagesize ($image);
        $width = $sizes[ 0 ];
        return $width;
    }

    public function onEvent_ForumUserBuyPackage($form)
    {
        $_idPackage = $form->Request[ 'buyPackageId' ];
        if ((int) $_idPackage > 0) {
            $_info = $this->DbManager->selectrow ("SELECT * FROM ?# WHERE `id` = ?d", 'merchant_packages', $_idPackage);
            
            if ($_info[ 'price' ] <= $this->AuthManager->User->user_balance) {
                $merchant = CreateObject ('Money_Tariffication', array (
                    'DbManager' => $this->DbManager
                ));
                $_res = $merchant->writeData (array (
                    'packageid' => $_idPackage, 
                    'userid' => $this->AuthManager->User->userID, 
                    'user_name' => $this->AuthManager->User->user_name
                ), 'userpackage');
                
                if (count ($_res[ 'check' ][ 'error' ]) == 0) {
                    $this->DbManager->query ("UPDATE ?# SET 
											`user_balance` = ?d
											WHERE
												userID = ?d LIMIT 1", 'forum_users', ($this->AuthManager->User->user_balance - $_info[ 'price' ]), $this->AuthManager->User->userID);
                    
                    $this->msg = 'Пакет куплен';
                    $this->showBuyButton = false;
                    $this->AuthManager->User->user_balance = $this->AuthManager->User->user_balance - $_info[ 'price' ];
                } else {
                    $this->error = implode ('<br/>', $_res[ 'check' ][ 'error' ]);
                }
            } else {
                $this->error = 'У вас не хватает денег на счету';
            }
        }
    }

    public function onEvent_ForumUserProfileUpdate($form)
    {
        $this->Form = $form;
        $this->uploadAvatar ();
        if ($this->AuthManager->User->userID > 0) {
            $_new_user = $this->Form->Request[ 'user' ];
            if ($this->Form->Request[ 'user' ][ 'user_password' ] !== '' && ($this->Form->Request[ 'user' ][ 'user_password' ] == $this->Form->Request[ 'user' ][ 'user_password_confirm' ])) {
                unset ($_new_user[ 'user_password_confirm' ]);
                setcookie ('hash', '', time (), '/', '.' . SERVER_NAME, 0, 0);
                //$_new_user['user_password'] = ($_new_user['user_password']);
            } else {
                //$_new_user[ 'user_password' ] = $this->AuthManager->User->user_password;
                unset ($_new_user[ 'user_password' ]);
                unset ($_new_user[ 'user_password_confirm' ]);
            }
            if (!filter_var($_new_user[ 'user_email' ], FILTER_VALIDATE_EMAIL)) {
                $this->error = "Задайте верный e-mail";
            } else {
                if (!empty($_new_user[ 'user_email' ])) {
                    $_user_email_count = $this->DbManager->selectCell("SELECT count(user_email) FROM ?# WHERE user_email=?",'forum_users',$_new_user[ 'user_email' ]);
                } else {
                    $this->error = "Задайте e-mail";
                }
            }
            if ($_new_user['fz152_agreement']=='on') {
                $_new_user['fz152_agreement']=1;
            }
            if ($_new_user['pager_subscribe']=='on') {
                $_new_user['pager_subscribe']=1;
            } else {
                $_new_user['pager_subscribe']=0;
            }
            if (! $this->error) {
                //проверка уникальности почты
                $countUniqEmail = $this->DbManager->selectCell("SELECT count(user_email) FROM ?# WHERE user_email=? and userID<>?d",'forum_users',$_new_user[ 'user_email' ],$this->AuthManager->User->userID);
                if ($countUniqEmail>0 || ($_user_email_count>0 && $this->AuthManager->User->user_email<>$_new_user[ 'user_email' ])) {
                    $this->error = "Такой e-mail уже есть у другого пользователя. Задайте другой e-mail.";
                    //$_new_user[ 'user_email' ] = $this->AuthManager->User->user_email;
                } else {
                    $_new_user[ 'userID' ] = $this->AuthManager->User->userID;
                    $_new_user[ 'user_name' ] = $this->AuthManager->User->user_name;
                    $_new_user[ 'registered' ] = $this->AuthManager->User->registered;
                    $_new_user[ 'is_admin' ] = $this->AuthManager->User->is_admin;
                    $_new_user[ 'danger_level' ] = $this->AuthManager->User->danger_level;
                    $_new_user[ 'user_balance' ] = $this->AuthManager->User->user_balance;


                    $message = "Добрый день, '".$this->AuthManager->User->user_name."'!"."\n";
                    $message .= "Ваш профиль на форуме http://".SERVER_NAME." был обновлен.\n\n";
                    $message .= "-------------------------------------\n";
                    $message .= "С Уважением, Робот http://".SERVER_NAME."\n";
                    
                    $Mailer = CreateObject("Mail_PHPMailer");
                    $Mailer->From = 'noreply@'.SERVER_NAME.'';      // от кого
                    $Mailer->FromName = 'http://'.SERVER_NAME.'/';   // от кого
                    $Mailer->AddAddress($_new_user[ 'user_email' ],iconv("utf-8", "windows-1251", $_new_user[ 'user_name' ])); // кому - адрес, Имя
                    $Mailer->Subject = iconv("utf-8", "windows-1251", "Приватное сообщение с ".SERVER_NAME."");  // тема письма
                    $Mailer->Body = iconv("utf-8", "windows-1251", $message);
                    @$Mailer->Send();
                    $Mailer->ClearAllRecipients();
                    @$Mailer->ClearAttachments();
                    unset($Mailer);


                    if (is_array ($this->Form->Request[ 'igroups' ])) {
                        $_new_user[ 'ignore_groups' ] = join (",", array_keys ($this->Form->Request[ 'igroups' ]));
                    } else {
                        $_new_user[ 'ignore_groups' ] = '';
                    }
                    if (isset ($this->Form->Request[ 'delUserPic' ])) {
                        $_file = MOUNT_DIR . '/upload/resized-' . $_new_user[ 'userID' ] . '.jpg';
                        if (file_exists ($_file)) {
                            unlink ($_file);
                        }
                        $_file = MOUNT_DIR . '/upload/avatar-' . $_new_user[ 'userID' ] . '.jpg';
                        if (file_exists ($_file)) {
                            unlink ($_file);
                        }
                    }

                    $allowRefererList = $this->AuthManager->allowRefererList;
                    for ($i=0;$i<count($allowRefererList);$i++) {
                        if(strstr($_SERVER['HTTP_REFERER'],$allowRefererList[$i]) && !empty($_SERVER['HTTP_REFERER'])){
                            $valid = 1;
                            break;
                        }
                    }
                    if ($valid != 1) {
                        $this->error = "Нельзя отправить";
                    } else {
                        $this->AuthManager->AuthDb->SaveUser ($_new_user);
                    }

                }
                
            }

            if (! $this->error)
                header ("location: /personal/settings/");
        } else {
            header ('Location: /register/');
        }
    
    }

    public function onEvent_UserLinkPackage($form)
    {
        $_packageId = (int) $_POST[ '_packageId' ];
        $_objId = (int) $_POST[ '_objId' ];
        if ($_packageId > 0) {
            $cSerives = CreateObject ('Money_Commercialservice', array (
                'DbManager' => $this->DbManager
            ));
            $_res = $cSerives->makeServiceLink ($_objId, $_packageId, $this->AuthManager->User->userID);
            if (isset ($_res[ 'check' ][ 'errors' ]) && count ($_res[ 'check' ][ 'errors' ]) > 0) {
                $this->error = implode ('<br/>', $_res[ 'check' ][ 'errors' ]);
            } else {
                $this->msg = "Пакет добавлен";
            }
        } else {
            $this->error = 'Не выбран пакет';
        }
    }

    public function onEvent_ThemeUpTop($form)
    {
        if ($this->AuthManager->User->userID == 0 || COMMERCIAL_ON == 0) {
            header ('Location: /forum/');
            exit ();
        }
        
        $tId = (int) $this->_url_params[ 3 ];
        
        if ((int) $form->Request[ '_package' ] == 0) {
            $this->error = 'Не выбран пакет';
        } elseif ($this->error == '' && (int) $gID > 0) {
            if (is_array ($_obj) && count ($_obj) > 0) {
                $cSerives = CreateObject ('Money_Commercialservice', array (
                    'DbManager' => $this->DbManager
                ));
                $_res = $cSerives->makeServiceLink ($tId, (int) $form->Request[ '_package' ], $this->AuthManager->User->userID);
                if (count ($_res[ 'check' ][ 'errors' ] > 0)) {
                    $this->error = implode ('<br/>', $_res[ 'check' ][ 'errors' ]);
                }
            }
        }
    }

    public function onEvent_FavoriteUserThemeAdd($form)
    {
        $this->Form = $form;
        if ($this->Form->Request[ 'theme' ] != '' && $this->AuthManager->User->userID > 0) {
            $result = $this->DbManager->query ("INSERT INTO ?# SET userID=?d, themeID=?d", 'forum_db_favorites', $this->AuthManager->User->userID, $this->Form->Request[ 'theme' ]);
            //var_dump($result);
            die();
            //header('Location: /personal/favorites/');
        }
    }

    public function onEvent_FavoriteUserThemeDelete($form)
    {
        $this->Form = $form;
        if ($this->Form->Request[ 'theme' ] != '' && $this->AuthManager->User->userID > 0) {
            $this->DbManager->query ("DELETE FROM ?# WHERE userID=?d AND themeID=?d", 'forum_db_favorites', $this->AuthManager->User->userID, $this->Form->Request[ 'theme' ]);
            header ('Location: /personal/favorites/');
        }
    }

    function listMyPackages($user_id = 0, $_method = '', $display = array(1))
    {
        if ($user_id == 0) {
            $user_id = $this->AuthManager->User->userID;
        }
        
        $_res = array ();
        
        if ($user_id > 0 && COMMERCIAL_ON == 1) {
            $merchant = CreateObject ('Money_Tariffication', array (
                'DbManager' => $this->DbManager
            ));
            $_res = $merchant->getListPackageByUser ($user_id, $_method, $display);
        }
        
        return $_res;
    }

    function listTableServices($_package_id = 0)
    {
        $_list = array ();
        if (COMMERCIAL_ON == 1) {
            $merchant = CreateObject ('Money_Tariffication', array (
                'DbManager' => $this->DbManager
            ));
            
            $_list = $merchant->listTableServices ($_package_id);
        }
        return $_list;
    }

    function Prepare(&$ds)
    {
        if (isMobile()) {
            $ds->assign("is_mobile", 'mobile');
        } else {
            $ds->assign("is_mobile", 'not mobile');
        }
        list ($null, $personal, $type) = $this->_url_params;
        //print_r($this->AuthManager);
        $ForumManager = CreateObject ("Forum_Manager");
        $_groups = $ForumManager->LoadGroups ();
        $ds->assign ("groups", $_groups);
        if ($type == 'uptheme' && $this->AuthManager->User->userID == 0) {
            header ('Location: /pages/up/');
            exit ();
        }
        if ($this->AuthManager->User->userID || $this->_url_params[ 3 ] == 'sms') {
            switch ($type) {
                case 'buyuser':
                    if (isset ($_GET[ 'q' ])) {
                        $userlist = $this->AuthManager->searchOldUserByName ($_GET[ 'q' ]);
                        echo (implode ("\n", $userlist));
                    } else {
                        $this->buyNewUser($_GET['u'],$_GET['t']);
                    }
                    exit ();
                    break;
                case 'buykarma' :
                    $this->setKarma ();
                    exit();
                    break;
                case 'sellkarma' :
                    $this->sellKarma ();
                    exit();
                    break;
                case 'hidetheme' :
                    $this->hideTheme(intval($this->_url_params[ 3 ]));
                    exit();
                    break;
                case 'listmyactions' :
                    if (COMMERCIAL_ON == 1) {
                        $ds->assign ("title_part", "СПИСОК ПЛАТЕЖЕЙ");
                        $this->MainTemplate = "forum/personal/tarification/listmyactions.tpl";
                        $cSerives = CreateObject ('Money_Commercialservice', array (
                            'DbManager' => $this->DbManager
                        ));
                        $_list = $cSerives->listLogActions ($this->AuthManager->User->userID);
                        $ds->assign ('listActions', $_list);
                    }
                    break;
                case 'listmypackages' :
                    if (COMMERCIAL_ON == 1 && $this->AuthManager->User->userID > 0) {
                        $ds->assign ("title_part", "СПИСОК ПАКЕТОВ");
                        $_lst = $this->listMyPackages (0, '', array (
                            0, 
                            1
                        ));
                        
                        if (isset ($_lst)) {
                            $merchant = CreateObject ('Money_Tariffication', array (
                                'DbManager' => $this->DbManager
                            ));
                            $_lst = $merchant->mkTimeInByMethod ($_lst, 'hour', '%01.2f');
                            $ds->assign ("packages", $_lst[ 'packages' ]);
                        }
                        
                        $this->MainTemplate = "forum/personal/tarification/listmypackages.tpl";
                    }
                    break;
                case 'uptheme' :
                    $moveTo = '';
                    if (COMMERCIAL_ON == 1 && $this->AuthManager->User->userID > 0) {
                        $ds->assign ("title_part", "ПОДНЯТИЕ ТЕМЫ");
                        $this->MainTemplate = "forum/personal/tarification/uptheme.tpl";
                        $merchant = CreateObject ('Money_Tariffication', array (
                            'DbManager' => $this->DbManager
                        ));
                        $_list = $merchant->getPackageByServiceInUser ('uptheme', $this->AuthManager->User->userID);
                        if (count ($_list) > 0) {
                            $gID = $this->DbManager->selectcell ("SELECT `groupID`
									FROM ?#
									WHERE 
									`themeID` = ?d AND `hidden` = 0", "forum_db_themes", $this->_url_params[ 3 ]);
                            
                            if ((int) $gID > 0) {
                                $_topCnt = $this->DbManager->selectcell ("SELECT COUNT(*)
										FROM ?#
										WHERE 
										`groupID` = ?d
										AND `is_top` = 1 AND `hidden` = 0", "forum_db_themes", $gID);
                            }
                            
                            if ((int) $_topCnt < 5) {
                                $ds->assign ("listPackages", $_list);
                                $ds->assign ("upTheme", $this->_url_params[ 3 ]);
                            } else {
                                $this->error = 'Вы не можете поднять в ТОП, все места заняты';
                            }
                        } else {
                            $moveTo = '/pages/buypackage/';
                        }
                    } else {
                        $moveTo = '/pages/up/';
                    }
                    if ($moveTo != '') {
                        header ('Location: ' . $moveTo);
                        exit ();
                    }
                    break;
                case 'buypackage' :
                    if (COMMERCIAL_ON == 1 && $this->AuthManager->User->userID > 0) {
                        $ds->assign ("title_part", "КУПИТЬ СЕРВИСОВ");
                        $_package_id = $this->_url_params[ 3 ];
                        
                        $_lst = $this->listTableServices ($_package_id);
                        $merchant = CreateObject ('Money_Tariffication', array (
                            'DbManager' => $this->DbManager
                        ));
                        $_lst = $merchant->mkTimeInByMethod ($_lst, 'hour', '%01.2f');
                        if (! isset ($_lst[ 'packages' ][ $_package_id ][ 'info' ][ 'isactive' ]) || ! isset ($_lst[ 'packages' ][ $_package_id ][ 'info' ][ 'display' ]) || $_lst[ 'packages' ][ $_package_id ][ 'info' ][ 'isactive' ] != 1 || $_lst[ 'packages' ][ $_package_id ][ 'info' ][ 'display' ] != 1) {
                            header ('Location: /forum/');
                            exit ();
                        }
                        
                        if (isset ($_lst[ 'packages' ][ $_package_id ])) {
                            $ds->assign ("package", $_lst[ 'packages' ][ $_package_id ]);
                        }
                        
                        $_lst = $this->listMyPackages (0, 'notlink');
                        if (isset ($_lst[ 'packages' ][ $_package_id ])) {
                            $this->showBuyButton = false;
                        }
                        if (isset ($this->showBuyButton)) {
                            $ds->assign ("showBuyButton", $this->showBuyButton);
                        } else {
                            $ds->assign ("showBuyButton", true);
                        }
                        
                        $this->MainTemplate = "forum/personal/tarification/buypackage.tpl";
                    } else {
                        header ('Location: /forum/');
                        exit ();
                    }
                    break;
                case 'tarification' :
                    if (COMMERCIAL_ON == 1) {
                        $ds->assign ("title_part", "СПИСОК СЕРВИСОВ");
                        $this->MainTemplate = "forum/personal/tarification/listservices.tpl";
                        $_lst = $this->listTableServices ();
                        $_myLst = $this->listMyPackages (0, 'notlink');
                        $merchant = CreateObject ('Money_Tariffication', array (
                            'DbManager' => $this->DbManager
                        ));
                        $_lst = $merchant->mkTimeInByMethod ($_lst, 'hour', '%01.2f');
                        if (is_array ($_lst[ 'packages' ])) {
                            foreach ($_lst[ 'packages' ] as $key => $value) {
                                //echo '<pre>' . print_r($_myLst['packages'][$key], true) . '</pre>';
                                if (! isset ($_myLst[ 'packages' ][ $key ])) {
                                    $_lst[ 'packages' ][ $key ][ 'info' ][ 'showBuy' ] = true;
                                }
                            }
                        }
                        $ds->assign ("listServices", $_lst[ 'service' ]);
                        $ds->assign ("listPackages", $_lst[ 'packages' ]);
                    } else {
                        header ('Location: /forum/');
                        exit ();
                    }
                    break;
                case 'favorites' :
                    $_favorites = $this->GetUserFavorites ($this->AuthManager->User->userID);
                    $this->MainTemplate = "forum/personal/favorites.tpl";
                    $ds->assign ("favorite", $_favorites);
                    $ds->assign ("title_part", "ИЗБРАННОЕ");
                    $ds->assign ("_per_page", PER_PAGE);
                    break;
                case 'blacklist' :
                    $this->showBlackListManage($ds);
                    break;
                case 'album' :
                    $this->MainTemplate = "forum/personal/gallery/index.tpl";
                    break;
                case 'passport' :
                    $this->MainTemplate = "forum/personal/passport/passport.tpl";
                    $ds->assign ("title_part", "МОЙ ПАСПОРТ");
                    $_passport_info = $this->LoadPassport ((isset ($this->_url_params[ 3 ]) && (int) $this->_url_params[ 3 ] > 0) ? $this->_url_params[ 3 ] : $this->AuthManager->User->userID);
                    $thumb_image = $this->upload_path . "avatar-" . $_passport_info[ 'userID' ] . ".jpg";
                    $large_image = $this->upload_path . "resized-" . $_passport_info[ 'userID' ] . ".jpg";
                    

                    $ds->assign ("_large_image", $large_image);
                    if (file_exists ($thumb_image)) {
                        $ds->assign ("_thumb_image", $thumb_image);
                    }
                    
                    $this->parseGroups ($ds, $_groups, $this->AuthManager->User->ignore_groups);
                    break;
                case 'files' :
                    $this->MainTemplate = "forum/personal/file/index.tpl";
                    break;

                //TODO Вынести payment за пределы действия пермишнов (если не залогиненый, то и нет возможности попасть на эти страницы)
                //или разобраться как дать права незалогиненым открывать те или иные страницы (regplat, w1)
                case 'payment' :
                    list ($null, $personal, $type, $subtype, $action, $addition_param) = $this->_url_params;
                    $ix = sprintf("forumAuth_FindUserById_%d", $this->AuthManager->User->userID);
                    xcache_unset($ix);

                    if (isset ($subtype)) {
                        switch ($subtype) {
                            case 'robokassa' :
                                $ds->assign('paymentLogin',ROBOKASSA_LOGIN);
                                if (isset ($action) && $action != '') {
                                    switch ($action) {
                                        case 'pay' :
                                            $this->MainTemplate = "forum/personal/payment/wmr_pay.tpl";
                                            break;
                                        case 'success' :
                                            $this->MainTemplate = "forum/personal/payment/wmr_sccess.tpl";
                                            break;
                                        case 'fail' :
                                            $this->MainTemplate = "forum/personal/payment/wmr_fail.tpl";
                                            break;
                                        case 'getcrc' :
                                            echo md5(ROBOKASSA_LOGIN.":".$_GET['summ'].":".$_GET['invId'].":".ROBOKASSA_PASSWORD1.":Shp_item=".$_GET['shpItm']);
                                            exit();
                                            break;
                                    }
                                } else {
                                    $this->MainTemplate = "forum/personal/payment/wmr.tpl";
                                }
                                break;
                            case 'regplat' :
                                $ds->assign('id_merchant',REGPLAT_MERCHANT_ID);
                                $ds->assign('DOMAIN',SERVER_NAME);
                                //$paydesc = iconv("UTF-8", "WINDOWS-1251",'forum.site  - пополнение баланса пользователя '.$this->AuthManager->User->user_name);
                                $paydesc = 'forum.site  - пополнение баланса пользователя '.$this->AuthManager->User->user_name;
                                $ds->assign('converted_desc',$paydesc);
                                $ds->assign('id_order',$this->AuthManager->User->userID);
                                $ds->assign('payment_system_name','Regplat');
                                if (isset ($action) && $action != '') {
                                    switch ($action) {
                                        case 'pay' :
                                            $this->MainTemplate = "forum/personal/payment/regplat_pay.tpl";
                                            break;
                                        case 'success' :
                                            $this->MainTemplate = "forum/personal/payment/payment_success.tpl";
                                            break;
                                        case 'fail' :
                                            $this->MainTemplate = "forum/personal/payment/payment_fail.tpl";
                                            break;
                                        /*
                                        case 'getcrc' :
                                            echo md5(ROBOKASSA_LOGIN.":".$_GET['summ'].":".$_GET['invId'].":".ROBOKASSA_PASSWORD1.":Shp_item=".$_GET['shpItm']);
                                            exit();
                                            break;
                                         * 
                                         */
                                    }
                                } else {
                                    $this->MainTemplate = "forum/personal/payment/regplat.tpl";
                                }
                                break;
                            case 'w1' :
                                $ds->assign('payment_system_name','WalletOne - W1');
                                $ds->assign('DOMAIN',SERVER_NAME);
                                $str_no_encode = 'forum.site  - пополнение баланса пользователя '.$this->AuthManager->User->user_name;
                                $paydesc = "BASE64:".base64_encode($str_no_encode);
                                $payment_no = $this->AuthManager->User->userID."_".uniqid().'_'.date('Y_m_d');
                                $success_url = "http://".SERVER_NAME."/personal/payment/w1/success/";
                                $fail_url = "http://".SERVER_NAME."/personal/payment/w1/fail/";

                                $amount_id = $_POST['amount_rub']>0?$_POST['amount_rub']:0;
                                $signature_key = WMI_SIGNATURE_CODE;

                                //$fields["WMI_SIGNATURE"] = $signature;

                                if (isset ($action) && $action != '') {
                                    switch ($action) {
                                        case 'form' :

                                            $fields = array();
                                            // Добавление полей формы в ассоциативный массив
                                            $fields["WMI_MERCHANT_ID"]    = WMI_MERCHANT_ID;
                                            $fields["WMI_PAYMENT_AMOUNT"] = $amount_id;
                                            $fields["WMI_CURRENCY_ID"]    = WMI_CURRENCY_ID;
                                            $fields["WMI_PAYMENT_NO"]     = $payment_no;
                                            $fields["WMI_DESCRIPTION"]    = $paydesc;
                                            $fields["WMI_SUCCESS_URL"]    = $success_url;
                                            $fields["WMI_FAIL_URL"]       = $fail_url;
                                            //Сортировка значений внутри полей
                                            foreach($fields as $name => $val) {
                                                if (is_array($val)) {
                                                    usort($val, "strcasecmp");
                                                    $fields[$name] = $val;
                                                }
                                            }

                                            uksort($fields, "strcasecmp");
                                            $fieldValues = "";
                                            foreach($fields as $value) {
                                                if (is_array($value))
                                                    foreach($value as $v) {
                                                        $v = iconv("utf-8", "windows-1251", $v);
                                                        $fieldValues .= $v;
                                                    }
                                                else {
                                                    $value = iconv("utf-8", "windows-1251", $value);
                                                    $fieldValues .= $value;
                                                }
                                            }

                                            $signature = base64_encode(pack("H*", md5($fieldValues . $signature_key)));

                                            $ds->assign('id_merchant_w1',WMI_MERCHANT_ID);
                                            $ds->assign('id_currency_w1',WMI_CURRENCY_ID);

                                            $ds->assign('amount_w1',$amount_id);
                                            $ds->assign('converted_desc_w1',$paydesc);
                                            $ds->assign('id_order_w1',$payment_no);

                                            $ds->assign('success_w1',$success_url);
                                            $ds->assign('fail_w1',$fail_url);
                                            $ds->assign('signature_w1',$signature);
                                            $this->DbManager->query ("INSERT INTO ?#
                                                                        SET id=NULL,
                                                                            amount=?,
                                                                            currency_id=?d,
                                                                            payment_no=?,
                                                                            description=?,
                                                                            order_state='init',
                                                                            request_datetime=NOW(),
                                                                            user_id=?d,
                                                                            request_signature=?",
                                                'wallet_one',
                                                $amount_id,
                                                WMI_CURRENCY_ID,
                                                $payment_no,
                                                $str_no_encode,
                                                $this->AuthManager->User->userID,
                                                $signature
                                            );
                                            $this->MainTemplate = "forum/personal/payment/w1_form.tpl";
                                            break;
                                        case 'success' :
                                            $this->MainTemplate = "forum/personal/payment/payment_success.tpl";
                                            break;
                                        case 'fail' :
                                            $this->MainTemplate = "forum/personal/payment/payment_fail.tpl";
                                            break;
                                    }
                                } else {
                                    $this->MainTemplate = "forum/personal/payment/w1.tpl";
                                }
                                break;
                            case 'sms' :
                                if (isset ($action) && $action != '') {

                                } else {
                                    $this->MainTemplate = "forum/personal/payment/sms.tpl";
                                    if ($this->Form->Request[ 'sms' ][ 'code' ]) {
                                        $this->Form->Request[ 'sms' ][ 'code' ];
                                        $MoneySMS = CreateObject ("Money_Sms");
                                        $_result = $MoneySMS->CheckSmsCode ($this->Form);
                                        if ($_result > 0) {
                                            $ds->assign ('result', $_result);
                                            $this->DbManager->query ("UPDATE ?# SET `user_balance`=`user_balance`+50 WHERE `userID`=?d", 'forum_users', $this->AuthManager->User->userID);
                                            $this->DbManager->query ("INSERT INTO `merchant_logactions` SET id=NULL, `date`=NOW(), `userid`=?d, `action`=?, `payment`=?", $this->AuthManager->User->userID, 'Пополнение баланса через sms', 50);
                                            //print_r($this->DbManager->error);
                                            $this->AuthManager->User->user_balance = $this->AuthManager->User->user_balance + 50;
                                        }
                                        $ds->assign ("request", 1);
                                    }
                                }
                                break;
                            
                            case 'bank' :
                                if (isset ($action) && $action != '') {
                                    $this->MainTemplate = "forum/personal/payment/bank_blank.tpl";
                                } else {
                                    $this->MainTemplate = "forum/personal/payment/bank.tpl";
                                }
                                break;
                        }
                    } else {
                        $_transactions = $this->DbManager->select ("-- CACHE: 0h 10m 0s
                                SELECT *
                                FROM ?#
                                WHERE userID=?d
                                order by transactionID DESC
                                LIMIT 0,50
                                "
                            , 'forum_db_billing',
                            $this->AuthManager->User->userID);
                        $ds->assign ("_dataGrid", $_transactions);
                        $this->MainTemplate = "forum/personal/payment/index.tpl";
                    }
                    break;
                case 'mythemes' :
                    $_themes = $this->DbManager->selectPage ($this->TotalPages, "-- CACHE: 0h 10m 0s
                                                SELECT
                                                    fu.`themeID`,
                                                    fu.`groupID`,
                                                    fu.`caption`,
                                                    fu.`author`,
                                                    fu.`messages`,
                                                    fu.`updated_by`,
                                                    fu.`updated`,
                                                    fu.`hidden`,
                                                    if(ifnull(top_end,now())>=now(),is_top,0) as is_top,
                                                    if(ifnull(top_end,now())>=now(),hottop,0) as hottop,
                                                    fu.`enddate`
                                                FROM ?# fu
                                                WHERE fu.`authorID`=?d
                                                and hidden = 0
                                                ORDER BY
                                                    if(ifnull(top_end,now())>=now(),is_top,0) DESC,
                                                    if(ifnull(top_end,now())>=now(),hottop,0) DESC,
                                                    fu.updated DESC
												LIMIT ?d, ?d", 'forum_db_themes', $this->AuthManager->User->userID, (($this->Page - 1) * $this->CountPerPage), $this->CountPerPage);
                    /*
                    if (COMMERCIAL_ON == 1) {
                        $merchant = CreateObject ('Money_Tariffication', array (
                            'DbManager' => $this->DbManager
                        ));
                        $_listServices = $merchant->getPackageByServiceInUser('', $this->AuthManager->User->userID, array('addcommercialtheme'));
                        //var_dump($_listServices);
                        //die();


                        $_packages_id = $this->DbManager->select ("SELECT 
												`objid`,
												`packageid`,
												`status`
											FROM
												?#
											WHERE
												`userid` = ?d", "merchant_packageforuser", $this->AuthManager->User->userID);
                        
                        if (count ($_packages_id) > 0) {
                            $_id = array ();
                            
                            foreach ($_packages_id as $value) {
                                $_id[ $value[ 'packageid' ] ] = $value[ 'packageid' ];
                            }
                            
                            $_names = array (
                                'addcommercialtheme'
                            );
                            $_not_id = $this->DbManager->selectcol ("SELECT
													sp.packageid
												FROM
													?# sp
												JOIN ?# s ON s.system_name IN (?a) AND s.id = sp.serviceid
												WHERE
													sp.packageid IN (?a)
												GROUP BY sp.packageid", "merchant_serviceinpackage", "merchant_services", $_names, $_id);
                            
                            if (! is_array ($_not_id)) {
                                $_not_id = array ();
                            }
                            
                            $_services_name = $this->DbManager->select ("SELECT
													p.id AS ARRAY_KEY, 
													p.name
												FROM
													?# p
												WHERE
													p.id IN (?a)
												ORDER BY
													name
												", "merchant_packages", $_id);
                            
                            $_out_id = array ();
                            foreach ($_packages_id as $value) {
                                $value[ 'name' ] = $_services_name[ $value[ 'packageid' ] ][ 'name' ];
                                if (((int) $value[ 'objid' ] > 0) || ((int) $value[ 'objid' ] == 0 && ! in_array ($value[ 'packageid' ], $_not_id)))
                                    $_out_id[ $value[ 'objid' ] ][ ] = $value;
                            }
                        }
                        if (isset ($_out_id) && count ($_out_id) > 0) {
                            $ds->assign ("listServices", $_out_id);
                        }
                        if ($this->msg != null) {
                            $ds->assign ("_msg", $this->msg);
                        }
                    }
                    */
                    
                    $ds->assign ("__total_rows", $this->TotalPages);
                    $ds->assign ("__page", $this->Page);
                    $ds->assign ("__pagePrev", $this->Page - 1);
                    $ds->assign ("__pageNext", $this->Page + 1);
                    $ds->assign ("__pages", ceil (($this->TotalPages + 1) / $this->CountPerPage));
                    $ds->assign ("__countperpage", $this->CountPerPage);
                    
                    $this->MainTemplate = "forum/personal/mythemes.tpl";
                    $ds->assign ("mythemes", $_themes);
                    $ds->assign ("title_part", "Мои темы");
                    break;
                default :
                    $thumb_image = $this->upload_path . "avatar-" . $this->AuthManager->User->userID . ".jpg";
                    $large_image = $this->upload_path . "resized-" . $this->AuthManager->User->userID . ".jpg";

                    $_KarmaTransactions = $this->DbManager->select ("-- CACHE: 0h 10m 0s
                                SELECT k.created, k.rating, u.user_name, k.messageId
                                FROM ?# k
                                LEFT JOIN ?# u ON k.userId=u.userID
                                WHERE k.authorID=?d
                                order by k.log_id DESC
                                LIMIT 0,50
                                "
                        , 'forum_db_rating_log'
                        , 'forum_users'
                        ,$this->AuthManager->User->userID);
                    $ds->assign ("_dataGrid", $_KarmaTransactions);

                    $ds->assign ("_large_image", $large_image);
                    if (file_exists ($thumb_image))
                        $ds->assign ("_thumb_image", $thumb_image);
                    $this->MainTemplate = "forum/personal/index.tpl";
                    $ds->assign ("title_part", "МОЙ ПАСПОРТ");
                    
                    $this->parseGroups ($ds, $_groups, $this->AuthManager->User->ignore_groups);
            }
        
     // информация для постраничного вывода.
        } else {
            list ($null, $personal, $type, $subtype, $action, $addition_param) = $this->_url_params;
            if (isset ($action) && $action != '') {
                switch ($action) {
                    case 'pay' :
                        $this->MainTemplate = "forum/personal/payment/wmr_pay.tpl";
                        if ($this->DbManager->error) {
                            $ds->assign ('result', serialize ($this->DbManager->error));
                        } else {
                            $this->DbManager->query ("INSERT INTO `merchant_payments_wm` SET `id`=null, `number`=?, `amount`=? ", @$_POST[ 'LMI_PAYMENT_NO' ], @$_POST[ 'LMI_PAYMENT_AMOUNT' ]);
                            $ds->assign ('result', 'YES');
                        }
                        break;
                    case 'success' :
                        $this->MainTemplate = "forum/personal/payment/wmr_sccess.tpl";
                        break;
                    case 'fail' :
                        $this->MainTemplate = "forum/personal/payment/wmr_fail.tpl";
                        break;
                }
            } else {
                switch ($type) {
                    case 'passport' :
                        $thumb_image = $this->upload_path . "avatar-" . $_GET[ 'uid' ] . ".jpg";
                        $large_image = $this->upload_path . "resized-" . $_GET[ 'uid' ] . ".jpg";
                        $ds->assign ("_large_image", $large_image);
                        if (file_exists ($thumb_image))
                            $ds->assign ("_thumb_image", $thumb_image);
                        $this->MainTemplate = "forum/personal/passport/passport.tpl";
                        $ds->assign ("title_part", "МОЙ ПАСПОРТ");
                        $_passport_info = $this->LoadPassport ((isset ($this->_url_params[ 3 ]) && (int) $this->_url_params[ 3 ] > 0) ? $this->_url_params[ 3 ] : 0);
                        break;
                    default :
                        header ("Location: /forum/");
                
     //$this->MainTemplate = "forum/personal/index.tpl";
                //$ds->assign("title_part", "МОЙ ПАСПОРТ");
                }
            }
        }
        if ($this->AuthManager->User->userID > 0) {
            $ForumPager = CreateObject ("Forum_Pager");
            $_pager_stat = $ForumPager->CheckMessStat ($this->AuthManager->User->userID);
            $ds->assign ("_pager_info", $_pager_stat);
        }
        $ds->assign ("_user_passport_info", $_passport_info);
        $ds->assign ("_page_totalrows", $this->TotalRows);
        $ds->assign ("_page_curpage", $this->Page);
        $ds->assign ("_page_countperpage", $this->CountPerPage);
        $ds->assign ("_error", $this->error);
        $ds->assign ("_msg", $this->msg);
    }
    public function showBlackListManage(&$ds)
    {
        $this->MainTemplate = 'forum/personal/blacklist.tpl';
        if ($_POST['action'] == 'removeuser') {
            $validUser = $this->getPermissionModel()->Decrypt($_POST['userId']);
            if (intval($validUser)>0) {
                $this->getPermissionModel()->removeUserFromPersonalBlackList($validUser,$this->AuthManager->User->userID);
            }
            die(json_encode(array('success'=> 1)));
        }
        if ($_POST['action'] == 'addBlackUser') {
            $result = $this->getPermissionModel()->addUserInPersonalBlackList($_POST['user'],$this->AuthManager->User->userID);
            if ($result['errors']) {
                $this->Errors = $result['errors'];
            }
        }
        if ($_POST['action'] == 'savePrivateSettings'){
            $this->getPermissionModel()->savePersonalBlackListSettings($this->AuthManager->User->userID,array('hideAnonymous'=>$_POST['hideAnonymous'],'hideUsers'=>$_POST['hideUsers']));
        }
        $blackListSettings = $this->getPermissionModel()->getPersonalBlackListSettings($this->AuthManager->User->userID);
        $bannedUsers = $this->getPermissionModel()->getUsersInPersonalBlackList($this->AuthManager->User->userID);
        if (isset($_GET['q'])) {
            echo(implode("\n",$this->AuthManager->searchUserByName($_GET['q'])));
            exit();
        }
        $ds->assign('blackListSettings',$blackListSettings);
        $ds->assign('bannedUsers',$bannedUsers);
    }
    //---------------Вывод шаблона
    public function parseGroups(&$ds, $_groups, $ignored)
    {
        $igroups = split (",", $ignored);
        
        //Parse groups for a form
        $count = 0;
        $leftGr = array ();
        $rightGr = array ();
        $commonGr = array ();
        foreach ($_groups as &$group) {
            $count ++;
            
            if (in_array ($group[ 'groupID' ], $igroups))
                $group[ 'i' ] = 1;
            else
                $group[ 'i' ] = 0;
            
            if ($count % 2)
                $leftGr[ ] = $group;
            else
                $rightGr[ ] = $group;
            $commonGr[] = $group;
        }
        
        $ds->assign ("groups_left", $leftGr);
        $ds->assign ("groups_right", $rightGr);
        $ds->assign ("groups_common", $commonGr);
    }

    public function Display(&$parser)
    {
        $parser->display ($this->MainTemplate);
    }

    public function LoadPassport($_userid)
    {
        $_passport_info = $this->DbManager->selectrow ("SELECT * FROM ?# WHERE userID=?d", 'forum_users', $_userid);
        return $_passport_info;
    }

    private function buyNewUser($userName,$passType) {

        if ($this->AuthManager->User->userID > 0 && $this->AuthManager->User->user_balance >= OLD_NICKNAME_COST) {
            if (!empty($userName) && ($passType==1 || $passType==2)){
                $_passport_info = $this->DbManager->selectrow ("SELECT * FROM ?# WHERE userID=?d", 'forum_users', $this->AuthManager->User->userID);
                if ($_passport_info['user_balance']>=OLD_NICKNAME_COST) {
                    $result_balance = $_passport_info['user_balance'] - OLD_NICKNAME_COST;
                    $this->DbManager->query ("UPDATE ?# SET
                                                    user_balance = ?d
                                                    ,danger_level = ifnull(danger_level,0)+1
                                                    WHERE
                                                        userID = ?d", 'forum_users', $result_balance, $this->AuthManager->User->userID);
                    $this->AuthManager->User->user_balance = $result_balance;
                    $use_pass = 2;//не менять пароль
                    $this->AuthManager->AuthDb->SaveUser ($this->AuthManager->CreateUserArray ($this->AuthManager->User),$use_pass);
                    $this->getForumModel()->billingLog(OLD_NICKNAME_COST,'Операция покупки пользователя '.$userName.' на сумму '.OLD_NICKNAME_COST.'р.');

                    $ix = sprintf("forumAuth_FindUserById_%d", $this->AuthManager->User->userID);
                    if (xcache_isset($ix)) {
                        $_user = unserialize(xcache_get($ix));
                        $_user['user_balance'] = $this->AuthManager->User->user_balance;
                        xcache_set($ix, serialize($_user), 1200);

                    }
                    $reslut_pass = $this->AuthManager->setUserPassword ($userName,$passType);
                    /*
                    $fp = fopen('log.txt', 'a+');
                    fwrite($fp, "\n\n-----------------\n\n");
                    fwrite($fp, '2.'.var_export($reslut_pass,true));
                    fwrite($fp, "\n-----------------\n");
                    fclose($fp);
                    */

                    $result = array (
                        'submitOn' => true,
                        'callFunc' => 'BuyUser',
                        'pass_genn' => $reslut_pass,
                        'pass_type' => $passType,
                        'balance' => number_format($result_balance, 2, '.', '')
                    );
                    $this->sendJSON ($result);
                } else {
                    $this->sendErrors (array (
                        'pass_genn' => 'Стоимость данной услуги '.OLD_NICKNAME_COST.' руб.'
                    ));
                }
                /*
                                $fp = fopen('log.txt', 'w+');
                                fwrite($fp, "\n\n-----------------\n\n");
                                fwrite($fp, '1.'.var_export(var_export($this->AuthManager->User->user_balance,true),true));
                                fwrite($fp, "\n-----------------\n");
                                fwrite($fp, '2.'.var_export(var_export($_passport_info,true),true));
                                fwrite($fp, "\n-----------------\n");
                                fwrite($fp, '3.'.var_export(var_export($result_balance,true),true));
                                fwrite($fp, "\n-----------------\n");
                                fclose($fp);
                */
            } else {
                $this->sendErrors (array (
                    'pass_genn' => 'Ошибочные параметры.'
                ));
            }
        } else {
            $this->sendErrors (array (
                'pass_genn' => 'Стоимость данной услуги '.OLD_NICKNAME_COST.' руб.'
            ));
        }
    }
    private function setKarma() {
        if ($this->AuthManager->User->userID > 0 && $this->AuthManager->User->user_balance >= KARMA_UNIT_COST) {
            $_passport_info = $this->DbManager->selectrow ("SELECT * FROM ?# WHERE userID=?d", 'forum_users', $this->AuthManager->User->userID);
            $result_balance = $_passport_info['user_balance'] - KARMA_UNIT_COST;
            $result_karma = $_passport_info['danger_level']+1;
            $this->DbManager->query ("UPDATE ?# SET
											user_balance = ?d
											,danger_level = ifnull(danger_level,0)+1
											WHERE
												userID = ?d", 'forum_users', $result_balance, $this->AuthManager->User->userID);
            $this->AuthManager->User->user_balance = $result_balance;
            $this->AuthManager->User->danger_level = $result_karma;

            $use_pass = 2;//не менять пароль
            $this->AuthManager->AuthDb->SaveUser ($this->AuthManager->CreateUserArray ($this->AuthManager->User),$use_pass);

            //$this->getForumModel()->billingLog(KARMA_UNIT_COST,'Операция покупки кармы на сумму '.KARMA_UNIT_COST.'р.');

            $ix = sprintf("forumAuth_FindUserById_%d", $this->AuthManager->User->userID);
            if (xcache_isset($ix)) {
                $_user = unserialize(xcache_get($ix));
                $_user['user_balance'] = $this->AuthManager->User->user_balance;
                $_user['danger_level'] = $this->AuthManager->User->danger_level;
                xcache_set($ix, serialize($_user), 1200);
            }

            $result = array (
                'submitOn' => true,
                'callFunc' => 'BuyKarma',
                'karma' => intval ($result_karma),
                'balance' => number_format($result_balance, 2, '.', '')
            );
            $this->sendJSON ($result);
        } else {
            $this->sendErrors (array (
                'karma' => 'Стоимость данной услуги '.KARMA_UNIT_COST.' руб.'
            ));

        }
    }
    private function sellKarma() {
        if ($this->AuthManager->User->userID > 0 && $this->AuthManager->User->danger_level >= 0 && $this->AuthManager->User->banned==0) {
            $_passport_info = $this->DbManager->selectrow ("SELECT * FROM ?# WHERE userID=?d", 'forum_users', $this->AuthManager->User->userID);
            if ($this->AuthManager->User->danger_level >0 && $_passport_info['danger_level']>0) {
                $result_karma = $_passport_info['danger_level'] - 1;
                $result_balance = $_passport_info['user_balance'] + KARMA_UNIT_COST;
                $this->DbManager->query ("UPDATE ?# SET
											danger_level = danger_level-1
											,user_balance = user_balance+?d
											WHERE
												userID = ?d", 'forum_users', KARMA_UNIT_COST, $this->AuthManager->User->userID);

                $this->AuthManager->User->user_balance = $result_balance;
                $this->AuthManager->User->danger_level = $result_karma;
                $use_pass = 2;//не менять пароль
                $this->AuthManager->AuthDb->SaveUser ($this->AuthManager->CreateUserArray ($this->AuthManager->User),$use_pass);
                $ix = sprintf("forumAuth_FindUserById_%d", $this->AuthManager->User->userID);
                if (xcache_isset($ix)) {
                    $_user = unserialize(xcache_get($ix));
                    $_user['user_balance'] = $this->AuthManager->User->user_balance;
                    $_user['danger_level'] = $this->AuthManager->User->danger_level;
                    xcache_set($ix, serialize($_user), 1200);
                }
            } else {
                $this->sendErrors (array (
                    'karma' => 'Карма должны быть больше нуля'
                ));
            }

            $system_info = "\nPOST:".var_export($_POST,true)."\n";
            $system_info .= "\nGET:".var_export($_GET,true)."\n";
            $system_info .= "\nSERVER:".var_export($_SERVER,true)."\n";
            $system_info .= "\nSESSION:".var_export($_SESSION,true)."\n";

            $this->getForumModel()->billingLog(KARMA_UNIT_COST,'Операция продажи кармы на сумму '.KARMA_UNIT_COST.'р.'.$system_info);



            $result = array (
                'submitOn' => true,
                'callFunc' => 'SellKarma',
                'karma' => intval ($result_karma),
                'balance' => number_format($result_balance, 2, '.', '')
            );
            $this->sendJSON ($result);
        } else {
            $this->sendErrors (array (
                'karma' => 'Действие невозможно'
            ));

        }
    }

    private function hideTheme($themeID) {
        if ($this->AuthManager->User->userID > 0 && $this->AuthManager->User->user_balance >= HIDE_THEME_COST) {
            $this->DbManager->query ("UPDATE ?# SET
											user_balance = ?d
											WHERE
												userID = ?d", 'forum_users', ($this->AuthManager->User->user_balance - HIDE_THEME_COST), $this->AuthManager->User->userID);
            $this->AuthManager->User->user_balance = $this->AuthManager->User->user_balance - HIDE_THEME_COST;
            $ix = sprintf("forumAuth_FindUserById_%d", $this->AuthManager->User->userID);
            if (xcache_isset($ix)) {
                $_user = unserialize(xcache_get($ix));
                $_user['user_balance'] = $this->AuthManager->User->user_balance;
                xcache_set($ix, serialize($_user), 1200);
            }
            $this->getForumModel()->billingLog(HIDE_THEME_COST,'Операция скрытия темы '.$themeID.' на сумму '.HIDE_THEME_COST.'р.');

            $this->DbManager->query ("UPDATE ?# SET
											hidden = 1,
											is_locked = 1,
											hidden_time=now()
											WHERE
                                                themeID = ?d
											    and authorID = ?d", 'forum_db_themes',
                $themeID
                , $this->AuthManager->User->userID);
            $this->DbManager->query ("UPDATE ?# SET
											hidden = 1,
											hidden_time=now()
											WHERE
                                                themeID = ?d", 'forum_db_messages',
                $themeID);
            $this->DbManager->query ("UPDATE ?#
                                SET hidden = 1
                                WHERE themeID = ?d
                                AND hidden = 0", "forum_messages_attaches", $themeID);

            $this->getForumModel()->themeHideLog($themeID);

            $result = array (
                'submitOn' => true,
                'callFunc' => 'HideMyTheme',
                'balance' => number_format($this->AuthManager->User->user_balance, 2, '.', '')
            );
            $this->sendJSON ($result);
        } else {
            $this->sendErrors (array (
                'balance' => 'Стоимость данной услуги '.HIDE_THEME_COST.' руб.'
            ));

        }
    }


    public function GetUserFavorites($_userid)
    {
        $_favorites = $this->DbManager->select ("SELECT 
												t.`caption`, 
												t.`groupID`,
												f.`themeID`,
												t.`updated`,
												t.`messages`,
												t.`updated_by`, 
												t.`updated_by_id`, 
												t.`author`,
												t.`views`
											FROM 
												?# f 
												RIGHT JOIN ?# t ON (f.`themeID` = t.`themeID`) 
											WHERE 
												f.`userID`=?d 
												ORDER BY t.`updated` DESC", 'forum_db_favorites', 'forum_db_themes', $_userid);
        return $_favorites;
    }
    public function sendJSON($jsonText)
    {
        $this->getForumModel ()->sendJSON ($jsonText);
    }

    public function sendErrors($errors)
    {
        $this->getForumModel ()->sendErrors ($errors);
    }


}
