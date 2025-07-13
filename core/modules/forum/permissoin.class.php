<?php
class Permission
{
    private $dbManager = null;
    private $authManager = null;

    public function __construct($dbManager, $authManager)
    {
        $this->dbManager = $dbManager;
        $this->authManager = $authManager;
    }

    public function checkUserByUsersBlackList($groupId, $userId)
    {
        $query = 'SELECT userId FROM ?# WHERE groupId=?d AND userId=?d';
        $result = $this->dbManager->selectcell ($query, 'forum_db_groups_blacklist', $groupId, $userId);
        return (bool) $result;
    }

    public function checkUserByUsersWhiteList($groupId, $userId)
    {
        $query = 'SELECT userId FROM ?# WHERE groupId=?d AND userId=?d';
        $result = $this->dbManager->selectcell ($query, 'forum_db_groups_whitelist', $groupId, $userId);
        return (bool) $result;
    }

    public function isGroupOwner($groupInfo, $userId)
    {
        return (bool) ($userId == $groupInfo[ 'ownerId' ] && $userId >0);
    }

    public function isThemeOwner($themeId, $userId)
    {
        $query = 'SELECT authorID FROM ?# WHERE themeId=?d';
        $resultId = $this->dbManager->selectcell ($query, 'forum_db_themes', $themeId);
        return (bool) ($userId == $resultId && $userId >0);
    }


    public function checkUserIpByBlackList($groupId, $ip)
    {
        
        $query = 'SELECT groupId FROM ?# WHERE groupId=?d AND ip=?';
        $result = $this->dbManager->selectcell ($query, 'forum_db_groups_blacklist_ip', $groupId, $ip);
        return (bool) $result;
    }

    public function checkUserByBlackLists($groupId, $userId)
    {
        return (bool) ($this->checkUserIpByBlackList ($groupId, $this->authManager->RemoteAddr) || $this->checkUserByUsersBlackList ($groupId, $userId));
    }

    public function isGroupModerator($groupId, $userId)
    {
        $query = 'SELECT userId FROM ?# WHERE groupId=?d AND userId=?d';
        $result = $this->dbManager->selectcell ($query, 'forum_db_groups_moderators', $groupId, $userId);
        return (bool) $result;
    }


    public function getBlackListUsers($groupId)
    {
        $query = 'SELECT userId FROM ?# WHERE groupId=?d';
        $moderators = $this->dbManager->selectCol ($query, 'forum_db_groups_blacklist', $groupId);
        $users = $this->authManager->getUsersByIds ($moderators);
        return $users;
    }

    public function getWhiteListUsers($groupId)
    {
        $query = 'SELECT userId FROM ?# WHERE groupId=?d';
        $moderators = $this->dbManager->selectCol ($query, 'forum_db_groups_whitelist', $groupId);
        $users = $this->authManager->getUsersByIds ($moderators);
        return $users;
    }


    public function getGroupModerators($groupId)
    {
        $query = 'SELECT userId FROM ?# WHERE groupId=?d';
        $moderators = $this->dbManager->selectCol ($query, 'forum_db_groups_moderators', $groupId);
        $users = $this->authManager->getUsersByIds ($moderators);
        return $users;
    }

    public function addGroupModerator($groupId, $userName)
    {
        if ('' == $userName) {
            return array (
                'errors' => array (
                    'Укажите пользователя'
                )
            );
        }
        $userId = $this->getUserIdByName ($userName);
        if (! $userId) {
            return array (
                'errors' => array (
                    'Такого пользователя не существует'
                )
            );
        }
        
        if ($this->isGroupModerator ($groupId, $userId)) {
            return array (
                'errors' => array (
                    'Пользователь уже присутствует в списке'
                )
            );
        }
        
        $this->dbManager->query ('INSERT INTO ?# SET userId=?d, groupId=?d', 'forum_db_groups_moderators', $userId, $groupId);
        return true;
    }

    /**
     * 
     * Добавляет пользователя к уровням доступа к теме
     * @param unknown_type $themeId
     * @param unknown_type $userId
     * @param unknown_type $optRead
     * @param unknown_type $optWrite
     */
    public function addThemeUserAccess($themeId, $userName, $optRead, $optWrite)
    {
        if ('' == $userName) {
            return array (
                'errors' => array (
                    'Укажите пользователя'
                )
            );
        }
        $userId = $this->getUserIdByName ($userName);
        if (! $userId) {
            return array (
                'errors' => array (
                    'Такого пользователя не существует'
                )
            );
        }
        if ($this->checkThemeUserAccess ($themeId, $userId)) {
            return array (
                'errors' => array (
                    'Пользователь уже присутствует в списке'
                )
            );
        }
        $this->dbManager->query ('INSERT INTO ?# SET themeId=?d, userId=?d, optRead=?d,optWrite=?d', 'forum_settings_themes_userAccess', $themeId, $userId, $optRead, $optWrite);
        return true;
    }

    public function removeThemeUserAccess($themeId, $userId)
    {
        $this->dbManager->query ('DELETE FROM ?# WHERE themeId=?d AND userId=?d', 'forum_settings_themes_userAccess', $themeId, $userId);
    }

    public function getThemeAccessUsers($themeId)
    {
        return $this->dbManager->select ('
            SELECT
                u.userID,
                u.user_name,
                t.optRead as access_type,
                l.*
            FROM ?# u
            LEFT JOIN ?# l ON (l.userId=u.userID)
            LEFT JOIN ?# t ON (l.themeId=t.themeId)
            WHERE l.themeId=?d',
            'forum_users',
            'forum_settings_themes_userAccess',
            'forum_db_themes',
            $themeId);
    }
    public function getThemeRightsByUserId($themeId,$userId)
    {
        $userOptions = 0;
        if ($themeId>0 && $userId>0) {
            $userOptions = $this->dbManager->selectrow('SELECT t.optRead as access_type,s.optRead,s.optWrite FROM ?# s LEFT JOIN ?# t ON (s.themeId=t.themeId) WHERE s.themeId=?d AND s.userId=?d', 'forum_settings_themes_userAccess', 'forum_db_themes', $themeId, $userId);
        }
        if (($themeId>0 && $userId==0) || (!$userOptions)) {
            $userOptions = $this->dbManager->selectrow('SELECT t.optRead as access_type, 0 as optRead, 0 as optWrite FROM ?# t WHERE t.themeId=?d', 'forum_db_themes', $themeId);
        }
        if (!$userOptions) {
            return array('access_type'=>0, 'optRead'=>0,'optWrite'=>0);
        }
        return $userOptions;
    }
    public function checkThemeUserAccess($themeId, $userId)
    {
        $result = $this->dbManager->selectrow ('SELECT * FROM ?# WHERE userId=?d AND themeId=?d', 'forum_settings_themes_userAccess', $userId, $themeId);
        if ($result[ 'userId' ]) {
            return $result;
        } else {
            return false;
        }
    }

    public function addBlackUser($groupId, $userName)
    {
        if ('' == $userName) {
            return array (
                'errors' => array (
                    'Укажите пользователя'
                )
            );
        }
        $userId = $this->getUserIdByName ($userName);
        if (! $userId) {
            return array (
                'errors' => array (
                    'Такого пользователя не существует'
                )
            );
        }
        
        if ($this->checkUserByUsersBlackList ($groupId, $userId)) {
            return array (
                'errors' => array (
                    'Пользователь уже присутствует в списке'
                )
            );
        }
        
        $this->dbManager->query ('INSERT INTO ?# SET userId=?d, groupId=?d', 'forum_db_groups_blacklist', $userId, $groupId);
        return true;
    }

    public function addWhiteUser($groupId, $userName)
    {
        if ('' == $userName) {
            return array (
                'errors' => array (
                    'Укажите пользователя'
                )
            );
        }
        $userId = $this->getUserIdByName ($userName);
        if (! $userId) {
            return array (
                'errors' => array (
                    'Такого пользователя не существует'
                )
            );
        }
        if ($this->checkUserByUsersWhiteList ($groupId, $userId)) {
            return array (
                'errors' => array (
                    'Пользователь уже присутствует в списке'
                )
            );
        }
        
        $this->dbManager->query ('INSERT INTO ?# SET userId=?d, groupId=?d', 'forum_db_groups_whitelist', $userId, $groupId);
        return true;
    }

    public function addBlackIp($groupId, $ip)
    {
        if ('' == $ip) {
            return array (
                'errors' => array (
                    'Укажите IP-адрес'
                )
            );
        }
        
        if ($this->checkUserIpByBlackList ($groupId, $ip)) {
            return array (
                'errors' => array (
                    'Адрес уже присутствует в списке'
                )
            );
        }
        
        $this->dbManager->query ('INSERT INTO ?# SET ip=?, groupId=?d', 'forum_db_groups_blacklist_ip', $ip, $groupId);
        return true;
    }

    public function getIpList()
    {
        return $this->dbManager->select ('SELECT * FROM ?#', 'forum_db_groups_blacklist_ip');
    }

    public function getUserIdByName($userName)
    {
        $userName = iconv('utf8','windows-1251',$userName);
        return $this->dbManager->selectcell ('SELECT userID FROM forum_users WHERE user_name LIKE BINARY ?', $userName);
    }

    public function addUserInPersonalBlackList($userName, $authorId)
    {
        if ($id = $this->getUserIdByName ($userName)) {
            if ($this->checkUserByPersonalBlackList ($id, $authorId)) {
                return array (
                    'errors' => array (
                        'Пользователь уже добавлен в черный список'
                    )
                );
            } else {
                return $this->dbManager->query ('INSERT INTO ?# SET userId=?d, ownerId=?d', 'forum_users_blacklist', $id, $authorId);
            }
        } else {
            return array (
                'errors' => array (
                    'Пользователя не существует'
                )
            );
        }
    }

    public function getPersonalBlackListSettings($ownerId)
    {
        return $this->dbManager->selectRow ('SELECT * FROM ?# WHERE userId=?d', 'forum_users_blacklist_settings', $ownerId);
    }

    public function savePersonalBlackListSettings($ownerId, $setting)
    {
        return $this->dbManager->query ('
        								INSERT INTO 
        									?# 
    									(userId,hideAnonymous,hideUsers) 
    									VALUES 
    										(?d,?d,?d) 
										ON DUPLICATE KEY 
										UPDATE 
											hideAnonymous=?d, 
											hideUsers=?d', 'forum_users_blacklist_settings', $ownerId, $setting[ 'hideAnonymous' ], $setting[ 'hideUsers' ], $setting[ 'hideAnonymous' ], $setting[ 'hideUsers' ]);
    }

    public function removeUserFromPersonalBlackList($userId, $ownerId)
    {
        return $this->dbManager->query ('delete from forum_users_blacklist where ownerId=?d AND userId=?d', $ownerId, $userId);
    }

    public function getUsersInPersonalBlackList($userId)
    {
        $result_tmp = $this->dbManager->select ('
        								SELECT
        									u.*,
        									b.hidden
										FROM 
											?# b 
										LEFT JOIN ?# u 
											ON (b.userId=u.userID) 
										WHERE 
											b.ownerId=?d', 'forum_users_blacklist', 'forum_users', $userId);
        $result = array();
        foreach ($result_tmp as $item) {
            $item['userEncode'] = $this->Encrypt($item['userID']);
            $result[] =  $item;
        }
        return $result;
    }

    public function getUsersIdsInPersonalBlackList($userId)
    {
        return $this->dbManager->selectCol ('
        								SELECT
        									b.userId 
										FROM 
											?# b 
										WHERE 
											b.ownerId=?d', 'forum_users_blacklist', $userId);
    }

    public function checkUserByPersonalBlackList($userId, $authorId)
    {
        return $this->dbManager->selectcell ('SELECT userId FROM forum_users_blacklist WHERE userId=?d AND ownerId=?d', $userId, $authorId);
    }

    public function removeBlackUser($groupId, $userId)
    {
        $this->dbManager->query ('DELETE FROM ?# WHERE groupId=?d AND userId=?d', 'forum_db_groups_blacklist', $groupId, $userId);
    }

    public function removeWhiteUser($groupId, $userId)
    {
        $this->dbManager->query ('DELETE FROM ?# WHERE groupId=?d AND userId=?d', 'forum_db_groups_whitelist', $groupId, $userId);
    }

    public function removeModerator($groupId, $userId)
    {
        $this->dbManager->query ('DELETE FROM ?# WHERE groupId=?d AND userId=?d', 'forum_db_groups_moderators', $groupId, $userId);
    }

    public function removeBlackIp($groupId, $ip)
    {
        $this->dbManager->query ('DELETE FROM ?# WHERE ip=? AND groupId=?d', 'forum_db_groups_blacklist_ip', $ip, $groupId);
    }

    public function isGroupOpen($groupInfo)//!!!!!обратить внимание, не GroupID
    {
        if ($this->isGroupModerator ($groupInfo[ 'groupID' ], $this->authManager->User->userID)
            || $this->isGroupOwner ($groupInfo, $this->authManager->User->userID)
            || $this->authManager->User->is_admin) {
            return true;
        }
        if ($groupInfo[ 'deny_all' ]) {
            return $this->checkUserByUsersWhiteList ($groupInfo[ 'groupID' ], $this->authManager->User->userID);
        } else {
            if ($groupInfo[ 'deny_guest' ] && $this->authManager->User->userID == 0) {
                return false;
            }
            if ($groupInfo[ 'deny_user' ] && $this->checkUserByUsersBlackList ($groupInfo[ 'groupID' ], $this->authManager->User->userID)) {
                return false;
            }
        }
        if ($this->checkUserIpByBlackList ($groupInfo[ 'groupID' ], $this->authManager->RemoteAddr)) {
            return false;
        }
        return true;
    }
    public function Encrypt ($plaintext) {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $plaintext_utf8 = utf8_encode($plaintext);
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, MCENCRYPT_KEY,$plaintext_utf8, MCRYPT_MODE_CBC, $iv);
        $ciphertext = $iv . $ciphertext;
        $ciphertext_base64 = base64_encode($ciphertext);
        return $ciphertext_base64;
    }
    public function Decrypt ($ciphertext_base64) {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $ciphertext_dec = base64_decode($ciphertext_base64);
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);
        $ciphertext_dec = substr($ciphertext_dec, $iv_size);
        $plaintext_utf8_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, MCENCRYPT_KEY,$ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
        return $plaintext_utf8_dec;
    }
}