<?php
class Auth_User
{
	var $userID = 0;
	var $user_name = "Анонимно";
	var $banned=0;
	var $Allow=1;
	public function __construct()
    {
        $this->userID = 0;
        $this->user_name = 'Анонимно'.rand(10000,99999);
        $this->Allow = 1;
        $this->banned = 0;
        //var_dump($GLOBALS['ForumCore']->AuthManager->Forwarder);
        //die();
        
       // $this->FirstName = GUEST_FIRSTNAME;
        //$this->Rang = 0;
    }
	public function CreateArray()
    {
        $_array = get_object_vars($this);
    	while (list($prop, $val) = each($_array))
        	$_result[$prop] = $val;
        return $_result;
    }
	public function CreateFromArray($_record, $_guest_result = true)
    {
        $_user = null;
        if ($_record)
        {
            if ($_guest_result && !(isset($_record['user_id']) && $_record['user_id']>0))
            {
            	$_user = CreateObject("Auth_Guest");
            }
            else
            {
            	$_user = new Auth_User;
	            foreach ($_record as $_prop => $_val)
	            {
	            	$_user->$_prop = $_val;
	            }
            }    
        }
        return $_user;
    }
	
    public function IsLogined()
    {
    	return $this->user_id != 0;
    }
    
   	public function SetConfirmed()
    {
    	$this->_confirmed = true;
    }
    
   	public  function CreateConfirmationCode()
    {
        $_code = sprintf('%s%u%s',
            $this->Email,
            !empty($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'localhost',
            !empty($GLOBALS['ForumCore']->AuthManager->Forwarder)?$GLOBALS['ForumCore']->AuthManager->Forwarder:'no proxy'
        );
        
        $this->ConfirmationCode = md5($_code);
        return $this->ConfirmationCode;         
    }
    
    public function GetConfirmationCode()
    {
    	return $this->ConfirmationCode;
    }
    
    public function ValidateConfirmationCode()
    {
    	$_code = sprintf('%s%u%s',
            $this->Email,
            !empty($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'localhost',
            !empty($GLOBALS['ForumCore']->AuthManager->Forwarder)?$GLOBALS['ForumCore']->AuthManager->Forwarder:'no proxy'
        );
        
       	return ($this->ConfirmationCode == md5($_code));
    }
    public function CheckUserRights($_module_id,$_event,$_user_id = null)
    {
    	if ($_user_id === null)
		{
			$_user_id = $this->user_id;
		}
		$_rights = $GLOBALS['DbManager']->selectcell("-- CACHE 0h 0m 59s SELECT `rights` FROM ?# WHERE module_id=?d AND user_id=?d AND event=?",'usr_userright', $_module_id,$_group_id,$_event);
		if ($_rights)
		{
			return $_rights;	
		}
		else
		{
			return $this->Group->CheckGroupRights($_module_id,$_event,$this->Group->id);
		}
    }
}
