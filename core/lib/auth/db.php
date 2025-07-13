<?php
class Auth_Db
{
    var $DbManager;

    function __construct()
    {
        $this->DbManager = $GLOBALS[ 'ForumCore' ]->DbManager;
    }

    public function FindUserById($id)
    {
        $ix = sprintf("forumAuth_FindUserById_%d", $id);
        if (xcache_isset($ix)) {
            $_user = unserialize(xcache_get($ix));
        } else {
                $_user = $this->DbManager->selectrow ("
                                                        SELECT
                                                            u.*,
                                                            ifnull(u.danger_level,0) as danger_level
                                                        FROM
                                                            ?# u
                                                        WHERE
                                                            `userID`=?d
                                                    ", 'forum_users', $id);
            xcache_set($ix, serialize($_user), 240);
        }
        return $_user;
    }

    public function FindUserOnline($_user_id)
    {
        $_user = $this->DbManager->selectrow ("
												SELECT 
													u.*,
                                                    ifnull(u.danger_level,0) as danger_level
												FROM 
													?# u
												WHERE 
													`userID`=?d
											", 'forum_users_online', $_user_id);
        return $_user;
    }

    public function SetUserOnline($_user)
    {
        $_user = $this->DbManager->query ("
											INSERT INTO 
												?# 
											SET 
												?a", 'forum_users_online', $_user);
        return $_user;
    }

    public function GetRegisterSMSCode($_smscode)
    {
        $_result = $this->DbManager->selectcell ("SELECT `ID` FROM ?# WHERE `smscode` = ? AND `userID`=?d ", 'sms_auth_keys', $_smscode, 0);
        if ($_result > 0) {
            return true;
        } else {
            return false;
        }
    
    }

    public function UpdateSmsRegistration($_smscode, $_userid)
    {
        $this->DbManager->query ("UPDATE ?# SET `userID`=?d WHERE smscode=? AND userID=0", 'sms_auth_keys', $_userid, $_smscode);
    }

    public function UpdateUserOnline($_user_id)
    {
        $_user = $this->DbManager->query ("
											UPDATE 
												?# 
											SET 
												`lastlogin` = NOW(),
												`user_ip` = ?d,
											WHERE 
												`userID`=?d", 'forum_users_online', ip2long($_SERVER['REMOTE_ADDR']), $_user_id);
        $this->DbManager->query ('DELETE FROM ?# WHERE `lastlogin` < NOW() - INTERVAL 10 MINUTES', 'forum_users_online');
        return $_user;
    }

    public function SaveConfirmationCode($_code, $_user_id)
    {
        $_user = $this->DbManager->query ("
											UPDATE 
												?# 
											SET 
												`confirmcode` = ?,
												`user_ip` = ?d,
												`lastlogin` = NOW()
											WHERE 
												`userID`=?d", 'forum_users', $_code, ip2long($_SERVER['REMOTE_ADDR']), $_user_id);
        return $_user;
    }

    public function FindUserByAuth($_login, $_password)
    {
        return $this->DbManager->selectRow ("
										SELECT 
											* 
										FROM 
											?# 
										WHERE 
											`user_name`=?s 
										AND 
											user_password=?s
										", 'forum_users', $_login, md5 ($_password));
    }

    public function FindUserByLogin($_login)
    {
        return $this->DbManager->selectRow ("
										SELECT
											DISTINCT *
										FROM
											?#
										WHERE
											`user_name` = ?s
										", 'forum_users', $_login);
    }

    //----------------------------Сохраняем нового/текущего пользователя в БД---------------//	
    public function SaveUser($_user,$use_pass = 1)
    {
        if (isset ($_user[ 'userID' ]) && $_user[ 'userID' ] > 0) {
            $this->UpdateUser ($_user, $use_pass);
            return $_user[ 'userID' ];
        } else {
            return $this->InsertUser ($_user);
        }
    }

    private function UpdateUser($_user, $use_pass = 1)
    {
        $_query = "UPDATE `forum_users` SET ";
        $userID = $_user['userID'];
        $ix = sprintf("forumAuth_FindUserById_%d", $userID);
        xcache_unset($ix);
        //echo ':use_pass<br>';
        //var_dump($use_pass);
        //echo __FILE__.':'.__LINE__.':use_pass<br>';
        foreach ($_user as $key => $val) {

            //$_query .="`$key`='".mysql_escape_string($val)."', ";

            
            if ($key != 'userID') {
                if ($key == 'user_password' && $use_pass==1) {
                    $val = md5 ($val);
                }
                $_query .= "`$key`='" . mysql_escape_string ($val) . "', ";
            }

        }
        $_query .= "`active`=1 WHERE `userID`='" . $_user[ 'userID' ] . "'";
        $this->DbManager->query ($_query);
    }

    private function InsertUser($_user)
    {
        $_query = "INSERT INTO `forum_users` SET ";
        foreach ($_user as $key => $val) {
            if ($key == 'user_password') {
                $val = md5 ($val);
            }
            $_query .= "`$key`='" . mysql_escape_string ($val) . "', ";
        }
        $_query .= "`userID`=null, `registered`=NOW(), `lastlogin`=NOW(), `active` = 0";
        return $this->DbManager->query ($_query);
    }

    public function FindUserByName($_username)
    {
        return $this->DbManager->selectcell ("SELECT DISTINCT `userID` FROM ?# WHERE `user_name`=?s", 'forum_users', $_username);
    }

    public function FindUserByEmail($_email)
    {
        return $this->DbManager->selectcell ("SELECT DISTINCT `userID` FROM ?# WHERE `user_email`=?s", 'forum_users', $_email);
    }

    public function UpdateCounter()
    {
        //Update counter
        $this->DbManager->query ("UPDATE 
				?#  
			SET 
				`value` = `value` + 1
			WHERE 
				`name`= ?s", 'forum_stat', 'users_count');
    }

    public function getUsersByIds($ids)
    {
        $query = 'SELECT DISTINCT * FROM ?# WHERE userID IN (?a)';
        return $this->DbManager->select ($query, 'forum_users', $ids);
    }

    public function searchUserByName($key)
    {
        $query = 'SELECT DISTINCT user_name FROM ?# WHERE user_name LIKE ?';
        return $this->DbManager->selectCol ($query, 'forum_users', $key.'%');
    }
    public function searchOldUserByName($key)
    {
        $query = 'SELECT DISTINCT user_name FROM ?# WHERE is_admin=0 AND userID NOT IN (SELECT userId FROM ?#) AND DATEDIFF( now( ) , lastlogin )>?d AND DATEDIFF( now( ) , registered )>?d AND user_name LIKE ?';
        $return = $this->DbManager->selectCol ($query, 'forum_users', 'forum_db_groups_moderators', OLD_NICKNAME_EXPIRE,OLD_NICKNAME_EXPIRE, $key.'%');
        return $return;
    }
}