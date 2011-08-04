<?php
class Publisher extends FTPUploader{
	
	private $page;
	private $html;
	private $ftp;
	private $site;
	public function __construct($page = false){
		global $INK_User;
		if($page != false){
			$this->page = $page;
		}
		
		$this->site = $INK_User->getCustomer()->getSite();
		parent::__construct($this->site);
	}
	public function publish(){
		$path = $this->buildpage();
		$this->createSitemap();
		try{
			$this->uploadFile($this->page, $path, false);
		}catch(FTPException $e){
			//try passive if active mode fails
			$this->uploadFile($this->page, $path, true);
		}
		return true;
	}
	public function publishResources($resources){
		$resourcemap = $this->createResourcemap($resources);
		$uploader = new HttpUploader($this->site);
		$uploader->uploadFile($resourcemap, 'xml');
	}
	private function buildFilelist($resources){
		global $dRep;
		$files = array();
		foreach($resources as $index => $file){
			if($file instanceof File){
				$files[] = $file;
			}else{
				$files[] = $dRep->getFile($file);	
			}
		}
		return $files;
	}
	private function createResourcemap($resources){
		$filelist = $this->buildFilelist($resources);	
		$document = new DOMDocument('1.0', 'utf-8');
		$rootElement = $document->createElement('resources');
		foreach($filelist as $file){
			$xmlNode = $document->createElement('resource');
			$xmlNode->appendChild($this->createAttribute($document, 'filename', $file->getFilename()));			
			$xmlNode->appendChild($this->createAttribute($document, 'id', $file->getId()));
			$xmlNode = $rootElement->appendChild($xmlNode);
		}
		$document->appendChild($rootElement);
		$path = $this->saveResourcemap($document);
		return $path;
	}
	private function createAttribute($document, $name, $value){
		$attribute = $document->createAttribute($name);
		$text = $document->createTextNode($value);
		$text = $attribute->appendChild($text);
		return $attribute;
	}
	private function buildpage(){
		$pagebuilder = new PageBuilder($this->page);
		//expect a file location from pagebuilder.
		return $pagebuilder->build($this->site, true);	//build the page, and set return filepath to true in order to get the filepath not html
	}
	public function createSitemap(){
		$pagelist = new Pagelist();
		$pagelist->xmlmode();
		$xml = $pagelist->getList();
		$sitemap = $this->saveSitemap($xml);
		$uploader = new HttpUploader($this->site);
		$uploader->uploadFile($sitemap, 'xml');
	}
	private function saveResourcemap($document){
		$path = 'cache/templates/'.$this->site->getId();
		if(!is_dir($path)){
			$this->createFolder($path);
		}
		$fileName = $path.'/resourcemap.xml';
		if($document->save($fileName) !== false){
			return $fileName;
		}
		throw new Exception('cantsaveresourcemap');
		
	}
	private function saveSitemap($xml){
		if(!is_dir('cache/pages/'.$this->site->getId())){
			$this->createFolder('cache/pages/'.$this->site->getId());	
		}
		$fileName = 'cache/pages/'.$this->site->getId().'/sitemap.xml';
		return $this->createFile($fileName, $xml);
	}
	private function createFile($fileName, $content){
		$fm = new FileMaker();
		return $fm->makeFile($fileName, $content);
	}
	private function createFolder($path){
		$fm = new FileMaker();
		$fm->makeDirectory($path);
	}
}


?>