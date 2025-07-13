<?php
	class Money_Commercialservice
	{
		public function __construct ($args)
		{
			$this->DbManager = $args[0]['DbManager'];
		}
		
		public function makeServiceLink ($objid, $packageid, $userid = 0, $services = array(), $skipcheck = false)
		{
			$_res = array('check' => array('msg' => array(), 'errors' => array()));
			$merchant = CreateObject('Money_Tariffication', array('DbManager' => $this->DbManager));
			$objid = (int)$objid;

			if (!is_array($services) || count($services) == 0){
				$services = $merchant->getMakeCheckPackage($packageid, $userid);
			}

			if (isset($services['packages']) && is_array($services['packages']) && count($services['packages']) > 0 && $userid > 0 && $objid>0){
				foreach ($services['packages'] as $package){
					if (!$skipcheck)
						$_res['check']['errors'] = $this->checkServices($objid, $package['list'], $userid);

					if (count($_res['check']['errors']) == 0){
						foreach ($package['list'] as $service){
							switch ($service['system_name']){
								case 'addcommercialtheme':
									$this->DbManager->query(
														"UPDATE ?#
														SET `enddate` = ?s
														WHERE `themeID` = ?d
														LIMIT 1",
														"forum_db_themes",
														$merchant->endDate(date("Y-m-d H:i:s"), $service['acttime'], 'acttime'),
														$objid
													);
									/*
									$this->DbManager->query(
														"UPDATE ?#
														SET
															`autoup` = 1,
															`autoup_interval` = ?d,
															`autoup_endtime` = ?s
														WHERE
															`themeID` = ?d
														LIMIT 1",
														"forum_db_themes",
														60*60*24,
														$merchant->endDate(date("Y-m-d H:i:s"), $service['acttime'], 'acttime'),
														$objid
													);
									*/
								break;
								case 'uptheme':
									$this->DbManager->query(
														"UPDATE ?#
														SET `is_top` = 1, `top_end` = ?s
														WHERE `themeID` = ?d
														LIMIT 1",
														"forum_db_themes",
														$merchant->endDate(date("Y-m-d H:i:s"), $service['acttime'], 'acttime'),
														$objid
													);
								break;
								case 'autouptheme':
									$this->DbManager->query(
														"UPDATE ?#
														SET
															`autoup` = 1,
															`autoup_interval` = ?d,
															`autoup_endtime` = ?s
														WHERE
															`themeID` = ?d
														LIMIT 1",
														"forum_db_themes",
														$merchant->mkTime($service['period'], 'period'),
														$merchant->endDate(date("Y-m-d H:i:s"), $service['acttime'], 'acttime'),
														$objid
													);
								break;
								case 'unhiddentheme':
									$_rw = $this->DbManager->selectrow(
														"SELECT * FROM ?#
														WHERE
															`themeID` = ?d
														AND
															`authorID` = ?d
														LIMIT 1",
														"forum_db_themes",
														$objid,
														$userid
													);

									if ($_rw['enddate'] == '0000-00-00 00:00:00'){
										$_dt = "'" . date("Y-m-d H:i:s") . "'";
									}else{
										$_dt = '`enddate`';
									}
									
									$this->DbManager->query(
														"UPDATE ?#
														SET
															`hidden` = 0,
															`is_locked` = 0,
															`enddate` = (" . $_dt . " + INTERVAL ?d SECOND),
															`hidden_status` = 0
														WHERE
															`themeID` = ?d
														AND
															`authorID` = ?d
														LIMIT 1",
														"forum_db_themes",
														$merchant->mkTime($service['acttime'], 'acttime'),
														$objid,
														$userid
													);
								break;
								case 'edittheme':
									$this->DbManager->query(
														"UPDATE ?#
														SET
															`moderated` = 1
														WHERE
															`themeID` = ?d
														AND
															`authorID` = ?d
														LIMIT 1",
														"forum_db_themes",
														$objid,
														$userid
													);
								break;
							}
						}
						$_src = $package['info'];
						$_src['objid'] = $objid;
						$_rs = $merchant->writeData($_src, 'servicelinkuser');

						$package = $this->DbManager->selectRow(
							"SELECT 
								*
							FROM 
								?#
							WHERE 
								`id` = ?d
							",
							$this->prefix . "packages",
							$value['packageid']
						);
						$_sql = "
								INSERT INTO ?#
								(
									`userid`,
									`username`,
									`action`,
									`message`,
									`status`,
									`payment`
								)
								VALUES (
								?d,
								?s,
								?s,
								?s,
								?d,
								?d
								)
								";

						$package = $this->DbManager->selectRow(
							"SELECT 
								*
							FROM 
								?#
							WHERE 
								`id` = ?d
							",
							"merchant_packages",
							$_src['packageid']
						);

						$this->DbManager->query($_sql,
											"merchant_logactions",
											$userid,
											'',
											'Прикреплен пакет "' . $package['name'] . '" [' . $_src['packageid'] . '] к теме ' . $_src['objid'],
											'',
											1,
											0
											);
					}
				}
			}else{
				$_res['check']['errors'][] = 'Пакет не найден.';
			}
			return $_res;
		}
		
		public function checkServices($objid, $services, $userid = 0){
			$_err = array();
			foreach ($services as $service){
				switch ($service['system_name']){
					case 'uptheme':
						$gID = $this->DbManager->selectrow(
									"SELECT *
									FROM ?#
									WHERE 
									`themeID` = ?d LIMIT 1",
									"forum_db_themes", $objid);

							if (count($gID) == 0){
								$_err[] = 'Тема не найдена';
							}
							if ((int)$gID['hidden'] != 0){
								$_err[] = 'Тема скрыта';
							}

							if ((int)$gID['is_top'] != 0){
								$_err[] = 'Тема уже в топе';
							}
							if ((int)$gID['hidden'] == 0 && (int)$gID['is_top'] == 0){
								$name = $this->DbManager->selectrow(
										"SELECT *
										FROM ?#
										WHERE 
										`groupID` = ?d LIMIT 1",
										"forum_db_groups", (int)$gID['groupID']);

								$cnt = $this->DbManager->selectcell(
										"SELECT COUNT(*)
										FROM ?#
										WHERE 
										`groupID` = ?d
										AND `hidden` = 0
										AND `is_top` = 1",
										"forum_db_themes", $gID['groupID']);

								if ((int)$cnt >= (int)$name['in_top']){
									$_err[] = 'В группе "' . $name['caption'] . '" уже поднято ' . (int)$name['in_autoup'] . ' тем';
								}
							}
							if ($gID['groupID'] == 0){
								$_err[] = 'Вы не выбрали тему';
							}
						break;
						case 'autouptheme':
							$upID = $this->DbManager->selectrow(
									"SELECT *
									FROM ?#
									WHERE 
									`themeID` = ?d LIMIT 1",
									"forum_db_themes", $objid);

							//(int)$upID['groupID']
							if (count($upID) == 0){
								$_err[] = 'Тема не найдена';
							}
							if ((int)$upID['hidden'] != 0){
								$_err[] = 'Тема скрыта';
							}

							if ((int)$upID['autoup'] != 0 && (int)$upID['autoup_interval'] != 60*60*24 && (int)$upID['autoup_interval'] > 0){
								$_err[] = 'Тема уже автоматически поднимается';
							}

							if ((int)$upID['hidden'] == 0 && (int)$upID['autoup'] == 0){
								$name = $this->DbManager->selectrow(
										"SELECT *
										FROM ?#
										WHERE 
										`groupID` = ?d LIMIT 1",
										"forum_db_groups", (int)$upID['groupID']);
								
								$cnt = $this->DbManager->selectcell(
										"SELECT COUNT(*)
										FROM ?#
										WHERE 
										`groupID` = ?d
										AND `hidden` = 0
										AND `autoup` = 1
										AND `autoup_interval` != " . (int)(60*60*24) .
										"AND `autoup_interval` > 0",
										"forum_db_themes", $upID['groupID']);

								if ((int)$cnt >= (int)$name['in_autoup']){
									$_err[] = 'В группе "' . $name['caption'] . '" ' . (int)$name['in_autoup'] . ' тем уже автоматически поднимаются';
								}
							}

							if ((int)$objid == 0){
								$_err[] = 'Вы не выбрали тему';
							}
						break;
						case 'unhiddentheme':
							$unID = $this->DbManager->selectrow(
									"SELECT *
									FROM ?#
									WHERE 
									`themeID` = ?d
									AND `authorID` = ?d LIMIT 1",
									"forum_db_themes", $objid, $userid);

							if (count($unID) == 0){
								$_err[] = 'Тема не найдена';
							}
							if ($unID['enddate'] == '0000-00-00 00:00:00' && !in_array((int)$unID['hidden_status'], array(1, 2))){
								$_err[] = 'Тему нельзя продлить, она не являеться комерческой';
							}
							if ((int)$unID['hidden_status'] != 1 && (int)$unID['hidden'] == 1){
								$_err[] = 'Тему нельзя продлить, поскольку она скрыта администратором';
							}
							if ((int)$unID['hidden_status'] != 2 && (int)$unID['is_locked'] == 1){
								$_err[] = 'Тему нельзя продлить, поскольку она закрыта администратором';
							}
							if ((int)$objid == 0){
								$_err[] = 'Вы не выбрали тему';
							}
						break;
						case 'edittheme':
							$editID = $this->DbManager->selectcell(
									"SELECT `authorID`
									FROM ?#
									WHERE 
									`themeID` = ?d
									AND `authorID` = ?d
									AND `moderated` = 0 LIMIT 1",
									"forum_db_themes", $objid, $userid);

							if ((int)$editID == 0){
								$_err[] = 'Тема уже редактируется';
							}

							if (((int)$editID > 0 && $editID != $userid) || ((int)$userid == 0)){
								$_err[] = 'Нельзя редактировать чужую тему';
							}

							if ((int)$objid == 0){
								$_err[] = 'Вы не выбрали тему';
							}
						break;
					}
			}
			return $_err;
		}
		
		public function listLogActions($userid){
				$_sql = "SELECT *
							FROM ?#
							WHERE 
							`userid` = ?d";
				$list = $this->DbManager->select($_sql,
						"merchant_logactions",
						$userid);

				$_themes = array();
				foreach ($list as $key=>$value){
					if (preg_match("/^(Прикреплен пакет)/ims", $value['action'])){
						preg_match_all("/([0-9]{1,})$/ims", trim($value['action']), $_id);
						$_id = $_id[0][0];
						$_themes[$key] = $_id;
					}
				}
				if (count($_themes) > 0){
					$_sql = "SELECT
								*,
								`themeID` as ARRAY_KEY
								
								FROM ?#
								WHERE 
								`themeID` IN (?a)";

					$listThemes = $this->DbManager->select($_sql,
							"forum_db_themes",
							$_themes);

					foreach ($_themes as $key=>$value){
						$list[$key]['action'] = preg_replace("/(" . $value . ")$/ims", '"<a href="/forum/' . $listThemes[$value]['groupID'] . '/' . $listThemes[$value]['themeID'] . '/" target="_blank">' . $listThemes[$value]['caption'] . '</a>"', $list[$key]['action']);
					}
				}
				return $list;
		}
	}