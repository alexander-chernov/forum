<?php
/* 
 * Search library
 */
class Search_Manager
{
    protected $MainTemplate = "forum/search/index.tpl";
    var $Form = null;
    var $Results = null;
    var $Query = null;
	var $Groups = array();
	var $SListThemes = array();

    function __construct()
    {
        $this->DbManager = $GLOBALS['ForumCore']->DbManager;
		
		$this->Groups = $this->LoadGroups();
    }
	
	public function LoadGroups()
	{
		$_result = $this->DbManager->select(" -- CACHE: 0h 20m 30s
											SELECT 
												* 
											FROM 
											?# 
											ORDER BY 
												`caption` ASC
											",
											"forum_db_groups"
										);
		return $_result;
	}

    /*
     * TODO: запись хэша в куки
     */
    public function onEvent_forumSearch($form)
    {
        $this->Form = $form;
        $this->Query = $this->Form->Request['query'];
        $this->SListThemes = (isset($this->Form->Request['sListThemes']) ? $this->Form->Request['sListThemes'] : array());
        
        //проверяем лог
        $check = $this->DbManager->select("
                                            SELECT
                                                *
                                            FROM
                                                `forum_db_search_queries`
                                            WHERE
                                                `hash` = ?s
                                          ",
                                          $this->Query
        );

        //выполняем непосредственно поисковый запрос
        $TotalRows = 0;
        $inject = '';
		$inflect = $this->inflect($this->Query);
        if (SEARCH_BOOLEAN_MODE > 0) $inject = 'IN BOOLEAN MODE';
        $data = $this->DbManager->selectPage($TotalRows,
                                             "
                                              SELECT
                                                *
                                              FROM
                                                `forum_db_search_index`
                                              WHERE MATCH(`caption`) AGAINST (? ".$inject.")
                                              { AND `groupID` IN (?a) }
                                              ORDER BY `created` DESC
                                             ",
                                             $inflect,
                                          	 (count($this->SListThemes) == 0 ? DBSIMPLE_SKIP : $this->SListThemes)
                                             );
        $this->Results = $data;

        //логгируем запрос
        if (count($check) > 0) {        //hash найден
            $count = (int)$check[0]['count'] + 1;
            $this->DbManager->query("
                                        UPDATE
                                            `forum_db_search_queries`
                                        SET
                                            `count` = ?d,
                                            `results` = ?d
                                        WHERE
                                            `hash` = ?s
                                    ",
                                    $count,
                                    $TotalRows,
                                    $this->Query
            );
        }
        else {                          //хэш не найден
            $this->DbManager->query("
                                        INSERT INTO
                                            `forum_db_search_queries`
                                        VALUES
                                            (?s, 1, ?d)
                                    ",
                                    $this->Query,
                                    $TotalRows
            );
        }
    }
	
	private function inflect($word)
	{
		static $gl = array('а', 'я', 'о', 'ё', 'ы', 'и', 'е', 'э', 'у', 'ю');
		static $end = array('а', 'ам', 'ами', 'ах', 'ая', 'о', 'ом', 'е', 'ей', 'ем', 'ет', 'ешь', 'ею', 'и', 'им', 'ишь', 'у', 'ут', 'ую', 'ю', 'юю', 'ить', 'еть', 'ать', 'ыть', 'оть', 'ять');
		
		$word = iconv('utf-8', 'windows-1251', $word);
		foreach ($gl as &$words) $words = iconv('utf-8', 'windows-1251', $words);
		foreach ($end as &$words) $words = iconv('utf-8', 'windows-1251', $words);
		
		$last = strlen($word) - 1;
		$ending = $word[$last];
		$inflect = array();
		
		if (in_array($ending, $gl)) {
			$word = substr($word, 0, -1);
		}
		
		$inflect[] = $word;

		foreach ($end as $g) {
			$temp = $word;
			$word .= $g;
			$inflect[] = $word;
			$word = $temp;
		}			
		
		foreach ($inflect as &$words) $words = iconv('windows-1251', 'utf-8', $words);
		$inflect = join(" ", $inflect);
		return $inflect;
	}

    public function Prepare(&$ds)
    {
        if (isMobile()) {
            $ds->assign("is_mobile", 'mobile');
        } else {
            $ds->assign("is_mobile", 'not mobile');
        }
        $ds->assign("results", $this->Results);
		$ds->assign("_query", $this->Query);
		$ds->assign("title_part", "ПОИСК");
		$ds->assign("groups", $this->Groups);
        $ds->assign("SListThemes", $this->SListThemes);
		
		/*
        require_once(LIB_DIR.'search/LinguaStemRu.php');
        $stemmer = new Lingua_Stem_Ru();
		$stemmer->stem_word('Котёровыми');
		
	    require_once(LIB_DIR.'system/stem.php');
		Debug(stem('начинался'));
		*/
	}

    public function Display(&$parser)
    {
        $parser->display($this->MainTemplate);
    }
}
?>