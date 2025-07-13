<?php
/**
 * Created by JetBrains PhpStorm.
 * User: frank
 * Date: 15.03.13
 * Time: 12:30
 * To change this template use File | Settings | File Templates.
 */
include dirname(__FILE__) . '/../cron/__header__.php';
$ip   = $_SERVER['REMOTE_ADDR'];
$url  = $_SERVER['QUERY_STRING'];
if (filter_var($url, FILTER_VALIDATE_URL)) {
    $db->query('INSERT INTO ?# SET ip = inet_aton(?), url = ?s'
        ,'forum_url_route'
        ,$ip
        ,$url
    );
    Header('Location: '.$url);
    exit();
} else {
    Header('Location: http://'.$_SERVER['SERVER_NAME'].'/');
    exit();
}
