<?php
abstract class Listmaker {
	protected $xml;
	protected $vars;
	protected $multi;
	protected $dRep;
	protected $parent;
	protected $listVars;
	
	protected $document;
	protected $rootElement;
	public function __construct($vars=array(), $listVars = array(), $parent = 0, $multi = true, $xml = false){
		global $dRep;
		$this->dRep = $dRep;
		$this->vars = $vars;
		$this->multi = $multi;
		$this->parent = $parent;
		$this->listVars = $listVars;
		$this->xml = $xml;
		//create the menu
		$this->document = new DOMDocument('1.0');
		$list = $this->createList($parent);
		if($list !== false){
			$this->document->appendChild($list);
		}
	}
	abstract protected function createList($parent, $level = 1);
	 
	public function xmlmode(){
		if(!$this->xml){
			$this->xml = true;
			$this->document = new DOMDocument('1.0');
			$this->rootElement = $this->document->createElement('pages');
			$this->createList($this->parent);
			
			$this->rootElement = $this->document->appendChild($this->rootElement);
		}
	}
	public function listmode(){
		if($this->xml){
			$this->xml = false;	
			$this->document = new DOMDocument('1.0');		
			$this->document->appendChild($this->createList($this->parent));
		}
	}
	protected function createAttribute($name, $value){
		$attribute = $this->document->createAttribute($name);
		$text = $this->document->createTextNode($value);
		$text = $attribute->appendChild($text);
		return $attribute;
	}
	public function getList(){
		if($this->xml){
			return $this->document->saveXML();
		}else{
			return $this->document->saveHTML();
		}
	}
	public function show($type){
		echo $this->getList();
		return true;
	}
}
?>