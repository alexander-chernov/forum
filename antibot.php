<?php
session_start();
require(dirname(__FILE__).'/core/config/base.php');

$invert = false;
if (isset($_GET['invert'])) {
    $invert = true;
}

if (empty($_SESSION['_thread'])) {
    $code = strval(rand(10000, 99999));
    $encrypted = encrypt($code,ENCRYPT_KEY,ENCRYPT_IV,ENCRYPT_BIT_CHECK);
    $_SESSION['_thread'] = $encrypted;
} else {
    $code = decrypt($_SESSION['_thread'],ENCRYPT_KEY,ENCRYPT_IV,ENCRYPT_BIT_CHECK);
}


include dirname(__FILE__).'/core/lib/piccode/piccode.php';