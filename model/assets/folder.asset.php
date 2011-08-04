<?php
class Folder extends Asset{
	protected $id;
	protected $site;
	protected $parent;
	protected $name;
	protected $resource;
	protected $order;
	protected $files = array();
	
	public function getSite(){
		return $this->site;
	}
	public function getOrder(){
		return $this->order;
	}
	public function getParent(){
		return $this->parent;
	}
	public function getUrl(){
		return '?rt=filesystem/changefolder&folderId='.$this->id;	
	}
	public function getFiles(){
		return $this->files;	
	}
	public function getName(){
		return $this->name;	
	}
}
