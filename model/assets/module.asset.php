<?php
class Module extends Asset{
	protected $id;
	protected $name;
	protected $description;
	protected $classname;
	protected $route;
	protected $standard;
	protected $role;
	protected $index;
	protected $prio;
	protected $parent;
	protected $system;
	protected $kids = array();
	protected $rights = array();

	public function getId(){
		return $this->id;	
	}
	public function getParent(){
		return $this->parent;
	}
	public function isStandard(){
		return ($this->standard == '1');
	}
	public function getKids(){
		return $this->kids;
	}
	public function getName(){
		return $this->name;	
	}
	public function getPriority(){
		return $this->prio;	
	}
	public function AllowAnonomousAccess(){
		return ($this->system == '3');
	}
	public function isIndex(){
		return ($this->index == '1') ? true : false;	
	}
	public function FirstLoginIndex(){
		return ($this->index == '2') ? true : false;		
	}
	public function getRoute(){
		return $this->route;
	}
	public function SystemStatus(){
		return $this->system;
	}
	public function getIndexRoute(){
		if(isset($_SERVER['SERVER_NAME']) && strpos($_SERVER['SERVER_NAME'], 'localhost')){
			return '/?rt='.$this->route.'/index';
		}
		return '/'.$this->route.'/index';
	}
	public function getDescription(){
		return $this->description;	
	}
	public function getClass(){
		if(isset($this->classname) && $this->classname != ''){
			return $this->classname;
		}
		$name = explode(' ', $this->name);
		if(isset($name[0])){
			return strtolower($name[0]);
		}
		return '';
	}	
	public function getRights(){
		return $this->rights;
	}
}
?>