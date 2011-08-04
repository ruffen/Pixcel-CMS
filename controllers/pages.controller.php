<?php
class PagesController extends BaseController{
	public function index(){
		$site = $this->INK_User->getCustomer()->getSite();
		$templates = $this->dRep->getTemplateCollection(array('site' => $site->getId()));
		$menu = new PageList(array('id' => 'dhtmlgoodies_tree2'), array('noDrag' => 'true'));
		$page = $this->getPage();
		$this->template->template = $page->getTemplate();
		$this->template->siteName = $site->getName();
		$this->template->sitemap = $menu->getList();
		$this->template->templates = $templates;
		$this->template->page = $page;
		$this->template->indexText = ($page->isIndex()) ? ' - (index)' : '';

		if($userId = MemoryHandler::getInstance()->pageLocked($page) !== false){
			$userWithLock = $this->dRep->getUser($userId);
			$this->template->locked = true;
			$this->template->userWithLocked = $userWithLock;
			$this->template->disable = false;
			if($userWithLock->getId() != $this->INK_User->getId()){
				$this->template->disable = true;
			}
		}else{
			$this->template->disable = false;
			$this->template->locked = false;
		}
	}
	public function lockpage(){
		$page = $this->getPage();
		if($userId = MemoryHandler::getInstance()->lockPage($page, $this->INK_User) !== true){
			$userWithLock = $this->dRep->getUser($userId);
			if($userWithLock->getId() != $this->INK_User->getId()){
				$result = array('name' => $this->INK_User->getUsername(), 'error' => 'islocked');
			}else{
				$result = array('name' => $this->INK_User->getUsername(), 'success' => 'sameuser');
			}
			
		}else{
			$result = array('name' => $this->INK_User->getUsername(), 'success' => 'locked');
		}
		echo json_encode($result);
	}
	public function unlockpage(){
		$page = $this->getPage();
		if(MemoryHandler::getInstance()->unlockPage($page)){
			$return = array('success' => 'page_unlocked');
		}else{
			$return = array('error' => 'page_notunocked');
		}
		echo json_encode($return);
	}
	public function sitemap(){
		global $varChecker;
		$drag = $varChecker->getValue('drag');
		$noDrag = ($drag == 'false') ? 'true' : 'false';

		$site = $this->INK_User->getCustomer()->getSite();
		
		$menu = new PageList(array('id' => 'sitemap'), array('noDrag' => $noDrag) );
		$siteName = $site->getName();
		$sitemap = $menu->getList();
		include_once('view/pages/sitemap.pages.php');
	}
	public function savesitemap($pageTree = null, $parent = 0){
		global $varChecker;
		if($pageTree == null){
			$pageTree = $varChecker->getValue('pages');
			//TODO: modify to cater for sites. 
			$pageTree = $pageTree[$parent];
		}
		//loop all pages and set the parent id
		$order = 0;
		foreach($pageTree as $index => $value){
			$page = (is_array($value)) ? $this->dRep->getPage($index) : $this->dRep->getPage($value);
			$oldParent = $page->getParent();
			$oldOrder = $page->getOrder();
			//change ordering
			if($oldOrder != $order){
				$page->setOrder($order);
				//update page in storage
				$this->dRep->updatePage($page, 'order', $page->getOrder());
			}
			//change parent
			if($oldParent != $parent){
				$page->setParent($parent);
				//update the page in stoarge			
				$this->dRep->updatePage($page, 'parent', $page->getParent());
			}
			if(is_array($value)){
				$this->savesitemap($pageTree[$index], $index);
			}
			$order++;
		}
		if($parent == 0){
			echo json_encode(true);
		}
	}
	public function newpage(){
		$site = $this->INK_User->getCustomer()->getSite();
		$templates = $this->dRep->getTemplateCollection(array('site' => $site->getId()));
		include('view/pages/basicdetails.pages.php');
	}
	public function getPagedetails($save = false){
		global $varChecker;
		if($save === false){
			$page = $this->getPage();
		}else{
			$page = $save;
		}
		if($userId = MemoryHandler::getInstance()->pageLocked($page) !== false && $save === false){
			$userWithLock = $this->dRep->getUser($userId);
			$locked = true;
		}else{
			$locked = false;
		}
		$jsonPage = array(
			'id' => $page->getId(), 
			'title' => $page->getTitle(), 
			'description' => $page->getDescription(),
			'keywords' => $page->getKeywords(),
			'locked' => $locked,
			'status' => $page->published(),
			'oldStatus' => $page->published(true),
			'date' => $page->getPublishDate('l j F @ g:ia'),
			'index' => $page->isIndex()
		);
		if($locked){
			$jsonPage['name'] = $userWithLock->getUsername();
			if($userWithLock->getId() == $this->INK_User->getId()){
				if($varChecker->getValue('cancel') != ''){
					$result  = MemoryHandler::getInstance()->unlockPage($page);
					$jsonPage['success'] = 'canceled';
					$jsonPage['locked'] = false;
					unset($jsonPage['name']);
				}else{
					$jsonPage['success'] = 'islocked';
				}
			}else{
				$jsonPage['error'] = 'islocked';
			}		
		}
		if($save){
			$jsonPage['success'] = 'saved';
		}
		return json_encode($jsonPage);
	}
	public function savePage(){
		global $varChecker;
		$new = false;
		$page = $this->getPage();
		if($userId = MemoryHandler::getInstance()->pageLocked($page) !== false){
			$userWithLock = $this->dRep->getUser($userId);
			if($userWithLock->getId() != $this->INK_User->getId()){
				echo json_encode(array('error' => 'isLocked'));
				return false;
			}
		}	

		if($page->getId() == 'new'){
			$page->setParent(0);
			$new = true;
		}		
		//getting page values before saving the page
		$page->setPublished('draft');
		$page->setLang(1, 'Title', $varChecker->getValue('pagename'));
		$page->setLang(1, 'Keywords', $varChecker->getValue('keywords'));
		$page->setLang(1, 'Description', $varChecker->getValue('description'));
		if(!is_object($page->getTemplate()) || $varChecker->getValue('template') != $page->getTemplate()->getId()){
			$template = $this->dRep->getTemplate($varChecker->getValue('template'));
			$page->setProperties(
				array(
					'template' => $template,
					'author' => $this->INK_User
				)
			);			
		}
		try{
			$page = $this->dRep->savePage($page);
			if($new){
				if(MemoryHandler::getInstance()->lockPage($page, $this->INK_User) !== true){
					throw new LockException("Could not lock new page");
				}
			}else{
				MemoryHandler::getInstance()->unlockPage($page);
			}
			print $this->getPagedetails($page);
		}catch(Expression $e){
			print json_encode(array('error' => 'save_error'));
		}
	}
	public function revisionlist(){
		$page = $this->getPage();
		$alternate = false;
		foreach($page->revisions() as $index => $revision){
			if($revision->isDraft()){
				continue;
			}
			$alt = ($alternate) ? 'tralt' : '';
			$date = $revision->getDate('d F Y');
			$time = $revision->getDate('H:i');
			$username = $revision->getAuthor()->getUsername();
			$revId = $revision->getId();
			$current = ($revision->getId() == $page->currentRevision()->getId()) ? ' current' : '';
			include('view/pages/revisiontable.pages.php');
			$alternate = !$alternate;
		}
	}
	public function useRevision(){
		global $varChecker;
		$page = $this->getPage();
		$page = $this->dRep->changePageRevision($page, $varChecker->getValue('idRev'));
		print $this->getPageDetails();
	}
	public function deletePage(){
		$page = $this->getPage();
		if($userId = MemoryHandler::getInstance()->pageLocked($page) !== false){
			$userWithLock = $this->dRep->getUser($userId);
			if($userWithLock->getId() != $this->INK_User->getId()){
				echo json_encode(array('error' => 'isLocked'));
				return false;
			}
		}		
		try{
			$this->dRep->delPage($page);		
			echo json_encode(array('success' => 'delete_success'));
		}catch(ChildError $e){
			echo json_encode(array('error' => $e->getMessage()));
		}/*catch(Exception $e){
			echo json_encode(array('error' => 'delete_error'));
		}*/
	}
	public function deleteRevision(){
		global $varChecker;
		$page = $this->getPage();
		$revisions = array();
		foreach($page->revisions() as $index => $revision){
			if($revision->getId() != $varChecker->getValue('idRev')){
				$revisions[] = $revision;	
			}	
		}
		$page->setProperties(array('revisions' => $revisions));
		$this->dRep->delPage($page, $varChecker->getValue('idRev'));
		echo json_encode(array('status' => 'ok'));
	}
	public function publishpage(){
		global $varChecker;
		/**
		 *TODO: check user access
		 *		canPublish does not actually check, dummy method
		 **/
		$page = $this->getPage();
		if($varChecker->getValue('action') == 'publish'){
			if($page->published() == 1 || $page->published() == 2){
				echo json_encode(array('error' => 'alredy_published'));
				return false;
			}
		}else if($varChecker->getValue('action') == 'expire'){
			if($page->published() > 2){
				echo json_encode(array('error' => 'alredy_expired'));
				return false;
			}
		}else{
			throw new DataException('invalid_pubaction');
		}
		if($this->INK_User->canPublish($page)){
			switch($page->published()){
				case 0:												//draft
				case 2:												//waiting publish approval
				case 4: 											//withdrawn, set to publish
				case 5: $status = 1;$msg = 'pub';break;				//Expired -- Set all these to published
				case 3:												//waiting withdraw approval
				case 1: $status = 4;$msg = 'withdrawn';break;		//published, set to withdrawn
			}	
		}else{
			switch($page->published()){
				case 4 :											//withdrawn
				case 5 : 											//expired
				case 3 : 											//waiting withdrawn approval
				case 0 : $status = 2;$msg = 'pub_wait';break;		//draft --- set all these to waiting publish approval
				case 2 :											//waiting publish approval
				case 1 : $status = 3;$msg = 'withdrawn_wait';break;	//published -- set all these to waithing withdraw approval
			}			
		}
		$page->setPublished($status);
		
		$this->dRep->updatePage($page, 'published', $page->published());
		$page = $this->dRep->getPage($page->getId());
		$publisher = new Publisher($page);

		switch($status){
			case 1:
			case 4: $publisher->publish();break;
		}
		echo json_encode(array('success' => $msg, 'status' => $status, 'oldStatus' => $page->published(true), 'date' => $page->getPublishDate('l j F @ g:ia')));
	}
	public function indexpage(){
		$page = $this->getPage();
		$page->setAsIndex();
		$this->dRep->updatePage($page, 'index', $page->isIndex());
		$publisher = new Publisher($page);
		$publisher->createSitemap();
		
		echo json_encode(array('success' => 'indexset'));
		
	}
	public function spotmenu(){
		global $varChecker;
		$page = $this->getPage();
		if($userId = MemoryHandler::getInstance()->pageLocked($page) !== false){
			$userWithLock = $this->dRep->getUser($userId);
			if($userWithLock->getId() != $this->INK_User->getId()){
				$this->template->disable = true;
				$msgController = new MessageController($this->dRep);
				return $msgController->getMessage('isLocked');
			}
		}
		if($varChecker->getVAlue('tplId') < 1){
			throw new DataException('invalidtemplate');
		}
		//if template has changed set the new one. 
		if($varChecker->getValue('tplId') != $page->getTemplate()->getId()){
			$template = $this->dRep->getTemplate($varChecker->getValue('tplId'));
			$page->setProperties(array('template' => $template));			
		}
		
		$this->dRep->savePage($page, false);
		ob_start();
		foreach($page->getTemplate()->getSpots() as $tplSpotId => $spot){
			if(!$spot->uservalue()){
				continue;
			}
			$id = $tplSpotId;
			$order = $spot->order();
			$name =  $spot->getName();
			$spotType = $spot->getType();
			$sysName = $spot->systemName();
			$description = $spot->getDescription();
			$buttonSrc = $spot->getButtonimage();
			include('view/pages/spotbutton.pages.php');
		}
		$spots = ob_get_clean();
		ob_start();
		include('view/pages/spotmenu.pages.php');
		echo json_encode(array('success' => 'spotmenu', 'html' => ob_get_clean()));
	}
	public function spotedit(){
		global $varChecker;
		$page = $this->getPage();
		$spotTplId = $varChecker->getValue('spotId');
		$spot = $page->getSpot($spotTplId);
		try{
			$value = $page->getValue($spotTplId, 'admin');
		}catch(DataException $ex){
			$value= '';
		}
		$admin = $spot->giveadmin($spotTplId, $value);
	}
	public function spotscript(){
		global $varChecker;
		$page = $this->getPage();
		$spot = $page->getSpot($varChecker->getValue('spotId'));
		echo 'spots/'.$spot->systemName().'.js';
	}
	public function setspotvalue(){
		global $varChecker;
		$page = $this->getPage();
		$spotTplId = $varChecker->getValue('spotId');
		$spot = $page->getSpot($spotTplId);
		//langId needs to come from somewhere..... TODO!
		try{
			$page->setLang(1, $spotTplId , $varChecker->getValue('value'));
			$this->dRep->savePage($page, false);
		}catch(Exception $e){
			echo json_encode(array('result' => 'fail'));
		}
		echo json_encode(array('result' => 'saved'));
	}
	private function getPage(){
		global $varChecker;
		if($varChecker->getValue('id') !== false && $varChecker->getValue('id') != 'new'){
			//return session page if we have it
			$page = $this->dRep->getPage($varChecker->getValue('id'), false);
			if($page->getSiteId() == $this->INK_User->getCustomer()->getSite()->getId()){
				return ($page instanceof Page) ? $page : $this->dRep->getPage($varChecker->getValue('id'));
			}
		}
		//we are visiting for first time, clear session, maybe to this in Repository? 
		unset($_SESSION['Page']);
		$pages = $this->dRep->getPageCollection(array(
			'parent' => 0,
			'siteId' => $this->INK_User->getCustomer()->getSite()->getId()
			)
		);
		if(count($pages) > 0){
			return $pages[0];	
		}
		$page = new Page();
		$page->setProperties(array('id' => 'new'));
		return $page;
	}
}
?>