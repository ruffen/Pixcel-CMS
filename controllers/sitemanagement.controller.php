<?php
class SitemanagementController extends BaseController{
	public function index(){
		$site = $this->INK_User->getCustomer()->getSite();
		$customer = $this->INK_User->getCustomer();

		$this->template->site = $site;
		$this->template->sites = $this->INK_User->getCustomer()->getSites();
		$this->template->ftpdetails = $site->getFtpDetails();
	}
	public function Save(){
		
		
		
	}
}
?>