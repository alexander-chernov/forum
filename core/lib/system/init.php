<?php

// подгрузка объектов.
function __autoload($class_name)
{
    list ($folder, $file) = explode ("_", strtolower ($class_name));
    if (is_file (LIB_DIR . '' . $folder . '/' . $file . '.php')) {
        require_once LIB_DIR . '' . $folder . '/' . $file . '.php';
    } elseif (is_file (MODULE_DIR . '' . $folder . '/' . $file . '.php')) {
        require_once MODULE_DIR . '' . $folder . '/' . $file . '.php';
    }
}

// создание объекта по имени класса
function CreateObject()
{
    $_args = func_get_args ();
    $object_name = array_shift ($_args);
    if (isset ($GLOBALS[ $object_name ])) {
        $_object = $GLOBALS[ $object_name ];
    } else {
        $_object = $GLOBALS[ $object_name ] = new $object_name ($_args);
    }
    return $_object;
}

// отладочная функция
function Debug($variable, $ip = false)
{
    if ($ip == true && substr($_SERVER[ 'REMOTE_ADDR' ],0,7) == '10.0.0.') {
        static $style = 'background-color:#FFF; color:#000; padding: 3px;';
        echo '<pre style="' . $style . '">';
        var_dump ($variable);
        echo '</pre>';
    }
}

function dbCacher($key, $value)
{
  /*  if ($value === null) {
        $ret = unserialize (xcache_get ($key));
        return $ret;
    } else {
        @xcache_unset ($key);
        xcache_set ($key, serialize ($value));
    }*/
}

interface Init
{

    public function Initialize();

    public function Authorize();

    public function AccessDeny();

    public function EventInterceptor();
}
class System_Init implements Init
{
    var $_site_url;
    var $_url_params;
    var $CurrentParam;
    var $DbManager;
    var $AuthManager;
    var $Errors;

    function __construct()
    {
        $this->ParseUrl ();
        $this->DbManager = CreateObject ("DbSimple_Generic");
        $this->DbManager = @$this->DbManager->connect (file_get_contents (CONFIG_DIR . "/system/database.cfg"));
        if ($this->DbManager->error) {
            header ('Location: /offline.php');
            exit ();
        }
        $this->DbManager->setCacher ('dbCacher');
        $this->DbManager->query ("SET SESSION wait_timeout = 120");
        $this->DbManager->query ("SET NAMES utf8");
        
        if ($this->CurrentParam == '') {
            header ('Location: /forum/');
        }
    }

    private function getCounters()
    {
        static $params = array (
            'themes_count', 'users_count', 'messages_count'
        );
        
        $data = $this->DbManager->select ('
				SELECT
					*
				FROM
					?#
				WHERE
					`name` IN (?a)
				', "forum_stat", $params);
        
        $collect = array ();
        foreach ($data as $arr)
            $collect[ $arr[ 'name' ] ] = $arr[ 'value' ];
        return $collect;
    }

    private function ParseUrl()
    {
        $_request_uri = strtolower (ereg_replace ("\\?.*", "", $_SERVER[ "REQUEST_URI" ] . "/"));
        $_request_uri = strtolower (ereg_replace ("/{2,12}", "/", $_request_uri));
        $this->_site_url = $_request_uri;
        $this->_url_params = explode ("/", $_request_uri);
        array_pop ($this->_url_params);
        unset ($this->_url_params[ 0 ]);
        $this->CurrentParam = end ($this->_url_params);
    }

    private function SetExecuteLibrary($_url_params)
    {
        $_Libraries = file_get_contents (CONFIG_DIR . "/pages.cfg");
        $_Libraries = eval ("return array(" . $_Libraries . ");");
        $_first_param = strtolower ($_url_params[ 1 ]);
        if (isset ($_Libraries[ $_first_param ])) {
            $_object = $_Libraries[ $_first_param ][ 0 ];
            $$_object = CreateObject ($_object);
            $Smarty = CreateObject ("System_Template");
            $$_object->Prepare ($Smarty);
            $Smarty->assign ("_system_user", $this->AuthManager->CreateUserArray ($this->AuthManager->User));
            $Smarty->assign ("_system_url_params", $this->_url_params);
            
            $Smarty->assign ("COMMERCIAL_ON", COMMERCIAL_ON);
            
            $Smarty->assign ("_counters", $this->getCounters ());

            $ix = 'forum_informer_pogoda';
            if (xcache_isset($ix)) {
                $_informer_p = unserialize(xcache_get($ix));
            } else {
                $_informer_p = informer_pogoda();
                xcache_set($ix, serialize($_informer_p), 60);
            }
            $Smarty->assign ("_informer_p", $_informer_p);
            $Smarty->assign ("_informer_day", informer_day ());
            $Smarty->assign ("_informer_date", informer_date ());
            
            if ($GLOBALS[ 'ForumCore' ]->Protector->_read_only == 1) {
                $Smarty->assign ("readonly", "1");
                $Smarty->assign ("ban_type", $GLOBALS[ 'ForumCore' ]->Protector->ban_type);
                $Smarty->assign ("ban_element", $GLOBALS[ 'ForumCore' ]->Protector->ban_element);
            }
            
            $$_object->Display ($Smarty);
        } else {
            header ('Location: /forum/');
        }
    }

    public function Initialize()
    {
        $this->checkRobokassaPay ();
        $this->SetExecuteLibrary ($this->_url_params);
    
    }

    public function AccessDeny()
    {
        echo "access deny";
    }

    public function Authorize()
    {
        $this->AuthManager = CreateObject ("Auth_Manager");
    
    }

    public function EventInterceptor()
    {
        $Form = CreateObject ("Form_Manager");
        if (isset ($Form->Request[ 'event' ]) && $Form->Request[ 'event' ] != '') {
            $_dir_handle = opendir (CONFIG_DIR . "events");
            while ( $file = readdir ($_dir_handle) ) {
                if ($file != '.' && $file != '..' && eregi ('.cfg', $file)) {
                    $_config_str .= file_get_contents (CONFIG_DIR . "events/" . $file);
                }
            }
            $_possible_events = eval ("return array(" . $_config_str . ");");
            //Debug($_possible_events);
            foreach ($_possible_events as $_event => $_execute_manager) {
                if (strtolower ($_event) == strtolower ($Form->Request[ 'event' ])) {
                    $this->_event ($_execute_manager, $_event, $Form);
                }
            }
        }
    }

    private function _event($obj, $_event, $_sender)
    {
        $_event = "onEvent_" . $_event;
        //Debug($_event);
        foreach ($obj as $key => $object) {
            $$object = CreateObject ($object);
            $$object->$_event ($_sender);
        }
    }

    public function SetError($sender, $_error)
    {
        $this->Errors[ $sender ][ ] = $_error;
    }

    private function checkRobokassaPay()
    {
        if (!$_POST['SignatureValue']) {
            return false;
        }
        // регистрационная информация (пароль #2)
        // registration info (password #2)
        $mrh_pass2 = ROBOKASSA_PASSWORD2;
        
        //установка текущего времени
        //current date
        $tm = getdate (time () + 9 * 3600);
        $date = "$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";
        
        // чтение параметров
        // read parameters
        $out_summ = $_POST[ "OutSum" ];
        $inv_id = $_POST[ "InvId" ];
        $shp_item = $_POST[ "Shp_item" ];
        $crc = $_POST[ "SignatureValue" ];
        
        $crc = strtoupper ($crc);
        
        $my_crc = strtoupper (md5 ("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));
        
        // проверка корректности подписи
        // check signature
        if ($my_crc != $crc) {
            echo "bad sign\n";
            exit ();
        }
        
        // признак успешно проведенной операции
        // success
        echo "OK$inv_id\n";
        
        // запись в файл информации о прведенной операции
        // save order info to file
        $this->DbManager->query('INSERT INTO ?# SET ?#=?s, ?#=?s, ?#=?s','robokassa_log','orderNum',$inv_id,'orderSumm',$out_summ,'orderDate',$date);
        $this->DbManager->query('UPDATE ?# SET ?#=?#+?s WHERE ?#=?d','forum_users','user_balance','user_balance',$out_summ,'userID',$shp_item);
        exit();
    }
}