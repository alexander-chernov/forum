<?php

// максимально возможное количество соединений в единицу времени.
define("PROTECT_FLOOD_HITS",9000);
define('THEME_LEGHT',200);
define('AUTHOR_LEGHT',30);
// типы банов пользователей
define("BAN_FULL",1);
define("BAN_AUTH_ONLY",10);
define("BAN_READ_ONLY",11);
define("BAN_FLOOD",12);
define("COMMERCIAL_ON", 1);
//после этого времени в любой теме появляется каптча (в секундах)
define("TIMEOUT_THEME_UPDATE", 2592000); //30*24*60*60=2592000
//когда тема становится старой
define("OLD_THEME", 2592000); //150*24*60*60=12960000
//старые темы можно поднимать только столько раз в ниже количество секунд
define("OLD_THEME_COUNT", 5);
//старые темы можно поднимать только раз в столько секунд
define("OLD_THEME_TIMEOUT", 600); //10*60=2592000
//с этой кармы можно менять карму
define('USER_VOTE_LEVEL',1);
define('USER_DAILY_KARMA',100);
define('USER_PAGER_TIMEOUT_MESS',10);        // in seconds
//количество горячих тем в топе, на странице
define('HOTTOP_THEMES',50);
//количество закрепленных
define('STIKER_HOTTOP_THEMES',25);
define('PER_PAGE',50);
//ниже этого уровня сообщения скрываются
define('MESSAGE_HALFHIDE_RATING',-5);
define('MESSAGE_HIDE_RATING',-10);
//ниже этого уровня пользователь писать вообще не может
define('USER_MAT_LEVEL',-200);
//ниже этого уровня пользователь может писать только в матоязычных темах
define('USER_LEVEL',-100);
// стоимость редактирования сообщения
define('EDIT_MESSAGE_COST',0);
//стоимость единицы кармы
define('KARMA_UNIT_COST',1);
//стоимость удаления темы
define('HIDE_THEME_COST',10);
//стоимость редактирования темы
define('EDIT_THEME_COST',1);
//стоимость скрытия сообщения в своей теме
define('HIDE_MESSAGE_COST',0);
//срок, после которого ник считается старым (в днях)
define('OLD_NICKNAME_EXPIRE',730);
//стоимость покупки занятого ника
define('OLD_NICKNAME_COST',500);
// время, в течение которого можно редактировать сообщения (в секундах)
define('EDIT_MESSAGE_TIME_LIMIT',3600);
//стоимость добавления в черный список анонимов
define('EDIT_HIDE_ANONIM_COST',0);
//define("SERVER_NAME",'forum.loc');
define("SEARCH_BOOLEAN_MODE", 0);
define('MAX_FILES_UPLOAD',9);
//количество сообщений в день от анонима
define('ANONYMOUS_MESSAGES_LIMIT_DAY',0);
//количество секунд до того момента как вновь зарегистрированным пользователям можно писать в личку
define('FRESH_USERS_PAGER_SECOND_LIMIT',86400);// 60*60*24 = сутки
//количество секунд до того момента как вновь зарегистрированным пользователям можно создавать темы и сообщения
define('FRESH_USERS_SECOND_LIMIT',3600);// 60*60 = 1 час
define('WORD_POST_COUNT',30);
define("SERVER_NAME",$_SERVER['SERVER_NAME']);
define ('HOME_DIR',realpath(dirname(__FILE__) . '/../../').'/');
define('DANGER_LEVEL_RED', 5);
define('DANGER_LEVEL_YELLOW', 1);
define('AUTOUP_BAN', 30);
define('PREVENTION_MODE', 0);
define('QUERY_DEBUG_MODE', 0);
define('COMMERCIAL_ADDTHEME',0);

//цена поднятия темы в разделе
define('TOP_PRICE',500);
//цена поднятия темы в разделе на сутки
define('TOP_DAY_PRICE',100);
//цена поднятия темы в топе
define('TOP30_PRICE',1000);
//цена поднятия темы в топе на сутки
define('TOP30_DAY_PRICE',200);

define('ORIG_WIDTH',800);
define('ORIG_HEIGHT',600);
define('THUMB_WIDTH',150);
define('THUMB_HEIGHT',150);
define('FILESIZE_MAX',50000000);        // in bytes

define('SIMILAR_PERCENT',95);       // при такой схожести сообщения считаюся похожими и помечаются как спам
define('STRLEN_HIDE',500);          // проверка сообщения на большое количество букв
define('LINE_HIDE',10);             // проверка сообщения на большое количество строк


/*
 * 		регистрационные данные робокассы
 */
define(AUTH_ERROR_WRONG,"Извините, введнные Вами имя пользователя или пароль не верны. Попробуйте еще раз.");
define(AUTH_ERROR_NOLOGIN,"Извините, Вы забыли ввести имя пользователя");
define(AUTH_ERROR_CAPTCHA,"Извините, Вы ввели неверный код");
/**
 * Доступ к теме
 */

define('ALLOW_READ_THEME_ALL',0);
define('ALLOW_READ_THEME_AUTHUSER',1);
define('ALLOW_READ_THEME_VALIDUSER',2);
define('DISALLOW_READ_THEME_VALIDUSER',3);
define('ALLOW_READ_FOR_ALL_THEME_VALIDUSER',4);

define('ALLOW_WRITE_THEME_ALLUSER',0);
define('ALLOW_WRITE_THEME_AUTHUSER',1);
define('ALLOW_WRITE_THEME_VALIDUSER',2);
define('DISALLOW_WRITE_THEME_VALIDUSER',3);

define('ENCRYPT_KEY','SuperStrongPasswordEncryptionWord');
define('ENCRYPT_IV',"EncryptedIV");
define('ENCRYPT_BIT_CHECK',8);

/*define('','');
define('','');
*/
function makeJSON($value)
{
	return json_encode ($value);
}

function encrypt($text, $key, $iv, $bit_check) {
    $text_num = str_split($text, $bit_check);
    $text_num = $bit_check - strlen($text_num[count($text_num) - 1]);
    for ($i = 0; $i < $text_num; $i++) {
        $text = $text . chr($text_num);
    }
    $cipher = mcrypt_module_open(MCRYPT_TRIPLEDES, '', 'cbc', '');
    mcrypt_generic_init($cipher, $key, $iv);
    $decrypted = mcrypt_generic($cipher, $text);
    mcrypt_generic_deinit($cipher);
    return base64_encode($decrypted);
}

function decrypt($encrypted_text, $key, $iv, $bit_check) {
    $cipher = mcrypt_module_open(MCRYPT_TRIPLEDES, '', 'cbc', '');
    mcrypt_generic_init($cipher, $key, $iv);
    $decrypted = mdecrypt_generic($cipher, base64_decode($encrypted_text));
    mcrypt_generic_deinit($cipher);
    $last_char = substr($decrypted, -1);
    for ($i = 0; $i < $bit_check - 1; $i++) {
        if (chr($i) == $last_char) {


            $decrypted = substr($decrypted, 0, strlen($decrypted) - $i);
            break;
        }
    }
    return $decrypted;
}
function isMobile(){
    $agents = array('acs-'
    ,'alav'
    ,'alca'
    ,'amoi'
    ,'audi'
    ,'aste'
    ,'avan'
    ,'benq'
    ,'bird'
    ,'blac'
    ,'blaz'
    ,'brew'
    ,'cell'
    ,'cldc'
    ,'cmd-'
    ,'dang'
    ,'doco'
    ,'eric'
    ,'hipt'
    ,'inno'
    ,'ipaq'
    ,'java'
    ,'jigs'
    ,'kddi'
    ,'keji'
    ,'leno'
    ,'lg-c'
    ,'lg-d'
    ,'lg-g'
    ,'lge-'
    ,'maui'
    ,'maxo'
    ,'midp'
    ,'mits'
    ,'mmef'
    ,'mobi'
    ,'mot-'
    ,'moto'
    ,'mwbp'
    ,'nec-'
    ,'newt'
    ,'noki'
    ,'opwv'
    ,'palm'
    ,'pana'
    ,'pant'
    ,'pdxg'
    ,'phil'
    ,'play'
    ,'pluc'
    ,'port'
    ,'prox'
    ,'qtek'
    ,'qwap'
    ,'sage'
    ,'sams'
    ,'sany'
    ,'sch-'
    ,'sec-'
    ,'send'
    ,'seri'
    ,'sgh-'
    ,'shar'
    ,'sie-'
    ,'siem'
    ,'smal'
    ,'smar'
    ,'sony'
    ,'sph-'
    ,'symb'
    ,'t-mo'
    ,'teli'
    ,'tim-'
    ,'tosh'
    ,'tsm-'
    ,'upg1'
    ,'upsi'
    ,'vk-v'
    ,'voda'
    ,'w3c '
    ,'wap-'
    ,'wapa'
    ,'wapi'
    ,'wapp'
    ,'wapr'
    ,'webc'
    ,'winw'
    ,'winw'
    ,'xda'
    ,'xda-');
    $uagent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if(stristr($uagent,'windows')&&!stristr($uagent,'windows ce'))
        return false;

    $ipad = strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
    $iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
    $palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
    $berry = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
    $ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
    $mobile = strpos($_SERVER['HTTP_USER_AGENT'],"Mobile");
    $symb = strpos($_SERVER['HTTP_USER_AGENT'],"Symbian");
    $operam = strpos($_SERVER['HTTP_USER_AGENT'],"Opera M");
    $htc = strpos($_SERVER['HTTP_USER_AGENT'],"HTC_");
    $fennec = strpos($_SERVER['HTTP_USER_AGENT'],"Fennec/");
    $winphone = strpos($_SERVER['HTTP_USER_AGENT'],"WindowsPhone");
    $wp7 = strpos($_SERVER['HTTP_USER_AGENT'],"WP7");
    $wp8 = strpos($_SERVER['HTTP_USER_AGENT'],"WP8");
    if ($ipad || $iphone || $android || $palmpre || $ipod || $berry || $mobile || $symb || $operam || $htc || $fennec || $winphone || $wp7 || $wp8 === true) {
        return true;
    } else {
        if( (eregi('up.browser|up.link|windows ce|iemobile|mini|mmp|symbian|midp|wap|phone|pocket|mobile|pda|psp',$uagent))
            || (stristr($_SERVER['HTTP_ACCEPT'],'text/vnd.wap.wml')
                ||stristr($_SERVER['HTTP_ACCEPT'],'application/vnd.wap.xhtml+xml'))
            || (isset($_SERVER['HTTP_X_WAP_PROFILE'])
                ||isset($_SERVER['HTTP_PROFILE'])
                ||isset($_SERVER['X-OperaMini-Features'])
                ||isset($_SERVER['UA-pixels']))
            || (isset($agents[substr($uagent,0,4)]))
            || (strlen($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])>3)
            || (intval($_GET['mobi']) == 1) )
        {
            return true;
        }
    }
}
