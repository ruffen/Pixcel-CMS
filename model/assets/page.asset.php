<?php
/**
 * Class Page extends abstract class Asset. Page is a representation of pages in the system.
 *
 */
class Page extends Asset{
	protected $lang;
	protected $parent;
	protected $order;
	protected $author;
	protected $template;
	protected $created;
	protected $changed;
	protected $published;
	protected $publishedDate;
	protected $columns;
	protected $revisions = array();
	protected $siteId;
	protected $role = array();
	protected $index;
	
	public function getPublishDate($format = 'D j M h:i'){
		$date =date($format, $this->publishedDate); 
		return $date;
	}
	public function getDatecreated($format = 'D j M h:i'){
		return date($format, $this->created);		
	}
	public function getFirstCreated($format = 'D j M h:i'){
		$revision = $this->currentRevision();
		if(is_object($revision)){
			return $revision->getDate($format);
		}
		//if revision is not an object, current page is current revision
		return $this->getDatecreated($format);
	}
	public function revisions(){
		return $this->revisions;	
	}
	public function published($history = false){
		if($history === false || $this->published != 0){
			return $this->published;
		}
		foreach($this->revisions as $index => $revision){
			if($revision->published() > 0){
				return $revision->published();
			}
		}

		return 0;
	}
	public function getStatus(){
		switch($this->published){
			case 0: return 'Draft';break;
			case 1: return 'Published';break;
			case 2: return 'Waiting publish approval';break;
			case 3: return 'Waiting withdraw approval';break;
			case 4: return 'Withdrawn';break;
			case 5: return 'Expired';break;
			default: return 'Unknown'; break;
		}
	}
	public function setPublished($status, $swapPublish = false){
		if(!is_int($status)){
			switch(strtolower($status)){
				case 'draft' : $this->published = 0; break;
				case 'published' : $this->published = 1;break;
				case 'waiting publish approval' : $this->published = 2;break;
				case 'waiting withdraw approval' : $this->published = 3;break;
				case 'withdrawn' : $this->published = 4; break;
				default : $this->published = 0;break;
			}	
		}else{
			$this->published = $status;
		}
	}
	public function setLang($language, $var, $value){
		if(is_numeric($var)){
			$var = 'spot'.$var;
		}
		$langId = (is_object($language)) ? $language->getId() : $language;
		if(!isset($this->lang[$langId])){
			$this->lang[$langId] = new Pagetext();
			$this->lang[$langId]->id = $langId;	
		}
		$var = strtolower($var);
		$this->lang[$langId]->$var = $value;
	}
	public function getLang(){
		return $this->lang;
	}
	public function getTitle(){
		try{
			return (isset($this->lang[1])) ? $this->lang[1]->title : '';
		}catch(DataException $e){
			return '';
		}
	}
	public function getDescription(){
		try{
			return (isset($this->lang[1])) ? $this->lang[1]->description : '';
		}catch(DataException $e){
			return '';
		}
	}
	public function getKeywords(){
		try{
			return (isset($this->lang[1])) ? $this->lang[1]->keywords : '';
		}catch(DataException $e){
			return '';
		}
	}
	public function getValue($tplSpotId, $allValues = false){
		//need to return all languages values if specified, not just pr language
		$spotvar = 'spot'.$tplSpotId;
		
		if($allValues !== false && is_bool($allValues)){
			$return = array();
			foreach($this->lang as $langId => $values){
				try{
					$return[$langId] = $values->$spotvar;
				}catch(DataException $e){}
			}
			return $return;
		}
		if(isset($this->lang[1]) && isset($this->lang[1]->$spotvar)){
			$spot = $this->getSpot($tplSpotId);
			//if we are getting value for admin, we dont want to give the value with view
			return ($allValues == 'admin') ? $this->lang[1]->$spotvar : $spot->getContent($this->lang[1]->$spotvar );
		}
		
		throw new DataException('novalue');
	}
	public function getTemplate(){
		return $this->template;
	}
	public function getAuthor(){
		return $this->author;	
	}
	public function nextRevision(){
		//always return the id above the highest id
		$nextRev = 1;
		foreach($this->revisions as $index => $revision){
			$nextRev = ($revision->getId() > $nextRev) ? $revision->getId() : $nextRev;			
		}
		$nextRev++;
		return $nextRev;
	}
	public function currentRevision(){
		foreach($this->revisions as $index => $revision){
			if($revision->isCurrent()){
				return $revision;
			}
		}
		return 1;
	}
	public function setOrder($order){
		$this->order = $order;
	}
	public function getOrder(){
		return $this->order;	
	}
	public function getParent(){
		return $this->parent;	
	}
	public function setParent($parentId){
		$this->parent = $parentId;
	}
	public function getUrl($friendly = true){
		$name = $this->getTitle();
		$name = str_replace(' ', '_', $name);
		$name = str_replace(':', '', $name);
		return $name;
	}
	public function getRoles(){
		return $this->role;	
	}
	public function getSpot($tplSpotId){
		$spots = $this->template->getSpots();
		if(isset($spots[$tplSpotId])){
			return $spots[$tplSpotId];
		}
		throw new DataException('nospot');
	}
	public function setSpot($newSpot){
		$spots = $this->template->getSpots();
		foreach($spots as $index => $spot){
			if($spot->getId() == $newSpot->getId()){
				$spots[$index] = $newSpot;					
			}
		}
		$this->template->setProperties(array('spots' => $spots));	
	}
	
	public function isIndex()
	{
		return ($this->index == 0 || $this->index == false) ? false : true;
	}
	public function setAsIndex(){
		$this->index = true;
	}
	public function getSiteId(){
		return $this->siteId;
	}
}
?>
