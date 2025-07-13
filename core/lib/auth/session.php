<?php
	class Auth_Session
	{
		private $session_name = 'hash';
		private $hash = '';
		private $server_name = '';
		
		public function __construct($hash, $server_name, $db_manager, $session_name = '')
	    {
	    	if (trim($session_name) != '')
	    		$this->session_name = trim($session_name);
	    	$this->server_nam = $server_name;
	    		

	    	$this->hash = $hash;

	    	session_name('hash');
	    	session_set_save_handler  (array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc'));
	    }
	    
	    public function open($save_path, $session_name){
      		setcookie($this->session_name, md5($this->hash), 0, '/', '.' . $this->server_name, 0, 0);
      		$_COOKIE[$this->session_name] = md5($this->hash);
	  	}
	    
	    public function close(){
	    	return true;
	    }
	    
	    public function read($id){
	    	
	    	return true;
	    }
	    
	    public function write($id, $sess_data){

	    	return true;
	    }
	    
	    
	    public function destroy($id){
	    	return true;
	    }

	    public function gc($maxlifetime){
	    	return true;
	    }
	}
?>