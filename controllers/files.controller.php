<?php
class FilesController extends BaseController{
	public function index(){

	}
	public function upload(){
		global $varChecker;
		if($varChecker->getValue('fileindex') !== false){
			$fileindex = $varChecker->getValue('fileindex');
			$fileindex = (empty($fileindex)) ? 0 : $fileindex;
			$handle = $this->findFilehandle($fileindex);
		}else{
			$handle = $this->findFilehandle();
		}
		$uploader = new FileUploader($handle, true);
		$error = '';
		$path = 'cache/templates/'.$this->INK_User->getCustomer()->getSite()->getId().'/';
		$fmaker = new FileMaker();
		$fmaker->makeDirectory($path, false);	
		//upload the file
		try{
			//upload to cms
			$path = $uploader->upload($path);
			//upload to client server
			$result = array('success' => 'file_uploaded', 'filepath' => $path, 'msg' => 'file uploaded');
		}catch(Exception $e){
			$result = array('error' => $e->getMessage());
		}
		echo json_encode($result);
	}
	public function Unpack(){
		global $varChecker;
		$path = $varChecker->getValue('path');
		$zipper = new Zipper();
		
		if($zipper->IsZipFile($path)){
			$fmaker = new FileMaker();
			$filename = substr($path, strrpos($path, '/') + 1);
			$folderpath = str_replace($filename, '', $path);
			$folderpath = $folderpath.'resources/';
			$fmaker->makeDirectory($folderpath, true);
			$files = $zipper->Decompress($path, $folderpath);
			$processedFiles = $this->saveFiles($files, $folderpath);
			$savedFiles = $processedFiles['saved'];
			$errorfiles = $processedFiles['error'];
		}else{
			//create files array with just one file				
		}
		if(count($savedFiles) > 0){
			$result = array('success' => 'resourcesuploaded', 'files' => $savedFiles, 'errorfiles' => $errorfiles, 'folder' => $folderpath, 'rawcount' => count($files));
		}else{
			$result = array('error' => 'resourcesfailed', 'files' => $savedFiles, 'errorfiles' => $errorfiles, 'folder' => $folderpath, 'rawcount' => count($files));
			
		}
		
		echo json_encode($result);		
	}
	private function saveFiles($files, $path){
		global $varChecker;
		$fileController = new FileSystemController($this->dRep);
		$savedFiles = array();
		$errorFiles = array();
		set_time_limit (0);
		$varChecker->changeVariable('id', 'resources');
		foreach($files as $index => $file){
			try{
				$result = $fileController->saveFile($path.$file);
			}catch(DataException $e){
				$errorFiles[] = $path.$file;
				continue;
			}
			if(isset($result['success'])){
				$savedFiles[] = $result['fileid'];
			}else{
				$errorFiles[] = $result['error'].' message: '.$result['msg'];			
			}
		}
		$this->publishfiles();
		set_time_limit(60);
		$return['saved'] = $savedFiles;
		$return['error'] = $errorFiles;
		return $return;
	}
	public function publishfiles(){
		$publisher = new Publisher();
		$publisher->publishResources($this->dRep->getFileCollection(array('site' => $this->INK_User->getCustomer()->getSite()->getId())));
	}
	private function findFilehandle($index = false){
		if($index === false){
			$handleArray = array('file', 'templatefiles');
		}else{
			$handleArray = array('templatefiles_'.$index, 'file_'.$index);
		}
		foreach($handleArray as $handle){
			if(isset($_FILES[$handle])){
				return $_FILES[$handle];
			}	
		}
		throw new DataException('nofilehandleset '.$handle.' index '.$index.' ');
	}
}
?>