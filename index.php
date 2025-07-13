<?php
setlocale(LC_ALL, 'ru_RU.UTF8', 'russian_RUSSIAN', 'ru', 'RU', 'Russian_Russia');
header("Content-Type: text/html;charset=utf-8");
session_start();
$start = microtime(1);
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
// инициализация
if (PREVENTION_MODE == 1)
{
	header('Location: /offline.php');
	exit;
}
require_once(LIB_DIR."system/init.php");
require_once(LIB_DIR.'system/informers.php');
$ForumCore = CreateObject("System_Init");
// процесс авторизации пользователя!!!
$ForumCore->Authorize();
$ForumCore->Protector = CreateObject("System_Protector");
/*
if (!$ForumCore->AuthManager->User->banned)
{
	$ForumCore->EventInterceptor();
	$ForumCore->Initialize();
}
else
{
	$ForumCore->AccessDeny();
}
*/
$ForumCore->EventInterceptor();
$ForumCore->Initialize();
//echo '<!--'.((microtime(1) - $start)*1000).'-->';