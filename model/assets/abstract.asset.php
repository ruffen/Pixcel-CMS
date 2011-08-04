<?php
abstract class Asset{
	protected $id;
	public function __construct(){

		
	}
	public function setProperties($properties){
		$vars = get_defined_vars($this);
		foreach($properties as $propName => $property){
			if(array_key_exists($propName, $vars['properties'])){
				$this->$propName = $property;
			}else{
				throw new PropertyException('Could not set property: '.$propName.' with value'.$property);	
			}
		}
	}
	public function getVars(){
		return array_keys(get_object_vars($this));	
	}
	public function getId(){
		if(isset($this->id)){
			return $this->id;
		}
		return 'new';
	}
	public function __get($var){
		if(isset($this->$var)){
			return $this->$var;	
		}
		throw new DataException('Could not return variable: '.$var);
	}
}
?>