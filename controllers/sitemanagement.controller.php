<?php
class SystemController extends BaseController{
	public function index(){
		$site = $this->INK_User->getCustomer()->getSite();
		$customer = $this->INK_User->getCustomer();

		$this->template->sites = $this->INK_User->getCustomer()->getSites();
	}
}
?>