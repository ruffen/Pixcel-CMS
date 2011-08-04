<?php
class PageList extends Listmaker{
	protected function createList($parent, $level = 1){
		global $INK_User;
		$root = false;
		$pages = $this->dRep->getPageCollection(array(
			'parent' => $parent,
			'siteId' => $INK_User->getCustomer()->getSite()->getId()
		));
		if(count($pages) == 0){
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
		foreach($pages as $index => $page){
			$level = ($level != 1) ? $level : '';
			$listIndex = $index + 1;
			if($this->xml){
				$xmlNode = $this->document->createElement('page');
				$xmlNode->appendChild($this->createAttribute('url', $page->getUrl()));			
				$xmlNode->appendChild($this->createAttribute('id', $page->getId()));
				$xmlNode->appendChild($this->createAttribute('name', $page->getTitle()));
				$xmlNode->appendChild($this->createAttribute('published', $page->published()));
				$xmlNode->appendChild($this->createAttribute('index', $page->isIndex()));
				$xmlNode = $this->rootElement->appendChild($xmlNode);
			}else{
				$htmlNode = $this->document->createElement('li');
				
				$htmlNode->appendChild($this->createAttribute('id', 'node'.$level.$listIndex));
				foreach($this->listVars as $variable => $value){
					$htmlNode->appendChild($this->createAttribute($variable, $value));
				}
				$htmlNode = $root->appendChild($htmlNode);
				$anchor = $this->document->createElement('a');
				$anchor->appendChild($this->createAttribute('href', $page->getUrl()));
				$anchor->appendChild($this->createAttribute('rel', $page->getId()));
				$anchorText = $this->document->createTextNode($page->getTitle());
				$anchorText = $anchor->appendChild($anchorText);
				$anchor = $htmlNode->appendChild($anchor);
				if($page->isIndex()){
					$listText = $this->document->createTextNode(' - (index)');
					$listText = $htmlNode->appendChild($listText);					
				}

			}
			
			if($this->multi){
				$child = $this->createList($page->getId());
				if(!$this->xml && $child != ''){
					$htmlNode->appendChild($child);
				}
			}
		}
		return $root;
	}
}
?>