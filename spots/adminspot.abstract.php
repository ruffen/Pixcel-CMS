<?php
abstract class AdminSpot extends Spot{
	protected $value = array();
	public function giveAdmin($spotTplId, $value = ''){
		
	}
	public function setvalue($value, $lang){
		$langId = (is_object($lang)) ? $lang->getId() : $lang;
		$this->value[$lang] = serialize($value);
	}
	public function getValues(){
		return $this->value;
	}
	abstract function getContent($value);
}
