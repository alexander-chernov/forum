<?php

// настраиваем нашу функцию для получения информации о ценных бумагах
function fetch_ticker($symbol)
{
   // из какого-то источника
   return $ticker_info;
}

function smarty_function_load_ticker($params, &$smarty)
{

$DbManager = $GLOBALS['ForumCore']->DbManager;
$ticker_info = $DbManager->select("SELECT * FROM `forum_db_groups`"); 
   // присваиваем переменную шаблона
   $smarty->assign($params['assign'], $ticker_info);
}
