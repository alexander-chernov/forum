<?php
class Forum_Admin
{
	var $MainTemplate = 'admin/main.tpl';
	
	var $Params = array();
	var $Errors = array();
	var $Messages = array();
    var $Pictures = array();
	var $Filters = array();
	var $BanTypes = array();
	
	/* Привилегии */
	var $adminID = null;
	var $maxBan = null;
	var $netBan = null;
	var $userGroups = null;
	var $canConfirm = null;
	var $adminNotes = null;
	var $commercial = null;
	
	/* Params */
	var $groupID = null;
	var $themeID = null;
	var $userID = null;
	var $messageID = null;
	var $noteID = null;
	var $banID = null;
	
	/* Filters */
	var $banType = null;
	var $wordFilter = null;
	var $comFilter = null;
	var $colorFilter = null;
	var $searchIP = null;
	
	/* List of commerce themes */
	var $CommerceGroups = array();
	
	var $Page = 1;
	var $CountPerPage = 30;
	var $TotalPages = 1;
	
	var $objEditId = 0;
	
	var $Folder = 'forum';			//контроллер по-умолчанию
	var $Action = 'groups';			//действие по-умолчанию
	var $PageTitle = null;
	
	var $DataGrid = null;			//данные таблиц
	var $TableHeaders = null;		//заголовки таблиц

	var $banPeriod = array(86400 => '1 день',
							172800 => '2 дня',
							259200 => '3 дня',
							604800 => '1 неделя',
							2419200 => '1 месяц',
							4838400 => '2 месяца',
							7257600 => '3 месяца',
							14515200 => '6 месяцев',
							29030400 => '1 год',
							300000000 => 'Навсегда');
							
	var $locMsg = array(1 => 'Операция выполнена');
	

	public function __construct()
	{
		//session_start();
		$this->_url_params = $GLOBALS['ForumCore']->_url_params;
		$this->DbManager = $GLOBALS['ForumCore']->DbManager;
		
		$this->BanTypes = $GLOBALS['ForumCore']->Protector->Locale;	
		$this->checkUserRights();

		
		if (isset($_REQUEST['_msg'])){
			if (!is_array($_REQUEST['_msg'])){
				$_REQUEST['_msg'] = array(is_array($_REQUEST['_msg']));
			}
			foreach ($_REQUEST['_msg'] as $value){
				if (isset($this->locMsg[$value]))
					$this->Messages[] = $this->locMsg[$value];
			}
				
		}
		
		if (isset($_GET['p']) && $_GET['p'] > 0)
		{
			$this->Page = intval($_GET['p']);
		}

		//Детектируем фильтры
		if (count($_POST['filter']) > 0) {
			foreach ($_POST['filter'] as $key => $value) $this->Filters[$key] = $value;
		}
		
		//Определяем action по URL
		$folder = $this->_url_params[2];
		if (!$folder) $folder = $this->Folder;
		
		$action = $this->_url_params[3];
		if (!$action) $action = $this->Action;
		
		if (isset($this->_url_params[4])) $action .= '_'.$this->_url_params[4];
		else $action .= '_index';

		if ($action != '_') $this->Action = $action;
		$this->getMoreParams();
		
		//Вызываем запрошенный action
		$method = 'action_'.$this->Action;
		if (method_exists(__CLASS__, $method)) {
			$this->configureAction($this->Action);
			call_user_func(array(__CLASS__, $method));
		}
	}

	private function action_addmoney_index(){
		if ($this->commercial != 1){
			header('Location: /.admin/');
			exit;
		}
		if (isset($_GET['userid']) && isset($_GET['_addmoney'])){
			$_user = $this->DbManager->selectrow(
						"SELECT
								*
							FROM
								?#
							WHERE
								`userID` = ?d
						",
						'forum_users',
						$_GET['userid']
				);

			if (count($_user)>0){
				$this->Params['_msg'] = array('Вы пополнили баланс пользователя "' . $_user['user_name'] . '" на сумму ' . $_GET['_addmoney'] . '. И баланс составляет ' . $_user['user_balance']);
			}
		}
		$this->Params['obj']['action'] = 'Пополнение через кассу';
	}
	
	public function onEvent_AddMoney($form)
	{
		if ($this->commercial != 1){
			header('Location: /.admin/');
			exit;
		}
		$_user = $this->DbManager->selectrow(
					"SELECT
							*
						FROM
							?#
						WHERE
							`userID` = ?d
					",
					'forum_users',
					$form->Request['_data']['userid']
			);

		if (count($_user['user_balance']) == 0){
			$this->Errors[] = 'Пользователь не найден';
		}

		if (isset($_user['user_balance']) && ($_user['user_balance'] + (float)$form->Request['_data']['money']) < 0){
			$this->Errors[] = 'Нельзя отнять такую сумму, баланс пользователя ' . $_user['user_balance'];
		}

		if ((int)$form->Request['_data']['money'] == 0){
			$this->Errors[] = 'Сумма должна быть больше ноля';
		}
		if ((float)$form->Request['_data']['money']<0){
			$znak = '';
		}else{
			$znak = '+';
		}

		if (count($this->Errors) == 0){
			$_sql = "UPDATE ?# SET `user_balance` = (`user_balance` " . $znak . (float)$form->Request['_data']['money'] . ")
									WHERE `userID` = ?d LIMIT 1";
			$this->DbManager->query($_sql, 'forum_users', $_user['userID']);
			
			$_sql = "INSERT INTO ?# SET
								`userid` = ?d,
								`username` = ?s,
								`action` = ?s,
								`message` = ?s,
								`status` = 0,
								`payment` = ?s
								";
			$this->DbManager->query($_sql,
										'merchant_logactions',
										$_user['userID'],
										$_user['user_name'],
										$form->Request['_data']['action'],
										(trim($form->Request['_data']['comment']) != '' ? $form->Request['_data']['action'] . "\r\n" : "") . "Сумма внесена Модератором(\"" . $GLOBALS['ForumCore']->AuthManager->User->user_name . " [" . $GLOBALS['ForumCore']->AuthManager->User->userID . "]\")",
										$form->Request['_data']['money']
										);
			header('Location: /.admin/commercial/addmoney/?userid=' . (int)$form->Request['_data']['userid'] . "&_addmoney=" . (float)$form->Request['_data']['money']);
			exit;
		}
		$this->Params['obj'] = $form->Request['_data'];
	}

	//Проверка доступа
	private function checkUserRights() {
		if ($GLOBALS['ForumCore']->AuthManager->User->is_admin == 1) {
			$this->adminID = $GLOBALS['ForumCore']->AuthManager->User->userID;
			
			//Get rights
			$rights = $this->DbManager->select("SELECT
							*
						FROM
							?#
						WHERE
							`userID` = ?d
					",
					'forum_users_rights',
					$this->adminID
			);
			
			if (count($rights) > 0) {
				$this->maxBan = $rights[0]['max_ban'];
				$this->userGroups = split(",", $rights[0]['groups']);
				$this->canConfirm = (int)$rights[0]['can_confirm'];
				$this->adminNotes = (int)$rights[0]['admin_notes'];
				$this->netBan = (int)$rights[0]['net_ban'];
				$this->commercial = (int)$rights[0]['commercial'];

				$this->Params['confirm'] = $this->canConfirm;
				$this->Params['admin_notes'] = $this->adminNotes;
			}
		}
		else {
			$form = '
				<form id="login_form" action="" method="post">
				<input type="hidden" value="cmsauthuserbyform" name="event"/>
				<table style="border: 1px solid #000">
					<tr>
						<td><label class="auth_login" for="username">Логин:</label></td>
						<td><input type="text" maxlength="20" value="" class="auth_login_inp" id="username" name="user_name"/></td>
					</tr>
					<tr>
						<td><label class="auth_passw" for="password">Пароль:</label></td>
						<td><input type="password" maxlength="20" value="" class="auth_login_inp" id="password" name="user_password"/></td>
					</tr>
					<tr>
						<td align="right" colspan="2"><input type="submit" value="вход" id="submit_auth" name="Submit"/></td>
					</tr>
				</table>
				</form>';
			exit($form);
		}
	}
	
	//Настройка action'а
	private function configureAction($action) {
		require_once(MOUNT_DIR.'/core/config/admin.cfg.php');
		$cfg = $Admin_Cfg[$action];
		
		if (is_array($cfg)) {
			$this->Folder = $cfg['folder'];
			$this->PageTitle = $cfg['title'];
			$this->TableHeaders = $cfg['headers'];
		}
	}
	
	private function loadGroups($type = null)
	{
		$_groups = $this->DbManager->select("SELECT
				`groupID`, `caption`, `commerce`
			FROM 
				?# 
			ORDER BY 
				`caption` ASC
			",
			"forum_db_groups"
		);	
		
		$data = array();
		foreach ($_groups as $group) {
			//Вставлять ли проверку на права?
			// && in_array($group['groupID'], $this->userGroups)
			if ($group['groupID'] != $this->groupID) {
				if (isset($type)) $data[$group['groupID']] = $group;
				else $data[$group['groupID']] = $group['caption'];
			}
						
			if ($group['groupID'] == $this->groupID) {
				if ($group['commerce'] == 1) {
					$this->Params['currentGroupIsCommerce'] = 1;
				}
				$this->Params['groupName'] = $group['caption'];
				$this->Params['groupParentId'] = $this->groupID;
			}
			
			if ($group['commerce'] == 1) {
				$this->CommerceGroups[] = $group['groupID'];
			}
		}
		
		$this->Params['groups'] = $data;
	}
	
	private function loadThemes($ds)
	{
		if (count($ds) > 0) {
			$data = $this->DbManager->select("SELECT
					`groupID`, `themeID`, `caption`
				FROM 
					?# 
				WHERE
					`themeID` IN (?a)
				",
				"forum_db_themes",
				$ds
			);	
			
			$dump = array();
			foreach ($data as $val) $dump[$val['themeID']] = $val['caption'];
			$this->Params['themes'] = $dump;
		}
	}
	
	private function loadAuthors($ds)
	{
		if (count($ds) > 0) {
			$data = $this->DbManager->select("SELECT
					`userID`, `user_name`
				FROM 
					?# 
				WHERE
					`userID` IN (?a)
				",
				"forum_users",
				$ds
			);	
			
			$dump = array();
			foreach ($data as $val) $dump[$val['userID']] = $val['user_name'];
			$this->Params['authors'] = $dump;
		}
	}
	
	private function loadRules()
	{
		return $this->DbManager->select("SELECT
				`ruleID`, `caption`
			FROM 
				?# 
			ORDER BY 
				`ruleID` ASC
			",
			"forum_rules"
		);	
	}
	
	//Получаем доп. параметры (ID группы, etc.)
	//Проверка на права - тут же
	private function getMoreParams() {
		//Получаем название группы в списке тем	
		if ($this->Action == 'themes_index') {
			$this->groupID = $this->_url_params[5];
			$this->loadGroups(true);			
			$this->user_rights_check();
		}
		if (isset($_POST['_objId'])){
			$this->objEditId = $_POST['_objId'];
		}
		//Получаем название темы в списке сообщений
		elseif ($this->Action == 'messages_index') {
			if (isset($this->_url_params[5]) && $this->_url_params[5] != 'commerce') {
				$this->themeID = $this->_url_params[5];
				
				$temp = $this->DbManager->select(
						"SELECT
								`caption`, `groupID`, `messages`, `created`
							FROM
								?#
							WHERE
								`themeID` = ?d
						",
						'forum_db_themes',
						$this->themeID
				);
				
				$this->TotalPages = $temp[0]['messages'];
				$this->Params['themeName'] = $temp[0]['caption'];
				$this->Params['themeParentID'] = $this->themeID;
				$this->Params['themeID'] = $this->themeID;
				$this->groupID = $temp[0]['groupID'];
				$this->Params['groupID'] = $this->groupID;
				//$this->Params['created'] = $temp[0]['created'];
				
				$this->user_rights_check();
			}
			else {
				$this->comFilter = $this->_url_params[6];
			}
			
			$this->loadGroups();
		}
		elseif ($this->Action == 'stoplight_index') {
			$this->loadGroups();
			
			if ($this->_url_params[5] == 'color') {
				$this->colorFilter = $this->_url_params[6];
			}
		}
		elseif ($this->Action == 'messages_filter') {
			$this->loadGroups();
		}
		//Получаем правила для бана
		elseif ($this->Action == 'nicknames_edit') {
			$this->Params['rules'] = $this->loadRules();
			$this->userBanID = $this->_url_params[5];
			$this->Params['banPeriod'] = $this->banPeriod;
		}
		elseif ($this->Action == 'userlist_index') {
			$this->Params['rules'] = $this->loadRules();
		}
		elseif ($this->Action == 'nicknames_list') {
			$this->Params['rules'] = $this->loadRules();
		}
		elseif ($this->Action == 'stoplight_complaint') {
			$temp = $this->loadRules();
			foreach ($temp as $key => &$value) {
				$this->Params['rules'][$value['ruleID']] = $value['caption'];
			}
		}
		//Присваиваем доп.параметры
		elseif ($this->Action == 'themes_edit' && isset($this->_url_params[5])) {
			$this->themeID = $this->_url_params[5];
		}
		elseif ($this->Action == 'package_edit' && isset($this->_url_params[5])) {
			$this->objEditId = $this->_url_params[5];
		}
		elseif ($this->Action == 'messages_edit' && isset($this->_url_params[5])) {
			$this->messageID = $this->_url_params[5];
		}
		elseif ($this->Action == 'userlist_edit' && isset($this->_url_params[5])) {
			$this->userID = $this->_url_params[5];
		}
		elseif ($this->Action == 'words_edit' && isset($this->_url_params[5])) {
			$this->wordID = $this->_url_params[5];
		}
		elseif ($this->Action == 'notes_edit' && isset($this->_url_params[5])) {
			$this->noteID = $this->_url_params[5];
		}
		elseif ($this->Action == 'networks_edit' && isset($this->_url_params[5])) {
			$this->netBanID = $this->_url_params[5];
		}
		elseif ($this->Action == 'ip_edit' && isset($this->_url_params[5])) {
			$this->banID = $this->_url_params[5];
			$this->Params['rules'] = $this->loadRules();
		}
		elseif ($this->Action == 'ip_index' && $this->_url_params[5] == 'type') {
			$this->banType = (int)$this->_url_params[6];
		}
		elseif ($this->Action == 'ip_index' && $this->_url_params[5] == 'search') {
			$this->searchIP = $this->_url_params[6];
		}
		elseif ($this->Action == 'words_index' && $this->_url_params[5] == 'type') {
			$this->wordFilter = 1;
		}
		elseif ($this->Action == 'pager_send' && $this->_url_params[5] == 'to') {
			$this->Params['userto'] = (int)$this->_url_params[6];
			
			if ($this->adminID == $this->Params['userto']) $this->Errors[] = 'Вы не можете отправлять сообщения самому себе!'; 
			
			$this->Params['user_name'] = $this->DbManager->selectcell(
					"SELECT
							`user_name`
						FROM
							?#
						WHERE
							`userID` = ?d
					",
					'forum_users',
					$this->Params['userto']
			);
			
			if (strlen($this->Params['user_name']) == 0) $this->Errors[] = 'Указан неверный ID пользователя!'; 
		}
	}
	
	private function user_rights_check() 
	{	
		if (!in_array($this->groupID, $this->userGroups)) {
			$this->Action = 'error_index';
			$this->Errors[] = 'У вас нет доступа к этой странице';
		}
	}
	private function action_commerce_index()
	{
		//$_commerce_group = $this->DbManager->selectcol("SELECT `groupID` FROM ?# WHERE `commerce`=1","forum_db_groups");
		$this->DataGrid = $this->DbManager->selectPage($this->TotalPages,
										"SELECT 
											g.`caption` as `themename`,
											m.`content` as `message`,
											INET_NTOA(m.`author_ip`) as `author_ip2`,
											m.*,
											t.*
										FROM 
											?# t 
											LEFT JOIN ?# m ON (t.messageID=m.messageID AND t.themeID=m.themeID)
											LEFT JOIN ?# g ON (t.themeID=g.themeID)
                                        WHERE
                                            t.hidden = 0
                                            AND m.hidden = 0
                                            AND g.hidden = 0
                                        ORDER BY t.id DESC
										LIMIT ?d, ?d",
                                        'forum_messages_attaches',
                                        'forum_db_messages',
										'forum_db_themes',
										($this->Page - 1) * $this->CountPerPage,
										$this->CountPerPage
								);

        $collect = array();					//collect IDs for faster js checker
        foreach ($this->DataGrid as &$row) {
            $collect[] = $row['id'];
        }
        $this->Params['id'] = join(',', $collect);

	}
	private function action_error_index()
	{
			//Nothing is neccesary here
	}
	
	/* 
	 *	Список действий (actions)
	 *	Порядок работы каждой функции:
	 *	1. Получение данных из таблиц
	 *  2. Фильтрация данных (long2ip etc.)
	 */
	 
	private function action_groups_index()
	{
		$this->DataGrid = $this->DbManager->select("SELECT
				*
			FROM 
				?#
			WHERE
				groupID IN (?a)
			ORDER BY
				`caption` ASC
			",
			"forum_db_groups",
			$this->userGroups
		);

		foreach ($this->DataGrid as &$row) {
			$row['themes'] = (int)$row['themes'];
		}	
		
		//Get also admin notes
		$this->Params['notes'] = $this->DbManager->select("SELECT
				*
			FROM 
				?#
			ORDER BY
				messageID DESC
			LIMIT
				0, 50
			",
			"forum_admin_notes"
		);
	}
	
	private function action_themes_index()
	{
		$this->DataGrid = $this->DbManager->selectPage(
			$this->TotalPages, 
			"SELECT
				*,
				INET_NTOA(`author_ip`) `author_ip2`
			FROM 
				?#
			WHERE 
				1=1
				{ AND `groupID` = ?d }
			ORDER BY
				`is_top` DESC,
				`updated` DESC
			LIMIT
				?d, ?d
			",
			"forum_db_themes",
			(empty($this->groupID)? DBSIMPLE_SKIP : $this->groupID),
			($this->Page - 1) * $this->CountPerPage,
			$this->CountPerPage
		);
		
		$collect = array();					//collect IDs for faster js checker
		$counter = 0;
		foreach ($this->DataGrid as &$row) {
			$collect[] = $row['themeID'];
			if ($row['author_ip'] != 0 && $row['author_ip'] != 2147483647) {
				$row['author_ip'] = $row['author_ip2'];
			}
			else {
				$iptemp = split(", ", $row['s_ip']); 
				$row['author_ip'] = $iptemp[0];
			}
			$row['messages'] = (int)$row['messages'];
			
			$counter++;
			$row['counter'] = $counter + ($this->Page - 1) * $this->CountPerPage;
		}	
		$this->Params['themeIDs'] = join(',', $collect);
	}
	
	private function action_themes_edit()
	{
		$this->DataGrid = $this->DbManager->selectRow(
			"SELECT
				*
			FROM 
				?#
			WHERE 
				`themeID` = ?d
			",
			"forum_db_themes",
			$this->themeID
		);
	}
	
	private function getMessagesByTheme()
	{
		$this->DataGrid = $this->DbManager->select(
			"SELECT
				m.*,
				INET_NTOA(m.author_ip) as author_ip2,
                u.user_name as hide_author,
                h.datetime as hide_time
			FROM 
				?# m
            LEFT JOIN ?# h ON m.messageID = h.messageID
            LEFT JOIN ?# u ON h.userID = u.userID
			WHERE
				 m.is_deleted = 0
				{ AND m.themeID = ?d }
				{ AND m.created >= ?s }
			ORDER BY
				messageID DESC
			LIMIT 
				?d, ?d
			",
			"forum_db_messages",
            "forum_db_hide_log",
            "forum_users",
			(empty($this->themeID)? DBSIMPLE_SKIP : $this->themeID),
			(isset($this->Params['created']) ? $this->Params['created'] : DBSIMPLE_SKIP),
			($this->Page - 1) * $this->CountPerPage,
			$this->CountPerPage
		);
		
		$collect = array();					//collect IDs for faster js checker
		$idents = array();						//collect userIDs for real nick names
		$themes = array();
		$counter = 0;
		foreach ($this->DataGrid as &$row) {
			$collect[] = $row['messageID'];
			if ($row['author_ip'] != 0 && $row['author_ip'] != 2147483647) {
				$row['author_ip'] = $row['author_ip2'];
			}
			else {
				$iptemp = split(", ", $row['old_ip']); 
				$row['author_ip'] = $iptemp[0];
			}
			
			if ($row['authorID']) $idents[] = $row['authorID'];
			$themes[] = $row['themeID'];
			
			$counter++;
			$row['counter'] = $counter + ($this->Page - 1) * $this->CountPerPage;
		}
		$this->Params['messageIDs'] = join(',', $collect);
		
		//$this->loadAuthors(array_unique($idents));
		if (!isset($this->ThemeID)) $this->loadThemes(array_unique($themes));
	}
	
	private function getPicturesList() {
        $this->DataGrid = $this->DbManager->select("-- CACHE: 0h 10m 00s
            SELECT
                *
            FROM
                ?#
            { LIMIT
                 ?d, ?d }
            ",
            "forum_messages_attaches",
            (($this->Page - 1) * $this->CountPerPage), // : DBSIMPLE_SKIP
            ($this->CountPerPage)
        );
        $collect = array();
        foreach ($this->DataGrid as &$row) {
            $collect[] = $row['id'];
        }
        $this->Params['pictureIDs'] = join(',', $collect);
    }
    private function getUnreadMessages()
	{
        $this->CountPerPage = 10;
		$this->DataGrid = $this->DbManager->select("-- CACHE: 1h 0m 00s
			SELECT 
				*,
				INET_NTOA(`author_ip`) `author_ip2`
			FROM
				?#
			WHERE 
				`is_deleted` = 0 
				AND `hidden` = 0 
				AND `is_moderated` = 0
				AND `created` > (NOW() - INTERVAL 1 DAY )
				{ AND `groupID` IN (?a) }
				{ AND `groupID` NOT IN (?a) }
				{ AND `themeID` = ?d }
			ORDER BY
				messageID DESC
			{ LIMIT 
				 ?d, ?d }
			",
			"forum_db_messages",
			(($this->comFilter == 'yes') ? $this->CommerceGroups : DBSIMPLE_SKIP),
			(($this->comFilter == 'no') ? $this->CommerceGroups : DBSIMPLE_SKIP),
			(empty($this->themeID) ? DBSIMPLE_SKIP : $this->themeID),
			(($this->comFilter != 'all') ? ($this->Page - 1) * $this->CountPerPage : DBSIMPLE_SKIP),
			(($this->comFilter != 'all') ? $this->CountPerPage : DBSIMPLE_SKIP)
		);
		//(empty($this->comFilter) ? $this->userGroups : DBSIMPLE_SKIP),
		
		$collect = array();					//collect IDs for faster js checker
		$idents = array();;					//collect userIDs for real nick names
		$themes = array();
		$counter = 0;
		foreach ($this->DataGrid as &$row) {
			$collect[] = $row['messageID'];
			if ($row['author_ip'] != 0 && $row['author_ip'] != 2147483647) {
				$row['author_ip'] = $row['author_ip2'];
			}
			else {
				$iptemp = split(", ", $row['old_ip']); 
				$row['author_ip'] = $iptemp[0];
			}
			
			if ($row['authorID']) $idents[] = $row['authorID'];
			$themes[] = $row['themeID'];
			
			$counter++;
			$row['counter'] = $counter + ($this->Page - 1) * $this->CountPerPage;
		}
		$this->Params['messageIDs'] = join(',', $collect);
		
		//$this->loadAuthors(array_unique($idents));
		if (!isset($this->ThemeID)) $this->loadThemes(array_unique($themes));
	}
	
	
    private function action_pictures_index()
    {
        $this->getPicturesList();
    }

	private function action_messages_index()
	{
		if ($this->themeID > 0) {
			$this->getMessagesByTheme();
		}
		else {
			$this->getUnreadMessages();
		}
	}
	
	private function action_messages_filter()
	{
		if (isset($_GET['init'])) {
			$this->Params['_filter']['init'] = $_GET['init'];
		}
		$this->Params['_filter']['end'] = date("Y-m-d H:i:s", time());
		if (isset($_GET['addr'])) {
			if (!isset($this->Params['_filter']['init'])){
				$this->Params['_filter']['init'] = date("Y-m-d H:i:s", time() - 60*60*24*5);
			}
			$this->Params['_filter']['addr'] = $_GET['addr'];
		}
	}
	
	private function action_messages_edit()
	{
		$this->DataGrid = $this->DbManager->selectRow("SELECT
				*
			FROM 
				?#
			WHERE 
				`messageID` = ?d
			",
			"forum_db_messages",
			$this->messageID
		);
	}
	
	private function action_stoplight_index() 
	{
        $this->CountPerPage = 10;
		$this->DataGrid = $this->DbManager->select("-- CACHE: 0h 10m 00s
			SELECT 
				*,
				INET_NTOA(`author_ip`) `author_ip2`
			FROM 
				?#
			WHERE 
				`hidden` = 0
				AND `is_deleted` = 0
				AND `created` > (NOW() - INTERVAL 3 DAY )
				{ AND `danger_level` >= ?d }
				{ AND `danger_level` >= ?d }
				{ AND `danger_level` <= ?d }
				{ AND `danger_level` >= ?d }
			ORDER BY
				`danger_level` DESC
			LIMIT
				?d, ?d
			",
			"forum_db_messages",
			(($this->colorFilter == null) ? DANGER_LEVEL_YELLOW : DBSIMPLE_SKIP),
			(($this->colorFilter == 'yellow') ? DANGER_LEVEL_YELLOW : DBSIMPLE_SKIP),
			(($this->colorFilter == 'yellow') ? DANGER_LEVEL_RED : DBSIMPLE_SKIP),
			(($this->colorFilter == 'red') ? DANGER_LEVEL_RED : DBSIMPLE_SKIP),
			($this->Page - 1) * $this->CountPerPage,
			$this->CountPerPage
		);
		
		$collect = array();					//collect IDs for faster js checker
		$idents = array();						//collect userIDs for real nick names
		$themes = array();
		$counter = 0;
		foreach ($this->DataGrid as &$row) {
			$collect[] = $row['messageID'];
			if ($row['author_ip'] != 0 && $row['author_ip'] != 2147483647) {
				$row['author_ip'] = $row['author_ip2'];
			}
			else {
				$iptemp = split(", ", $row['old_ip']); 
				$row['author_ip'] = $iptemp[0];
			}
			
			if ($row['danger_level'] >= DANGER_LEVEL_RED) $row['color'] = '#F20';
			else $row['color'] = '#FF0';
			if ($row['authorID']) $idents[] = $row['authorID'];
			$themes[] = $row['themeID'];
			
			$counter++;
			$row['counter'] = $counter;
		}
		$this->Params['messageIDs'] = join(',', $collect);
		
		//$this->loadAuthors(array_unique($idents));
		$this->loadThemes(array_unique($themes));
	}	
	
	private function action_stoplight_complaint() 
	{
		//Get complaint list
		$msg_id = (int)$_GET['mid'];
		
		if ($msg_id > 0) {
			$this->DataGrid = $this->DbManager->select("SELECT
					`userID`, `ruleID`
				FROM 
					?#
				WHERE 
					`messageID` = ?d
				",
				"forum_users_complaint",
				$msg_id
			);
			
			if (count($this->DataGrid > 0)) {
				$idents = array();;					//collect userIDs for real nick names
				foreach ($this->DataGrid as &$row) $idents[] = $row['userID'];
				$this->loadAuthors(array_unique($idents));				
			}
			else {
				$this->Errors[] = 'Жалоб на сообщение не найдено';
			}
		}
		else {
			$this->Errors[] = 'Неверный ID сообщения';
		}
	}
	
	private function action_userlist_index() 
	{
		$this->DataGrid = $this->DbManager->selectPage(
			$this->TotalPages,"-- CACHE: 0h 10m 00s
            SELECT
				*,
				INET_NTOA(`user_ip`) `user_ip`
			FROM 
				?#
			ORDER BY 
				`user_name` ASC
			LIMIT
				?d, ?d
			",
			"forum_users",
			($this->Page - 1) * $this->CountPerPage,
			$this->CountPerPage
		);

		$collect = array();					//collect IDs for faster js checker
		foreach ($this->DataGrid as &$row) {
			$collect[] = $row['userID'];
			$row['user_ip'] = $row['user_ip'];
		}
		$this->Params['userIDs'] = join(',', $collect);
	}
	
	private function action_userlist_edit()
	{
		$this->DataGrid = $this->DbManager->selectRow(
			"SELECT
				*
			FROM 
				?#
			WHERE 
				`userID` = ?d
			",
			"forum_users",
			$this->userID
		);
	}

	public function onEvent_ForumAddPackage($form)
	{
		if ($this->commercial != 1)
			return false;

		$merchant = CreateObject('Money_Tariffication', array('DbManager' => $this->DbManager));
		if ((int)$this->objEditId>0){
			$_POST['pack']['id'] = $this->objEditId;
			if (!isset($_POST['pack']['mayup'])){
				$_POST['pack']['mayup'] = 0;
			}
			if (!isset($_POST['ingroup']) || !is_array($_POST['ingroup'])){
				$_POST['pack']['groups_id'] = '';
			}else{
				$_POST['pack']['groups_id'] = $merchant->mkArray2String($_POST['ingroup']);
			}
		}

		$_res = $merchant->writeData($_POST['pack'], 'package');

		$this->Params['pack'] = $_res['data'];
		$this->Params['pack']['lifetime'] = $this->Params['pack']['lifetime'];
		$inpack = $form->Request["inpack"];
		$sList = $form->Request["sList"];
		$idList = $form->Request["idList"];

		if (!isset($sList) || !is_array($sList)){
			$sList = array();
		}

		if (!isset($inpack) || !is_array($inpack)){
			$inpack = array();
		}

		if ((int)$_res['data']['id']>0 && count($_res['check']['error']) == 0){
			if (is_array($inpack) && count($inpack)>0 && (int)$this->objEditId>0){
				$this->DbManager->query("DELETE FROM ?# WHERE `packageid` = ?d
										AND `serviceid` NOT IN (?a)",
									"merchant_serviceinpackage",
									$this->objEditId,
									$inpack);

			}elseif((int)$this->objEditId>0){
				$this->DbManager->query("DELETE FROM ?# WHERE `packageid` = ?d",
									"merchant_serviceinpackage",
									$this->objEditId);
			}
		}
		$_sErrors = array();

		foreach ($inpack as $key=>$value){
			if (isset($sList[$value])){
				$_resServ = array();
				$_servData = array(
									'id' => $idList[$value],
									'packageid' => (int)$_res['data']['id'],
									'serviceid' => $value,
									'periodical' => $sList[$value]['periodical'],
									'period' => $sList[$value]['period'],
									'acttime' => $sList[$value]['acttime']
								);

				if ((int)$_res['data']['id']>0 && count($_res['check']['error']) == 0){
					$_resServ = $merchant->writeData($_servData, 'servpackage');
				}
				
				foreach ($this->Params['listServices'] as $key3=>$value3){
					if ($_servData['serviceid'] == $value3['sId']){
						$_name = $this->Params['listServices'][$key3]['name'];
						$this->Params['listServices'][$key3] = $_servData;
						$this->Params['listServices'][$key3]['sId'] = $_servData['serviceid'];
						$this->Params['listServices'][$key3]['name'] = $_name;
						$this->Params['listServices'][$key3]['isSelected'] = 1;
						$_id = $key3;
					}
				}
				if (isset($_resServ['check']['error'])){
					foreach ($_resServ['check']['error'] as $value2){
						$_sErrors[] = $value2;
					}
				}
			}
		}

		if ((int)$_res['data']['id']>0 && count($_res['check']['error']) == 0 && count($_sErrors) == 0){
			header('Location: /.admin/commercial/package/edit/' . $_res['data']['id'] . '/');
			exit;
		}elseif(count($_res['check']['error'])>0 || count($_sErrors) > 0){
			$this->Params['objID'] = (int)$_res['data']['id'];
			foreach ($_res['check']['error'] as $value){
				$this->Errors[] = $value;
			}
			foreach ($_sErrors as $value){
				$this->Errors[] = $value;
			}
		}
	}

	public function onEvent_ForumPackageDelete($form)
	{
		if ($this->commercial != 1){
			header('Location: /.admin/');
			exit;
		}
		$hidId = $form->Request["hidId"];
		$merchant = CreateObject('Money_Tariffication', array('DbManager' => $this->DbManager));
		$_cnt = $merchant->checkPackInUser($hidId);
		if ($_cnt == 0){
			foreach ($hidId as &$value){
				$merchant->deletePackage($value);
			}
			header('Location: /.admin/commercial/package/');
			exit;
		}else{
			$this->Errors[] = 'Один из выбранных пакетов не может быть удален';
		}
	}
	
	public function onEvent_ForumPackageActive($form, $_dsp = 1)
	{
		if ($this->commercial != 1){
			header('Location: /.admin/');
			exit;
		}
		$hidId = $form->Request["hidId"];
		if ($form->Request["hidId"] && is_array($hidId) && count($hidId)){
			$merchant = CreateObject('Money_Tariffication', array('DbManager' => $this->DbManager));
			$_res = array();
			foreach ($hidId as &$value){
				if ((int)$value > 0){
					$_res[$value] = $merchant->writeData(array('id' => $value, 'isactive' => $_dsp), 'packactive');
				}
			}
			
			if (isset($this->DataGrid) && is_array($this->DataGrid) && count($this->DataGrid) > 0){
				foreach ($this->DataGrid as &$value){
					if (in_array($value['id'], $hidId) && count($_res[$value['id']]['check']['error']) == 0){
						$value['isactive'] = $_dsp;
					}elseif(count($_res[$value['id']]['check']['error']) > 0 && $_dsp == 0){
						$this->Errors[] = 'Пакет ' . $value['name'] . ' не активирован, используется';
					}
				}
			}
		}
	}

	public function onEvent_ForumPackageUnActive($form)
	{
		if ($this->commercial != 1){
			header('Location: /.admin/');
			exit;
		}
		$this->onEvent_ForumPackageActive(&$form, 0);
	}
	
	public function onEvent_ForumPackageDisplay($form, $_dsp = 1)
	{
		if ($this->commercial != 1){
			header('Location: /.admin/');
			exit;
		}
		$hidId = $form->Request["hidId"];
		if ($form->Request["hidId"] && is_array($hidId) && count($hidId)){
			$merchant = CreateObject('Money_Tariffication', array('DbManager' => $this->DbManager));
			$_res = $merchant->writeData(array('id' => $hidId, 'display' => $_dsp), 'packdisplay');
			
			if (isset($this->DataGrid) && is_array($this->DataGrid) && count($this->DataGrid) > 0){
				foreach ($this->DataGrid as &$value){
					if (in_array($value['id'], $hidId)){
						$value['display'] = $_dsp;
					}
				}
			}
		}
	}

	public function onEvent_ForumPackageUnDisplay($form)
	{
		if ($this->commercial != 1){
			header('Location: /.admin/');
			exit;
		}
		$this->onEvent_ForumPackageDisplay(&$form, 0);
	}

	private function action_package_edit() 
	{
		if ($this->commercial != 1){
			header('Location: /.admin/');
			exit;
		}
		$this->Groups = $this->DbManager->select("SELECT
				*
			FROM 
				?#
			ORDER BY
				`caption` ASC
			",
			"forum_db_groups"
		);
		$this->Params['groups'] = $this->Groups;

		$merchant = CreateObject('Money_Tariffication', array('DbManager' => $this->DbManager));
		list($_obj) = $merchant->infoPackage($this->objEditId);

		if ($_obj['isactive'] == 1){
			$this->Errors[] = 'Пакет активный редактирование не возможно';
		}
		if (!isset($this->Params['pack'])){
			$this->Params['pack'] = $_obj;
			$this->Params['pack']['lifetime'] = $this->Params['pack']['lifetime'];
		}
		$this->Params['objID'] = $this->objEditId;
		
		$this->Params['listServices'] = $merchant->listServiceByPackage($this->objEditId);
		if (!isset($_REQUEST['inpack'])){
			foreach ($this->Params['listServices'] as $key=>$value){
				if ($value['packageid'] == $this->objEditId && (int)$this->objEditId>0){
					$this->Params['listServices'][$key]['isSelected'] = 1;
				}
			}
		}
	}

	private function action_package_add()
	{
		if ($this->commercial != 1){
			header('Location: /.admin/');
			exit;
		}
		$this->Groups = $this->DbManager->select("SELECT
				*
			FROM 
				?#
			ORDER BY
				`caption` ASC
			",
			"forum_db_groups"
		);
		$this->Params['groups'] = $this->Groups;

		$this->Params['listServices'] = $this->DbManager->select(
			"SELECT
				s.name,
				s.id sId
			FROM 
				?# s
			ORDER BY
				s.name
			",
			"merchant_services"
		);
		$this->Params['objID'] = 0;
	}

	private function action_package_index() 
	{
		if ($this->commercial != 1){
			header('Location: /.admin/');
			exit;
		}
		$merchant = CreateObject('Money_Tariffication', array('DbManager' => $this->DbManager));
		$this->DataGrid = $this->DbManager->selectPage(
			$this->TotalPages,
			"SELECT
				*, 
				(`lifetime` / " . $merchant->tLife . ") as `lifetime`
			FROM 
				?#
			ORDER BY
				id DESC
			LIMIT
				?d, ?d
			",
			"merchant_packages",
			($this->Page - 1) * $this->CountPerPage,
			$this->CountPerPage
		);

		$collect = array();
		$counter++;
		foreach ($this->DataGrid as &$row) {
			$collect[] = $row['id'];
			$row['price'] = sprintf("%.2f", $row['price']);
			$row['lifetime'] = sprintf("%.2f", $row['lifetime']);
			$row['counter'] = $counter + ($this->Page - 1) * $this->CountPerPage;
			$counter++;
		}
		$this->Params['packIDs'] = join(',', $collect);
	}

	private function action_networks_edit() 
	{
		if ((int)$this->netBan == 1){
			list($this->Params['ban']) = $this->DbManager->select("SELECT
					*,
					INET_NTOA(`init_ip`) `init_ip`,
					INET_NTOA(`end_ip`) `end_ip`
				FROM 
					?# 
				WHERE
					`id` = ?d
				",
				"forum_networks_banned",
				$this->netBanID
			);

			$etime = date_parse($this->Params['ban']['banned_time']);
			$etime = mktime($etime['hour'], $etime['minute'], $etime['second'], $etime['month'], $etime['day'], $etime['year']);
			$etime += $this->Params['ban']['banned_period'];
	
			$this->Params['binfo'] = array('btime' => $this->Params['ban']['banned_time'],
							'etime' => date("Y-m-d H:i:s", $etime));
			$this->Action = 'networks_add';
			$this->Params['rules'] = $this->loadRules();
			if (count($this->Params['ban'])>0){
				$this->Params['ban']['init_ip_list'] = explode('.', $this->Params['ban']['init_ip']); 
				$this->Params['ban']['end_ip_list'] = explode('.', $this->Params['ban']['end_ip']);
			}
		}else{
			header("Location: /.admin/bans/networks/");
			exit;
		}
	}

	private function action_packservices_index()
	{
		if ($this->commercial != 1){
			header('Location: /.admin/');
			exit;
		}
		$this->DataGrid = $this->DbManager->selectPage(
			$this->TotalPages,
			"SELECT
				spack.*,
				pack.name as pack_name,
				serv.name as service_name
			FROM 
				?# spack
			JOIN ?# pack ON spack.packageid = pack.id
			JOIN ?# serv ON spack.serviceid = serv.id
			ORDER BY
				pack.id DESC, serv.name
			LIMIT
				?d, ?d
			",
			"merchant_serviceinpackage",
			"merchant_packages",
			"merchant_services",
			($this->Page - 1) * $this->CountPerPage,
			$this->CountPerPage
		);
		$collect = array();
		$counter = 1;
		foreach ($this->DataGrid as &$row) {
			$row['counter'] = $counter + ($this->Page - 1) * $this->CountPerPage;
			$counter++;
			$collect[] = $row['id'];
		}
		$this->Params['banIDs'] = join(',', $collect);
	}

	private function action_networks_add() 
	{
		if ((int)$this->netBan == 1){
			$this->Params['binfo'] = array('btime' => '', 'etime' => '');
			$this->Params['rules'] = $this->loadRules();
		}else{
			header("Location: /.admin/bans/networks/");
			exit;
		}
	}

	private function action_networks_index() 
	{
		$this->DataGrid = $this->DbManager->select("SELECT
				*,
				INET_NTOA(`init_ip`) `init_ip`,
				INET_NTOA(`end_ip`) `end_ip`
			FROM 
				?# 
			WHERE
				`init_ip` <> `end_ip` 
				AND
				`banned` = 1
			ORDER BY
				`banned_time` DESC
			",
			"forum_networks_banned"
		);

		$counter = 1;
		foreach ($this->DataGrid as &$row) {
			$row['init_ip'] = ($row['init_ip']);
			$row['end_ip'] = ($row['end_ip']);
			($row['banned'] == 1) ? $row['banned'] = 'Да' : $row['banned'] = 'Нет';
			$row['banned_period'] = date("Y-m-d H:i:s", strtotime($row['banned_time']) + $row['banned_period']);
			$row['ban_type'] = $GLOBALS['ForumCore']->Protector->Locale[$row['ban_type']];

			$row['counter'] = $counter + ($this->Page - 1) * $this->CountPerPage;
			$counter++;
			$collect[] = $row['id'];
		}
		$this->Params['banIDs'] = join(',', $collect);
	}
	
	private function action_ip_index() 
	{
		$this->DataGrid = $this->DbManager->selectPage(
			$this->TotalPages,
			"SELECT
				*,
				INET_NTOA(`init_ip`) `init_ip`,
				INET_NTOA(`end_ip`) `end_ip`
				FROM 
				?# 
			WHERE
				`init_ip` = `end_ip` 
				AND
				`banned` = 1
				{ AND `init_ip` = INET_ATON(?s) }
				{ AND `ban_type` = ?d }
			ORDER BY
				`banned_time` DESC
			LIMIT
				?d, ?d
			",
			"forum_networks_banned",
			(isset($this->searchIP) ? $this->searchIP : DBSIMPLE_SKIP),
			(($this->banType > 0) ? $this->banType : DBSIMPLE_SKIP),
			($this->Page - 1) * $this->CountPerPage,
			$this->CountPerPage
		);

		$collect = array();					//collect IDs for faster js checker
		$users = array();
		$counter = 0;
		foreach ($this->DataGrid as &$row) {
			($row['banned'] == 1) ? $row['banned'] = 'Да' : $row['banned'] = 'Нет';
			$row['banned_period'] = date("Y-m-d H:i:s", strtotime($row['banned_time']) + $row['banned_period']);
			if ($row['is_confirmed'] == 0) {
				$row['banned_month'] = date("Y-m-d H:i:s", strtotime($row['banned_time']) + 2419200);
			}
			$collect[] = $row['id'];
			
			$counter++;
			$row['counter'] = $counter + ($this->Page - 1) * $this->CountPerPage;
			
			$users[] = $row['adminID'];
		}
		$this->Params['banIDs'] = join(',', $collect);
		
		$this->loadAuthors(array_unique($users));
	}
	
	private function action_ip_add() 
	{
		$ip = $_GET['ip'];
		
		if (isset($_GET['msgID']) && $_GET['msgID'] > 0) {
			//get message for comment
			$message = $this->DbManager->selectRow("SELECT
					`content`, `author`, `realname`
				FROM 
					?# 
				WHERE
					`messageID` = ?d 
				",
				"forum_db_messages",
				(int)$_GET['msgID']
			);
		}
		elseif (isset($_GET['themeID']) && $_GET['themeID'] > 0) {
			//get theme message for comment
			$message = $this->DbManager->selectRow("SELECT
					`caption`, `author`, `realname`
				FROM 
					?# 
				WHERE
					`themeID` = ?d 
				",
				"forum_db_themes",
				(int)$_GET['themeID']
			);	
		}
        elseif (isset($_GET['pagerMsgId']) && $_GET['pagerMsgId'] > 0) {
            //get theme message for comment
            $message = $this->DbManager->selectRow("SELECT
					m.content as content, m.fromuser as author, u.user_name as realname
				FROM
					?# m
                LEFT JOIN ?# u ON m.fromuser = u.userID
				WHERE
					m.id = ?d
				",
                "forum_users_pager",
                "forum_users",
                (int)$_GET['pagerMsgId']
            );
        }
        elseif (isset($_GET['authorID']) && $_GET['authorID'] > 0) {
            $message = $this->DbManager->selectRow("SELECT
					user_name as author,
					user_name as realname
				FROM
					?#
				WHERE
					`userID` = ?d
				",
                "forum_users",
                (int)$_GET['authorID']
            );
        }

		//check for already banned ips
		$this->DataGrid = $this->DbManager->selectRow("SELECT
				*,
				INET_NTOA(`init_ip`) `init_ip`,
				INET_NTOA(`end_ip`) `end_ip`
			FROM 
				?# 
			WHERE
				INET_ATON(?s) BETWEEN `init_ip` AND `end_ip`
				AND `banned` = 1
			",
			"forum_networks_banned",
			$ip, $ip
		);
		
		if (count($this->DataGrid) == 0) {
			$this->Params['rules'] = $this->loadRules();
			$this->Params['addr'] = $_GET['ip'];	
			$this->Params['form'] = 1;	

			$this->Params['author'] .= $message['author'];
			if (strlen($message['realname']) > 0 && $message['realname'] != $message['author']) {
				$this->Params['realname'] .= $message['realname'];
			}
			
			$this->Params['content'] = '';
			if (isset($_GET['group'])) $this->Params['content'] .= "Группа: ".$_GET['group']."\n";
			
			if ($_GET['themeID'] > 0) $this->Params['content'] .= "Заголовок темы: ".$message['caption'];
			else {
				if (isset($_GET['theme'])) $this->Params['content'] .= "Тема: ".$_GET['theme']."\n";
				$this->Params['content'] .= "Текст сообщения: ".$message['content']."\n";
			}
		}
		else {
			$this->Errors[] = 'Такой адрес уже числиться в списке банов!';
			$this->Params['form'] = 0;
			$row = $this->DataGrid;	

			//IP-address
			if ($this->DataGrid['init_ip'] == $this->DataGrid['end_ip']) {
				$collect = array();					//collect IDs for faster js checker
				$users = array();

				$row['counter'] = 1;
				($row['banned'] == 1) ? $row['banned'] = 'Да' : $row['banned'] = 'Нет';
				$row['banned_period'] = date("Y-m-d H:i:s", strtotime($row['banned_time']) + $row['banned_period']);
				if ($row['is_confirmed'] == 0) {
					$row['banned_month'] = date("Y-m-d H:i:s", strtotime($row['banned_time']) + 2419200);
				}
				$collect[] = $row['id'];
				
				$users[] = $row['adminID'];
				
				$this->Params['banIDs'] = join(',', $collect);
				$this->loadAuthors(array_unique($users));
				
				$this->Params['template'] = 'ip_index';
			}
			else {
				//Network
				($row['banned'] == 1) ? $row['banned'] = 'Да' : $row['banned'] = 'Нет';
				$row['banned_period'] = date("Y-m-d H:i:s", strtotime($row['banned_time']) + $row['banned_period']);
				$row['ban_type'] = $GLOBALS['ForumCore']->Protector->Locale[$row['ban_type']];
				$row['counter'] = 1;
				
				$this->Params['template'] = 'networks_index';
			}
			
			unset($this->DataGrid);
			$this->DataGrid[0] = $row;
		}
	}



	public function onEvent_ForumSaveBanUser($form)
	{
			$this->DbManager->query("
				UPDATE
					?# 
				SET
					adminID = ?d,
					ban_period = ?d,
					ruleID = ?d,
					admin_comment = ?s 
				WHERE
					`userID` = ?d
					LIMIT 1
				",
				"forum_banned_users",
				$this->adminID,
				$form->Request["ban"]["ban_period"],
				$form->Request["ban"]["ruleID"],
				$form->Request["ban"]["admin_comment"],
				$this->userBanID
			);
	}

	private function action_nicknames_edit()
	{
		list($this->Params['ban']) = $this->DbManager->selectPage(
				$this->TotalPages,
				"SELECT 
					*
				FROM 
					?# us
				WHERE
					us.userID = ?d
				",
				"forum_banned_users",
				$this->userBanID
		);
		$etime = date_parse($this->Params['ban']['when_banned']);
		$etime = mktime($etime['hour'], $etime['minute'], $etime['second'], $etime['month'], $etime['day'], $etime['year']);
		$etime += $this->Params['ban']['ban_period'];
		$this->Params['ban']['btime'] = $this->Params['ban']['when_banned'];
		$this->Params['ban']['etime'] = date("Y-m-d H:i:s", $etime);
	}

	private function action_nicknames_list()
	{
		$_typeSearch = "us.user_name LIKE";
		$userSearch = isset($_POST['usersearch']);
		if (isset($_POST['usersearch'])){
			$userSearch = $_POST['usersearch'];
		}elseif($_GET['usersearch']){
			$userSearch = $_GET['usersearch'];
		}
		if (isset($_REQUEST['userIsId']) && 
				$_REQUEST['userIsId'] == 1){
			$_typeSearch = "us.userID =";
			$this->Params['userIsId'] = 1;
			$_sTxt = (int)$userSearch;
		}else{
			$_sTxt = '%' . preg_replace("/\s/ims", "%", $userSearch) . '%';
			$this->Params['userIsId'] = 0;
		}

        if (isset($_GET['msgID']) && $_GET['msgID'] > 0) {
            //get message for comment
            $message = $this->DbManager->selectRow("SELECT
					`content`, `author`, `realname`
				FROM
					?#
				WHERE
					`messageID` = ?d
				",
                "forum_db_messages",
                (int)$_GET['msgID']
            );
        }
        elseif (isset($_GET['themeID']) && $_GET['themeID'] > 0) {
            //get theme message for comment
            $message = $this->DbManager->selectRow("SELECT
					`caption`, `author`, `realname`
				FROM
					?#
				WHERE
					`themeID` = ?d
				",
                "forum_db_themes",
                (int)$_GET['themeID']
            );
        }
        $this->Params['content'] = "\n";
        if (isset($_GET['group'])) $this->Params['content'] .= "Группа: ".$_GET['group']."\n";

        if ($_GET['themeID'] > 0) $this->Params['content'] .= "Заголовок темы: ".$message['caption'];
        else {
            if (isset($_GET['theme'])) $this->Params['content'] .= "Тема: ".$_GET['theme']."\n";
            $this->Params['content'] .= "Текст сообщения: ".$message['content']."\n";
        }

        if (isset($_REQUEST['_msg']) && is_array($_REQUEST['_msg']) && in_array('mkBan', $_REQUEST['_msg'])){
			$_users = $this->DbManager->select(
				"SELECT 
					us.user_name
				FROM 
					?# us
				WHERE
					us.userID IN (?a)
				ORDER BY
					us.user_name
				",
				"forum_users",
				$_REQUEST['_bId']
			);
			
			$this->Errors[] = 'Пользователи забанены:';
			if (is_array($_users))
				$_msgUsers = '';
				foreach($_users as $value){
					$this->Errors[] = $value['user_name'];
				}
		}

		if ($userSearch != ''){
			$this->DataGrid = $this->DbManager->selectPage(
				$this->TotalPages,
				"SELECT 
					*
				FROM 
					?# us
				WHERE
					" . $_typeSearch . " ?s
				ORDER BY
					us.user_name
				LIMIT
					?d, ?d
				",
				"forum_users",
				$_sTxt,
				($this->Page - 1) * $this->CountPerPage,
				$this->CountPerPage
			);
		}

		$collect = array();
		if (is_array($this->DataGrid)){
			foreach ($this->DataGrid as &$row) {
				$collect[] = $row['userID'];
			}
		}

		$this->Params['banPeriod'] = $this->banPeriod;
		$this->Params['banIDs'] = join(',', $collect);
		$this->Params['userCounter'] = (int)(($this->Page - 1) * $this->CountPerPage) + 1;
		$this->Params['userSearch'] = $userSearch;
		$this->Params['pagerAddParams'] = 'usersearch=' . $userSearch . '&';
        $this->Params['rules'] = $this->loadRules();
	}

	private function action_nicknames_index() 
	{
		$this->DataGrid = $this->DbManager->selectPage(
			$this->TotalPages,
			"SELECT 
				b.*,
				INET_NTOA(b.`user_ip`) `user_ip`,
				u.`userID` as `userID`,
				u.`user_name` as `user_name`
			FROM 
				?# b
				LEFT JOIN ?# u ON (b.`userID` = u.`userID`) WHERE b.`banned` = 1
			ORDER BY
				b.`when_banned` DESC
			LIMIT
				?d, ?d
			",
			"forum_banned_users",
			"forum_users",
			($this->Page - 1) * $this->CountPerPage,
			$this->CountPerPage
		);

		$collect = array();
		$counter++;
		foreach ($this->DataGrid as &$row) {
			$row['ban_end'] = strtotime($row['when_banned']) + $row['ban_period'];

			$etime = date_parse($row['when_banned']);
			$etime = mktime($etime['hour'], $etime['minute'], $etime['second'], $etime['month'], $etime['day'], $etime['year']);
			$etime += $row['ban_period'];

			$row['ban_end'] = date("Y-m-d H:i:s", $etime);
			$collect[] = $row['userID'];
			
			$row['counter'] = $counter + ($this->Page - 1) * $this->CountPerPage;
			$counter++;
		}
		$this->Params['banIDs'] = join(',', $collect);
	}
	
	private function action_words_index() 
	{
		$this->DataGrid = $this->DbManager->selectPage(
			$this->TotalPages,
			"SELECT
				*
			FROM 
				?#
			WHERE
				1 = 1
				{ AND `flag_caption` = ?d }
				{ AND `flag_content` = ?d }
				{ AND `flag_author` = ?d }
			ORDER BY 
				`added` DESC
			LIMIT
				?d, ?d
			",
			"forum_db_filter",
			(($this->wordFilter == 1) ? 0 : DBSIMPLE_SKIP),
			(($this->wordFilter == 1) ? 0 : DBSIMPLE_SKIP),
			(($this->wordFilter == 1) ? 0 : DBSIMPLE_SKIP),
			($this->Page - 1) * $this->CountPerPage,
			$this->CountPerPage
		);
		
		$collect = array();
		$counter = 0;
		foreach ($this->DataGrid as &$row) {
			$collect[] = $row['wordID'];
			$counter++;
			$row['counter'] = $counter + ($this->Page - 1) * $this->CountPerPage;
		}
		$this->Params['wordIDs'] = join(',', $collect);
	}
	
	private function action_words_edit() 
	{
		$this->DataGrid = $this->DbManager->selectRow(
			"SELECT
				*
			FROM 
				?#
			WHERE
				`wordID` = ?d
			",
			"forum_db_filter",
			$this->wordID
		);
	}
	
	private function action_notes_index()
	{
		$this->DataGrid = $this->DbManager->select("SELECT
				*
			FROM 
				?#
			ORDER BY
				messageID DESC
			",
			"forum_admin_notes"
		);
		
		$collect = array();					//collect IDs for faster js checker
		foreach ($this->DataGrid as &$row) $collect[] = $row['id'];
		$this->Params['notesIDs'] = join(',', $collect);
	}
	
	private function action_notes_edit()
	{
		$this->DataGrid = $this->DbManager->selectRow("SELECT
				*
			FROM 
				?#
			WHERE
				`id` = ?d
			",
			"forum_admin_notes",
			$this->noteID
		);
	}
	
	private function action_ip_edit()
	{
		$this->DataGrid = $this->DbManager->selectRow("SELECT
				*,
				INET_NTOA(`init_ip`) `init_ip`
			FROM 
				?#
			WHERE
				`id` = ?d
			",
			"forum_networks_banned",
			$this->banID
		);
		$etime = date_parse($this->DataGrid['banned_time']);
		$etime = mktime($etime['hour'], $etime['minute'], $etime['second'], $etime['month'], $etime['day'], $etime['year']);
		$etime += $this->DataGrid['banned_period'];

		$this->Params['binfo'] = array('btime' => $this->DataGrid['banned_time'],
						'etime' => date("Y-m-d H:i:s", $etime));
	}
	
	private function action_words_add()
	{
		//Deprecated
	}
	
	private function action_pager_send()
	{
		//Nothing here, lol.
	}
	
	/* Конец списка действий */
	
	/* 
	 *	Список событий (events)
	 *	Порядок работы каждой функции:
	 *  1. Валидация данных
	 *	2. Запись данных в базу
	 *  3. Перенаправление на action
	 */
	
	public function onEvent_ForumHideMessage($form)
	{
		if (count($form->Request["messages"]) == 0) $this->Errors[] = "Вы не выбрали ни одного сообщения!";
		else {
			$keys = array_keys($form->Request["messages"]);
			
			foreach ($keys as $key) {			
				$this->DbManager->query("
					UPDATE 
						?#
					SET
						`hidden` = 1
					WHERE
						`messageID` = ?d
					",
					"forum_db_messages",
					$key
				);
			}
				
			if ($this->Action == 'messages_index') {
				header("Location: /.admin/forum/messages/index/".(isset($this->comFilter) ? "commerce/".$this->comFilter : $this->themeID));
			}
			elseif ($this->Action == 'messages_filter') {
				//global $_SESSION;
				$_add = '?filter[addr]=' . $form->Request["filter"]['addr'] . '&filter[end]=' . $form->Request["filter"]['end'] . '&filter[init]=' . $form->Request["filter"]['init'] . '&event=forumfiltermessage&_msg=1';
				header("Location: /.admin/forum/messages/filter/" . $_add);
			}
			else {
				header("Location: /.admin/forum/stoplight/index".(isset($this->colorFilter) ? "color/".$this->colorFilter : ''));
			}
			
			
		}
	}
	
	public function onEvent_ForumTrashMessage($form)
	{
		if (count($form->Request["messages"]) == 0) $this->Errors[] = "Вы не выбрали ни одного сообщения!";
		else {
			$keys = array_keys($form->Request["messages"]);
			
			foreach ($keys as $key) {
				$this->DbManager->query("
					UPDATE 
						?#
					SET
						`is_deleted` = 1
					WHERE
						`messageID` = ?d
					",
					"forum_db_messages",
					$key
				);
			}
			
			if ($this->Action == 'messages_index') {
				header("Location: /.admin/forum/messages/index/".(isset($this->comFilter) ? "commerce/".$this->comFilter : $this->themeID));
			}
			elseif ($this->Action == 'messages_filter') {
				//global $_SESSION;
				$_add = '?filter[addr]=' . $form->Request["filter"]['addr'] . '&filter[end]=' . $form->Request["filter"]['end'] . '&filter[init]=' . $form->Request["filter"]['init'] . '&event=forumfiltermessage&_msg=1';
				header("Location: /.admin/forum/messages/filter/" . $_add);
			}
			else {
				header("Location: /.admin/forum/stoplight/index".(isset($this->colorFilter) ? "color/".$this->colorFilter : ''));
			}

		}
	}

    public function onEvent_ForumTrashPicture($form)
    {
/*
        if (count($form->Request["pictures"]) == 0) $this->Errors[] = "Вы не выбрали ни одной картинки!";
        else {
            $keys = array_keys($form->Request["pictures"]);
*/
        if (count($form->Request["img"]) == 0) $this->Errors[] = "Вы не выбрали ни одной темы!";
        else {
            $keys = array_keys($form->Request["img"]);
            $themes = $form->Request["theme"];
            $messages = $form->Request["message"];
            $filenames = $form->Request["filename"];
            /*
            $pics = $this->DbManager->select("
                SELECT
                    *
                FROM
                    ?#
                WHERE
                    `id` IN (?a)
                ",
                "forum_messages_attaches",
                $keys
            );
            */
            foreach ($keys as $item) {
                $themeID = $themes[$item];
                $messageID = $messages[$item];
                $filenameID = $messages[$item];
                $file = HOME_DIR . 'attaches/' . $themeID . '/' . $messageID . '/' . $filenameID;
                $thumb_file = HOME_DIR . 'attaches/' . $themeID . '/' . $messageID . '/thumb_' . $filenameID;
                @unlink($file);
                @unlink($thumb_file);

            }
            $this->DbManager->query("
                UPDATE
                    ?#
                SET
                    `hidden` = 1
                WHERE
                    `id` IN(?a)
                ",
                "forum_messages_attaches",
                $keys
            );
            header("Location: /.admin/forum/commerce/index/".$this->groupID."/?p=".$_GET['p']);
/*
            if ($this->Action == 'pictures_index') {
                header("Location: /.admin/forum/pictures/index/".(isset($this->comFilter) ? "commerce/".$this->comFilter."?p=".$_GET['p'] : $this->themeID));
            }
            else {
                header("Location: /.admin/forum/stoplight/index".(isset($this->colorFilter) ? "color/".$this->colorFilter : ''));
            }
*/

        }
    }

	
	public function onEvent_ForumReadMessage($form)
	{
		if (count($form->Request["messages"]) == 0) $this->Errors[] = "Вы не выбрали ни одного сообщения!";
		else {
			$keys = array_keys($form->Request["messages"]);

			foreach ($keys as $key) {
				$this->DbManager->query("
					UPDATE 
						?#
					SET
						`is_moderated` = 1
					WHERE
						`messageID` = ?d
					",
					"forum_db_messages",
					$key
				);
			}
			
			if ($this->Action == 'messages_index') {
				header("Location: /.admin/forum/messages/index/".(isset($this->comFilter) ? "commerce/".$this->comFilter : $this->themeID));
			}
			elseif ($this->Action == 'messages_filter') {
				//global $_SESSION;
				$_add = '?filter[addr]=' . $form->Request["filter"]['addr'] . '&filter[end]=' . $form->Request["filter"]['end'] . '&filter[init]=' . $form->Request["filter"]['init'] . '&event=forumfiltermessage&_msg=1';
				header("Location: /.admin/forum/messages/filter/" . $_add);
			}
		}
	}
	
	public function onEvent_ForumNullMessage($form)
	{
		if (count($form->Request["messages"]) == 0) $this->Errors[] = "Вы не выбрали ни одного сообщения!";
		else {
			$keys = array_keys($form->Request["messages"]);
			
			//Nullify danger level
			foreach ($keys as $key) {
				$this->DbManager->query("
					UPDATE 
						?#
					SET
						`danger_level` = 0
					WHERE
						`messageID` = ?d
					",
					"forum_db_messages",
					$key
				);
			}
			
			//Delete complaints
			$this->DbManager->query("
				DELETE FROM 
					?#
				WHERE
					`messageID` IN (?a)
				",
				"forum_users_complaint",
				$keys
			);
			
			header("Location: /.admin/forum/stoplight/index/".(isset($this->colorFilter) ? "color/".$this->colorFilter : ''));
		}
	}
	
	public function onEvent_ForumEditTheme($form)
	{
		//checkbox parse

        //if ($this->AuthManager->User->is_admin) {

            $checkboxes = array("hidden", "is_locked", "is_top","hottop");
            $this->checkboxParser($checkboxes, 'theme', $form);
            if (isset($form->Request["theme"]['top_end']) && in_array(trim($form->Request["theme"]['top_end']), array('', '0000-00-00 00:00:00'))){
                unset($form->Request["theme"]['top_end']);
            }

            $this->DbManager->query("
                UPDATE
                    ?#
                SET
                    ?a
                    " . (!isset($form->Request["theme"]['top_end']) ? ", `top_end` = NULL " : "" ) . "
                WHERE
                    `themeID` = ?d
                ",
                "forum_db_themes",

                $form->Request["theme"],
                $this->themeID
            );

            if ((int)$this->themeID>0){
                $_cnt = $this->DbManager->query("
                    UPDATE
                        ?#
                    SET
                        `hidden` = ?d
                    WHERE
                        `themeID` = ?d
                    ",
                    "forum_db_messages",
                    $form->Request["theme"]['hidden'],
                    $this->themeID
                );
            }
        //}
		$this->DataGrid = $form->Request["theme"];
	}
	
	public function onEvent_ForumEditMessage($form)
	{
		//checkbox parse

        //if ($this->AuthManager->User->is_admin) {
            $checkboxes = array("hidden");
            $this->checkboxParser($checkboxes, 'message', $form);

            $this->DbManager->query("
                UPDATE
                    ?#
                SET
                    ?a
                WHERE
                    `messageID` = ?d
                ",
                "forum_db_messages",
                $form->Request["message"],
                $this->messageID
            );
        //}
		$this->DataGrid = $form->Request["message"];
	}
	
	public function onEvent_ForumFilterMessage($form)
	{
		$form->Request['filter']['init'] = trim($form->Request['filter']['init']);
		$this->DataGrid = $this->DbManager->select(
			"SELECT
				*,
				INET_NTOA(`author_ip`) `author_ip2`
			FROM 
				?#
			WHERE 
				1=1
				AND `is_deleted` = 0
				AND `hidden` = 0
				AND `created` > (NOW() - INTERVAL 7 DAY )
				{ AND `author_ip` = INET_ATON(?s) }
			ORDER BY
				messageID DESC
			LIMIT 
				?d, ?d
			",
			"forum_db_messages",
			(empty($form->Request['filter']['addr'])? DBSIMPLE_SKIP : $form->Request['filter']['addr']),
			($this->Page - 1) * $this->CountPerPage,
			$this->CountPerPage
		);
		
		$collect = array();					//collect IDs for faster js checker
		$idents = array();;					//collect userIDs for real nick names
		$themes = array();
		$counter = 0;
		foreach ($this->DataGrid as &$row) {
			$collect[] = $row['messageID'];
			if ($row['author_ip'] != 0 && $row['author_ip'] != 2147483647) {
				$row['author_ip'] = $row['author_ip2'];
			}
			else {
				$iptemp = split(", ", $row['old_ip']); 
				$row['author_ip'] = $iptemp[0];
			}
			if ($row['authorID']) $idents[] = $row['authorID'];
			$themes[] = $row['themeID'];
			
			$counter++;
			$row['counter'] = $counter;
		}
		$this->Params['messageIDs'] = join(',', $collect);
		$this->Params['_filter'] = $form->Request['filter'];
		
		//$this->loadAuthors(array_unique($idents));
		if (!isset($this->ThemeID)) $this->loadThemes(array_unique($themes));
	}
	
	public function onEvent_ForumHideTheme($form)
	{
		if (count($form->Request["themes"]) == 0) $this->Errors[] = "Вы не выбрали ни одной темы!";
		else {
			$keys = array_keys($form->Request["themes"]);
			$this->DbManager->query("
				UPDATE
					?#
				SET
					`hidden` = 1
				WHERE
					`themeID` IN(?a)
				",
				"forum_db_themes",
				$keys
			);
			
			$this->DbManager->query("
				UPDATE
					?#
				SET
					`hidden` = 1
				WHERE
					`themeID` IN(?a)
				",
				"forum_db_messages",
				$keys
			);

			header("Location: /.admin/forum/themes/index/".$this->groupID."/?p=".$_GET['p']);
		}		
	}
    public function onEvent_ForumHideImg($form)
    {
        if (count($form->Request["img"]) == 0) $this->Errors[] = "Вы не выбрали ни одной темы!";
        else {
            $keys = array_keys($form->Request["img"]);
            $this->DbManager->query("
                UPDATE
                    ?#
                SET
                    `hidden` = 1
                WHERE
                    `id` IN(?a)
                ",
                "forum_messages_attaches",
                $keys
            );

            header("Location: /.admin/forum/commerce/index/".$this->groupID."/?p=".$_GET['p']);
        }
    }

	
	public function onEvent_ForumMoveTheme($form)
	{
		if (count($form->Request["themes"]) == 0) $this->Errors[] = "Вы не выбрали ни одной темы!";
		else {
			$selector = (int)$form->Request['selector'];
			$groupID = (int)$form->Request["move".$selector];
			$keys = array_keys($form->Request["themes"]);
			
			foreach ($keys as $key) {
				$this->DbManager->query("
					UPDATE
						?#
					SET
						`groupID` = ?d
					WHERE
						`themeID` = ?d
					",
					"forum_db_themes",
					$groupID,
					$key
				);
				
				$this->DbManager->query("
					UPDATE
						?#
					SET
						`groupID` = ?d
					WHERE
						`themeID` = ?d
					",
					"forum_db_messages",
					$groupID,
					$key
				);
			}
				
			header("Location: /.admin/forum/themes/index/".$this->groupID."/?p=".$_GET['p']);
		}		
	}
	
	public function onEvent_ForumBanDouble($form)
	{
		$this->onEvent_ForumBanUser($form);
		$this->onEvent_ForumBanAddr($form);
	}

	private function setUsersBannedFlag($keys, $flag)
	{
		if (count($keys)>0){
			$flag = (int)$flag;
			
			$this->DbManager->query("
				UPDATE
					?#
				SET
					`banned` = ?d
				WHERE
					`userID` IN (?a)
				LIMIT " . (int)count($keys),
				"forum_users",
				$flag,
				$keys
			);
		}
	}
	
	public function onEvent_ForumBanUser($form)
	{
		if (count($form->Request["users"]) == 0) $this->Errors[] = "Вы не выбрали ни одного пользователя";
		else {
			$keys = array_keys($form->Request["users"]);
			$_url = '';
			foreach ($keys as $key) {
				$this->DbManager->query('
					REPLACE INTO 
						?#(userID, user_ip, banned, adminID, when_banned, ban_period, ruleID, admin_comment, is_confirmed) 
					VALUES(?d, INET_ATON(?s), 1, ?d, NOW(), ?d, ?d, ?s, ?d)', 
					"forum_banned_users", 
					$key, 
					$address[$key],
					$this->adminID,
					$form->Request["ban"]["ban_period"],
					$form->Request["ban"]["ruleID"],
					$form->Request["ban"]["admin_comment"],
					($form->Request["ban"]["time"] <= $this->maxBan) ? 1 : 0
				);
				$_url .= '&_bId[]=' . $key;
			}
			
			$this->setUsersBannedFlag($keys, true);
			
			header("Location: /.admin/bans/nicknames/list/?usersearch=" . $form->Request["usersearch"] . '&userIsId=' . $form->Request["userIsId"] . '&_msg[]=mkBan' . $_url);
		}
		$this->Params['ban'] = $form->Request["ban"];
	}
	
	public function onEvent_ForumBanAddr($form)
	{
		if (count($form->Request["users"]) == 0) $this->Errors[] = "Вы не выбрали ни одного пользователя";
		else {
			$address = $form->Request["addr"];
			$keys = array_keys($form->Request["users"]);

			//Check ip in already in banned networks
			$collect = array();
			foreach ($keys as $key) $collect[] = $address[$key];
			
			foreach ($keys as $key) {
				$ip = $address[$key];
				
				$this->DbManager->query('
					REPLACE INTO 
						?#(init_ip, end_ip, banned, banned_time, banned_period, adminID, ruleID, comments, ban_type, is_confirmed) 
					VALUES(INET_ATON(?s), INET_ATON(?s), ?d, NOW(), ?d, ?d, ?d, ?s, ?d, ?d)', 
					"forum_networks_banned", 
					$ip, 
					$ip,
					$this->adminID,
					$form->Request["ban"]["time"],
					$form->Request["ban"]["ruleID"],
					$form->Request["ban"]["comment"],
					$form->Request["ban"]["type"],
					($form->Request["ban"]["time"] <= $this->maxBan) ? 1 : 0
				);
			}
			header("Location: /.admin/users/userlist/");
		}		
	}
	
	public function onEvent_ForumBanConfirmNick($form)
	{
		if (count($form->Request["bans"]) == 0) $this->Errors[] = "Вы не выбрали ни одной записи";
		else {
			$keys = array_keys($form->Request["bans"]);
			$this->DbManager->query("
				UPDATE
					?#
				SET
					`is_confirmed` = 1
				WHERE
					`id` IN(?a)
				",
				"forum_banned_users",
				$keys
			);
			
			header("Location: /.admin/bans/nicknames/");
		}		
	}
	
	public function onEvent_ForumBanConfirmAddr($form)
	{
		if (count($form->Request["bans"]) == 0) $this->Errors[] = "Вы не выбрали ни одной записи";
		else {
			$keys = array_keys($form->Request["bans"]);
			
			foreach ($keys as $key) {
				$this->DbManager->query("
					UPDATE
						?#
					SET
						`is_confirmed` = 1
					WHERE
						`id` = ?d
					",
					"forum_networks_banned",
					$key
				);
			}
			
			header("Location: /.admin/bans/ip/index/".(isset($this->banType) ? "type/".$this->banType : ''));
		}		
	}	
	
	
	public function onEvent_ForumBanChangeType($form)
	{
		if (count($form->Request["bans"]) == 0) $this->Errors[] = "Вы не выбрали ни одной записи";
		else {
			$keys = array_keys($form->Request["bans"]);
			
			$selector = (int)$form->Request['selector'];
			$ban_type = (int)$form->Request["move".$selector];
			
			$this->DbManager->query("
				UPDATE
					?#
				SET
					`ban_type` = ?d
				WHERE
					`id` IN(?a)
				",
				"forum_networks_banned",
				$ban_type,
				$keys
			);
			
			header("Location: /.admin/bans/ip/index/".(isset($this->banType) ? "type/".$this->banType : ''));
		}		
	}	
	
	public function onEvent_ForumPagerSend($form)
	{
		if (strlen($form->Request["pager"]["content"]) == 0) {
			$this->Errors[] = "Вы не ввели текст сообщения!";
		}
		else {
			$temp = new Forum_Pager();
			$temp->MessageSend($this->adminID, $form->Request["pager"]["userto"], $form->Request["pager"]["content"]);
			
			unset($temp);
			$this->Errors[] = "Сообщение успешно отправлено!";
		}
	}

	public function onEvent_ForumBanNetDelete($form)
	{
		if (count($form->Request["bans"]) == 0) $this->Errors[] = "Вы не выбрали ни одной записи";
		else{
			$this->DbManager->query(
				'UPDATE ?#
				SET `banned` = 0
				WHERE 
				`id` IN (?a) LIMIT ' . count($form->Request["bans"]),
				"forum_networks_banned", 
				$form->Request["bans"]);
		}
		header("Location: /.admin/bans/networks/");
		exit;
	}

	public function onEvent_ForumBanNetWorks($form)
	{
		$net = $form->Request["ban"];
		$net['init_ip'] = implode('.', $net['init_ip_list']); 
		$net['end_ip'] = implode('.', $net['end_ip_list']);

		if ($this->netBan == 1){
			$check = $this->DbManager->select(
							'SELECT *, INET_NTOA(`init_ip`) init_ip, INET_NTOA(`end_ip`) end_ip FROM ?# WHERE
							( 
								(`init_ip` <= INET_ATON(?s) AND `end_ip` >= INET_ATON(?s))
								OR
								(`init_ip` <= INET_ATON(?s) AND `end_ip` >= INET_ATON(?s))
							)
							AND `banned` = 1
							AND `init_ip` != `end_ip`
							AND `id` != ?d',
							"forum_networks_banned", 
							$net['init_ip'], 
							$net['init_ip'], 
							$net['end_ip'],
							$net['end_ip'],
							(int)$this->netBanID);
			if (is_array($check) && count($check) > 0){
				$this->Errors[] = 'Выбранная Вами подсеть забанена либо является частью подсети';
				foreach ($check as $value){
					$this->Errors[] = $value['id'] . ' Сеть ' . $value['init_ip'] . ' - ' . $value['end_ip'];
				} 
			}
			if (trim($net['init_ip']) == '' || trim($net['end_ip']) == ''){
				$this->Errors[] = 'Начальный IP или Конечный IP пустой';
			}
			//	$this->DbManager->setLogger('myLogger');
			

			if (count($this->Errors) == 0){
				if ((int)$this->netBanID == 0){
					$_id = $this->DbManager->query('
						INSERT INTO 
							?#(init_ip, end_ip, banned, banned_time, banned_period, adminID, ruleID, comments, ban_type, is_confirmed) 
						VALUES(INET_ATON(?s), INET_ATON(?s), 1, NOW(), ?d, ?d, ?d, ?s, ?d, ?d)', 
						"forum_networks_banned", 
						$net['init_ip'], 
						$net['end_ip'],
						$form->Request["ban"]["banned_period"],
						$this->adminID,
						$form->Request["ban"]["ruleID"],
						$form->Request["ban"]["comments"],
						$form->Request["ban"]["ban_type"],
						1
					);
				}else{
					$_id = (int)$this->netBanID;
					
					$this->DbManager->query(
							'UPDATE 
							?#
							SET
							init_ip = INET_ATON(?s),
							end_ip = INET_ATON(?s),
							banned_period = ?d,
							adminID = ?d,
							ruleID = ?d,
							comments = ?s,
							ban_type = ?d
							WHERE id = ?d LIMIT 1',
							"forum_networks_banned", 
							$net['init_ip'], 
							$net['end_ip'],
							$form->Request["ban"]["banned_period"],
							$this->adminID,
							$form->Request["ban"]["ruleID"],
							$form->Request["ban"]["comments"],
							$form->Request["ban"]["ban_type"],
							$_id
					);
				}
			}
		}
		if (count($this->Errors) == 0){
			header("Location: /.admin/bans/networks/edit/" . $_id . "/");
			exit;
		}else{
			$net['init_ip_list'] = explode('.', $net['init_ip']); 
			$net['end_ip_list'] = explode('.', $net['end_ip']);
			$this->Params['ban']   = $net;
		}
	}

	public function onEvent_ForumBanIp($form)
	{
		$ip = $form->Request["ban"]["value"];
		
		$this->DbManager->query('
			INSERT INTO 
				?#(init_ip, end_ip, banned, banned_time, banned_period, adminID, ruleID, comments, ban_type, is_confirmed, author, realname) 
			VALUES(INET_ATON(?s), INET_ATON(?s), 1, NOW(), ?d, ?d, ?d, ?s, ?d, ?d, ?s, ?s)', 
			"forum_networks_banned", 
			$ip, 
			$ip,
			$form->Request["ban"]["time"],
			$this->adminID,
			$form->Request["ban"]["ruleID"],
			$form->Request["ban"]["comment"],
			$form->Request["ban"]["type"],
			($form->Request["ban"]["time"] <= $this->maxBan) ? 1 : 0,
			$form->Request["ban"]["author"],
			$form->Request["ban"]["realname"]
		);

		header("Location: /.admin/bans/ip/");
	}
	
	public function onEvent_ForumUnBanIp($form)
	{
		if (count($form->Request["bans"]) == 0) $this->Errors[] = "Вы не выбрали ни одной записи";
		else {
			$keys = array_keys($form->Request["bans"]);
			
			foreach ($keys as $key) {
				$this->DbManager->query("
					UPDATE
						?#
					SET
						`banned` = 0
					WHERE
						`id` = ?d
					",
					"forum_networks_banned",
					$key
				);
			}
			
			header("Location: /.admin/bans/ip/index/".(isset($this->banType) ? "type/".$this->banType : ''));
		}		
	}
	
	public function onEvent_ForumUnBanUser($form)
	{
		if (count($form->Request["bans"]) == 0) $this->Errors[] = "Вы не выбрали ни одной записи";
		else {
			$keys = array_keys($form->Request["bans"]);

			$this->DbManager->query("
				UPDATE
					?# 
				SET
					`banned` = 0
				WHERE
					`userID` IN (?a)
					LIMIT " . count($keys) . "
				",
				"forum_banned_users",
				$keys,
				$this->wordID
			);
			$this->setUsersBannedFlag($keys, false);
		
			header("Location: /.admin/bans/nicknames/");
			exit;
		}		
	}
	
	public function onEvent_ForumEditBadWord($form)
	{
		//checkbox parse
		$checkboxes = array("flag_caption", "flag_content", "flag_author");
		$this->checkboxParser($checkboxes, 'word', $form);
		
		$this->DbManager->query("
			UPDATE
				?# 
			SET
				?a
			WHERE
				`wordID` = ?d
			",
			"forum_db_filter",
			$form->Request["word"],
			$this->wordID
		);	

		$this->DataGrid = $form->Request["word"];
	}
	
	public function onEvent_ForumAddBadword($form)
	{
		//проверка на повторение
		$word = $form->Request['word']["filter_string"];
		$check = $this->DbManager->select('SELECT `filter_string` FROM ?# WHERE `filter_string` = ?s', "forum_db_filter", $word);
		
		if (strlen($word) == 0) $this->Errors[] = "Вы не ввели слово!";
		elseif (count($check) > 0) $this->Errors[] = "Слово уже есть в списке!";
		else {
			//checkbox parse
			$checkboxes = array("flag_caption", "flag_content", "flag_author");
			$this->checkboxParser($checkboxes, 'word', $form);
		
			$this->DbManager->query("
				INSERT INTO
					?#(filter_string, added, authorID, flag_caption, flag_content, flag_author)
				VALUES
					(?s, NOW(), ?d, ?d, ?d, ?d)
				",
				"forum_db_filter",
				$form->Request['word']["filter_string"],
				$this->adminID,
				$form->Request['word']["flag_caption"],
				$form->Request['word']["flag_content"],
				$form->Request['word']["flag_author"]
			);

			header("Location: /.admin/bans/words");
		}
	}
	
	public function onEvent_ForumOpenBadword($form)
	{
		if (count($form->Request["words"]) == 0) $this->Errors[] = "Вы не выбрали ни одного слова!";
		else {
			$keys = array_keys($form->Request["words"]);
			$hide = array(
				'flag_caption' => 0,
				'flag_content' => 0,
				'flag_author' => 0
			);
			
			foreach ($keys as $key) {
				$this->DbManager->query("
					UPDATE 
						?#
					SET
						?a
					WHERE
						`wordID` = ?d
					",
					"forum_db_filter",
					$hide,
					$key
				);
			}
			
			header("Location: /.admin/bans/words/");
		}
	}
	
	
	public function onEvent_ForumEditNotes($form)
	{
		$this->DbManager->query("
			UPDATE
				?# 
			SET
				?a
			WHERE
				`id` = ?d
			",
			"forum_admin_notes",
			$form->Request["notes"],
			$this->noteID
		);	

		$this->DataGrid = $form->Request["notes"];
	}
	
	public function onEvent_ForumAddNotes($form)
	{
		//проверка на повторение
		$text = $form->Request['notes']["content"];
		
		if (strlen($text) == 0) $this->Errors[] = "Вы не ввели текст!";
		else {
			$this->DbManager->query("
				INSERT INTO
					?#(id, content, created)
				VALUES
					(NULL, ?s, NOW())
				",
				"forum_admin_notes",
				$text
			);

			header("Location: /.admin/extra/notes/");
		}
	}
	
	public function onEvent_ForumDeleteNotes($form)
	{
		if (count($form->Request["notes"]) == 0) $this->Errors[] = "Вы не выбрали ни одной заметки!";
		else {
			$keys = array_keys($form->Request["notes"]);

			foreach ($keys as $key) {
				$this->DbManager->query("
					DELETE FROM
						?#
					WHERE
						`id` = ?d
					",
					"forum_admin_notes",
					$key
				);
			}
			
			header("Location: /.admin/extra/notes/");
		}
	}
	
	public function onEvent_ForumEditBan($form)
	{
		$this->DbManager->query("
			UPDATE
				?# 
			SET
				`init_ip` = INET_ATON(?s),
				`end_ip` = INET_ATON(?s),
				`comments` = ?s,
				`author` = ?s,
				`realname` = ?s,
				`ruleID` = ?d,
				`ban_type` = ?d,
				`banned_period` = ?d
			WHERE
				`id` = ?d
			",
			"forum_networks_banned",
			$form->Request["ban"]["init_ip"],
			$form->Request["ban"]["init_ip"],
			$form->Request["ban"]["comments"],
			$form->Request["ban"]["author"],
			$form->Request["ban"]["realname"],
			$form->Request["ban"]["ruleID"],
			$form->Request["ban"]["ban_type"],
			$form->Request["ban"]["time"],
			$this->banID
		);	

		$this->DataGrid = $form->Request["ban"];
	}
	/* Конец списка events */
	
	//Чекбоксы -> (int)
	private function checkboxParser($checkboxes, $prefix, $form)
	{
		foreach ($checkboxes as $key) {
			if (isset($form->Request[$prefix][$key])) $form->Request[$prefix][$key] = 1;
			else $form->Request[$prefix][$key] = 0;
		}	
	}
	
	/* Подготовка данных и вывод */
	public function Prepare(&$ds)
	{
		$ds->assign('_params', $this->Params);
		$ds->assign('_errors', $this->Errors);
		$ds->assign('_messages', $this->Messages);
        $ds->assign('_pictures', $this->Pictures);
		$ds->assign('_filters', $this->Filters);
		$ds->assign('_ban_types', $this->BanTypes);
		
		$ds->assign('_commercial', $this->commercial);

		$ds->assign('_com_filter', $this->comFilter);
		$ds->assign('_search_ip', $this->searchIP);
		
		$ds->assign('_folder', $this->Folder);
		$ds->assign('_action', $this->Action);
		$ds->assign('_pageTitle', $this->PageTitle);
		$ds->assign('_dataGrid', $this->DataGrid);
		$ds->assign('_tableHeaders', $this->TableHeaders);
		
		$ds->assign("__total_rows", $this->TotalPages);
		$ds->assign("__page", $this->Page);
		$ds->assign("__next", $this->Page + 1);
		$ds->assign("__pages", ceil(($this->TotalPages + 1) / $this->CountPerPage));
		$ds->assign("__countperpage", $this->CountPerPage);
	}

	public function Display(&$parser)
	{
		$parser->display($this->MainTemplate);
	}
}