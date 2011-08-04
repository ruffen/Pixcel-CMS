<?php
class ImageLink extends AdminSpot{
	public function giveAdmin($spotTplId, $value = ''){
		global $dRep;
		$value = unserialize($value);
		$url = (is_array($value) && isset($value['url'])) ? $value['url'] : '';
		$menu = new FolderList(array());
		$folderlist = $menu->getList($value);
		$imagelist = $this->filelist($value);
		$spotId = $spotTplId;
		include('spots/adminviews/imagelink.admin.php');
	}
	private function filelist($fileId){
		global $dRep;
		$customer = unserialize($_SESSION['customer']);
		$siteurl = trim($customer->getSite()->getUrl(), '/').'/';
		$folders = $dRep->getFolderCollection(array('site' => $customer->getSite()->getId()));
		if(count($folders > 0)){
			$folder = $folders[0];
			ob_start();
			foreach($folder->getFiles() as $index => $image){
				$alter = ($index % 2 == 0) ? 'tralt' : '';
				$selected = ($image->getId() == $fileId) ? 'selected' : '';
				$extension = $image->getFiletype()->getName();
				$filename = $image->getFilename();
				$source = $siteurl.$image->getFoldername().'/'.$image->getId().'.'.$image->getExtension();
				$source = 'cache/templates/'.$customer->getSite()->getId().'/'.$image->getFilename();
				$timestamp = $image->getTimestamp('M d Y @ h:i');
				$id = $image->getId();
				$checkbox = false;
				$size = $image->getSize('kb');
				include('view/files/filelist.files.php');	
			}
			return ob_get_clean();
		}
		return 'error - no folders';
	}
	public function getContent($value){
	
	}

}
?>
