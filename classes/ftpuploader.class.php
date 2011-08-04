<?php
class FTPUploader{
	protected $conndetails;
	protected $connId;
	private $passive = false;
	public function __construct($site = false){
		if(!$site instanceof Site){
			throw new FTPException('There is no site to publish to');
		}
		$this->setSiteCondetails($site);
		$this->connect();
		if($site->getPassivemode()){
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
		$this->checkFolderExists($this->conndetails['path'].'/'.$serverpath);
		ftp_pasv($this->connId, $this->passive);
		$ret = ftp_put($this->connId , $this->conndetails['path'].'/'.$serverpath.'/'.$filename , $localpath, FTP_BINARY);
/*		if ($ret != FTP_FINISHED) {
			$ret = ftp_nb_put($this->connId , $this->conndetails['path'].'/'.$serverpath.'/'.$filename , $localpath, FTP_ASCII, ftp_size($this->connId, $this->conndetails['path'].'/'.$serverpath.'/'.$filename ));
			while ($ret == FTP_MOREDATA) {
				// Continue uploading...
				$ret = ftp_nb_continue($this->connId);
			}
		}*/
		set_time_limit (60);
/*		if ($ret != FTP_FINISHED) {
			ob_start();
			print_r($ret);
			$test = ob_get_clean();
			throw new FTPException('errorinftp'.$this->conndetails['path'].'/'.$serverpath.'/'.$filename.'_'.$test);
		}*/
		
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
	private function setSiteCondetails($site){
		try{
			$this->conndetails= $site->getFtpDetails();
		}catch(Exception $ex){
			throw new FTPException('path_failed');
		}
	}
	private function connect(){
		$url = str_replace('sftp://', '', strtolower($this->conndetails['url']));
		$url = str_replace('ftp://', '', strtolower($url));
		if(strrpos($url, '/') == strlen($url)-1){
			$url = substr($url, 0, strlen($url) - 1);
		}
		try{
			$this->connId = ftp_connect(trim($url), $this->conndetails['port'], 10);
		}catch(Exception $e){
			throw new FTPException('connect_failed');			
		}
		if(!$this->connId){
			throw new FTPException('connect_failed');
		}
		try{
			if(!@ftp_login($this->connId, $this->conndetails['username'], $this->conndetails['password'])){
				throw new Exception();
			}
		}catch(Exception $e){
			if($this->connId !== false){
				ftp_close($this->connId);
			}
			throw new FTPException('login_failed');
		}
		try{
			@ftp_chdir($this->connId, $this->conndetails['path']);		
		}catch(Exception $e){
			throw new FTPException('path_failed');
		}
	}
	protected function getExtension($filename){
		return substr($filename, strrpos($filename, '.') + 1);
	}
}

?>
