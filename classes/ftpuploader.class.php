<?php
class FTPUploader{
	protected $connId;
	private $passive = false;
	private $site;
	public function __construct($site = false){
		if(!$site instanceof Site){
			throw new FTPException('There is no site to publish to');
		}
		$this->site = $site;
		$this->connect();
		if($site->UsesPassive()){
			$this->setPassiveMode();
		}else{
			$this->setActiveMode();
		}
	}
	public function setPassiveMode(){
		$this->passive = true;
	}
	public function setActiveMode(){
		$this->passive = false;
	}
	public function __destruct(){
		ftp_close($this->connId);
	}
	public function uploadFile($object, $localpath){
		if($object instanceof Page){
			$ext = 'php';
			$path = 'dynamic';
		}else if($object instanceof File){
			$ext = $object->getExtension();
			$path = $object->getFoldername();
		}else if(is_string($object)){
			$ext = $this->getExtension($object);
			$path = '';
		}
		$this->upload($path, $localpath, $object->getId().'.'.$ext);
		return true;
	}
	public function upload($serverpath, $localpath, $filename){
		set_time_limit (0);
		$this->checkFolderExists($this->site->getPath().'/'.$serverpath);
		ftp_pasv($this->connId, $this->passive);
		$ret = ftp_put($this->connId , $this->site->getPath().'/'.$serverpath.'/'.$filename , $localpath, FTP_BINARY);
		set_time_limit (60);
	}
	public function getFoldercontent($root){
		return ftp_nlist($this->connId, $root);
	}
	private function checkFolderExists($folderpath, $createDirectory = true){
		try{
			ftp_chdir($this->connId, $folderpath);
		}catch(Exception $ex){
			if($createDirectory){
				ftp_mkdir($this->connId, $folderpath);
			}else{
				return false;
			}
		}
		return true;
	}
	private function connect(){
		$url = str_replace('sftp://', '', strtolower($this->site->getFTPUrl()));
		$url = str_replace('ftp://', '', strtolower($url));
		if(strrpos($url, '/') == strlen($url)-1){
			$url = substr($url, 0, strlen($url) - 1);
		}
		try{
			$this->connId = ftp_connect(trim($url), $this->site->getPort(), 10);
		}catch(Exception $e){
			throw new FTPException('connect_failed');			
		}
		if(!$this->connId){
			throw new FTPException('connect_failed');
		}
		try{
			if(!@ftp_login($this->connId, $this->site->getFTPUsername(), $this->site->getFTPPassword())){
				throw new Exception();
			}
		}catch(Exception $e){
			if($this->connId !== false){
				ftp_close($this->connId);
			}
			throw new FTPException('login_failed');
		}
		try{
			if($this->site->getPath('root') != '/'){
				@ftp_chdir($this->connId, $this->site->getPath('root'));		
			}
		}catch(Exception $e){
			throw new FTPException('path_failed');
		}
	}
	protected function getExtension($filename){
		return substr($filename, strrpos($filename, '.') + 1);
	}
}

?>
