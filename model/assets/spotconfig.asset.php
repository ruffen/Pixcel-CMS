<?php
class Spotconfig extends Asset{
	protected $id;
	protected $index;
	protected $name;
	protected $description;

	public function getName(){
		return $this->name;
	}
	public function getDescription(){
		return $this->description;
	}
	public function getIndex(){
		return $this->index;
	}
}