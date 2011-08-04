<?php
class Pagetext extends Asset{
	private $language;
	private $name;
	private $description;
	public function __get($var){
		if(isset($this->$var)){
			return $this->$var;
		}
		throw new DataException($value.' is not a valid page text value');
	}
	public function __set($var, $value){
		$this->$var = $value;
		return true;
	}	
}

?>