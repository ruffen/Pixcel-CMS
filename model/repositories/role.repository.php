<?php
class RoleRepository extends MysqlDb{
	
	public function getRoleCollection($where){
		//here we need to find out what roles we are after
		foreach($where as $key => $value){
			switch($key){
				case 'userId' : return $this->getUserRoles($where);break;
				default : return $this->getCustomerRoles($where);break;
			}	
		}
	}
	public function getRole($id){
		global $dRep;
		$sql = "SELECT * FROM ink_roles WHERE roleId = ?";
		$row = $this->runSingleQuery($sql, array($id));
		$properties = array(
			'id' => $row['roleId'],
			'name' => $row['roleName'],
			'description' => $row['description'],
			'customer' => $row['customerId'],
			'moduleaccess' => $this->getModuleAccess($row['roleId']),
			'users' => $this->getUsers($row['roleId']),
			'rights' => $this->getRoleModuleRights($row['roleId']),
			'sites' => $dRep->getSiteCollection(array('roleId' => $row['roleId']))
			);
		$role = new Role();
		$role->setProperties($properties);		
		return $role;
	}
	public function saveRole($role){
		$customerId = $role->getCustomer();
		$sql = "INSERT INTO ink_roles (customerId, roleName, description) VALUES (?, ?, ?);";
		$values = array($customerId, $role->getName(), $role->getDescription());
		$id = $this->insertValues($sql, $values);
		$role->setProperties(array('id' => $id));
		$this->saveModuleAccess($role);
		$this->saveModuleRights($role);
		$this->saveRoleSites($role);
		$this->saveRoleUsers($role);
		return $id;
	}
	public function updateRole($role){
		$sql = "UPDATE ink_roles SET roleName = ?, description = ? WHERE roleId = ?;";	
		$values = array($role->getName(), $role->getDescription(), $role->getId());
		$this->insertValues($sql, $values);
		$this->saveModuleAccess($role);
		$this->saveModuleRights($role);
		$this->saveRoleSites($role);
		$this->saveRoleUsers($role);
		return $role->getId();
	}
	public function saveRoleUsers($role){
		foreach($role->getUsers() as $userId => $access){
			$values = array('userId' => $userId, 'roleId' => $role->getId());
			$sql = "DELETE FROM ink_user_in_roles WHERE userId = :userId AND roleId = :roleId;";
			$this->deleteValues($sql, $values);			
			if($access){
				$sql = "INSERT INTO ink_user_in_roles (userId, roleId) VALUES (:userId, :roleId);";
				$this->updateRow($sql, $values);
			}
		}
	}
	public function deleteRoleSites($role){
		$sql = "DELETE FROM ink_sites_in_roles WHERE roleId = ?";
		$this->deleteValues($sql, array($role->getId()));
	}
	public function saveRoleSites($role){
		$this->deleteRoleSites($role);
		foreach($role->getSites() as $site){
			$sql = "INSERT INTO ink_sites_in_roles (siteId, roleId) VALUES (?, ?)";
			$this->insertValues($sql, array($site->getId(), $role->getId()));
		}
	}
	public function deleteRole($role){
		$this->deleteModuleAccess($role);
		$sql = "DELETE FROM ink_roles WHERE roleId = ?";
		$this->deleteValues($sql, array($role->getId()));
	}
	private function saveModuleAccess($role){
		$this->deleteModuleAccess($role);
		$accessArray = $role->getModuleaccess();
		foreach($accessArray as $moduleId => $access){
			if($access){
				$sql = "INSERT INTO ink_modules_in_roles (roleId, moduleId) VALUES (?, ?);";
				$this->insertValues($sql, array($role->getId(), $moduleId));
			}
		}
	}
	private function saveModuleRights($role){
		$this->deleteModuleRights($role);
		$rights = $role->getRights();
		foreach($rights as $rightId => $moduleId){
			$sql = "INSERT INTO ink_module_role_rights (moduleId, rightId, roleId) VALUES (?, ?, ?);";
			$this->insertValues($sql, array($moduleId, $rightId, $role->getId()));
		}
	}
	private function deleteModuleRights($role){
		$sql = "DELETE FROM ink_module_role_rights where roleId = ?";
		$this->deleteValues($sql, array($role->getId()));
	}
	private function deleteModuleAccess($role){
		$sql = "DELETE FROM ink_modules_in_roles WHERE roleId = ?";
		$this->deleteValues($sql, array($role->getId()));
	}
	private function getCustomerRoles(array$where){
		global $dRep;
		$where = $this->sqlBuilder->createWhere($where);
		$sql = "SELECT * FROM ink_roles WHERE {$where}";
		$data = $this->runManyQuery($sql);
		$roles = array();
		foreach($data as $index => $row){
			$properties = array(
				'id' => $row['roleId'],
				'name' => $row['roleName'],
				'description' => $row['description'],
				'customer' => $row['customerId'],
				'moduleaccess' => $this->getModuleAccess($row['roleId']),
				'users' => $this->getUsers($row['roleId']),
				'rights' => $this->getRoleModuleRights($row['roleId']),
				'sites' => $dRep->getSiteCollection(array('roleId' => $row['roleId']))
			);
			$role = new Role();
			$role->setProperties($properties);
			$roles[] = $role;	
		}
		return $roles;
	}
	private function getRoleModuleRights($roleId){
		$sql = "SELECT * FROM ink_module_role_rights WHERE roleId = ?;";
		$data = $this->runManyQuery($sql, array($roleId));
		$access = array();
		foreach($data as $index => $row){
			$access[$row['rightId']] = $row['moduleId'];
		}
		return $access;
	}
	private function getUsers($roleId){
		$sql = "SELECT * FROM ink_user_in_roles WHERE roleId = ?;";
		$data = $this->runManyQuery($sql, array($roleId));
		$users = array();
		foreach($data as $index => $row){
			$users[$row['userId']] = true;
		}
		return $users;
	}
	private function getModuleAccess($roleId){
		$sql = "SELECT A.* FROM ink_modules_in_roles A WHERE roleId = ?";
		$data = $this->runManyQuery($sql, array($roleId));
		$moduleaccess = array();
		foreach($data as $index => $row){
			$moduleaccess[$row['moduleId']] = true;
		}
		return $moduleaccess;
	}
	private function getUserRoles(array$where){
		global $dRep;
		$where = $this->sqlBuilder->createWhere($where, 'B');
		$sql = "SELECT A.* FROM ink_roles A
				INNER JOIN ink_user_in_roles B ON (A.roleId = B.roleId)
				WHERE {$where}";
		$data = $this->runManyQuery($sql);
		$roles = array();
		foreach($data as $index => $row){
			$properties = array(
					'id' => $row['roleId'],
					'name' => $row['roleName'],
					'description' => $row['description'],
					'customer' => $row['customerId'],
					'moduleaccess' => $this->getModuleAccess($row['roleId']),
					'rights' => $this->getRoleModuleRights($row['roleId']),
					'sites' => $dRep->getSiteCollection(array('roleId' => $row['roleId']))
					);
			$role = new Role();
			$role->setProperties($properties);
			$roles[] = $role;	
		}
		return $roles;	
	}
}
?>