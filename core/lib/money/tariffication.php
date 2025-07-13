<?php
	class Money_Tariffication
	{
		private $prefix = 'merchant_';
		private $resultData = array('check' => array('msg' => array(), 'errno' => array(), 'error' => array()), 'data' => array(), 'type' => '', 'table' => '');
		public  $tPeriod  = 600;
		public  $tLife    = 600;
		public  $tactTime = 600;

		public function __construct ($args)
		{
			$this->DbManager = $args[0]['DbManager'];
			if (isset($args[0]['prefix']))
				$this->prefix = $args[0]['prefix'];
		}

		private function saveData(&$data, &$table)
		{
			if (trim($table) == '' || !is_array($data) || count($data) == 0)
				return 0;
			
			$_sql = '';
			foreach ($data as $key=>$value){
				if ($key == 'id')
					continue;
				$_params = "'" . mysql_real_escape_string($value) . "'";
				if ($_sql != ''){
					$_sql .= ",\n";
				}
				$_sql .= "`" . $key . "` = " . $_params;
			}
			$_sql = " SET " . $_sql;

			$_upd = true;
			$_ins_id = array();
			if (is_array($data['id'])){
				$_wsql = "";
				foreach ($data['id'] as &$value){
					if ($_wsql != ''){
						$_wsql .= ',';
					}
					if ((int)$value>0){
						$_ins_id[] = (int)$value;
						$_wsql .= (int)$value;
					}
				}
				$_wsql = "IN (" . $_wsql  . ")";
				$_rid = $_ins_id;
				if (count($_ins_id) == 0)
					$_upd = false;
			}else{
				$_rid = (int)$data['id'];
				$_ins_id[] = $_rid;
				if ((int)$data['id'] == 0)
					$_upd = false;
				$_wsql = "= " . (int)$data['id'];
			}
			if ($_upd > 0){
				$_sql = "UPDATE `" . $this->prefix . $table . "` " . $_sql . " WHERE `id` " . $_wsql;
			}else{
				$_sql = "INSERT INTO `" . $this->prefix . $table . "` " . $_sql;
			}

			$id = $this->DbManager->query($_sql,
									$table
									);

			return ((int)$data['id'] == 0 || count($_ins_id) == 0 ? (int)$id : $_rid);
		}

		private function prepareFieldDisplayPackage(&$data, &$type){
			$_res = array('check' => array('msg' => array(), 'error' => array()), 'data' => $data, 'type' => $type, 'table' => 'packages');
			$_res['data']['display'] = ((int)$data['display'] == 1 ? 1 : 0);
			if ((int)$_res['data'] == 0){
				$_res['check']['error'] = 'Ничего не выбрано';
			}
			return $_res;
		}

		public function prepareFieldBuyByUser(&$data, &$type){
			$_res = $this->resultData;
			$_res['table'] = 'packageforuser';
			$_res['type']  = $type;
			$_res['data']  = array();
			$package_id = $data['packageid'];
			$user_id   = $data['userid'];
			//$user_id, $package_id

			if (!is_array($package_id)){
				$package_id = array($package_id);
			}

			$_cnt = $this->checkPackInUser($package_id, $user_id);

			$_Dt = array(
							'userid'     => $user_id,
							'packageid' => '',
							'user_name'  => $data['user_name']
						);

			foreach ($package_id as $id){
				if ($id > 0){
					$_Dt['packageid'] = $id;
					$_res['data'][] = $_Dt;
				}
			}
			if($_cnt > 0){
				$_res['check']['error'][] = 'Пакеты уже куплен';
			}
			return $_res;
		}

		public function deletePackageInUser($id,$value)
		{
			if ((int)$id>0){
				$this->DbManager->query("
											DELETE FROM ?# WHERE `" . $value . "` = ?d LIMIT 1",
												$this->prefix . "packageforuser",
												$id
										);
				return true;
			}else{
				return false;
			}
		}

		public function deletePackage($id)
		{
			$_cnt = $this->checkPackInUser($id);
			if ($_cnt == 0){
				$_table = array('packages' => 'id',
									'serviceinpackage' => 'packageid');

				foreach ($_table as $key=>$value){
					$this->DbManager->query("
												DELETE FROM ?# WHERE `" . $value . "` = ?d LIMIT 1",
													$this->prefix . $key,
													$id
											);
				}
				return true;
			}else{
				return false;
			}
		}

		
		public function checkPackInUser($id, $uid = 0){
				if (!is_array($id))
					$id = array($id);

				$_cnt = $this->DbManager->select(
					"SELECT
						COUNT(*) as cnt 
					FROM 
						?#
					WHERE 
						`packageid` IN (?a)
						{ AND `userid` = ?d }
						AND status IN (0)
					",
					$this->prefix . "packageforuser",
					$id,
					($uid > 0 ? $uid : DBSIMPLE_SKIP)
				);
				return $_cnt[0]['cnt'];
		}
		
		private function prepareFieldActivePackage(&$data, &$type)
		{
			$data['id'] = (int)$data['id'];
			$data = array('id' => $data['id'], 'isactive' => $data['isactive']);
			$_res = array('check' => array('msg' => array(), 'error' => array()), 'data' => $data, 'type' => $type, 'table' => 'packages');
			if ($data['id']>0 && $_res['data']['isactive'] == 0){
				
				$_cnt = $this->checkPackInUser($data['id']);
				if ($_cnt > 0)
					$_res['check']['error'][] = 'Нельзя деактивировать';

			}elseif($data['id'] == 0){
				$_res['check']['error'][] = 'Ничего не выбрано';
			}

			$_res['data']['isactive'] = ($data['isactive'] == 1 ? 1 : 0);
			return $_res;
		}

		private function prepareFieldServicePackage(&$data, &$type)
		{
			$_res = array('check' => array('msg' => array(), 'error' => array()), 'data' => $data, 'type' => $type, 'table' => 'serviceinpackage');
			if (((int)$data['serviceid'] == 0
						|| (int)$data['packageid'] == 0) && (int)$data['periodical'] == 1){
				$_res['check']['error'][] = 'Не все поля заполнены';
			}
			if ($data['periodical'] != '0'
						&& $data['periodical'] != '1'){
				$_res['check']['error'][] = 'Не выбран тип сервиса';
			}

			$_res['data']['period'] = $_res['data']['period']*$this->tPeriod;
			$_res['data']['acttime'] = $_res['data']['acttime']*$this->tactTime;
			return $_res;
		}

		private function prepareFieldPackage(&$data, &$type)
		{
			$data['lifetime'] = (int)$data['lifetime']*$this->tLife;
			$data['price']    = (float)$data['price'];
			$_res = array('check' => array('msg' => array(), 'error' => array()), 'data' => $data, 'type' => $type, 'table' => 'packages');
			if ($data['name'] == '' ||
					$data['price'] <= 0){
					if ((float)$data['price'] <= 0){
						$_res['check']['error'][] = 'Цена должна быть больше ноля';
					//}elseif((int)$data['lifetime'] <= 0){
						//$_res['check']['error'][] = 'Время жизни должно быть больше ноля';
					}else{
						$_res['check']['error'][] = 'Не все поля заполнены';
					}
			}elseif ((int)$data['id'] == 0){
				$_res['data']['isactive'] = 0;
			}else{
				
			}
			return $_res;
		}

		private function prepareFieldServiceLinkUser(&$data, &$type)
		{
			$_res = $this->resultData;
			$_res['table'] = 'packageforuser';
			$_data = array();
			$_data['id'] = $data['uspid'];
			$_data['objid'] = $data['objid'];
			$_data['objtype'] = $data['objtype'];
			$_time = time();
			$_data['dateactivated'] = date("Y-m-d H:i:s", $_time);
			$_data['dateend'] = date("Y-m-d H:i:s", $_time + $data['lifetime']*$this->tLife);
			$_data['status'] = ($data['lifetime'] > 0 ? 1 : 2);
			$_res['data'] = $_data;
			return $_res;
		}

		public function prepareField(&$data, &$type)
		{
			$_res = array('check' => array('msg' => array(), 'error' => array()), 'data' => $data, 'table' => '');
			switch (strtolower($type)){
				case 'servicelinkuser':
					$_res = $this->prepareFieldServiceLinkUser($data, $type);
				break;
				case 'userpackage':
					$_res = $this->prepareFieldBuyByUser($data, $type);
				break;
				case 'servpackage':
					$_res = $this->prepareFieldServicePackage($data, $type);
				break;
				case 'packactive':
					$_res = $this->prepareFieldActivePackage($data, $type);
				break;
				case 'packdisplay':
					$_res = $this->prepareFieldDisplayPackage($data, $type);
				break;
				case 'package':
					$_res = $this->prepareFieldPackage($data, $type);
				break;
			}
			return $_res;
		}

		public function afterSave(&$data, &$type)
		{
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

			switch (strtolower($type)){
				case 'userpackage':
					if (count($data['check']['error']) == 0) {
						foreach ($data['data'] as $key=>$value){
							$package = $this->DbManager->selectRow(
								"
								SELECT 
									*
								FROM 
									?#
								WHERE 
									`id` = ?d
								",
								$this->prefix . "packages",
								$value['packageid']
							);
							$this->DbManager->query($_sql,
												$this->prefix . 'logactions',
												$value['userid'],
												$value['user_name'],
												'Был куплен пакет "' . $package['name'] . '" [' . $package['id'] . ']',
												"",
												1,
												$package['price']
												);
						}
					}
				break;
				case 'servpackage':
					//$data['data']['period'] = $_res['data']['period']/($this->tPeriod);
					//$data['data']['acttime'] = $_res['data']['acttime']/($this->tactTime);
                    $data['data']['period'] = $data['data']['period']/($this->tPeriod);
                    $data['data']['acttime'] = $data['data']['acttime']/($this->tactTime);
				break;
				case 'package':
					$data['data']['lifetime'] = $data['data']['lifetime']/($this->tLife);
				break;
				case 'servicelinkuser':
					if (count($data['check']['error']) == 0) {
						switch($data['data']['objtype']){
							case 'theme':
							break;
						}
					}
				break;
			}
			return $data;
		}

		public function writeData($data, $type)
		{
			$_res = $this->prepareField($data, $type);
			if (count($_res['check']['error']) == 0){
				if (isset($_res['data'][0]) && is_array($_res['data'][0])){
					foreach ($_res['data'] as $key=>$value){
						$_res['data'][$key]['id'] = $this->saveData($value, $_res['table']);
					}
				}else{
					$_res['data']['id'] = $this->saveData($_res['data'], $_res['table']);
				}
			}
			return $this->afterSave($_res, $type);
		}
		
		public function infoPackage($objEditId){
			$_res = $this->DbManager->select(
				"
				SELECT 
					*,
					(`lifetime` / " . $this->tLife . ") as `lifetime`
				FROM 
					?#
				WHERE 
					`id` = ?d
				",
				$this->prefix . "packages",
				$objEditId
			);
			$_res[0]['groups_id'] = $this->mkString2Array($_res[0]['groups_id']);
			return $_res;
		}

		public function listServiceByPackage($objEditId){
			return $this->DbManager->select(
						"SELECT
							s.name,
							sp.*,
							(sp.`period` / " . $this->tPeriod . " ) as period,
							(sp.`acttime` / " . $this->tactTime . " ) as acttime,
							s.id sId
						FROM 
							?# s
						LEFT JOIN ?# sp
							ON sp.serviceid = s.id AND sp.packageid = ?d
						ORDER BY
							id DESC, s.name
						",
						$this->prefix . "services",
						$this->prefix . "serviceinpackage",
						$objEditId
					);
		}
		
		public function listTableServices($_package_id = array(), $display = array(1)){
			if (!is_array($_package_id) && (int)$_package_id>0)
				$_package_id = array($_package_id);
			elseif (!is_array($_package_id))
				$_package_id = array();

			$_list = $this->DbManager->query(
				"SELECT 
					s.*,
					sp.*,
					p.*,
					p.name as pname,
					s.name as sname,
					sp.id spid,
					(sp.`period` / " . $this->tPeriod . " ) as period,
					(sp.`acttime` / " . $this->tactTime . " ) as acttime,
					(p.`lifetime` / " . $this->tLife . ") as `lifetime`
				FROM
					?# p
				JOIN ?# sp
					ON sp.packageid = p.id
				JOIN ?# s
					ON sp.serviceid = s.id
				WHERE
					p.display IN (?a) AND p.isactive = 1
					{ AND p.id IN (?a) }
				ORDER BY
					p.pos
				",
				$this->prefix . "packages",
				$this->prefix . "serviceinpackage",
				$this->prefix . "services",
				$display,
				(count($_package_id) > 0 ? $_package_id : DBSIMPLE_SKIP )
			);

			if (count($_list)>0){
				$_cnt = 0;
				$_name = array();
				$_keyservices = array();
				
				foreach ($_list as $value){
					$_name[$value['serviceid']] = $value['sname'];
				}
	
				asort($_name);
	
				foreach ($_list as $value){
					if (!isset($_keyservices[$value['system_name']]))
						$_keyservices[$value['system_name']] = array('sid' => $value['serviceid'], 'name' => $value['sname'], 'packages' => array());

					$_keyservices[$value['system_name']]['packages'][$value['id']] = $value['id'];

					$value['groups_id'] = $this->mkString2Array($value['groups_id']);
					$_res[$value['packageid']]['info'] = $value;
					$_res[$value['packageid']]['list'][$value['serviceid']] = $value;
				}
			}
			return array('service' => $_name, 'packages' => $_res, 'keyservices' => $_keyservices);
		}

		public function getListPackageByUser($userid, $status = '', $display = array(1))
		{
			$_res = array();
			$userid = (int)$userid;
			if ($userid>0){

				$_packages = $this->DbManager->select(
					"SELECT
						`packageid`, `status`
					FROM
						?#
					WHERE
						`userid` = ?d
						{AND `status` < ?d}
					",
					$this->prefix . "packageforuser",
					$userid,
					($status == 'notlink' ? 1 : DBSIMPLE_SKIP)
				);

				$_in_pack = array();
				foreach ($_packages as $value){
					$_in_pack[] = $value['packageid'];
					$_in_status[$value['packageid']] = $value['status'];
				}

				if (is_array($_in_pack) && count($_in_pack)>0){
					$_res = $this->listTableServices($_in_pack, $display);
					foreach ($_in_status as $key=>$value){
						$_res['packages'][$key]['info']['status'] = $value;
					}
				}
			}
			return $_res;
		}

		public function getPackageByServiceInUser($service, $userid, $nservice = array(), $params = array())
		{
			$_res = array();
			$userid = (int)$userid;
			if (!is_array($service) && $service != ''){
				$service = array($service);
			}elseif(!is_array($service)){
				$service = array();
			}
            //echo var_export($service,true).'<br>';
			if (!is_array($nservice))
				$nservice = array($nservice);
            //echo var_export($nservice,true).'<br>';
			if ((count($service) > 0 || count($nservice) > 0) && $userid>0){
				$_packages = $this->DbManager->selectcol(
					"SELECT
						`packageid` AS ARRAY_KEY,
						`packageid`
					FROM
						?#
					WHERE
						`userid` = ?d
					AND
						status = 0
					",
					$this->prefix . "packageforuser",
					$userid
				);
                //echo var_export($_packages,true).'<br>';

				if (is_array($_packages) && ($_packages)>0){
					$_npackages = array();
					if (is_array($nservice) && count($nservice) > 0){
						$_npackages = $this->DbManager->selectcol(
								"SELECT
									p.id
								FROM
									?# p
								JOIN
									?# sp ON sp.packageid = p.id
								JOIN
									?# s 
									ON
										sp.serviceid = s.id
								WHERE
									p.id IN (?a)
									AND s.system_name IN (?a)
								GROUP BY p.id
								ORDER BY
									p.name
								",
								$this->prefix . "packages",
								$this->prefix . "serviceinpackage",
								$this->prefix . "services",
								$_packages,
								$nservice
						);
						foreach ($_npackages as $value){
							if ($_packages[$value])
								unset($_packages[$value]);
						}
					}

					if (count($_packages)>0){

						$_packages = $this->DbManager->select(
							"SELECT
								p.id AS ARRAY_KEY,
								p.id,
								p.name,
								s.system_name
							FROM
								?# p
							JOIN
								?# sp ON sp.packageid = p.id
							JOIN
								?# s 
								ON
									sp.serviceid = s.id
							WHERE
								p.id IN (?a)
								{AND (p.groups_id LIKE ?s OR p.groups_id = '')}
								{AND s.system_name IN (?a)}
							GROUP BY p.id
							ORDER BY
								p.name
							",
							$this->prefix . "packages",
							$this->prefix . "serviceinpackage",
							$this->prefix . "services",
							$_packages,
							(isset($params['ingroup']) ? '%,' . implode(',', $params['ingroup']) . ',%' : DBSIMPLE_SKIP),
							(count($service) > 0 ? $service : DBSIMPLE_SKIP)
						);

						if (is_array($_packages) && count($_packages)>0){
							$_res = $_packages;
						}
					}
				}
			}
			return $_res;
		}

		public function getMakeCheckPackage($packageid, $userid, $checkservice = ''){
			$_res = array();
			if (!is_array($packageid) && (int)$packageid>0)
				$packageid	= array((int)$packageid);
			elseif(!is_array($packageid))
				$packageid	= array();

			$userid		= (int)$userid;
			if (count($packageid) > 0 && (int)$userid>0){
				$_id = $this->DbManager->selectcell(
					"SELECT
						`id`
					FROM
						?#
					WHERE
						`userid` = ?d
					AND
						`packageid` IN (?a)
					AND
						`status` = 0
					LIMIT 1
					",
					$this->prefix . "packageforuser",
					$userid,
					$packageid
				);

				if ($_id > 0){
					$_err = true;
					$_res = $this->listTableServices($packageid);
					foreach ($_res['packages'] as $kpackage=>$package){
						$_res['packages'][$kpackage]['info']['uspid']= $_id;
						if (trim($checkservice) != ''){
							foreach ($package['list'] as $kservice=>$service){
								$_res['packages'][$kpackage]['list'][$kservice]['uspid']= $_id;
								if ($service['system_name'] == $checkservice){
									$_err = false;
								}
							}
							if ($_err)
								$_res = array();						
						}
					}
				}
			}
			return $_res;
		}
		
		public function endDate($start, $time, $type){
			$_parse = date_parse($start);
			if (isset($_parse['year']) && $_parse['year']>0){
				$_time  = mktime($_parse['hour'], $_parse['minute'], $_parse['second'], $_parse['month'], $_parse['day'], $_parse['year']);
				return date("Y-m-d H:i:s", ($_time + $this->mkTime($time, $type)));
			}else{
				return false;
			}
		}

		public function mkTimeInByMethodCall($element, $method = null, $format = ''){
			
		}
		
		public function mkTimeInByMethod($list, $method = null, $format = ''){
			//$value['packages']['period'], 'period', 'hour', '%01.2f'
			if (is_array($list['packages'])){
				foreach ($list['packages'] as $keyPackage=>$valuePackage){
					$list['packages'][$keyPackage]['info']['period'] = $this->mkTimeByMethod($valuePackage['info']['period'], 'period', $method, $format);
					$list['packages'][$keyPackage]['info']['acttime'] = $this->mkTimeByMethod($valuePackage['info']['acttime'], 'acttime', $method, $format);
					$list['packages'][$keyPackage]['info']['lifetime'] = $this->mkTimeByMethod($valuePackage['info']['lifetime'], 'life', $method, $format);
					if (is_array($valuePackage['list'])){
						foreach ($valuePackage['list'] as $keyList=>$valueList){
							$list['packages'][$keyPackage]['list'][$keyList]['period'] = $this->mkTimeByMethod($valueList['period'], 'period', $method, $format);
							$list['packages'][$keyPackage]['list'][$keyList]['acttime'] = $this->mkTimeByMethod($valueList['acttime'], 'acttime', $method, $format);
							$list['packages'][$keyPackage]['list'][$keyList]['lifetime'] = $this->mkTimeByMethod($valueList['lifetime'], 'life', $method, $format);
						}
					}
				}
			}
			return $list;
		}

		public function mkTimeByMethod($time, $type, $method = null, $format = ''){
			$_time = $this->mkTime($time, $type);
			switch ($method){
				case 'day':
					$_res = ((int)$_time/(24*60*60));
				break;
				case 'hour':
					$_res = ((int)$_time/(60*60));
				break;
				default:
					$_res = $time;
				break;
			}

			if ($format != ''){
				return sprintf($format, $_res);
			}else{
				return $_res;
			}
		}

		public function mkTime($time, $type){
			$times = array('period' => $this->tPeriod, 'life' => $this->tLife, 'acttime' => $this->tactTime);
			if (isset($times[$type])){
				return ($time * $times[$type]);
			}
			return $time;
		}
		
		public function mkString2Array($string){
			$_list = explode(',', preg_replace("/^(,)(.*?)(,)$/ims", "\$2", $string));
			$_res = array();
			foreach ($_list as $key=>$value){
				if (trim($value) != '')
					$_res[$value] = $value;
			}
			return $_res;
		}
		
		public function mkArray2String($array){
			if (!is_array($array)){
				return '';
			}else{
				return ',' . implode(',', $array) . ',';
			}
		}
	}