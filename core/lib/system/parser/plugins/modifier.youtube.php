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
function smarty_modifier_youtube($string)
{

    preg_match("/\[youtube\](.*)\[\/youtube\]/si", $string, $matches);
    if (count($matches)>0) {
        preg_match("/\[youtube\](.*)tomsk\.fm\/watch\/(.*)\[\/youtube\]/si", $string, $matches1);
        preg_match("/\[youtube\](.*)smotri\.com\/video\/view\/\?id=(.*)\[\/youtube\]/Usi", $string, $matches2);
        preg_match("/\[youtube\](.*)rutube\.ru\/(.*)\?v=(.*)\[\/youtube\]/si", $string, $matches3);
        preg_match("/\[youtube\](.*)youtube\.com\/watch\?v=(.*)\[\/youtube\]/si", $string, $matches4);
        preg_match("/\[youtube\](.*)youtube\.com\/watch\?feature=player_embedded\&v=(.*)\[\/youtube\]/si", $string, $matches5);
        preg_match("/\[youtube\](.*)youtube\.com\/watch\?v=(.*)\&feature=player_embedded\[\/youtube\]/si", $string, $matches6);
        if (count($matches1)>0) {
            $code = base64_encode($matches1[2]);
            $string = preg_replace("/\[youtube\](.*)tomsk\.fm\/watch\/(.*)\[\/youtube\]/si", "<object width='448' height='370'><PARAM name='wmode' value='transparent'><param name='movie' value='http://tomsk.fm/export/".$code."'></param><param name='allowFullScreen' value='true'></param><embed src='http://tomsk.fm/export/".$code."' type='application/x-shockwave-flash' allowfullscreen='true' width='448' height='370' wmode='transparent'></embed></object>", $string);
        } elseif (count($matches2)>0) {
            $string = preg_replace("/\[youtube\](.*)smotri\.com\/video\/view\/\?id=(.*)\[\/youtube\]/Usi", '<object id="smotriComVideoPlayer\\2_1320166338.9513_4564" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="640" height="360"><param name="movie" value="http://pics.smotri.com/player.swf?file=\\2&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><PARAM name="wmode" value="transparent"><embed src="http://pics.smotri.com/player.swf?file=\\2&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"  width="640" height="360" type="application/x-shockwave-flash"></embed></object>', $string);
        } elseif (count($matches3)>0) {
            $string = preg_replace("/\[youtube\](.*)rutube\.ru\/(.*)\?v=(.*)\[\/youtube\]/si", "<OBJECT width='470' height='353'><PARAM name='movie' value='http://video.rutube.ru/\\3'></PARAM><PARAM name='wmode' value='transparent'></PARAM><PARAM name='allowFullScreen' value='true'></PARAM><EMBED src='http://video.rutube.ru/\\3' type='application/x-shockwave-flash' wmode='transparent' width='470' height='353' allowFullScreen='true' ></EMBED></OBJECT>", $string);
        } elseif (count($matches4)>0) {
            $string = preg_replace("/\[youtube\](.*)youtube\.com\/watch\?v=(.*)\[\/youtube\]/si", "<object width='425' height='344'><PARAM name='wmode' value='transparent'><param name='movie' value='http://www.youtube.com/v/\\2&hl=de&fs=1'></param><param name='allowFullScreen' value='true'></param><embed src='http://www.youtube.com/v/\\2&hl=de&fs=1' type='application/x-shockwave-flash' allowfullscreen='true' width='425' height='344' wmode='transparent'></embed></object>", $string);
        } elseif (count($matches5)>0) {
            $string = preg_replace("/\[youtube\](.*)youtube\.com\/watch\?feature=player_embedded\&v=(.*)\[\/youtube\]/si", "<object width='425' height='344'><PARAM name='wmode' value='transparent'><param name='movie' value='http://www.youtube.com/v/\\2&hl=de&fs=1'></param><param name='allowFullScreen' value='true'></param><embed src='http://www.youtube.com/v/\\2&hl=de&fs=1' type='application/x-shockwave-flash' allowfullscreen='true' width='425' height='344' wmode='transparent'></embed></object>", $string);
        } elseif (count($matches6)>0) {
            $string = preg_replace("/\[youtube\](.*)youtube\.com\/watch\?v=(.*)\&feature=player_embedded\[\/youtube\]/si", "<object width='425' height='344'><PARAM name='wmode' value='transparent'><param name='movie' value='http://www.youtube.com/v/\\2&hl=de&fs=1'></param><param name='allowFullScreen' value='true'></param><embed src='http://www.youtube.com/v/\\2&hl=de&fs=1' type='application/x-shockwave-flash' allowfullscreen='true' width='425' height='344' wmode='transparent'></embed></object>", $string);
        }
    } else {
        preg_match("/(.*)tomsk\.fm\/watch\/([0-9]+)/si", $string, $matches1);
        preg_match("/(.*)smotri\.com\/video\/view\/\?id=([0-9a-z]+)/Usi", $string, $matches2);
        preg_match("/(.*)rutube\.ru\/(.*)\?v=([0-9a-z]+)/si", $string, $matches3);
        preg_match("/(.*)youtube\.com\/watch\?v=([0-9a-z_-]+)/si", $string, $matches4);
        preg_match("/(.*)youtube\.com\/watch\?(.*)\&v=([0-9a-z_-]+)/si", $string, $matches5);
        if (count($matches1)>0) {
            $code = base64_encode($matches1[2]);
            $string = preg_replace("/(.*)tomsk\.fm\/watch\/([0-9]+)/i", "<object width='448' height='370'><PARAM name='wmode' value='transparent'><param name='movie' value='http://tomsk.fm/export/".$code."'></param><param name='allowFullScreen' value='true'></param><embed src='http://tomsk.fm/export/".$code."' type='application/x-shockwave-flash' allowfullscreen='true' width='448' height='370' wmode='transparent'></embed></object>", $string);
        }
        if (count($matches2)>0) {
            $string = preg_replace("/(.*)smotri\.com\/video\/view\/\?id=([0-9a-z]+)/i", '<object id="smotriComVideoPlayer\\2_1320166338.9513_4564" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="640" height="360"><param name="movie" value="http://pics.smotri.com/player.swf?file=\\2&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="true" /><PARAM name="wmode" value="transparent"><embed src="http://pics.smotri.com/player.swf?file=\\2&bufferTime=3&autoStart=false&str_lang=rus&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"  width="640" height="360" type="application/x-shockwave-flash"></embed></object>', $string);
        }
        if (count($matches3)>0) {
            $string = preg_replace("/(.*)rutube\.ru\/(.*)\?v=([0-9a-z]+)/i", "<OBJECT width='470' height='353'><PARAM name='movie' value='http://video.rutube.ru/\\3'></PARAM><PARAM name='wmode' value='transparent'></PARAM><PARAM name='allowFullScreen' value='true'></PARAM><EMBED src='http://video.rutube.ru/\\3' type='application/x-shockwave-flash' wmode='transparent' width='470' height='353' allowFullScreen='true' ></EMBED></OBJECT>", $string);
        }
        if (count($matches4)>0) {
            $string = preg_replace("/(.*)youtube\.com\/watch\?v=([0-9a-z_-]+)/i", "<object width='425' height='344'><PARAM name='wmode' value='transparent'><param name='movie' value='http://www.youtube.com/v/\\2&hl=de&fs=1'></param><param name='allowFullScreen' value='true'></param><embed src='http://www.youtube.com/v/\\2&hl=de&fs=1' type='application/x-shockwave-flash' allowfullscreen='true' width='425' height='344' wmode='transparent'></embed></object>", $string);
        }
        if (count($matches5)>0) {
            $string = preg_replace("/(.*)youtube\.com\/watch\?(.*)\&v=([0-9a-z_-]+)/i", "<object width='425' height='344'><PARAM name='wmode' value='transparent'><param name='movie' value='http://www.youtube.com/v/\\3&hl=de&fs=1'></param><param name='allowFullScreen' value='true'></param><embed src='http://www.youtube.com/v/\\3&hl=de&fs=1' type='application/x-shockwave-flash' allowfullscreen='true' width='425' height='344' wmode='transparent'></embed></object>", $string);
        }
    }


    return $string;
}

/* vim: set expandtab: */

?>
