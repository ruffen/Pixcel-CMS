<?php
class SitemanagementController extends BaseController{
	public function index(){
		$sites = $this->dRep->getSiteCollection(array('customer' => $this->INK_User->getCustomer()->getId()));
		$site = $this->getSite();
		if($site->getId() == 'new' && count($sites) > 0){
			$site = $sites[0];
		}
		$customer = $this->INK_User->getCustomer();
		$this->template->site = $site;
		$this->template->sites = $sites;
		$this->template->ftpdetails = $site->getFtpDetails();
	}
	public function TestFTPConnection(){
		$site = $this->getSite();
		try{
			$ftp = new FTPUploader($site);
		}catch(FTPException $e){
			return array('error' => $e->getMessage());
		}
		return array('success' => 'ftp_ok');
	}
	public function Save(){
		global $fido;
		$site = $this->getSite();
		$site = $this->dRep->saveSite($site);
		$fido->updateSite($site);
		
		$this->INK_User->getRole()->setAccess($site, true);
		$this->dRep->saveRole($this->INK_User->getRole());
		$fido->updateUser($this->INK_User);
		
		if($site->getId() != 'new'){
			return array('success' => 'savesite', 'id' => $site->getId());
		}else{
			return array('error' => 'savesite_failed');
		}
	}
	public function SiteDetails(){
	
	}
	public function SiteList(){
		$sites = $this->dRep->getSiteCollection(array('customer' => $this->INK_User->getCustomer()->getId()));
			
		$returnsites = array();
		foreach($sites as $site){
			$siteprops = array('id' => $site->getId(), 'name' => $site->getName());
			$returnsites[] = $siteprops;
		}
		return $returnsites;
	}
	
	private function getSite(){
		global $varChecker;
		try{
			$site = $this->dRep->getSite($varChecker->getValue('id'));
		}catch(DataException $e){
			$site = new Site();
		}
		$this->buildsiteFromPostdata($site);
		return $site;
	}
	private function buildsiteFromPostdata($site){
		global $varChecker;
		$site->setProperties($varChecker->getSentVariables());
	}
}
?>