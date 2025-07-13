<?php
class Forum_Filter
{
    var $BadWords = array();
    var $BadWordsExcludes = array();

    function __construct()
    {
        $this->DbManager = $GLOBALS['ForumCore']->DbManager;

        $this->BadWords = $this->DbManager->select(
            "-- CACHE: 4h 0m 0s
				SELECT 
					`filter_string`, `flag_caption`, `flag_content`, `flag_author` 
				FROM 
					?#
				WHERE  
					flag_caption = 1
					AND flag_content = 1
					AND flag_author = 1

			",
            'forum_db_filter'
        );
        $this->BadWordsExcludes = $this->DbManager->selectCol(
            "-- CACHE: 4h 0m 0s
				SELECT
					`filter_word`
				FROM
					?#
			",
            'forum_db_filter_exclude'
        );
    }

    function get_sec()
    {
        $mtime = microtime();
        $mtime = explode(" ", $mtime);
        $mtime = $mtime[1] + $mtime[0];
        return $mtime;
    }

    function CheckBadWords($data)
    {
        require_once LIB_DIR . 'system/parser/plugins/modifier.nl2br.php';
        require_once LIB_DIR . 'system/parser/plugins/modifier.bbcode.php';
        $return = false;
        $matches = array();
        // проверка автора
        $dvalue = $data['author'];
        $temp = preg_replace("/([\s\x{0}\x{0B}]+)/i", " ", trim($dvalue));
        $temp = strip_tags(html_entity_decode(smarty_modifier_nl2br(smarty_modifier_bbcode($temp))));
        $words = preg_split("/[\s,]+/", $temp);

        foreach ($this->BadWords as $badword) {
            foreach ($words as $wline) {
                if (
                    preg_match("/" . mb_strtolower($badword['filter_string'], 'UTF-8') . "/i", mb_strtolower($wline, 'UTF-8'), $matches)
                    && !in_array(mb_strtolower($wline, 'UTF-8'), $this->BadWordsExcludes)
                    && !empty($wline)
                ) {
                    $_SESSION['badword'] = $wline;
                    return true;
                }
            }
        }
        //проверка темы
        $dvalue = $data['caption'];
        $temp = preg_replace("/([\s\x{0}\x{0B}]+)/i", " ", trim($dvalue));
        $temp = strip_tags(html_entity_decode(smarty_modifier_nl2br(smarty_modifier_bbcode($temp))));
        $words = preg_split("/[\s,]+/", $temp);

        foreach ($this->BadWords as $badword) {
            foreach ($words as $wline) {
                if (
                    preg_match("/" . mb_strtolower($badword['filter_string'], 'UTF-8') . "/i", mb_strtolower($wline, 'UTF-8'), $matches)
                    && !in_array(mb_strtolower($wline, 'UTF-8'), $this->BadWordsExcludes)
                    && !empty($wline)
                ) {
                    $_SESSION['badword'] = $wline;
                    return true;
                }
            }
        }


        // проверка контента
        $dvalue = $data['content'];
        $temp = preg_replace("/([\s\x{0}\x{0B}]+)/i", " ", trim($dvalue));
        $temp = strip_tags(html_entity_decode(smarty_modifier_nl2br(smarty_modifier_bbcode($temp))));
        $words = preg_split("/[\s,]+/", $temp);
        if (count($words)<WORD_POST_COUNT){
            foreach ($this->BadWords as $badword) {
                    foreach ($words as $wline) {
                        if (
                            preg_match("/" . mb_strtolower($badword['filter_string'], 'UTF-8') . "/i", mb_strtolower($wline, 'UTF-8'), $matches)
                            && !in_array(mb_strtolower($wline, 'UTF-8'), $this->BadWordsExcludes)
                            && !empty($wline)
                        ) {
                            $_SESSION['badword'] = $wline;
                            return true;
                        }
                    }
            }
        }
        return $return;
    }

    function CheckBadLinks($link, $check_key)
    {
        /*
          $link = str_replace("http://www.","",$link);
          $link = str_replace("https://www.","",$link);
          $link = str_replace("ftp://www.","",$link);
          $link = str_replace("http://","",$link);
          $link = str_replace("https://","",$link);
          $link = str_replace("ftp://","",$link);
          */


    }

    function CheckForBot($message)
    {

    }
}
