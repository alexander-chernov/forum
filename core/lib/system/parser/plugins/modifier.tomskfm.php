<?php
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
function smarty_modifier_tomskfm($string)
{
    preg_match("/\[youtube\](.*)tomsk.fm\/watch\/(.*)\[\/youtube\]/si", $string, $matches);
    var_dump($matches);
    preg_match("/\[smotri\](.*)tomsk.fm\/watch\/(.*)\[\/smotri\]/si", $string, $matches);
    var_dump($matches);
    preg_match("/\[rutube\](.*)tomsk.fm\/watch\/(.*)\[\/rutube\]/si", $string, $matches);
    var_dump($matches);
    preg_match("/\[tomskfm\](.*)tomsk.fm\/watch\/(.*)\[\/tomskfm\]/si", $string, $matches);
    var_dump($matches);
    $code = base64_encode($matches[2]);
    $string = preg_replace("/\[tomskfm\](.*)tomsk.fm\/watch\/(.*)\[\/tomskfm\]/si", "<object width='448' height='370'><PARAM name='wmode' value='transparent'><param name='movie' value='http://tomsk.fm/export/".$code."'></param><param name='allowFullScreen' value='true'></param><embed src='http://tomsk.fm/export/".$code."' type='application/x-shockwave-flash' allowfullscreen='true' width='448' height='370' wmode='transparent'></embed></object>", $string);
    return $string;

}

/* vim: set expandtab: */

?>
