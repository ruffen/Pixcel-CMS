<?php
class ModuleList extends Listmaker{
	private $modules;
	private $controller;
	public function __construct($modules, $controller, $vars=array(), $listVars = array(), $parent = 0, $multi = true, $xml = false){
		$this->modules = $modules;
		$this->controller = $controller;
		parent::__construct($vars, $listVars, $parent, $multi, $xml);
	}
	protected function createList($parent, $level = 1){
		global $INK_User;
		$root = false;
		try{
			$modules = ($parent == 0) ? $this->modules : $this->getModules($parent);
		}catch(DataException $e){
			return $root;
		}
		
		if(count($this->modules) == 0){
			return $root;
		}
		if(!$this->xml){
			$root = $this->document->createElement('ul');
			if($parent > 0){
				$div = $this->document->createElement('div');
				$div->appendChild($this->createAttribute('id', 'module-picker'));
				$div->appendChild($root);
			}
			
			
			//add the variables that need to be on the list
			if($parent == $this->parent){
				foreach($this->vars as $varname => $value){
					$attribute = $root->appendChild($this->createAttribute($varname, $value));
				}
			}
		}
		foreach($modules as $index => $module){
			$level = ($level != 1) ? $level : '';
			$listIndex = $index + 1;
			if($this->xml){
				$xmlNode = $this->document->createElement('module');
				$xmlNode->appendChild($this->createAttribute('id', $folder->getId()));
				$xmlNode = $this->rootElement->appendChild($xmlNode);
			}else{
				$htmlNode = $this->document->createElement('li');
				$classname = ($this->iselected($module)) ? $module->getClass().'-selected' : $module->getClass();
				$htmlNode->appendChild($this->createAttribute('class', $classname));
				foreach($this->listVars as $variable => $value){
					$htmlNode->appendChild($this->createAttribute($variable, $value));
				}
				$htmlNode = $root->appendChild($htmlNode);
				$anchor = $this->document->createElement('a');
				
				$href = (count($module->getKids()) > 0) ? '#' : $module->getIndexRoute();
				$anchor->appendChild($this->createAttribute('href', $href));
				$anchorText = $this->document->createTextNode($module->getName());
				$anchorText = $anchor->appendChild($anchorText);
				$anchor = $htmlNode->appendChild($anchor);
			}
			
			if($this->multi){
				$child = $this->createList($module->getId());
				if(!$this->xml && $child != false){
					$htmlNode->appendChild($child);
				}
			}
		}
		if($parent > 0){
			return $div;
		}
		return $root;
	}
	private function iselected($module){
		if($module->getRoute() == $this->controller){
			return true;	
		}
		foreach($module->getKids() as $index => $kid){
			if($kid->getRoute() == $this->controller){
				return true;
			}
		}
		return false;
	}
	private function getModules($parentId){
		foreach($this->modules as $index => $searchModule){
			if($searchModule->getId() == $parentId){
				return $searchModule->getKids();
			}
		}
		throw new DataException('nomodulewithid');
	}
}
?>