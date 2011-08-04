<?php
class PageRevision extends Asset{
	protected $id;
	protected $timestamp;
	protected $current;
	protected $author;
	protected $published;

	public function isDraft(){
		return ($this->published == 0) ? true : false;
	}
	public function published(){
		return $this->published;
	}
	public function getAuthor(){
		return $this->author;
	}
	public function getDate($format = ''){
		return date($format, $this->timestamp);	
	}
	public function isCurrent(){
		return ($this->current == 1) ? true : false;
	}
}
?>