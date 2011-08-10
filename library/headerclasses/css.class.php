<?php
class CSS extends FileReader{
	private $folderPath;
	private $files;
	private $controller;
	public function CSS($controller){
		$document_root = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']);
		$script_root = str_replace('index.php', '', $_SERVER['PHP_SELF']);
		$this->folderPath = $document_root.'static/css/';
		$this->controller = $controller;
	}
	private function findFiles(){
		try{
			$files = $this->readFiles($this->folderPath);
			$cssString = '';
			foreach($files as $index => $file){
				if(strtolower($file) == strtolower($this->controller).'.css'){
					$cssString .= '<link rel="stylesheet" type="text/css" href="/static/css/'.$file.'" />';
				}
			}
		
		}catch(PathException $e){
			$cssString = '';
		}
		return $cssString;
	}
	public function getFiles(){
		if(empty($this->files)){
			$this->files = $this->findFiles();
		}
		return $this->files;
	}
}
?>