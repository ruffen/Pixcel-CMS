<?php
class Message extends Asset{

	protected $id;
	protected $key;
	protected $description;
	protected $type;
	
	public function getMessage(){
		return ($this->description == '') ? $this->key : $this->description;
	}
	public function type(){
		return $this->type;
	}
}
?>