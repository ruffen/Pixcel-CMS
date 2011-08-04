<?php
class Pagetext extends Asset{
	private $language;
	private $title;
	private $description;
	private $keywords;
	public function __get($var){
		if(property_exists($this, $var)){
			return $this->$var;
		}
		throw new DataException($var.' is not a valid page text value');
	}
	public function __set($var, $value){
		$this->$var = $value;
		return true;
	}
}
?>