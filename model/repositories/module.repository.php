<?php
class ModuleRepository extends MysqlDb{
	public function getModule($modulename){
		$sql = "SELECT * FROM ink_modules WHERE LOWER(routeName) = ?;";
		$row = $this->runSingleQuery($sql, array(strtolower($modulename)));
		if(count($row) == 0){
			throw new DataException("nomodule");
		}
		$properties = array(
			'id' => $row['moduleId'],
			'name' =>  $row['moduleName'],
			'route' => $row['routeName'],
			'standard' => $row['standardmodule'],
			'description' => $row['description'],
			'classname' => $row['className'],
			'index' => $row['cmsIndex'],
			'prio' => $row['priority'],
			'kids' => $this->getModuleCollection(array('parent' => $row['moduleId'])),
			'parent' => $row['parent'],
			'system' => $row['systemModule'],
			'rights' => $this->getModuleRights($row['moduleId'])
			);
		$module = new Module();
		$module->setProperties($properties);
		return $module;
	}
	public function getModuleCollection($where){
		if(array_key_exists('userId', $where)){
			return $this->getUserModules($where);	
		}else if(array_key_exists('site', $where)){
			return $this->getSiteModules($where);
		}
	}
	public function getSiteModules($where){
		$where['parent'] = (!isset($where['parent'])) ? 0 : $where['parent'];
		$sql = "SELECT * FROM ink_modules 
				WHERE parent = :parent AND (standardmodule = 1 OR moduleId IN (
					SELECT moduleId FROM ink_site_modules WHERE siteId = :site
				)) ORDER BY priority;";
		$data = $this->runManyQuery($sql, $where);
		$modules = $this->buildModulesFromSqlData($data, $where, $where['site']);
		return $modules;
	}
	public function getUsermodules($where){
		global $dRep;
		if(!isset($where['userId'])){
			throw new DataException('missing_param');
		}
		if(!isset($where['parent'])){
			$where['parent'] = 0;
		}
		$sites = $dRep->getSiteCollection(array('userId' => $where['userId']));
		$modules = array();
		$sql = "SELECT * FROM ink_modules A WHERE A.moduleId IN(
					SELECT B.moduleId FROM ink_site_modules B WHERE B.siteId = ? OR B.moduleId IN (
						SELECT C.moduleId FROM ink_modules C WHERE C.standardmodule = 1
					)
				) AND A.moduleId IN(
					SELECT D.moduleId FROM ink_modules_in_roles D 
					INNER JOIN ink_user_in_roles F ON (F.roleId = D.roleId AND F.userId = ?)
					INNER JOIN ink_user G ON (F.userId = G.userId)
					INNER JOIN ink_roles E ON (E.roleId = D.roleId AND E.customerId = G.customerId)

				) AND A.parent = ? ORDER BY A.priority ASC;;";
		foreach($sites as $index => $site){
			$values = array($site->getId(), $where['userId'], $where['parent']);
			$data = $this->runManyQuery($sql, $values);
			if($where['parent'] == 0){
				$newModules = $this->buildModulesFromSqlData($data, $where, $site->getId());
				$modules[$site->getId()] = $newModules[$site->getId()];
			}else{
				return $this->buildModulesFromSqlData($data, $where, $site->getId());
			}
		}
		return $modules;
	}
	private function buildModulesFromSqlData($data, $where, $siteId){
		$modules = array();
		if($where['parent'] == 0 && count($data) == 0){
			$modules[$siteId] = array();
		}
		foreach($data as $index => $row){
			$where['parent'] = $row['moduleId'];
			$properties = array(
				'id' => $row['moduleId'],
				'name' =>  $row['moduleName'],
				'route' => $row['routeName'],
				'standard' => $row['standardmodule'],
				'description' => $row['description'],
				'classname' => $row['className'],
				'index' => $row['cmsIndex'],
				'prio' => $row['priority'],
				'kids' => $this->getModuleCollection($where),
				'parent' => $row['parent'],
				'system' => $row['systemModule'],
				'rights' => $this->getModuleRights($row['moduleId'])
			);
			$module = new Module();
			$module->setProperties($properties);
			if($row['parent'] > 0){
				$modules[] = $module;
			}else{
				$modules[$siteId][] = $module;			
			}
		}
		
		return $modules;	
	}
	private function getModuleRights($moduleId){
		$sql = "SELECT * FROM ink_rights A INNER JOIN ink_module_rights B ON (A.rightId = B.rightId) WHERE B.moduleId = ?";
		$data = $this->runManyQuery($sql, array($moduleId));
		
		$rights = array();
		foreach($data as $index => $row){
			$right = new Right();
			$right->setProperties(array('id' => $row['rightId'], 'name' => $row['name'], 'key' => $row['key'], 'description', $row['description']));
			$rights[] = $right;
		}
		return $rights;
	}
}
?>