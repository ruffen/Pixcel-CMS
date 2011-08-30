<?php
class Site extends Asset{
	protected $id;
	protected $name;
	protected $url;
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
	public function getFtpDetails(){
		return array(
			'url' => $this->ftp_url, 
			'root' => $this->ftp_root, 
			'ftpuser' => $this->ftp_username, 
			'ftppass' => $this->ftp_password, 
			'protocol' => $this->ftp_mode, 
			'passv' => $this->ftp_passive,
			'port' => $this->getPort()
			);
	}
	public function getFTPUsername(){
		return $this->ftp_username;
	}
	public function getFTPPassword(){
		return $this->ftp_password;
	}
	public function getFTPUrl(){
		return $this->ftp_url;
	}
	public function getUrl(){
		return $this->url;
	}
	public function UsesPassive(){
		return $this->ftp_passive;
	}
	public function getPort(){
		if(empty($this->ftp_port)){
			if($this->ftp_mode == 'ftp' || empty($this->ftp_mode)){
				return 21;
			}else{
				return 22;
			}
		}else{
			return $this->ftp_port;
		}
		
	}
	public function getPath($path){
		switch($path){
			case 'root': return (empty($this->ftp_root)) ? '/' : $this->ftp_root;break;
			default : throw new PathException('nopath');break;
		}
	}
	public function getMode(){
		return $this->ftp_mode;
	}
	public function getRoot(){
		return $this->ftp_root;
	}
	public function getProtocol(){
		switch($this->ftp_mode){
			case 'ftp': return 'ftp://';break;
			case 'sftp': return 'sftp://';break;
			default : return 'ftp://';break;
		}
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