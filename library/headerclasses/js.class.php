<?php
class JS extends FileReader{
	private $folderPath;
	private $scripts;
	public function JS($controller){
		$document_root = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']);
		$script_root = str_replace('index.php', '', $_SERVER['PHP_SELF']);
		$this->folderPath = $document_root.'static/js/'.$controller;
		$this->scriptRoot = $script_root.'static/js/'.$controller.'/';
	}
	public function findScripts(){
		try{
			$files = $this->readFiles($this->folderPath);
			$jsString = '';
			foreach($files as $index => $file){
				$ext = substr($file, strrpos($file, '.') + 1);
				if($ext == 'js'){
					$jsString .= '<script type="text/javascript" src="'.$this->scriptRoot.$file.'"></script>'."\n";		
				}
			}
		
		}catch(PathException $e){
			$jsString = '';
		}
		return $jsString;
	}
	public function getScripts(){
		if(empty($this->scripts)){
			$this->scripts = $this->findScripts();
		}
		return $this->scripts;
	}

}
?>