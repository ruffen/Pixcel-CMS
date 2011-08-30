<?php
class User extends Asset{
	protected $id;
	protected $username;
	protected $password;
	protected $email;
	protected $firstname;
	protected $lastname;
	protected $roles = array();
	protected $modules = array();
	protected $customer;
	protected $sites = array();
	protected $active;
	
	public function getUsername(){
		return $this->username;	
	}
	public function getModules(){
		return $this->modules;
		throw new ModuleException('No modules found for userid: '.$this->id);
	}
	public function getCustomer(){
		return $this->customer;			
	}
	public function setCustomer($customer){
		$this->customer = $customer;
	}
	public function getFullname(){
		return $this->firstname.' '.$this->lastname;	
	}
	public function getFirstname(){
		return $this->firstname;
	}
	public function getLastname(){
		return $this->lastname;
	}
	public function getEmail(){
		return $this->email;
	}
	public function getRole(){
		if(count($this->roles) == 1){
			return $this->roles[0];
		}
		throw new DataException('rolecount'.count($this->roles));
	}
	public function getRoles(){
		return $this->roles;	
	}
	public function setRole($newRole){
		if(!is_array($newRole)){
			$this->roles = array($newRole);
		}
	}
	public function getPassword(){
		return $this->password;
	}
	public function canPublish($object){
		return true;
	}
	public function setActive($active){
		if($active === true || $active == 1 || $active == 'true'){
			$this->active = true;
		}else{
			$this->active = false;
		}
	}
	public function active(){
		return ($this->active == true);
	}

	public function getSite(){
		if(isset($_SESSION) && isset($_SESSION['site'])){
			$site = unserialize($_SESSION['site']);
			return $site;
		}else if(count($this->sites) > 0){
			return $this->sites[0];
		}
		throw new SiteException('nosites');
	}
	public function getSites(){
		return $this->sites;
	}
	public function HasControllerAccess($module){
		$modules = $this->modules;
		foreach($modules as $index => $userModule){
			if($module->getId() == $userModule->getId()){
				return true;
			}
			foreach($userModule->getKids() as $index => $kidUserModule){
				if($module->getId() == $kidUserModule->getId()){
					return true;
				}
			}
		}
		return false;
	}
}
?>
