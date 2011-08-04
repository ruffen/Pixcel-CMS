<?php
class HTTPUploader{
	private $site;
	public function __construct($site = false){
		if(!$site instanceof Site){
			throw new FTPException('There is no site to publish to');
		}
		$this->site = $site;
	}
	public function uploadFile($file, $localpath){
		if($file instanceof File){
			$path = $this->getFilepath($file, $localpath);
			$url = $this->getFileurl($file, $path);
		}else if(is_string($file)){
			$path = 'http://'.$_SERVER['HTTP_HOST'].'/'.str_replace($_SERVER['DOCUMENT_ROOT'].'/', '', $file);
			$url = trim($this->site->getUrl(), '/').'/'.'?rt=sysmessage/downloadfile&path='.urlencode($path);
		}else{
			throw new DataException('fileonly');
		}
		$result = file_get_contents($url);
		if($result == 'ok'){
			return true;
		}else{
			return $result;
		}
	}
	private function getFileurl($file, $path){
		$varPrefix = '';
		$secondVar = '?';
		if(strpos($this->site->getUrl(), 'localhost') !== false){
			$varPrefix = '?rt=';
			$secondVar = '&';
		}
		return trim($this->site->getUrl(), '/').'/'.$varPrefix.'sysmessage/downloadfile'.$secondVar.'path='.urlencode($path).'&id='.$file->getId();
	}
	protected function getFilepath($file, $localpath){
		if(strpos($localpath, 'resources') === false){
			return 'http://'.$_SERVER['HTTP_HOST'].'/cache/templates/'.$this->site->getId().'/'.$file->getFilename();	
		}
		return 'http://'.$_SERVER['HTTP_HOST'].'/cache/templates/'.$this->site->getId().'/resources/'.$file->getFilename();	
	}
	protected function getExtension($filename){
		return substr($filename, strrpos($filename, '.') + 1);
	}
}
?>
