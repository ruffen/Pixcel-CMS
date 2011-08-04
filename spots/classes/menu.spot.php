<?php
class Menu extends StaticSpot{
	public function giveAdminButton(){
		
	}
	public function getContent(){
		global $INK_User, $dRep;
		$pages = $dRep->getPageCollection(array(
			'parent' => 0,
			'siteId' => $INK_User->getCustomer()->getSite()->getId(),
			'published' => '1'
		));
		ob_start();
		$this->getPages(0);
		return ob_get_clean();
		
	}
	private function getPages($parent){
		global $INK_User, $dRep;
		$pages = $dRep->getPageCollection(array(
			'parent' => $parent,
			'siteId' => $INK_User->getCustomer()->getSite()->getId(),
			'published' => 1
		));
		if(count($pages) == 0){
			return '';
		}
		if($this->getConfigvalue('ul') != 'no' || $parent != 0){
			echo '<ul>';
		}
		foreach($pages as $index => $page){		
			if($this->getConfigvalue('showindex') == 'no' && $page->getParent() == '0' && $page->getOrder() == 0){
				continue;
			}
			ob_start();
			if($this->getConfigvalue('multi') == 'yes'){
				$this->getPages($page->getId());	
			}
			$link = $page->getUrl();
			$name = $page->getTitle();
			$submenu = ob_get_clean();
			include('spots/frontendview/menuelement.view.php');
		}
		if($this->getConfigvalue('ul') != 'no' || $parent != 0){
			echo '</ul>';
		}
		return '';
	}
}
?>
