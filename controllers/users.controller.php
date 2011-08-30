<?php
class UsersController extends BaseController{
	public function index(){
		$user = $this->getUser();
		ob_start();
		$this->groupprofile();
		$this->template->details = ob_get_clean();
		ob_start();
		$this->UsersMenu();
		$this->template->usermenu = ob_get_clean();
	}
	public function userprofile(){
		$this->template->user = $this->getUser();
		$customer = $this->INK_User->getCustomer();
		$this->template->customer = $customer;
		$this->template->groups = $this->dRep->getRoleCollection(array('customer' => $customer->getId()));
	}
	public function deleteGroup(){
		$group = $this->getGroup();
		if($group->hasUsers()){
			echo json_encode(array('error' => 'group_hasusers'));
			return false;
		}else if($group->getId() == 'new'){
			echo json_encode(array('error' => 'group_new'));
			return false;
		}
		$this->dRep->delRole($group);
		echo json_encode(array('success' => 'group_deleted'));
	}
	public function saveGroup(){
		global $varChecker, $fido;
		$group = $this->getGroup();
		$modules = $this->INK_User->getSite()->getModules();
		
		foreach($modules as $index => $module){
			$group->setAccess($module, ($varChecker->getValue('module_'.$module->getId()) == 'true'));
			foreach($module->getRights() as $index => $right){
				if($varChecker->getValue('right_'.$right->getId().'_'.$module->getId()) == 'true'){
					$group->setAccess($right, $module->getId());
				}
			}
			foreach($module->getKids() as $index => $module){
				$group->setAccess($module, ($varChecker->getValue('module_'.$module->getId()) == 'true'));
			}
		}
		$sites = array();
		foreach($this->INK_User->getCustomer()->getSites() as $site){
			if($varChecker->getValue('site_'.$site->getId()) == 'true'){
				$sites[] = $site;
			}
		}
		if(count($sites) == 0){
			throw new DataException('userneedsite');
		}
		$name = $varChecker->getValue('name');
		$group->setProperties(array('name' => $name, 'sites' => $sites));
		$this->dRep->saveRole($group);
		$this->INK_User->setRole($group);
		$fido->updateUser($this->INK_User);
		
		echo json_encode(array('success' => 'savedgroup', 'id' => $group->getId()));
	}
	public function saveUser(){
		global $varChecker;
		$result = array();
		$group = $this->dRep->getRole($varChecker->getValue('group'));
		$result['elements'] = '';
		if($group->getId() == 'new'){
			$result['elements'] .= 'group;';
		}
		$properties = array();
		$elements = array('altered_username' => 'username', 'firstname', 'lastname', 'email');

		foreach($elements as $index => $element){
			$key = (is_int($index)) ? $element : $index;
			$value = $varChecker->getValue($key);
			if(trim($value) == ''){
				$result['elements'] .= $element.';';
			}
			$properties[$element] = $value;
		}
		$user = $this->getUser();		
		if($varChecker->getValue('altered_password') != ''){
			if($varChecker->getValue('altered_password') == $varChecker->getValue('altered_password_repeat')){
				$properties['password'] = $varChecker->getValue('altered_password');
				$result['changed_pass'] = 'success';
			}else{
				$result['elements'] .= 'password;password_repeat';
				$result['error'] = (!isset($result['elements'])) ? 'passworderror' : $result['elements'];
			}
		}elseif($user->getId() == 'new'){
			$result['elements'] .= 'password;password_repeat';
			$result['error'] = (!isset($result['elements'])) ? 'nopassword' : $result['elements'];
		}
		
		if($result['elements'] != ''){
			$result['error'] = 'elementerror';
			$result['elements'] = trim($result['elements'], ';');
			echo json_encode($result);
			return ;
		}
		$user->addRole($group);
		$user->setProperties($properties);
		$user->setActive($varChecker->getValue('active'));
		$this->dRep->saveUser($user);

		if($this->INK_User->getId() == $user->getId()){
			global $fido;
			$fido->updateUser($user);
		}
		echo json_encode(array('success' => 'user_saved', 'id' => $user->getId()));
	}
	public function deleteUser(){
		$user = $this->getUser();
		if($user->getId() == $this->INK_User->getId()){
			echo json_encode(array('error' => 'delete_self'));
		}else{
			$this->dRep->delUser($user);
			echo json_encode(array('success' => 'user_deleted'));		
		}
	}
	public function UsersMenu(){
		global $varChecker;
		try{
			$drag = ($varChecker->getValue('drag') !== false && $varChecker->getValue('drag') != '') ? true : false;
		}catch(DataException $e){
			$drag = false;
		}
		
		$customer = $this->INK_User->getSite();
		$groups = $this->dRep->getRoleCollection(array('customer' => $customer->getId()));
		foreach($groups as $index => $group){
			ob_start();
			foreach($group->getUsers() as $userId => $ingroup){
				if($ingroup){
					$user = $this->dRep->getUser($userId);
					include('view/users/userrow.index.php');
				}
			}
			$users = ob_get_clean();
			include('view/users/grouprow.index.php');
		}
	}
	public function saveusermenu(){
		global $varChecker, $fido;
		$users = $varChecker->getValue('users');
		foreach($users as $groupId => $userlist){
			$id = str_replace('group_', '', $groupId);
			$group = $this->dRep->getRole($id);
			$users = array();
			foreach($userlist as $index => $userId){
				$uId = str_replace('user_', '', $userId);
				$user = $this->dRep->getUser($uId);
				$user->setProperties(array('role' => $group));
				$this->dRep->saveUser($user);
				if($this->INK_User->getId() == $user->getId()){
					$fido->updateUser($user);
				}
				$users[$user->getId()] = true;
			}
			$group->setProperties(array('users' => $users));
			$this->dRep->saveRole($group);
		}
		echo json_encode(array('success' => 'usermap_saved'));
	}
	public function groupprofile(){
		$site = $this->INK_User->getSite();
		$group = $this->getGroup();
		$modules = $site->getModules();
		$customer = $this->INK_User->getCustomer();
		
		include_once('view/users/groupdetails.users.php');
	}
	private function getGroup(){
		global $varChecker;
		try{
			if($varChecker->getValue('id') == 'new'){
				$group = new Role();
				$group->setProperties(array('id' => 'new'));
				return $group;
			}
			return $this->dRep->getRole($varChecker->getValue('id'));
		}catch(DataException $e){
			global $logger;
			$logger->Error("no variable id", $e);
		}
		$customer = $this->INK_User->getCustomer();
		$groups = $this->dRep->getRoleCollection(array('customer' => $customer->getId()));
		if(count($groups) == 0){
			throw new Exception('nogroup');
		}
		return array_shift($groups);
	}
	private function getUser(){
		global $varChecker;
		try{
			if($varChecker->getValue('id') == 'new'){
				$user = new User();
				$user->setProperties(array('id' => 'new'));
				return $user;
			}
			$user = $this->dRep->getUser($varChecker->getValue('id'));
			return $user;
		}catch(DataException $e){
			return $this->INK_User;	
		}
	}
}
?>