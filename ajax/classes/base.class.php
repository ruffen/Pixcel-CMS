<?php

abstract class base{
	private $registry;

	public function __construct($registry){
		
	}	
	
	public function save(){
		$this->registry->repository->saveObject($this);
	}
}

?>