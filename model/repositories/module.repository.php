<?php
class ModuleRepository extends MysqlDb{
	public function getModule($routename){
		if(is_array($routename)){
			$where = $this->sqlBuilder->createWhere($routename);
			$values = array();
		}else if(strtolower($routename) == 'index'){
			$where = 'cmsIndex = ?';
			$values = array(true);
		}else{
			$where = 'LOWER(routename) = ?';
			$values = array($routename);
		}
		
		$sql = "SELECT * FROM ink_modules WHERE {$where}";
		$row = $this->runSingleQuery($sql, $values);
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
			throw new Exception('removed function');
		}else if(array_key_exists('customer', $where)){
			return $this->getStandardmodules($where);
		}
	}
	public function getCustomerModules($where){
		if(!isset($where['parent'])){
			$where['parent'] = 0;
		}
		$sql = "SELECT * FROM ink_modules WHERE standardmodule = :standard AND parent = :parent";
	
	}
	public function getUsermodules($where){
		global $dRep;
		if(!isset($where['userId'])){
			throw new DataException('missing_param');
		}
		if(!isset($where['parent'])){
			$where['parent'] = 0;
		}
		$customer = unserialize($_SESSION['customer']);
		$modules = array();
		$sql = "SELECT * FROM ink_modules A 
					WHERE (
						A.moduleId IN(
							SELECT B.moduleId FROM ink_customer_modules B WHERE B.customerId = ? OR B.moduleId IN (
								SELECT C.moduleId FROM ink_modules C WHERE C.standardmodule = 1
							)
						) OR A.moduleId IN(
							SELECT D.moduleId FROM ink_modules_in_roles D 
							INNER JOIN ink_user_in_roles F ON (F.roleId = D.roleId AND F.userId = ?)
							INNER JOIN ink_user G ON (F.userId = G.userId)
							INNER JOIN ink_roles E ON (E.roleId = D.roleId AND E.customerId = G.customerId)
						)
					)
				AND A.parent = ? ORDER BY A.priority ASC;";
		$values = array($customer->getId(), $where['userId'], $where['parent']);
		$data = $this->runManyQuery($sql, $values);
		if($where['parent'] == 0){
			$modules = $this->buildModulesFromSqlData($data, $where);
		}else{
			return $this->buildModulesFromSqlData($data, $where);
		}
		return $modules;
	}
	private function buildModulesFromSqlData($data, $where){
		$modules = array();
		if($where['parent'] == 0 && count($data) == 0){
			$modules[] = array();
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
			$modules[] = $module;			
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