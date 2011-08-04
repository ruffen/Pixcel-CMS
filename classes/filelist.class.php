<?php
class FileList extends Listmaker{
	protected $fileanchor;
	public function __construct($vars=array(), $listVars = array(), $parent = 0, $fileanchor = true, $xml = false){
		$this->fileanchor = $fileanchor;
		parent::__construct($vars, $listVars, $parent, false, $xml);
	}
	protected function createList($parent, $level = 1){
		global $INK_User;
		$root = false;
		$files = $this->dRep->getFileCollection(array(
			'folder' => $parent,
			'site' => $INK_User->getCustomer()->getSite()->getId()
			));
		if(count($files) == 0){
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
		foreach($files as $index => $file){
			$level = ($level != 1) ? $level : '';
			$listIndex = $index + 1;
			if($this->xml){
				$xmlNode = $this->document->createElement('folder');
				$xmlNode->appendChild($this->createAttribute('id', $file->getId()));
				$xmlNode->appendChild($this->createAttribute('name', $file->getName()));
				$xmlNode = $this->rootElement->appendChild($xmlNode);
			}else{
				$htmlNode = $this->document->createElement('li');
				$htmlNode->appendChild($this->createAttribute('id', 'node'.$level.$listIndex));
				foreach($this->listVars as $variable => $value){
					if($variable == 'class' && $value == 'type'){
						$value = $file->getFileType()->getName();
					}
					$htmlNode->appendChild($this->createAttribute($variable, $value));
				}
				$htmlNode = $root->appendChild($htmlNode);
				$text = $this->document->createTextNode($file->getFilename());
				if($this->fileanchor){
					$anchor = $this->document->createElement('a');
					$anchor->appendChild($this->createAttribute('href', 'file_'.$file->getId()));
					$text = $anchor->appendChild($anchorText);
					$anchor = $htmlNode->appendChild($anchor);
				}else{
					$htmlNode->appendChild($text);
				}
			}
		}
		return $root;
	}
}
?>