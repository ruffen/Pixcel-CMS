<?php

class FileUploader{
	protected $filehandle;
	protected $tmpName;
	protected $realName;
	protected $path;
	public function __construct($filehandle, $checkfile){
		$this->filehandle = $filehandle;
		
		$this->tmpName = $this->filehandle['tmp_name'];
		$this->realName = $filehandle['name'];
		$this->setbasePath();
		if($checkfile){
			$this->checkFile();
		}
	}
	public function setbasePath(){
		$path = $_SERVER['SCRIPT_FILENAME'];
		//remove index.php
		$path = str_replace('index.php', '', $path);
		$this->path = $path;	
	}
	public function upload($path = ''){
		if($this->checkExtension($path)){
			return $this->movefile($path);
		}
		throw new IOException('Bad file extension');
	}
	protected function movefile($path = 'uploads/'){
		//try to move uploaded file
		
		//TODO: MAKE A WAY OF GETTING THE DIRECTORY FROM SOMEWHERE ELSE
		$path = $this->path.$path.$this->realName;
		if(move_uploaded_file($this->tmpName, $path)){
			return $path;
		}
		throw new IOException('Could not move file '.$this->filehandle['tmp_name'].' to: '.$path);
	}
	protected function checkExtension($filename){
		$ext = $this->getExtension($filename);
		return true;
	}
	protected function getExtension($filename){
		return substr($filename, strrpos($filename, '.') + 1);
	}
	protected function getFilename($filename){
		return substr($filename, strrpos($filename, '/') + 1);
	}
	
	protected function checkFile(){
		if(!empty($this->filehandle['error'])){
			switch($this->filehandle['error']){
				case '1':
					$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
					break;
				case '2':
					$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
					break;
				case '3':
					$error = 'The uploaded file was only partially uploaded';
					break;
				case '4':
					$error = 'No file was uploaded.';
					break;
				case '6':
					$error = 'Missing a temporary folder';
					break;
				case '7':
					$error = 'Failed to write file to disk';
					break;
				case '8':
					$error = 'File upload stopped by extension';
					break;
				case '999':
				default:
					$error = 'No error code avaiable';
			}
			throw new IOException($error);	
		}else if(!is_uploaded_file($this->filehandle['tmp_name'])){
			throw new IOException('Malicious file, or server attack, abort');
		}
	}
	public function giveUploader(){
		
		$uploaderPath = '';
		
	}
}

?>