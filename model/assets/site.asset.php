<?php
class Site extends Asset{
	protected $id;
	protected $name;
	protected $url;
	protected $modules;
	protected $templates;
	protected $ftp_url;
	protected $ftp_username;
	protected $ftp_password;
	protected $ftp_root;
	protected $ftp_passive;
	protected $ftp_mode;
	protected $ftp_port;
	
	public function getName(){
		return $this->name;	
	}
	public function getTemplates(){
		return $this->templates;	
	}
	public function setFtpDetails($details){
		$this->ftp_url = $details['url'];
		$this->ftp_root = $details['path'];
		$this->ftp_username = $details['username'];
		$this->ftp_password = $details['password'];
		$this->ftp_mode = $details['protocol'];
		$this->ftp_passive = $details['passv'];
		$this->ftp_port = $details['port'];
	}
	public function getFtpDetails(){
		return array(
					'url' => $this->ftp_url, 
					'path' => $this->ftp_root, 
					'username' => $this->ftp_username, 
					'password' => $this->ftp_password, 
					'protocol' => $this->ftp_mode, 
					'passv' => $this->ftp_passive,
					'port' => $this->ftp_port
				);
	}
	public function getUrl(){
		return $this->url;
	}
	public function getPassivemode(){
		return $this->ftp_passive;
	}
	public function getPort(){
		return $this->ftp_port;
	}
	public function getPath($path){
		switch($path){
			case 'root': return $this->ftp_root;break;
			default : throw new PathException('nopath');break;
		}
	}
	public function getRoot(){
		return $this->ftp_root;
	}
	public function getProtocol(){
		switch($this->ftp_mode){
			case 'ftp': return 'ftp://';break;
			case 'sftp': return 'sftp://';break;
			default : throw new SiteException('noftpmode');break;
		}
	}
	public function getModules(){
		if(isset($this->modules[$this->id])){
			return $this->modules[$this->id];
		}else if(isset($this->modules['new'])){
			return $this->modules['new'];
		}
		throw new DataException('nomodules');
	}
	public function hasTemplate($searchTemplate){
		foreach($this->templates as $template){
			print $template->getId().'-'.$searchTemplate->getId();
			if($template->getId() == $searchTemplate->getId()){
				return true;
			}
		}
		return false;
	}
}
?>