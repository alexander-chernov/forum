<?php
class Vote_Manager
{
	var $GroupID = 0;
	var $ThemeID = 0;
	function __construct()
	{
		$this->Form = CreateObject("Form_Manager");
		$this->DbManager = $GLOBALS['ForumCore']->DbManager;
		$this->AuthManager = CreateObject("Auth_Manager");
		$this->_url_params = $GLOBALS['ForumCore']->_url_params;
		list($_null,$forum,$this->GroupID,$this->ThemeID) = $this->_url_params;
	}
	public function onEvent_ForumVoteIt($form)
	{
		$this->Form = $form;
		$_vote = $this->Form->Request['vote'];
		foreach ($vote['answer'] as $key => $_val)
		{
			if ($_val > 0)
			{
				$this->DbManager->query("UPDATE ?# SET `answers`=`answers`+1 WHERE `answerID`=?d AND voteID=?d",'forum_db_vote_answers',$_val,$_vote['id']);
			}
		}
		$this->DbManager->query(
							"INSERT INTO 
								?# 
							SET 
								`userIP`=INET_ATON(?), 
								`userID`=?d, 
								`voteID`=?d",
							'forum_db_vote_stat',
							$_SERVER['REMOTE_ADDR'],
							$this->AuthManager->User->userID,
							$_vote['id']
						);
	}
	public function Prepare()
	{
	}
//---------------Вывод шаблона
	public function Display(&$parser)
	{
		$parser->display($this->MainTemplate);
	}
/*
 * получаем данные статистики голосования
 *  
 */
	public function GetVoteStat($_voteId)
	{
		$_question = $this->DbManager->selectrow("SELECT * FROM ?# WHERE voteID=?d",'forum_db_vote_questions',$_voteId);
		$_question['answers'] = $this->GetAnswers($_questoin['voteID']);
	}
	
	public function GetQuestion()
	{
		$_question = $this->DbManager->selectrow(
											"SELECT 
												q.* 
											FROM 
												?# q 
											WHERE 
												active=1 
												AND (q.groupID=0 OR q.groupID=?d) 
												AND (q.themeID=0 OR q.themeID=?d) 
												AND q.voteID NOT IN (SELECT voteID FROM ?# WHERE userIP=INET_ATON(?)) 
											LIMIT 1",
											'forum_db_vote_questions',
											($this->GroupID >0)?$this->GroupID:0,
											($this->ThemeID >0)?$this->ThemeID:0,
											'forum_db_vote_stat',
											$_SERVER['REMOTE_ADDR']
										);
		if ($_question['voteID'])
		{
			$_question['answers'] = $this->GetAnswers($_questoin['voteID']);
			return $_question;
		}
		else
		{
			return false;
		}
	}
	function GetAnswers($_voteID)
	{
		return  $this->DbManager->select(
												"SELECT * FROM ?# WHERE voteID=?d",
												'form_db_vote_answers',
												$_voteID
											); 
	}
}