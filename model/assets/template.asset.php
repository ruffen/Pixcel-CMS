<?php
class Template extends Asset{
	protected $id;
	protected $name;
	protected $filename;
	protected $customer;
	protected $spots = array();
	protected $folder;
	protected $site;
	public function getName(){
		return $this->name;	
	}
	public function getResourcefolder(){
		global $dRep;
		if(!is_object($this->folder) && !empty($this->folder) && $this->folder != 0){
			$folder = $dRep->getFolder($this->folder);
			return $folder;
		}else if(is_object($this->folder)){
			return $this->folder;		
		}else{
			return 0;
		}
	}
	public function getFilename(){
		return $this->filename;
	}
	public function getId(){
		return $this->id;	
	}
	public function getSpots(){
		return $this->spots;	
	}
	public function setSpot($spot){
		$this->spots[$spot->getTplSpotId()] = $spot;	
	}
	public function getSpot($id){
		if(isset($this->spots[$id])){
			return $this->spots[$id];
		}
		throw new DataException('nospot');
	}
	public function getSite(){
		global $dRep;
		if(empty($this->site) || $this->site == '' || $this->site == 0){
			return false;
		}
		if(is_object($this->site)){
			return $this->site;
		}
		return $dRep->getSite($this->site);
	}
}
?>