<?php
class FileSystemController extends BaseController{
	private $ftpConnection = false;
	public function index(){
		global $varChecker;
		$site = $this->INK_User->getCustomer()->getSite();
		$folders = $this->dRep->getFolderCollection(array('site' => $site->getId()));
		ob_start();
		$this->foldermenu('false');
		$this->template->folderlist = ob_get_clean();
		try{
			$this->template->folder = $this->getCurrentFolder();
		}catch(DataException $e){
		
		}
	}
	public function fileuploader(){
		//include_once('view/filesystem/fileuploader.filesystem.php');
	}
	public function uploadelement(){
		global $varChecker;
		$index = ($varChecker->getValue('index')) ? $varChecker->getValue('index') : 0;
		include_once('view/filesystem/uploadelement.filesystem.php');
	}
	public function foldermenu($noDrag = false)
	{
		global $varChecker;
		$drag = ($noDrag === false) ? $varChecker->getValue('drag') : $noDrag;
		$noDrag = ($drag == 'false') ? 'true' : 'false';

		$menu = new FolderList(array(), array('noDrag' => $noDrag));
		$folderlist = $menu->getList();
		include_once('view/filesystem/folderlist.filesystem.php');
	}
	public function deleteFolder(){
		$folder = $this->getCurrentFolder();
		if($folder->getId() == 'new'){
			echo json_encode(array('error' => 'del_newfolder'));
			return;
		}
		$this->dRep->delFolder($folder);
		echo json_encode(array('success' => 'folder_deleted'));
	}
	public function savefolders($folderTree = null, $parent = 'folder_0'){
		global $varChecker;
		if($folderTree == null){
			$folderTree = $varChecker->getValue('folders');
			//TODO: modify to cater for sites. 
			$folderTree = $folderTree[$parent];
		}
		//loop all pages and set the parent id
		$order = 0;
		foreach($folderTree as $index => $value){
			$folder = (is_array($value)) ? $this->dRep->getFolder(str_replace('folder_', '', $index)) : $this->dRep->getFolder(str_replace('folder_', '', $value));
			$oldParent = $folder->getParent();
			$oldOrder = $folder->getOrder();
			$properties = array();
			//change ordering
			if($oldOrder != $order){
				$properties['order'] = $order;
			}
			//change parent
			if($oldParent != $parent){
				$properties['parent'] = $this->dRep->getFolder(str_replace('folder_', '', $parent));
			}
			if(count($properties) > 0){
				$folder->setProperties($properties);
				$this->dRep->saveFolder($folder);
			}
			if(is_array($value)){
				$this->savefolders($folderTree[$index], $index);
			}
			$order++;
		}
		if($parent == 'folder_0'){
			echo json_encode(array('success' => 'true'));
		}
	}
	public function deletefile(){
		global $varChecker;
		$file = $this->dRep->getFile(str_replace('filebox_', '', $varChecker->getValue('fileId')));
		$this->dRep->delFile($file);
		echo json_encode(array('success' => 'file_deleted'));
	}
	public function movefile(){
	
	}
	public function savefile($path = ''){
		global $varChecker;
		$return = true;
		if($path == ''){
			$return = false;
			$path = $varChecker->getValue('filepath');
		}
		
		if(empty($path) || !$path){
			throw new DataException('missingpath');
		}
		$extension = substr($path, strrpos($path, '.'));
		
		$file = new File();
		$filetype = $this->dRep->getFiletype(str_replace('.', '', $extension));
		if($filetype->getId() == 'new'){
			throw new DataException('nofiletype');
		}
		$folder = $this->getCurrentFolder();
		$properties = array(
			'filename' => trim(substr($path, strrpos($path, '/')),'/'),
			'type' => $filetype,
			'folderId' => $folder->getId(),
			'filesize' => filesize($path)
		);
		try{
			$file->setProperties($properties);
			$file = $this->dRep->saveFile($file);
			//after saving file, we need to upload it to client server
			$connection = new HTTPUploader($this->INK_User->getCustomer()->getSite());
			$isUploaded = $connection->uploadFile($file, $path);
			if($isUploaded !== true){
				$result = array('fileid' => $file->getId(), 'error' => 'file_notuploaded', 'msg' => $isUploaded);
				$this->dRep->delFile($file);
			}else{
				$result = array('fileid' => $file->getId(), 'success' => 'file_saved');
			}
		}catch(Exception $ex){
			$result = array('error' => $ex->getMessage());
		}
		if($return){
			return $result;
		}else{
			echo json_encode($result);
		}
	}
	public function filelist(){
		global $varChecker;
		if($varChecker->getValue('publish') == 'publish'){
			$fileController = new FilesController($this->dRep);
			$fileController->publishfiles();
		}
		$folder = $this->getCurrentFolder();
		$getVar = (strpos($this->INK_User->getCustomer()->getSite()->getUrl(), 'localhost') === false) ? '' : '?rt=';
		$siteurl = trim($this->INK_User->getCustomer()->getSite()->getUrl(), '/').'/'.$getVar;
		
		foreach($folder->getFiles() as $index => $image){
			$id = $image->getId();
			$alter = ($index % 2 == 0) ? 'tralt' : '';
			$extension = $image->getFiletype()->getName();
			$filename = $image->getFilename();
			$source = $siteurl.$image->getFoldername().'/'.$image->getId().'.'.$image->getExtension();
			$timestamp = $image->getTimestamp('M d Y @ h:i');
			$size = $image->getSize('kb');
			$checkbox = true;
			include('view/files/filelist.files.php');	
		}
	}
	public function foldername(){
		$folder = $this->getCurrentFolder();
		$name = $folder->getName();
		include_once('view/filesystem/foldername.filesystem.php');
	}
	public function getFilebrowser(){
		global $varChecker;
		$folderId = 0;
		$rootname = "";
		if($varChecker->getValue('id') != 0){
			if($varChecker->getValue('direction') == 'up'){
				$folder = $this->dRep->getFolder($varChecker->getValue('id'));
				$folderId = $folder->getParent()->getId();
				if($folderId == 'new'){
					$folderId = 0;
					$rootname = "";
				}else{
					$rootname = $folder->getParent()->getName();
				}
			}else{
				$folderId = $varChecker->getValue('id');
				$folder = $this->dRep->getFolder($folderId);
				$rootname = $folder->getName();
			}
		}
		if($varChecker->getValue('siteId') !== false){
			$folders = $this->dRep->getFolderCollection(array('site' => $varChecker->getValue('siteId')));
			if(count($folders) == 0){
				$folderId = 0;
				$rootname = '';
			}
		}
		$includefiles = ($varChecker->getValue('type') != 'folder');
		$menu = new FolderList(array(), array('class' => 'type'), $folderId, false, false, $includefiles);
		$folderlist = $menu->getList();
		$root = 'current_'.$folderId;
		include_once('view/filesystem/filebrowser.filesystem.php');
		
	}
	public function createResourceFolder(){
		$folder = new Folder();
		$properties = array(
			'id' => 'new',
			'name' => 'resources',
			'parent' => 0,
			'resource' => 1
			);
		$folder->setProperties($properties);
		$folder = $this->dRep->saveFolder($folder);
		return $folder;
	}
	public function savefolder(){
		global $varChecker;
		$folder = $this->getCurrentFolder();
		if($varChecker->getValue('name') == false || trim($varChecker->getValue('name')) == ''){
			echo json_encode(array('error' => 'nofoldername'));
			return false;
		}
		$folder->setProperties(array('name' => $varChecker->getValue('name')));
		$folder = $this->dRep->saveFolder($folder);
		
		echo json_encode(array('success' => 'foldersaved', 'id' => $folder->getId()));
	}
	
	public function changefolder(){
		$folder = $this->getCurrentFolder();
		$return = array('foldername' => $folder->getName(), 'id' => $folder->getId());
		print json_encode($return);	
	}
	private function getCurrentFolder(){
		global $varChecker;
		try{
			$id = $varChecker->getValue('id');
			if($id == 'new'){
				$folder = new Folder();
				$folder->setProperties(array('id' => 'new'));
			}else{
				$folder = $this->dRep->getFolder($id);
			}			
		}catch(DataException $e){
			$folders = $this->dRep->getFolderCollection(array('site' => $this->INK_User->getCustomer()->getSite()->getId()));
			if(count($folders) == 0){
				throw new DataException('nofolder');
			}
			$folder = $folders[0];
		}
		return $folder;				
	}
}
