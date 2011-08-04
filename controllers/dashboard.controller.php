<?php
class DashboardController extends BaseController{
	public function index(){
		$pagelist = $pages = $this->dRep->getPageCollection(
			array(
				'siteId' => $this->INK_User->getCustomer()->getSite()->getId()
			),
			'dateCreated',
			10
		);
		$this->template->pagehistory = $pagelist;
	}
}
?>
