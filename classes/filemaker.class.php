<?php
class Filemaker{
	private $baseDir;
	public function __construct(){
		$this->baseDir = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']);
	}
	public function getFileContent($filename){
		$handle = fopen($filename, "rb");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		return $contents;
	}
	public function writeFile($filename, $content){
		$filename = $this->baseDir.str_replace($this->baseDir, '', $filename);
		$fh = fopen($filename, 'w+');
		fwrite($fh, $this->cleanContent($content));
		fclose($fh);
		//TODO: error handling here!
		return true;
	}
	public function makeFile($filename, $content){
		$filename = $this->baseDir.str_replace($this->baseDir, '', $filename);
		$fh = fopen($filename, 'w');
		fwrite($fh, $this->cleanContent($content));
		fclose($fh);
		return $filename;
	}
	private function cleanContent($content){
		return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $content);
	}
	public function moveFile($from, $to){
		rename($from, $to);
	}
	public function makeDirectory($path, $overwrite = false){
		if(is_dir($path)){
			if($overwrite){
				$this->removeDirectory($path);
			}else{
				return true;
			}
		}
		$result = mkdir($path);
		return $result;
	}
	private function removeDirectory($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") 
						rrmdir($dir."/".$object); 
					else 
						unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	} 
}
?>