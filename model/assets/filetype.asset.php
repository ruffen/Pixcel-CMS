<?php
class FileType extends Asset{
	protected $id;
	protected $name;
	protected $thumbnail;
	protected $description;
	
	public function getName(){
		return $this->name;	
	}
	public function getDescription(){
		return $this->description;	
	}
	public function getServerFolder(){
		//return name for now
		return $this->name;
	}
	public function getThumbnail(){
		return $this->thumbnail;
	}
}
