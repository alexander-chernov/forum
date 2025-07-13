<?
require_once(LIB_DIR."system/parser/Smarty.class.php");

// шаблонизатор.
/*
 * TODO: сделать обработчик функции забытых темплейтов, некую заглушку.
 */
function MakeForgottenTemplate($resource_type,$resource_name, &$template_source, &$template_timestamp, &$smarty_obj)
{
	
}
class System_Template extends Smarty
{
	function __construct($params = null)
	{
		if (isset($_COOKIE['system_template']) && $_COOKIE['system_template'] != '')
		{
			if (is_dir(TPL_DIR.$_COOKIE['system_template']))
			{
				$_skin = $_COOKIE['system_template'];
			}
			else
			{
				$_skin="default/";
			}
		}
		else
		{
			$_skin="default/";
			setcookie('system_template',$_skin,0,'/','.'.SERVER_NAME,0,0);
		}
		parent::Smarty();
		$this->template_dir = TPL_DIR.'default/';
		$this->compile_dir = CACHECOMPILED_DIR;
		$this->config_dir = CONFIG_DIR;
		$this->cache_dir = CACHE_DIR;
		$this->default_template_handler_func = "MakeForgottenTemplate";
	}
}
?>