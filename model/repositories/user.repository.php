<?php
class UserRepository extends MysqlDb{
	private $users = array();
	public function getUser($userId){
		global $dRep, $INK_User;
		if(isset($this->users[$userId])){
			return $this->users[$userId];	
		}
		$user = $this->searchUser(array('A.userId' => $userId));
		$this->users[$userId] = $user;
		return $user;
	}
	public function getUserCollection($where){
		if(array_key_exists('username', $where) && array_key_exists('password', $where)){
			return $this->searchUser($where);
		}
	}
	public function searchUser($where){
		global $dRep;
		$where = $this->sqlBuilder->createWhere($where, 'A', false);
		$sql = "SELECT A.* FROM ink_user A
				WHERE {$where};";
				
		$row = $this->runSingleQuery($sql);
		if(!isset($row['userId'])){
			throw new DataException('nouser_fromsql');
		}
		$properties = array(
			'id' => $row['userId'],
			'username' => $row['username'],
			'password' => $row['password'],
			'email' => $row['email'],
			'firstname' => $row['firstname'],
			'lastname' => $row['lastname'],
			'customer' => $dRep->getCustomer($row['customerId']),
			'roles' => $dRep->getRoleCollection(array('userId' => $row['userId'])),
			'module' => $dRep->getModuleCollection(array('userId' => $row['userId'], 'parent' => 0)),
		);
		$user = new User();
		$user->setProperties($properties);
		$user->setActive($row['active']);
		$this->users[$row['userId']] = $user;
		return $user;
	}
	public function saveUser($user){
		if(!isset($_SESSION['customer'])){
			$customer = $user->getCustomer();
		}else{
			$customer = unserialize($_SESSION['customer']);		
		}
		$sql = "INSERT INTO ink_user (customerId, username, password, email, firstname, lastname, active) VALUES (?, ?, ?, ?, ?, ?, ?);";
		$id = $this->insertValues($sql, array($customer->getId(), $user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getFirstname(), $user->getLastname(), $user->active));
		$user->setProperties(array('id' => $id));
		$this->saveUserRole($user);
		return $id;
	}
	public function deleteUser($user){
		$this->deleteUserroles($user);
		$this->deleteUsersites($user);
		$sql = "DELETE FROM ink_user WHERE userId = ?;";
		$this->deleteValues($sql, array($user->getId()));
	}
	public function saveUserRole($user){
		$this->deleteUserroles($user);
		//make thsi into loop, for more roles
		$roles = $user->getRoles();
		foreach($roles as $index => $role){
			$sql = "INSERT INTO ink_user_in_roles (userId, roleId) VALUES (?, ?);";
			$values = array($user->getId(), $role->getId());
			$this->insertValues($sql, $values);
		}
	}
	private function deleteUserroles($user){
		$values = array($user->getId());
		$sql = "DELETE FROM ink_user_in_roles WHERE userId = ?;";
		$this->deleteValues($sql, $values);
	}
	public function updateUser($user){
		$sql = "UPDATE ink_user SET username = ?, password = ?, email = ?, firstname = ?, lastname = ?, active = ? WHERE userId = ?;";
		$active = ($user->active()) ? 1 : 0;
		$values = array($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getFirstname(), $user->getLastname(), $active, $user->getId());
		$this->updateRow($sql, $values);
		$this->saveUserRole($user);
		return $user->getId();
	}
	
}
?>