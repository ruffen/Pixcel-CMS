<?php
class FolderList extends Listmaker{
	protected $includefiles;
	public function __construct($vars=array(), $listVars = array(), $parent = 0, $multi = true, $xml = false, $includefiles = false){
		$this->includefiles = $includefiles;
		parent::__construct($vars, $listVars, $parent, $multi, $xml);
	}
	protected function createList($parent, $level = 1){
		global $INK_User, $varChecker;
		$siteId = ($varChecker->getValue('siteId') === false) ? $INK_User->getSite()->getId() : $varChecker->getValue('siteId');
		$root = false;
		$folders = $this->dRep->getFolderCollection(array(
			'parent' => $parent,
			'site' => $siteId
			));
		if(count($folders) == 0){
			return $root;	
		}
		if(!$this->xml){
			$root = $this->document->createElement('ul');
			//add the variables that need to be on the list
			if($parent == $this->parent){
				foreach($this->vars as $varname => $value){
					$attribute = $root->appendChild($this->createAttribute($varname, $value));
				}
			}
		}
		foreach($folders as $index => $folder){
			$level = ($level != 1) ? $level : '';
			$listIndex = $index + 1;
			if($this->xml){
				$xmlNode = $this->document->createElement('folder');
				$xmlNode->appendChild($this->createAttribute('id', $folder->getId()));
				$xmlNode->appendChild($this->createAttribute('name', $folder->getName()));
				$xmlNode = $this->rootElement->appendChild($xmlNode);
			}else{
				$htmlNode = $this->document->createElement('li');
				$htmlNode->appendChild($this->createAttribute('id', 'node'.$level.$listIndex));
				foreach($this->listVars as $variable => $value){
					if($variable == 'class' && $value == 'type'){
						$value = 'directory';
					}
					$htmlNode->appendChild($this->createAttribute($variable, $value));
					$htmlNode = $root->appendChild($htmlNode);
				}
				$anchor = $this->document->createElement('a');
				$anchor->appendChild($this->createAttribute('href', 'folder_'.$folder->getId()));
				$anchorText = $this->document->createTextNode($folder->getName());
				$anchorText = $anchor->appendChild($anchorText);
				$anchor = $htmlNode->appendChild($anchor);
			}
			
			if($this->multi){
				$child = $this->createList($folder->getId());
				if(!$this->xml && $child != ''){
					$htmlNode->appendChild($child);
				}
			}
		}
		return $root;
	}
	public function getList(){
		if($this->xml){
			return $this->document->saveXML();
		}else{
			$html = $this->document->saveHTML();
			if($this->includefiles){
				$filelist = new FileList(array(), array('class' => 'type'), $this->parent, false);
				$html .= $filelist->getList();
			}
			return $html;
		}
	}

}
?>