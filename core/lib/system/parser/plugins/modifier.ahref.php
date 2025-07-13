<?php
function normalize_url($_url){
    return $_url;
	$url = $_url[2];
	if (((substr($url,0,1) == "\"") && (substr($url,-1,1) == "\""))||
		((substr($url,0,1) == "'")  && (substr($url,-1,1) == "'" ))) {
		$url = substr($url, 1, strlen($url)-2);
	}

	// Check for XSS attack
	$urlXSS = str_replace(array(ord(0), ord(9), ord(10), ord(13), ' ', "'", "\"", ";"),'',$url);
	if (preg_match('/^javascript:/is', $urlXSS)) {
		return false;
	}

	// Add leading "http://" if needed
	if (!preg_match("#^(http|ftp|https)\://#i", $url)) {
		$url = "http://".$url;
	}
	$result =  $_url[1] . '<a href="' . preg_replace('/javascript:/ims', '', $url) . '" target="_blank">';
	if (strlen($url) >20) {
		$result .= substr($url, 0, 20) . '...';
	} else {
		$result .= $url;
	}
	$result .='</a>';
	return $result;
}
function smarty_modifier_ahref($string)
{
    return preg_replace_callback("#(^|\s)((((http|https|ftp)://)|(www\.))\w+[^\s\[\]\<]+)#ims", 'normalize_url', $string);
}
?>