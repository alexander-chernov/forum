<?php
/**
 * Created by JetBrains PhpStorm.
 * User: frank
 * Date: 08.02.13
 * Time: 15:39
 * To change this template use File | Settings | File Templates.
 */
/*
function get($url) {
    $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
    $headers[] = 'Connection: Keep-Alive';
    $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=cp1251';
    $user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
    $process = curl_init($url);
    curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($process, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($process, CURLOPT_TIMEOUT, 30);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    $return = curl_exec($process);
    curl_close($process);
    return $return;
};
*/
if (xcache_isset('tomskru_actions')) {
    echo xcache_get('tomskru_actions');
    die();

} else {
    $content = file_get_contents('http://site.ru/system/service/skidki?count=4&order=rand');
    xcache_set('tomskru_actions', $content, 60);
    echo $content;
}


//var_dump(htmlentities($content));