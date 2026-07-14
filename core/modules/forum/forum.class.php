<?php
class ForumModel
{
    private $DbManager = null;
    private $AuthManager = null;
    private $DocumentManager = null;
    public $CountPerPage = 0;
    private $permissionModel = null;
    var $spamMailList = array(
        'mailforspam.com',
        'critsend.com',
        '10minutemail.com',
        'mailinator.com',
        'trash-mail.com',
        'jetable.org',
        'trashmail.net',
        'yopmail.com',
        'sorbs.net',
        'spamenmoins.com',
        'bluebottle.com',
        'dkim.org',
        'mytrashmail.com',
        'anti-abuse.org',
        'spamcannibal.org',
        'backscatterer.org',
        'redcondor.com',
        'tempemail.net',
        'allaboutspam.com',
        'checkor.com',
        'nowmymail.com',
        'mxlogic.com',
        'spamavert.com',
        'postini.com',
        'spamarrest.com',
        'spamhaus.org',
        'boxbe.com',
        'senderbase.org',
        'returnpath.net',
        'blacklistalert.org',
        'dnsbl.info',
        'appriver.com',
        'spambog.com',
        'apews.org',
        'trustedsource.org',
        'addressmunger.com',
        'mailingcheck.com',
        'spamgourmet.com',
        'gishpuppy.com',
        '0spam.com',
        'pobox.com',
        'authsmtp.com',
        'spampoison.com',
        'trustsphere.com',
        'mailexpire.com',
        'spam-stop.com',
        'spamschlucker.org',
        'spamcop.com',
        'tempinbox.com',
        'send-safe.com',
        'tuffmail.com',
        'sharklasers.com',
        'astrowave.ru',
        'rmqkr.net',
        'drdrb.com',
        'spam4.me'

    );


    public function __construct($dbManager, $authManager)
    {
        $this->DbManager = $dbManager;
        $this->AuthManager = $authManager;
    }

    public function getPermissionModel()
    {
        if (! $this->permissionModel) {
            require_once (MODULE_DIR . 'forum/permissoin.class.php');
            $this->permissionModel = new Permission ($this->DbManager, $this->AuthManager);
        }
        return $this->permissionModel;
    }

    public function updateGroup($groupInfo)
    {
        $groupId = $groupInfo[ 'groupID' ];
        $currentGroup = $this->getGroupInfo ($groupId);
        unset ($groupInfo[ 'groupID' ]);
        $isGroupOwner = $this->getPermissionModel ()->isGroupOwner ($currentGroup, $this->AuthManager->User->userID);
        $isGroupAdministrator = $this->getPermissionModel ()->isGroupModerator ($groupId, $this->AuthManager->User->userID);
        if ($isGroupAdministrator || $isGroupOwner || $this->AuthManager->User->is_admin) {
            $this->DbManager->query ('UPDATE ?# SET ?a WHERE groupID=?d', 'forum_db_groups', $groupInfo, $groupId);
            $groupInfo[ 'groupID' ] = $groupId;
        }
        return $groupInfo;
    }

    public function LoadThemesByRating($totalRows, $page, $type)
    {
        $ix = sprintf("forumLoadThemesByRating_t%d_p%d_%s", $totalRows, $page, $type);

        if (xcache_isset($ix)) {
            return unserialize(xcache_get($ix));
        }

        $query = '-- CACHE: 2h 10m 0s
        		SELECT 
        			t.*, 
        			r.rating as themeRating 
    			FROM 
    				forum_db_themes_rating r 
				JOIN forum_db_themes t ON (t.themeID=r.themeId) 
        		WHERE t.hidden=0
        		 ';
        if ($type == 'good') {
            $query .= ' AND rating >=0 ORDER BY rating DESC';
        }
        if ($type == 'bad') {
            $query .= ' AND rating <0 ORDER BY rating ASC';
        }
        $query .= ' LIMIT ?d,?d ';
        $_return = $this->DbManager->selectPage ($totalRows, $query, $this->CountPerPage * $page, $this->CountPerPage);

        xcache_set($ix, serialize($_return), 3600);

        return $_return;
    }

    public function updateMessagesCountByIp($groupId)
    {
        $ip = $this->AuthManager->RemoteAddr;
        $forwarder = $this->AuthManager->Forwarder;

        if ('' != $forwarder) {
            $ip = $ip . ' (' . $forwarder . ')';
        }
        $groupInfo = $this->getGroupInfo ($groupId);
        if ($groupInfo[ 'is_mat' ] || $this->AuthManager->User->userID > 0) {
            return false;
        }
        $this->DbManager->query ('INSERT INTO ?# (?#,?#) VALUES(?,1) ON DUPLICATE KEY UPDATE ?#=?#+1', 'forum_db_anonymous_counter', 'ip', 'messages', $ip, 'messages', 'messages');
    }

    public function getMessagesCountByIp($groupId)
    {
        $groupInfo = $this->getGroupInfo ($groupId);
        if ($groupInfo[ 'is_mat' ] || $this->AuthManager->User->userID > 0) {
            return 0;
        }
        $ip = $this->AuthManager->RemoteAddr;
        $forwarder = $this->AuthManager->Forwarder;

        if ('' != $forwarder) {
            $ip = $ip . ' (' . $forwarder . ')';
        }
        return $this->DbManager->selectcell ('SELECT messages FROM ?# WHERE ip=?', 'forum_db_anonymous_counter', $ip);
    }

    public function LoadThemesByHits(&$TotalRows,$period,$page, $limit)
    {
        $query = "-- CACHE: 0h 10m 0s
			SELECT
				t.*,
				h.hitsWeek as views,
                if(u.danger_level>0,concat('+',u.danger_level),u.danger_level) as karma
			FROM ?# t
			    LEFT JOIN ?# h ON t.themeID=h.themeID
			    LEFT JOIN ?# u ON t.authorID = u.userID 
			WHERE 
				 t.`hidden` = 0  
			ORDER BY 
				";
        switch ($period) {
            case '7' :
                $query .= ' h.hitsWeek DESC';
                break;
            case '1' :
                $query .= ' h.hitsToday DESC';
                break;
            case '30' :
                $query .= ' h.hitsMonth DESC';
                break;
            default :
                $query .= ' h.hitsToday DESC';
                break;
        }
        $query .= ", t.updated DESC LIMIT ?d,?d ";
        $_result = $this->DbManager->selectPage ($TotalRows,$query, "forum_db_themes", 'forum_db_themes_views_stat', "forum_users", (($page-1)*$limit),$limit);
        return $_result;
    }

    public function themeAddHit($themeId)
    {
	//Убрал чтобы муське дышалось легче, связано с http://forum.site/forum/week/ (ТОП ПРОСМОТРОВ ЗА НЕДЕЛЮ)
	//на морде нет!
        //$query = 'INSERT INTO ?# SET themeID=?d, userID=?d,created=NOW()';
        //$this->DbManager->query ($query, 'forum_db_themes_views', $themeId, $this->AuthManager->User->userID);
	return;
    }

    public function getFilesByMessagesIds($ids)
    {
        if (!is_array($ids)) {
        	$ix = sprintf("forumGetFilesByMessId_%d", $ids);
        	if (xcache_isset($ix)) {
        	    return unserialize(xcache_get($ix));
        	}
        }
	
        $files = $this->DbManager->select ('SELECT * FROM ?# WHERE `messageID` IN (?d) AND hidden=0', 'forum_messages_attaches', $ids);
        $result = array ();
        if (! $files) {
            return null;
        }
        foreach ($files as $key => $val) {
            $result[ ] = $val;
        }
        
        if (!is_array($ids)) {
            xcache_set($ix, serialize($result), 3600);
        }
        
        return $result;
    }

    public function uploadFiles($themeId, $messageId)
    {
    
	    include (LIB_DIR . 'PHPThumb/ThumbLib.inc.php');
        $i = 0;
        foreach ($_FILES as $key => $val) {
            if ($i < MAX_FILES_UPLOAD) {
                if (is_file($val[ 'tmp_name' ]) && file_exists($val[ 'tmp_name' ])) {
                    if (! is_dir (HOME_DIR . 'attaches/' . $themeId)) {
                        mkdir (HOME_DIR . 'attaches/' . $themeId);
                    }
                    if (! is_dir (HOME_DIR . 'attaches/' . $themeId . '/' . $messageId)) {
                        mkdir (HOME_DIR . 'attaches/' . $themeId . '/' . $messageId);
                    }
                }
                $imgId = microtime (1);
                $imgId = str_replace (',', '', $imgId);
                $imgId = str_replace ('.', '', $imgId);
                $imgId = str_replace (' ', '', $imgId);
                $ext = strtolower (array_pop (explode ('.', $val[ 'name' ])));
                $filename = $imgId . '.' . $ext;
                $newFile = HOME_DIR . 'attaches/' . $themeId . '/' . $messageId . '/' . $filename;
                
                if (copy ($val[ 'tmp_name' ], $newFile)) {
                    if ($ext!='gif') {
                        $thumb = PhpThumbFactory::create ($newFile);
                        $thumb->resize (ORIG_WIDTH, ORIG_HEIGHT);
                        $thumb->save ($newFile);
                        unset ($thumb);
                    //} else {
		//	@move_uploaded_file($val[ 'tmp_name' ], $newFile);
		    }
                    $thumb = PhpThumbFactory::create ($newFile);
                    $thumb->adaptiveResize (THUMB_WIDTH, THUMB_HEIGHT);
                    $thumb->save (HOME_DIR . 'attaches/' . $themeId . '/' . $messageId . '/thumb_' . $filename);
                    $this->DbManager->query ('INSERT INTO ?# SET id=NULL, themeID=?d,messageID=?d,filename=?,ext=?,size=?', 'forum_messages_attaches', $themeId, $messageId, $filename, '', filesize ($newFile));
                    unset ($thumb);
                }
            }
            $i ++;
        }

        $ix = sprintf("forumGetFilesByMessId_%d", $messageId);
        xcache_unset($ix);    
    }

    public function sendJSON($jsonText)
    {
        // если в буфере есть данные
        if (ob_get_contents ()) {
            // то эти данные уже не понадобятся
            ob_clean ();
        }
        // отправляем JSON и завершаемся
        die (makeJSON ($jsonText));
    }

    public function sendErrors($errors)
    {
        $response = array (
            'errors' => $errors, 
            'submitOn' => false
        );
        
        $this->sendJSON ($response);
    }

    public function validateFiles()
    {
        if (! $_FILES) {
            return true;
        }
        $i = MAX_FILES_UPLOAD;
        foreach ($_FILES as $key => $val) {
            if ($i == 0) {
                return false;
            }
            $i --;
            if ($val[ 'tmp_name' ] != '') {
                if (! (in_array (strtolower (array_pop (explode ('.', $val[ 'name' ]))), array (
                    'jpg', 
                    'png', 
                    'gif', 
                    'jpeg', 
                    'tiff'
                )) && filesize ($val[ 'tmp_name' ]) < FILESIZE_MAX)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getGroupInfo($groupID)
    {
        $ix = sprintf("forumGetGroupInfo_%d", $groupID);
        if (xcache_isset($ix)) {
            return unserialize(xcache_get($ix));
        }

        $_return = $this->DbManager->selectrow ("-- CACHE: 1h 0m 0s
            SELECT
                *
            FROM
                ?#
            WHERE
                `groupID`= ?d", "forum_db_groups", $groupID);

        xcache_set($ix, serialize($_return), 3600);

        return $_return;
    }

    public function getHotThemesCount()
    {
        $ix = 'forumGetHotThemesCount';
        if (xcache_isset($ix)) {
            return unserialize($ix);
        }

        $_return = $this->DbManager->selectcell ('-- CACHE: 0h 10m 0s
                SELECT
                    COUNT(*)
                FROM
                    ?#
                WHERE
                    hottop=1',
            "forum_db_themes"
        );

        xcache_set($ix, serialize($_return), 600);
        return $_return;
    }

    function cookie_parse($line)
    {
        $cookies = array ();
        $csplit = explode (';', $line);
        
        $cdata = array ();
        foreach ($csplit as $data) {
            $cinfo = explode ('=', $data);
            $cinfo[ 0 ] = trim ($cinfo[ 0 ]);
            if ($cinfo[ 0 ] == 'expires')
                $cinfo[ 1 ] = strtotime ($cinfo[ 1 ]);
            if ($cinfo[ 0 ] == 'secure')
                $cinfo[ 1 ] = "true";
            if (in_array ($cinfo[ 0 ], array (
                'domain', 
                'expires', 
                'path', 
                'secure', 
                'comment'
            ))) {
                $cdata[ trim ($cinfo[ 0 ]) ] = $cinfo[ 1 ];
            } else {
                $cdata[ 'value' ][ 'key' ] = $cinfo[ 0 ];
                $cdata[ 'value' ][ 'value' ] = $cinfo[ 1 ];
            }
            $cookies[ $cinfo[ 0 ] ] = $cinfo[ 1 ];
        }
        
        return $cookies;
    }

    public function SetMessageRedZone($_id)
    {
        $this->DbManager->query ("UPDATE `forum_db_messages` SET `danger_level` = ifnull(danger_level,0)+1 WHERE `messageID`=" . mysql_escape_string (intval ($_id)));
        // не путать danger_level у пользователя (карма) и danger_level у сообщения - жалобы
        return true;
    }

    public function SetThemeRedZone($_id)
    {
        $this->DbManager->query ("UPDATE `forum_db_themes` SET `danger_level` = `danger_level`+1 WHERE `themeID`=" . mysql_escape_string (intval ($_id)));
    }

    /** сбор информации о пользователе
     * ип пользователя не забанен
     * пользователь пришел со страницы форума
     * пользователь пишет с браузера
     * содержание сообщения не противоречит правилам
     */
    public function GetAuthorInfo($name)
    {
        $_result = array ();
        
        if (is_object ($this->AuthManager->User)) {
            $_user[ 'userID' ] = $this->AuthManager->User->userID;
            $_user[ 'user_name' ] = $this->AuthManager->User->user_name;
        } else {
            $_user[ 'userID' ] = $this->AuthManager->User[ 'userID' ];
            $_user[ 'user_name' ] = $this->AuthManager->User[ 'user_name' ];
        }
        $_result[ 'realname' ] = $_user[ 'user_name' ];

        //1. разобраться с Анонимно! Сохранять в куку сгенерированный ник
        //2. не работает проверка на старость тем, проверить
        //3. робокасса?
        //4. карма?

        $_result[ 'author' ] = ($name != '') ? $name : (($_user[ 'userID' ] > 0) ? $_user[ 'user_name' ] : $_result[ 'realname' ]);
        if (!empty($_result[ 'author' ])) {
            $_COOKIE['_cookie_name'] = $_result[ 'author' ];
            setcookie ('_cookie_name', $_result[ 'author' ], 3600, '/', '.' . SERVER_NAME, 0, 0);
        }
        $_result[ 'authorID' ] = $_user[ 'userID' ];
        $_result[ 'author_ip' ] = $this->AuthManager->RemoteAddr;

        $_result[ 'author_ip_grey' ] = $this->AuthManager->Forwarder;
        $_result[ 'referer' ] = $_SERVER[ 'HTTP_REFERER' ];
        $_result[ 'useragent' ] = $_SERVER[ 'HTTP_USER_AGENT' ];
        $_result[ 'fz152_agreement' ] = $this->AuthManager->User->fz152_agreement;
        $_result[ 'ban' ] = $this->DbManager->selectcell ("-- CACHE: 1h 0m 0s
                                                                SELECT
                                                                    `ban_type`
                                                                FROM
                                                                    ?#
                                                                WHERE
                                                                    INET_ATON(?) BETWEEN `init_ip` AND `end_ip`
                                                                ", 'forum_networks_banned', $_result[ 'author_ip' ]);
        $_result_ban2  = 0;

        if (!empty($_result[ 'author_ip_grey' ])) {
            $_result_ban2 = $this->DbManager->selectcell ("-- CACHE: 1h 0m 0s
                                                                    SELECT
                                                                        `ban_type`
                                                                    FROM
                                                                        ?#
                                                                    WHERE
                                                                        INET_ATON(?) BETWEEN `init_ip` AND `end_ip`
                                                                    ", 'forum_networks_banned', $_result[ 'author_ip_grey' ]);
        }
        if ($_result_ban2>0 && !empty($_result[ 'ban' ])) {
            $_result[ 'ban' ] = $_result_ban2;
        }
        $valid = 0;
        $allowRefererList = $this->AuthManager->allowRefererList;
        for ($i = 0; $i < count ($allowRefererList); $i ++) {
            if (strstr ($_result[ 'referer' ], $allowRefererList[ $i ]) && ! empty ($_result[ 'referer' ])) {
                $valid = 1;
                break;
            }
        }
        if ($valid != 1) {
            return false;
        }
        return $_result;
    }
    public function preparePermissionSql()
    {
        $permission_sql = '';
        if ($this->AuthManager->User->userID >0) {
            $privateSettings = $this->getPermissionModel ()->getPersonalBlackListSettings ($this->AuthManager->User->userID);
            if ($privateSettings[ 'hideAnonymous' ]) {
                $permission_sql .= ' AND m.authorID >0 ';
            }
            if ($privateSettings[ 'hideUsers' ]) {
                $blackUsers = $this->getPermissionModel ()->getUsersIdsInPersonalBlackList ($this->AuthManager->User->userID);
                if (! empty ($blackUsers)) {
                    $permission_sql .= ' AND m.authorID NOT IN (' . join (',', $blackUsers) . ') ';
                }
            }
        }
        return $permission_sql;
    }

    public function LoadMessageById($messageId)
    {
        $permission_sql = $this->preparePermissionSql();

        /*
         * $ix = sprintf("forumLoadMessageById_m%d_%s", $messageId, md5($permission_sql));
    
        if (!xcache_isset($ix)) {
            */
            $_result = $this->DbManager->selectrow ("-- CACHE: 6h 0m 0s, forum_db_messages.created
                SELECT
                    m.*,
                    (NOW()-m.created) as editInterval
                FROM
                    ?# m
                WHERE
                    m.`messageID`= ?d
                    ".$permission_sql."
                    AND m.`hidden`= 0
                    AND m.`is_deleted` = 0
                ", "forum_db_messages", $messageId);
        /*
            xcache_set($ix, serialize($_result), 600);
        } else {
            $_result = unserialize(xcache_get($ix));
        }
        */
        
        $author = preg_quote ($this->AuthManager->User->user_name);
        $ids = array ();
        foreach ($_result as $k => $v) {
            $ids[ ] = $v[ 'messageID' ];
            if (preg_match ('/' . $author . ' \([0-9]{2}.[0-9]{2}.[0-9]{4} \([0-9]{2}:[0-9]{2}\)\)/is', $v[ 'caption' ])) {
                $v[ 'forme' ] = 1;
            }
            $_result[ $k ] = $v;
        }
        $files = $this->getFilesByMessagesIds ($ids);
        foreach ($_result as $key => $val) {
            if ($files[ $val[ 'messageID' ] ]) {
                $val[ 'files' ] = $files[ $val[ 'messageID' ] ];
            }
            $_result[ $key ] = $val;
        }
        require_once LIB_DIR . 'system/parser/plugins/modifier.nl2br.php';
        require_once LIB_DIR . 'system/parser/plugins/modifier.bbcode.php';
        require_once LIB_DIR . 'system/parser/plugins/modifier.smiles.php';
        $_result[ 'formattedcontent' ] = smarty_modifier_smiles (smarty_modifier_nl2br (smarty_modifier_bbcode ($_result[ 'content' ])));
        return $_result;
    }

    // ajax-загрузка сообщений в теме
    public function LoadMessagesFromId($themeID, $messageID,$page=1)
    {
        $permission_sql = $this->preparePermissionSql();
        /*
        $ix = sprintf("forumLoadMessageFromId_t%d_m%d_p%d_%s", $themeID, $messageID, $page, md5($permission_sql));
        if (!xcache_isset($ix)) {
            */
            $_result = $this->DbManager->select ("-- CACHE: 1h 0m 0s, forum_db_messages.created
            SELECT
                m.*,
                INET_NTOA( m.author_ip ) as author_ip,
                m.author_ip_grey as author_ip_grey,
                if(u.danger_level>0,concat('+',u.danger_level),u.danger_level) as karma,
                (NOW()-m.created) as editInterval
            FROM
                ?# m
            LEFT JOIN ?# u ON m.authorID = u.userID
            WHERE
                m.`themeID`= ?d
                AND m.`hidden`= 0
                ".$permission_sql."
                AND m.`is_deleted` = 0
                AND m.messageID > ?d
            ORDER BY
                m.`messageID` DESC
            LIMIT ?d, ?d",
                "forum_db_messages",
                "forum_users",
                $themeID,
                $messageID,(($page - 1) * $this->CountPerPage),
                $this->CountPerPage);

            $author = preg_quote ($this->AuthManager->User->user_name);
            $ids = array ();
            foreach ($_result as $k => $v) {
                $ids[ ] = $v[ 'messageID' ];
                if (preg_match ('/' . $author . ' \([0-9]{2}.[0-9]{2}.[0-9]{4} \([0-9]{2}:[0-9]{2}\)\)/is', $v[ 'caption' ])) {
                    $v[ 'forme' ] = 1;
                }
                $_result[ $k ] = $v;
            }
            $files = array ();
            foreach ($_result as $key => $val) {
                $val[ 'hav_file' ] = 0;
                $path = MOUNT_DIR.'/attaches/'.$val[ 'themeID' ].'/'.$val[ 'messageID' ].'/';
                $massiv = glob($path."*.*"); //Соберет в массив все файлы
                $val[ 'hav_file' ] = count($massiv); //Вернет число файлов
                if ($val[ 'hav_file' ] > 0) {
                    $files = $this->getFilesByMessagesIds ($val[ 'messageID' ]);
                    $val[ 'files' ] = $files;
                    $val[ 'first_file' ] = $files[ 0 ];
                    $val[ 'count_files' ] = count($files);
                } else {
                    unset ($val[ 'files' ]);
                    unset ($val[ 'first_file' ]);
                    unset ($val[ 'count_files' ]);
                }
                $_result[ $key ] = $val;
            }
        /*
            xcache_set($ix, serialize($_result), 600);
        } else {
            $_result = unserialize(xcache_get($ix));
        }
        */
        //Redis optimize BEGIN
        if ($_SERVER['SERVER_NAME']=='forum.example.com') {
            $upd = $this->DbManager->query ("
                            UPDATE ?#
                            SET views = views + 1
                                WHERE themeID=?d", 'forum_db_themes', $themeID);
        } else {
            $redis = new Redis();
            $redis->pconnect('127.0.0.1');
            $key = sprintf("theme_view:%d", $themeID);
            $redis->incr($key);

        }
        //Redis optimize END
        return $_result;
    }
    // update сообщений в теме в кэш
    public function LoadMessagesFromIdToCache($themeID,$messageID)
    {
        $permission_sql = $this->preparePermissionSql();
        $ix = sprintf("forumLoadMessageFromId_t%d_m%d_p%d_%s", $themeID, $messageID, 1, md5($permission_sql));
        $ix_tmp = sprintf("forumLoadMessages_t%d_p%d_%s", $themeID,1,md5($permission_sql));

            $_result = $this->DbManager->select ("-- CACHE: 1h 0m 0s, forum_db_messages.created
            SELECT
                m.*,
                INET_NTOA( m.author_ip ) as author_ip,
                m.author_ip_grey as author_ip_grey,
                if(u.danger_level>0,concat('+',u.danger_level),u.danger_level) as karma,
                (NOW()-m.created) as editInterval
            FROM
                ?# m
            LEFT JOIN ?# u ON m.authorID = u.userID
            WHERE
                m.`themeID`= ?d
                AND m.`hidden`= 0
                ".$permission_sql."
                AND m.`is_deleted` = 0
                AND m.messageID > ?d
            ORDER BY
                m.`messageID` DESC
            LIMIT ?d, ?d",
                "forum_db_messages",
                "forum_users",
                $themeID,
                $messageID,
                0,
                $this->CountPerPage);

            $author = preg_quote ($this->AuthManager->User->user_name);
            $ids = array ();
            foreach ($_result as $k => $v) {
                $ids[ ] = $v[ 'messageID' ];
                if (preg_match ('/' . $author . ' \([0-9]{2}.[0-9]{2}.[0-9]{4} \([0-9]{2}:[0-9]{2}\)\)/is', $v[ 'caption' ])) {
                    $v[ 'forme' ] = 1;
                }
                $_result[ $k ] = $v;
            }
            $files = array ();
            foreach ($_result as $key => $val) {
                $val[ 'hav_file' ] = 0;
                $path = MOUNT_DIR.'/attaches/'.$val[ 'themeID' ].'/'.$val[ 'messageID' ].'/';
                $massiv = glob($path."*.*"); //Соберет в массив все файлы
                $val[ 'hav_file' ] = count($massiv); //Вернет число файлов
                if ($val[ 'hav_file' ] > 0) {
                    $files = $this->getFilesByMessagesIds ($val[ 'messageID' ]);
                    $val[ 'files' ] = $files;
                    $val[ 'first_file' ] = $files[ 0 ];
                    $val[ 'count_files' ] = count($files);
                } else {
                    unset ($val[ 'files' ]);
                    unset ($val[ 'first_file' ]);
                    unset ($val[ 'count_files' ]);
                }
                $_result[ $key ] = $val;
            }
            xcache_unset($ix);
            xcache_unset($ix_tmp);
            xcache_set($ix, serialize($_result), 600);
            xcache_set($ix_tmp, serialize($_result), 600);

        return $_result;
    }

    //---------------загрузка сообщений в теме
    public function LoadMessages(& $TotalRows, $_theme_id, $page = 1, $timestamp = null,$isModerator = false)
    {


        $permission_sql = $this->preparePermissionSql();
        /*
        $ix = sprintf("forumLoadMessages_t%d_p%d_%s", $_theme_id,$page,md5($permission_sql));

        if (!xcache_isset($ix.'_count')) {
            */
            $TotalRows = $this->DbManager->selectCell ("-- CACHE: 1h 0m 0s, forum_db_messages.created
            SELECT
				count(m.messageID)
			FROM
				?# m
			WHERE
				m.themeID= ?d
				{ AND m.created >= ?s }
				AND m.hidden= 0
				".$permission_sql."
				AND m.is_deleted = 0", "forum_db_messages", $_theme_id, (isset ($timestamp) ? $timestamp : DBSIMPLE_SKIP));
            $_result = $this->DbManager->select ("SELECT
				m.*,
				INET_NTOA( m.author_ip ) as author_ip,
				m.author_ip_grey as author_ip_grey,
			    if(u.danger_level>0,concat('+',u.danger_level),u.danger_level) as karma,
                (NOW()-m.created) as editInterval
			FROM
				?# m
            LEFT JOIN ?# u ON m.authorID = u.userID
			WHERE
				m.themeID= ?d
				{ AND m.created >= ?s }
				AND m.hidden= 0
				".$permission_sql."
				AND m.is_deleted = 0
			ORDER BY
				m.`messageID` DESC
            LIMIT ?d, ?d",
                "forum_db_messages",
                "forum_users",
                $_theme_id,
                (isset ($timestamp) ? $timestamp : DBSIMPLE_SKIP), (($page - 1) * $this->CountPerPage),
                $this->CountPerPage);

            $author = preg_quote ($this->AuthManager->User->user_name);
            $ids = array ();
            foreach ($_result as $k => $v) {
                $ids[ ] = $v[ 'messageID' ];
                if (preg_match ('/' . $author . ' \([0-9]{2}.[0-9]{2}.[0-9]{4} \([0-9]{2}:[0-9]{2}\)\)/is', $v[ 'caption' ])) {
                    $v[ 'forme' ] = 1;
                }
                $_result[ $k ] = $v;
            }
            $files = array ();
            foreach ($_result as $key => $val) {
                $val[ 'shortline_content' ] = $val['content'];
                $val[ 'hav_hide' ] = false;
                $strLen = mb_strlen($val['shortline_content'],"UTF-8");
                $lineItems = explode("\n",$val['shortline_content']);
                $lineCount = count($lineItems);
                $val[ 'strlen' ] = $strLen;
                $val[ 'linecount' ] = $lineCount;
                if ($strLen > STRLEN_HIDE || $lineCount > LINE_HIDE){
                    $val[ 'hav_hide' ] = true;
                    if (preg_match("/\[re\](.*?)\[\/re\]/is",$val[ 'shortline_content' ],$m)) {
                        $blocks = explode('[/re]',$val[ 'shortline_content' ]);
                        for ($i=0;$i<count($blocks);$i++) {
                            $blocks[$i] = str_replace('[re]','',$blocks[$i]);
                        }
                        $last_block = $blocks[count($blocks)-1];
                        $prev_block = $blocks[count($blocks)-2];
                        $strLenRe = mb_strlen($last_block,"UTF-8");
                        $lineItemsRe = explode("\n",$last_block);
                        $lineCountRe = count($lineItemsRe);
                        $val[ 'strlen2' ] = $strLenRe;
                        $val[ 'linecount2' ] = $lineCountRe;
                        $val[ 'lastblock' ] = $last_block;
                        $val[ 'prevblock' ] = $prev_block;
                        if ($strLenRe > STRLEN_HIDE){
                            $val[ 'hav_re' ] = true;
                            $val[ 'shortline_content' ] = '[re]'.$prev_block.'[/re]'.mb_substr($last_block, 0, STRLEN_HIDE,"UTF-8") . '...';
                        } else {
                            if ($lineCountRe > LINE_HIDE){
                                $val[ 'hav_re' ] = true;
                                $last_lines = '';
                                for ($i=0;$i<10;$i++){
                                    $last_lines .= $lineItemsRe[$i];
                                }
                                $last_lines .= '...';
                                $val[ 'shortline_content' ] = '[re]'.$prev_block.'[/re]'.$last_lines;
                            } else {
                                $val[ 'shortline_content' ] = '[re]'.$prev_block.'[/re]'.$last_block;
                            }
                        }
                    } else {
                        $val[ 'hav_re' ] = '';
                        if ($strLen > STRLEN_HIDE){
                            $val['shortline_content'] = mb_substr($val['shortline_content'], 0, STRLEN_HIDE,"UTF-8") . '...';
                        } else {
                            //$lineCount > LINE_HIDE
                            $val[ 'shortline_content' ] = '';
                            for ($i=0;$i<10;$i++){
                                $val[ 'shortline_content' ] .= $lineItems[$i];
                            }
                            $val[ 'shortline_content' ] .= '...';
                        }
                    }
                } else {
                    $val[ 'hav_hide' ] = false;
                    $val[ 'hav_re' ] = false;
                    $val[ 'shortline_content' ] = $val['content'];
                }

                $val[ 'hav_file' ] = 0;
                $path = MOUNT_DIR.'/attaches/'.$val[ 'themeID' ].'/'.$val[ 'messageID' ].'/';
                $massiv = glob($path."*.*"); //Соберет в массив все файлы
                $val[ 'hav_file' ] = count($massiv); //Вернет число файлов
                if ($val[ 'hav_file' ] > 0) {
                    $files = $this->getFilesByMessagesIds ($val[ 'messageID' ]);
                    $val[ 'files' ] = $files;
                    $val[ 'first_file' ] = $files[ 0 ];
                    $val[ 'count_files' ] = count($files);
                } else {
                    unset ($val[ 'files' ]);
                    unset ($val[ 'first_file' ]);
                    unset ($val[ 'count_files' ]);
                }
                $_result[ $key ] = $val;
            }
        /*
            xcache_set($ix, serialize($_result), 600);
            xcache_set($ix.'_count', serialize($TotalRows), 600);
        } else {
            $TotalRows = unserialize(xcache_get($ix.'_count'));
            $_result = unserialize(xcache_get($ix));
        }
        */
        //Redis optimize BEGIN
        if ($_SERVER['SERVER_NAME']=='forum.example.com') {
            $upd = $this->DbManager->query ("
                            UPDATE ?#
                            SET views = views + 1
                                WHERE themeID=?d", 'forum_db_themes', $_theme_id);
        } else {
            $redis = new Redis();
            $redis->pconnect('127.0.0.1');
            $key = sprintf("theme_view:%d", $_theme_id);
            $redis->incr($key);

        }
        //Redis optimize END

        return $_result;
    }

    public function LoadHotRead(& $TotalRows, $page = 1)
    {
        $_result = $this->DbManager->select ("-- CACHE: 0h 0m 20s
            SELECT
                t.*,
                ifnull(t.messages,0) as messages,
                if (ifnull(t.views,0)<ifnull(t.messages,0),ifnull(t.messages,0),ifnull(t.views,0)) as views,
                if(u.danger_level>0,concat('+',u.danger_level),u.danger_level) as karma
            FROM
                ?# t
            LEFT JOIN ?# u ON t.authorID = u.userID
            WHERE
                1=1
                AND `hidden` = 0
            ORDER BY
                if (ifnull(t.views,0)<ifnull(t.messages,0),ifnull(t.messages,0),ifnull(t.views,0)) DESC
            LIMIT ?d", "forum_db_themes", "forum_users", 50);
        //
        return $_result;
    }

    //---------------загрузка ТОП общения
    public function LoadTop(& $TotalRows, $page,$limit)
    {
        $ix = sprintf("forumLoadTop_p%d_l%d", $page, $limit);

        if (xcache_isset($ix.'_count')) {
            $TotalRows = unserialize(xcache_get($ix.'_count'));
            $_result = unserialize(xcache_get($ix));
        } else {
            //--if (ifnull(t.views,0)<ifnull(t.messages,0),ifnull(t.messages,0),ifnull(t.views,0)) as views,
            $_result = $this->DbManager->selectPage ($TotalRows,"-- CACHE: 0h 10m 0s
            SELECT
                t.*,
                0 as views,
                if(u.danger_level>0,concat('+',u.danger_level),u.danger_level) as karma,
                g.caption as group_title
            FROM
                ?# t
            LEFT JOIN ?# u ON t.authorID = u.userID
            LEFT JOIN ?# g ON t.groupID = g.groupID
            WHERE
                1=1
                AND t.hidden = 0
            ORDER BY
                t.messages DESC
            LIMIT ?d, ?d"
                , "forum_db_themes"
                , "forum_users"
                , "forum_db_groups"
                , ($page-1)*$limit,$limit);
            $TotalRows = count($_result);
            xcache_set($ix, serialize($_result), 600);
            xcache_set($ix.'_count', serialize($TotalRows), 600);
        }
        //Redis optimize BEGIN
        if ($_SERVER['SERVER_NAME']!='forum.example.com') {
            $redis = new Redis();
            $redis->pconnect('127.0.0.1');
            foreach ($_result as &$v) {
                $v['views'] = $redis->get(sprintf("theme_view:%d", $v['themeID']));
            }
        }
        //Redis optimize END

        return $_result;
    }

    /** 
     * загрузка ТОП 50 горячих тем
     * @param unknown_type $TotalRows
     * @param unknown_type $page
     * @param unknown_type $count
     * @param unknown_type $igroups
     */
    public function LoadHot(& $TotalRows, $page = 1, $count = 70, $igroups = null)
    {
        static $_commerce = array (
            5,
            27,
            38,
            44,
            49,
            50,
            55,
            56,
            57,
            64,
            65
        );
        $ix = sprintf("forumLoadHot_%s", md5(sprintf("p%d_c%d_g%s", $page, $count, implode(':', $igroups))));
/*
        if (xcache_isset($ix) and xcache_isset($ix.'_count')) {
            $_result = unserialize(xcache_get($ix));
            $TotalRows = unserialize(xcache_get($ix.'_count'));
        } else {
*/
            if ($page<2) {
                $_result1 = $this->DbManager->select ("-- CACHE: 1h 0m 00s
                        SELECT
                            t.*,
                            if(u.danger_level>0,concat('+',u.danger_level),u.danger_level) as karma,
                            g.caption as group_title
                        FROM
                            ?# t
                            LEFT JOIN ?# u ON t.authorID = u.userID
                            LEFT JOIN ?# g ON t.groupID = g.groupID
                        WHERE
                            ifnull(t.top_end,NOW()) >= NOW()
                            and t.hottop = 1
                            AND t.hidden = 0
                        ORDER BY
                            t.hottop DESC,
                            t.updated DESC,
                            t.enddate DESC
                        LIMIT 0, ?d
                    "
                    , "forum_db_themes"
                    , "forum_users"
                    , "forum_db_groups"
                    , STIKER_HOTTOP_THEMES
                );
                $TotalRows1 = count($_result1);
            } else {
                $result1 = array();
                $TotalRows1 = 0;
            }

            $TotalRows2 = $this->DbManager->selectCell ("-- CACHE: 0h 5m 00s
                        SELECT
                            count(t.themeID)
                        FROM
                            ?# t
                        WHERE
                            `updated` >= NOW() - INTERVAL 1 month
                            AND `hidden` = 0
                            and hottop = 0
                            AND `groupID` NOT IN(?a)
                    "
                , "forum_db_themes"
                , (is_array ($igroups) ? $igroups : $_commerce)
            );

            $_result2 = $this->DbManager->select ("-- CACHE: 0h 5m 00s
                    SELECT
                        t.*,
                        if(u.danger_level>0,concat('+',u.danger_level),u.danger_level) as karma,
                        g.caption as group_title
                    FROM
                        ?# t
                    LEFT JOIN ?# u ON t.authorID = u.userID
                    LEFT JOIN ?# g ON t.groupID = g.groupID
                    WHERE
                        t.updated >= NOW() - INTERVAL 1 month
                        AND t.hidden = 0
                        and t.hottop = 0
                        AND t.groupID NOT IN(?a)
                    ORDER BY
                        t.updated DESC
                    LIMIT ?d, ?d
                    "
                , "forum_db_themes"
                , "forum_users"
                , "forum_db_groups"
                , (is_array ($igroups) ? $igroups : $_commerce)
                , ($page - 1) * $count
                , $count
            );
            //$TotalRows2 = count($_result2);
            if (count($_result1)>0 && count($_result2)>0) {
                $_result = array_merge($_result1,$_result2);
            } elseif(count($_result1)==0 && count($_result2)>0) {
                $_result = $_result2;
            } else {
                $_result = $_result1;
            }
            $TotalRows = $TotalRows1 + $TotalRows2;
/*
            xcache_set($ix, serialize($_result), 300);
            xcache_set($ix.'_count', serialize($TotalRows), 300);
        }
*/

	//Redis optimize BEGIN
        if ($_SERVER['SERVER_NAME']!='forum.example.com') {
            $redis = new Redis();
            $redis->pconnect('127.0.0.1');
            foreach ($_result as &$v) {
                $v['views'] = $redis->get(sprintf("theme_view:%d", $v['themeID']));
            }
        }
	//Redis optimize END

//@file_put_contents('/tmp/werdtest.log', serialize($_result));
        return $_result;
    }

    /**
     * загрузка груп сообщений
     */
    public function LoadGroups()
    {
        if (xcache_isset('forumLoadGroups')) {
            $_result = unserialize(xcache_get('forumLoadGroups'));
        } else {
            $_result = $this->DbManager->select ("-- CACHE: 1h 0m 0s
											SELECT
												*
											FROM
											?#
											ORDER BY
												`caption` ASC
											", "forum_db_groups");
            xcache_set('forumLoadGroups', serialize($_result), 3600);
        }
        return $_result;
    }

    /**
     * 
     * загрузка тем в группе
     * @param unknown_type $TotalRows
     * @param unknown_type $group_id
     * @param unknown_type $page
     */
    public function LoadThemes(& $TotalRows, $group_id, $page = 1,$isModerator=false)
    {
        $ix = sprintf("forumLoadThemes_p%d_g%d", $page, $group_id);
        /*
        if (xcache_isset($ix.'_count')) {
            $TotalRows = unserialize(xcache_get($ix.'_count'));
            $_result = unserialize(xcache_get($ix));
        } else {
        */
            $query = "-- CACHE: 0h 10m 0s
			SELECT
				t.*,
			    if(u.danger_level>0,concat('+',u.danger_level),u.danger_level) as karma
			FROM
				?# t
            LEFT JOIN ?# u ON t.authorID = u.userID
			WHERE
				`groupID`=?d
				AND `hidden`=0
			ORDER BY
				is_top DESC,
				ifnull(if(is_top=1,top_end,'0000-00-00 00:00:00'),'0000-00-00 00:00:00') DESC,
				fact_date DESC
			LIMIT ?d, ?d";
            $_result = $this->DbManager->select ($query, "forum_db_themes", "forum_users", $group_id, (($page - 1) * $this->CountPerPage), $this->CountPerPage);
        /*
            xcache_set($ix, serialize($_result), 600);
            xcache_set($ix.'_count', serialize($TotalRows), 600);
        }
        */



	//Redis optimize BEGIN
        if ($_SERVER['SERVER_NAME']!='forum.example.com') {
            $redis = new Redis();
            $redis->pconnect('127.0.0.1');
            foreach ($_result as &$v) {
                $v['views'] = $redis->get(sprintf("theme_view:%d", $v['themeID']));
            }
        }
	//Redis optimize END

        
        return $_result;
    }

    public function updateGroupThemes(& $TotalRows, $group_id, $page = 1,$isModerator=false)
    {
        $_result = array();
        /*
        $ix = sprintf("forumLoadThemes_p%d_g%d", $page, $group_id);
        $query = "-- CACHE: 0h 10m 0s
        SELECT
            t.*,
            if(u.danger_level>0,concat('+',u.danger_level),u.danger_level) as karma
        FROM
            ?# t
        LEFT JOIN ?# u ON t.authorID = u.userID
        WHERE
            `groupID`=?d
            AND `hidden`=0
        ORDER BY
            is_top DESC,
            ifnull(if(is_top=1,top_end,'0000-00-00 00:00:00'),'0000-00-00 00:00:00') DESC,
            fact_date DESC
        LIMIT ?d, ?d";
        $_result = $this->DbManager->select ($query, "forum_db_themes", "forum_users", $group_id, (($page - 1) * $this->CountPerPage), $this->CountPerPage);
        xcache_set($ix, serialize($_result), 600);
        xcache_set($ix.'_count', serialize($TotalRows), 600);
        //Redis optimize BEGIN
        if ($_SERVER['SERVER_NAME']!='forum.example.com') {
            $redis = new Redis();
            $redis->pconnect('127.0.0.1');
            foreach ($_result as &$v) {
                $v['views'] = $redis->get(sprintf("theme_view:%d", $v['themeID']));
            }
        }
        //Redis optimize END
        */
        return $_result;


    }


    public function LoadRules()
    {
        $_result = $this->DbManager->select ("-- CACHE: 10h 0m 0s
											SELECT
												`ruleID`, `caption`
											FROM 
												?# 
											ORDER BY 
												`ruleID` ASC
											", "forum_rules");
        return $_result;
    }

    public function getCurrentTheme($themeId)
    {
        $ix = sprintf("forumGetCurrentTheme_%d", $themeId);
        if (xcache_isset($ix)) {
            return unserialize(xcache_get($ix));
        }
        $_result = $this->DbManager->selectrow ("-- CACHE: 0h 0m 10s
            SELECT
                *
            FROM
                ?#
            WHERE
                `themeID`=?d
            ", "forum_db_themes", $themeId);
            
        xcache_set($ix, serialize($_result), 30);
        return $_result;
    }
    
    public function isThemeModerator($themeId,$userId)
    {
        $ix = sprintf("forumIsThemeModerator_t%d_u%d", $themeId, $userId);

        if (xcache_isset($ix)) {
            return (bool) unserialize(xcache_get($ix));
        }

        $_return = $this->selectcell('SELECT userId FROM ?# WHERE themeId=?d AND userId=?d','forum_settings_themes_moderators',$themeId,$userId);

        xcache_set($ix, serialize($_return), 3600);

        return (bool) $_return;
    }
    
    public function editTheme($themeId,$themeInfo)
    {
        //проверка идет в месте, где эта функция вызывается
        $this->DbManager->query('UPDATE ?# SET ?a WHERE themeID=?d','forum_db_themes',$themeInfo,$themeId);

    }
    public function themeHideLog($themeId) {
        $this->DbManager->query('INSERT INTO ?# (?#,?#) VALUES(?,?)','forum_db_hide_log','themeID','userID',$themeId,$this->AuthManager->User->userID);
    }
    public function messageHideLog($messageId) {
        $this->DbManager->query('INSERT INTO ?# (?#,?#) VALUES(?,?)','forum_db_hide_log','messageID','userID',$messageId,$this->AuthManager->User->userID);
    }
    public function billingLog($amount,$desc) {
        $this->DbManager->query('INSERT INTO ?# (?#,?#,?#) VALUES(?,?,?)','forum_db_billing','userID','amount','note',$this->AuthManager->User->userID,$amount,$desc);
        $Mailer = CreateObject("Mail_PHPMailer");
        $Mailer->From = 'noreply@'.SERVER_NAME;      // от кого
        $Mailer->FromName = SERVER_NAME.' robot';   // от кого
        $Mailer->AddAddress('admin@example.com', iconv("utf-8", "windows-1251", 'Admin User')); // кому - адрес, Имя
        $Mailer->Subject = iconv("utf-8", "windows-1251", "MailLog from ".SERVER_NAME);  // тема письма
        $Mailer->Body = iconv("utf-8", "windows-1251", 'Время: '.date('Y-m-d H:i:s')."\n\n".'Пользователь: '.$this->AuthManager->User->user_name.' ('.$this->AuthManager->User->userID.")\n\n".$desc);
        @$Mailer->Send();
        $Mailer->ClearAllRecipients();
        @$Mailer->ClearAttachments();
        unset($Mailer);
    }
    public function spamMailList() {
        return $this->spamMailList;
    }

}