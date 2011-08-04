<?php
class FileReader{
	private $baseDir;
	public function __construct(){
		$this->baseDir = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']);
	}
	public function readFiles($directory){
		$directory = $this->baseDir.str_replace($this->baseDir, '', $directory);
		$files = array();
	    if ($dh = @opendir($directory)) {
	        while (($file = readdir($dh)) !== false) {
				$files[] = $file;
	        }
	        closedir($dh);
	    }else{
	    	throw new PathException('No directory named : '.$directory);
	    }
	    return $files;
	}
	public function readFile($filepath){
		
		$path = $this->baseDir.str_replace($this->baseDir, '', $filepath);
		if(!is_file($path)){
			throw new PathException('no file at '.$path);
		}
		
		$handle = fopen($path, 'r');
		$content = fread($handle);
		return $content;
	}

}

?>