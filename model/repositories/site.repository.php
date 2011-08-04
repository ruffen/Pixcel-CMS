<?php

class SiteRepository extends MysqlDb{
	public function getSite($id){
		global $dRep;
		$sql = "SELECT * FROM ink_customer_sites WHERE siteId = ?;";
		$row = $this->runSingleQuery($sql, array($id));
		$properties = array(
				'id' => $row['siteId'],
				'name' => $row['sitename'],
				'url' => $row['siteurl'],
				'templates' => $dRep->getTemplateCollection(array('site' => $row['siteId'])),
				'modules' => $dRep->getModuleCollection(array('site' => $row['siteId'])),
				'ftp_url' => $row['ftp_url'],
				'ftp_username' => $row['ftp_username'],
				'ftp_password' => $row['ftp_password'],
				'ftp_root' => $row['ftp_root'],
				'ftp_passive' => $row['ftp_passv'],
				'ftp_mode' => $row['ftp_mode'],
				'ftp_port' => $row['ftp_port']
			);
		$site = new Site();
		$site->setProperties($properties);
		return $site;
	}
	public function getSiteCollection(array$where){
		global $dRep, $INK_User;
		$values = array();
		$innerjoin = '';
		if(isset($where['userId'])){
			$innerjoin = "INNER JOIN ink_sites_in_roles B on (A.siteId = B.SiteId)
						  INNER JOIN ink_user_in_roles C ON (B.roleId = C.roleId AND C.userId = ?)";
			$values[] = $where['userId'];
			unset($where['userId']);
		}else if(isset($where['roleId'])){
			$innerjoin = "INNER JOIN ink_sites_in_roles B on (A.siteId = B.SiteId AND B.roleId = ?)";
			$values[] = $where['roleId'];
			unset($where['roleId']);
		}
		$where = (count($where) > 0) ? 'WHERE '.$this->sqlBuilder->createWhere($where, 'A') : '';
		
		$sql = "SELECT * FROM ink_customer_sites A {$innerjoin} {$where} AND softdelete  = ?;";
		$values[] = false;
		$data = $this->runManyQuery($sql, $values);
		$sites = array();
		foreach($data as $index => $row){
			$properties = array(
					'id' => $row['siteId'],
					'name' => $row['sitename'],
					'url' => $row['siteurl'],
					'templates' => $dRep->getTemplateCollection(array('site' => $row['siteId'])),
					'modules' => $dRep->getModuleCollection(array('site' => $row['siteId'])),
					'ftp_url' => $row['ftp_url'],
					'ftp_username' => $row['ftp_username'],
					'ftp_password' => $row['ftp_password'],
					'ftp_root' => $row['ftp_root'],
					'ftp_passive' => $row['ftp_passv'],
					'ftp_mode' => $row['ftp_mode'],
					'ftp_port' => $row['ftp_port']
				);
			$site = new Site();
			$site->setProperties($properties);
			$sites[] = $site;
		}
		return $sites;
	}
	public function saveSite($site){
		global $INK_User;
		$ftpDetails = $site->getFtpdetails();
		$sql = "INSERT INTO ink_customer_sites (customerId, sitename, siteurl, ftp_url,	ftp_root, ftp_username, ftp_password, ftp_passv, ftp_mode, ftp_port) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$values = array(
			$INK_User->getCustomer()->getId(),
			$site->getName(),
			$site->getUrl(),
			$ftpDetails['url'], 
			$ftpDetails['path'], 
			$ftpDetails['username'], 
			$ftpDetails['password'], 
			$ftpDetails['passv'], 
			$ftpDetails['protocol'], 
			$ftpDetails['port'],
		);
		$id = $this->insertValues($sql, $values);
		$site->setProperties(array('id' => $id));
		$this->saveSiteModules($site);
		$this->addSiteUser($INK_User, $site);
		return $id;
	}
	public function addSiteUser($user, $site){
		global $INK_User;
		$sql = "INSERT INTO ink_site_user (userId, siteId) VALUES (?,?);";
		$values = array($user->getId(), $site->getId());
		$this->insertValues($sql, $values);
	}
	public function updateSite($site){
		global $INK_User;
		$ftpDetails = $site->getFtpdetails();
		$sql = "UPDATE ink_customer_sites SET sitename = ?, siteurl = ?, ftp_url = ?, ftp_root = ?, ftp_username = ?, ftp_password = ?, ftp_passv = ?, ftp_mode = ?, ftp_port = ? WHERE siteID = ? AND customerId = ?;";
		$values = array(
			$site->getName(), 
			$site->getUrl(), 
			$ftpDetails['url'], 
			$ftpDetails['path'], 
			$ftpDetails['username'], 
			$ftpDetails['password'], 
			$ftpDetails['passv'], 
			$ftpDetails['protocol'], 
			$ftpDetails['port'],
			$site->getId(),
			$INK_User->getCustomer()->getId()
		);
		try{
			$this->updateRow($sql, $values);
			$this->saveSiteModules($site);
		}catch(PDOException $e){
			return false;
		}
		return $site->getId();
	}
	public function deletesite($site){
		$sql = "UPDATE ink_customer_sites SET softdelete = ? WHERE siteId = ?;";
		$values = array(true, $site->getId());
		$this->updateRow($sql, $values);
	}
	private function deleteSiteModules($site){
		$sql = "DELETE FROM ink_site_modules WHERE siteId = ?;";
		$this->deleteValues($sql, array($site->getId()));	
	}
	private function saveSiteModules($site){
		$customer = unserialize($_SESSION['customer']);
		$this->deleteSiteModules($site);
		$modules = $site->getModules();
		foreach($modules as $index => $module){
			$sql = "INSERT INTO ink_site_modules (customerId, siteId, moduleId) VALUES (?, ?, ?);";
			$values = array($customer->getId(), $site->getId(), $module->getId());
			$this->insertValues($sql, $values); 
			$kids = $module->getKids();
			foreach($kids as $childModule){
				$sql = "INSERT INTO ink_site_modules (customerId, siteId, moduleId) VALUES (?, ?, ?);";
				$values = array($customer->getId(), $site->getId(), $childModule->getId());
				$this->insertValues($sql, $values); 			
			}
		}
	}
}

?>