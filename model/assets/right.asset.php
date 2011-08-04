<?php
class Right extends Asset{
	protected $id;
	protected $key;
	protected $name;
	protected $description;
	
	public function getKey(){
		return $this->key;
	}
	public function getName(){
		return $this->name;	
	}
	public function getDescription(){
		return $this->description;	
	}
}
