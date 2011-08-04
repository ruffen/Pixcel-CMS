<?php
class File extends Asset{
	protected $id;
	protected $text = array();
	protected $filename;
	protected $type;
	protected $folderId;
	protected $size;
	protected $timestamp;
	
	public function setLang($language, $var, $value){
		$langId = (is_object($language)) ? $language->getId() : $language;
		if(!isset($this->lang[$langId])){
			$this->lang[$langId] = new Pagetext();	
		}
		$this->lang[$langId]->$var = $value;
	}
	public function getLang($lang = false, $value){
		return 'test';
	}
	public function getFolder(){
		return $this->folderId;	
	}
	public function getFiletype(){
		return $this->type;	
	}
	public function getExtension(){
		return strtolower(substr($this->getFilename(), strrpos($this->getFilename(), '.') + 1));
	}
	public function getFilename(){
		return $this->filename;	
	}
	public function GetSource(){
		return 'dynamic/Image/'.$this->id.'.'.$this->getExtension();
	}
	public function GetDescription(){
		if(isset($this->text[1]) && isset($this->text[1]->description)){
			return $this->text[1]->description;
		}
		return '';
	}
	public function getFoldername(){
		if(!$this->type instanceof FileType){
			return '';
		}else{
			return $this->type->getServerFolder();
		}
	}
	public function getSize($postfix = 'kb'){
		if($this->size == 0){
			return $this->size.$postfix;
		}	
		switch($postfix){
			case 'kb' : $size = $this->size / 1024; break;
			default : $size = $this->size / 1024; break;
		}
		return $size.$postfix;	
	}
	public function getTimestamp($format = 'M j Y @ H:i'){
		return date($format, $this->timestamp);
	}
	public function getPreviewImage($siteurl){
		$siteurl = (strpos($siteurl, 'localhost') !== false) ? $siteurl.'?rt=' : $siteurl;
		$ext = $this->getExtension();
		switch($ext){
			case 'eps' : 
			case 'doc' : 
			case 'docx': 
			case 'xls': 
			case 'xlsx': 
			case 'pdf': 
			case 'mp3': return $this->type->getThumbnail();break;
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'bmp':
			case 'png': return $siteurl.$this->getId().'.'.$ext;break;
		}
		return ;
	}
}