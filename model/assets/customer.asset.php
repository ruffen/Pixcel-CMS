<?php
class Customer extends Asset{
	protected $id;
	protected $name;
	protected $timezone;
	protected $sites;
	protected $roles;
	protected $newsletter;
	protected $subdomain;

	public function getSite(){
		if(isset($_SESSION) && isset($_SESSION['site'])){
			return unserialize($_SESSION['site']);
		}
		if(count($this->sites) > 0){
			return $this->sites[0];	
		}
		throw new DataException('nosites');
	}
	public function getSites(){
		return $this->sites;	
	}
	public function setSite($site){
		foreach($this->sites as $index => $storedSite){
			if($storedSite->getId() == $site->getId()){
				$this->sites[$index] = $site;
				return true;
			}
		}
		$this->sites[] = $site;
	}
	public function getName(){
		return $this->name;
	}
	public function getNewsletter(){
		return $this->newsletter;
	}
	public function getSubdomain(){
		return $this->subdomain;
	}
}
?>