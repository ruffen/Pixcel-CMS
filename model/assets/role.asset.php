<?php
class Role extends Asset{
	protected $id;
	protected $name;
	protected $description;
	protected $customer;
	protected $rights = array();
	protected $moduleaccess = array();
	protected $pageaccess = array();
	protected $users = array();
	protected $sites = array();
	
	public function getName(){
		return $this->name;	
	}
	public function getDescription(){
		return $this->description;	
	}
	public function hasAccess($object, $module = false){
		if($object instanceof Module){
			return (isset($this->moduleaccess[$object->getId()]) && $this->moduleaccess[$object->getId()]) ? true : false;
		}else if($object instanceof User){
			return (isset($this->users[$object->getId()]) && $this->users[$object->getId()]) ? true : false;
		}else if($object instanceof Right){
			$moduleId = ($module instanceof Module) ? $module->getId() : $module;
			return (isset($this->rights[$object->getId()]) && $this->rights[$object->getId()] == $moduleId && $this->rights[$object->getId()] !== false) ? true : false;
			
		}else if($object instanceof Site){
			foreach($this->sites as $site){
				if($site->getId() == $object->getId()){
					return true;
				}
			}
		}
		return false;
	}
	public function setAccess($object, $access){
		if($object instanceof Module){
			if($access){
				$this->moduleaccess[$object->getId()] = $access;
			}else if(isset($this->moduleaccess[$object->getId()])){
				unset($this->moduleaccess[$object->getId()]);
			}
		}else if($object instanceof Right){
			if($access){
				$this->rights[$object->getId()] = $access;
			}else if(isset($this->rights[$object->getId()])){
				unset($this->rights[$object->getId()]);
			}
		}else{
			throw new DataException('wrongtype');
		}
	}
	public function getSites(){
		return $this->sites;
	}
	public function getSite(){
		if(isset($_SESSION['site'])){
			return unserialize($_SESSION['site']);
		}else if(count($this->sites) > 0){
			//if no site set, return the first one in the list
			return $this->sites[0];
		}
		throw new DataException('norolesites');
	}
	public function getUsers(){
		return $this->users;
	}
	public function hasUsers(){
		return (count($this->users) > 0);
	}
	public function getModuleaccess(){
		return $this->moduleaccess;
	}
	public function getRights(){
		return $this->rights;
	}
	public function getCustomer(){
		return $this->customer;
	}
}
?>