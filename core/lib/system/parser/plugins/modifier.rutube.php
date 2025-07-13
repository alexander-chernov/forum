<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 10.11.2011
 * Time: 11:12:54
 * To change this template use File | Settings | File Templates.
 */

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty plugin
 *
 * Type:     modifier<br>
 * Name:     nl2br<br>
 * Date:     Feb 26, 2003
 * Purpose:  convert \r\n, \r or \n to <<br>>
 * Input:<br>
 *         - contents = contents to replace
 *         - preceed_test = if true, includes preceeding break tags
 *           in replacement
 * Example:  {$text|nl2br}
 * @link http://smarty.php.net/manual/en/language.modifier.nl2br.php
 *          nl2br (Smarty online manual)
 * @version  1.0
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @return string
 */
function smarty_modifier_rutube($string)
{
    $string = preg_replace("/\[rutube\](.*)rutube.ru\/(.*)\?v=(.*)\[\/rutube\]/si", "<OBJECT width='470' height='353'><PARAM name='movie' value='http://video.rutube.ru/\\3'></PARAM><PARAM name='wmode' value='transparent'></PARAM><PARAM name='allowFullScreen' value='true'></PARAM><EMBED src='http://video.rutube.ru/\\3' type='application/x-shockwave-flash' wmode='transparent' width='470' height='353' allowFullScreen='true' ></EMBED></OBJECT>", $string);
    //$string = preg_replace("/\[rutube\](.*)rutube.ru\/(.*)\?v=(.*)\[\/rutube\]/si", "\\1 \\2 \\3", $string);
    return $string;
}

/* vim: set expandtab: */

?>
