<?php
class Form_Manager
{
	var $Request = array();
	function __construct()
	{
		$this->Request = array_merge($_GET,$_POST);	
		$this->prot($this->Request);
	}
	
	private function prot(&$array){
		if (is_array($array)){
			foreach ($array as &$value){
				$this->prot($value);
			}
		}else{
			$array = htmlspecialchars($array);
		}
	}
}