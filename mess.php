<?php
ini_set('max_execution_time', 120);
define('MOUNT_DIR', dirname(__FILE__));
define('LIB_DIR',MOUNT_DIR.'/core/lib/');
define('MODULE_DIR',MOUNT_DIR.'/core/modules/');
define('CONFIG_DIR',MOUNT_DIR.'/core/config/');
define('LANG_DIR',CONFIG_DIR.'lang/');
// parser defines
define('TPL_DIR',MOUNT_DIR.'/core/templates/');
define('TPL_DIR',MOUNT_DIR.'/tmp/templates_c/');
define('CACHECOMPILED_DIR',MOUNT_DIR.'/tmp/templates_c/');
define('CACHE_DIR',MOUNT_DIR.'/tmp/cache/');
require_once(CONFIG_DIR."base.php");
// �������������
setlocale(LC_ALL, 'ru_RU.UTF8', 'russian_RUSSIAN', 'ru', 'RU', 'Russian_Russia');
header("Content-Type: text/html;charset=utf-8");
if (PREVENTION_MODE == 1)
{
	header('Location: /offline.php');
	exit;
}
require_once(LIB_DIR."system/init.php");
require_once(LIB_DIR.'system/informers.php');
$ForumCore = CreateObject("System_Init");
die();
// ������� ����������� ������������!!!
$ForumCore->Authorize();
$ForumCore->AuthManager = CreateObject ("Auth_Manager");
$result = $ForumCore->DbManager->select('
                SELECT *
                FROM ?# p
                LEFT JOIN ?# m ON p.messageID = m.id
                WHERE p.id = ?d
                AND (m.fromuser = ?d OR m.touser = ?d)
                '
            ,'forum_pager_messages_attaches'
            ,'forum_users_pager'
            ,$_GET['id']
            ,$ForumCore->AuthManager->User->userID
            ,$ForumCore->AuthManager->User->userID
            );



