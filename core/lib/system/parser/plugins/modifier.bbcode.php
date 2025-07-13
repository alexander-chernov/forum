<?php
function smarty_modifier_bbcode($string)
{
	$bbcode = array(
		"/\[b(.*?)\](.*?)\[\/b\]/is" => "<strong>$2</strong>",
		"/\[i(.*?)\](.*?)\[\/i\]/is" => "<i>$2</i>",
		"/\[re(.*?)\](.*?)\[\/re\]/is" => "<div class=\"box_cite\"> $2 </div>",
        "/&quot;/is" => "'",
        "/&amp;/is" => "&",
        //"/&lt;div/is" => "<div",
        "/&gt;/is" => ">",
        "/\[youtube(.*?)\](.*?)\[\/youtube\]/is" => "$2",
        "/\[smotri(.*?)\](.*?)\[\/smotri\]/is" => "$2",
        "/\[rutube(.*?)\](.*?)\[\/rutube\]/is" => "$2",
        "/\[tomskfm(.*?)\](.*?)\[\/tomskfm\]/is" => "$2",
        "/\[video(.*?)\](.*?)\[\/video\]/is" => "$2",
        //"/&lt;\/div/is" => "</div",

	);
    $result = '';
    $result = $string;
    for($i=0;$i<=10;$i++) {
        $result  = preg_replace(array_keys($bbcode), array_values($bbcode), $result);
    }

	return $result;
}
?>