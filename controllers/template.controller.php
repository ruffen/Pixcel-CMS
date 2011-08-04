<?php
class TemplateController extends BaseController{
	public function index(){
		try{
			$template = $this->getTpl();
		}catch(DataException $e){
			$template = new Template();
			$properties = array('id' => 'new', 'name' => '');
			$template->setProperties($properties);
		}
		$folder = $template->getResourcefolder();
		$site = $template->getSite();
		$this->template->template = $template;
		$this->template->site = $site;
		$this->template->sites = $this->dRep->getSiteCollection(array('roleId' => $this->INK_User->getRole()->getId()));
		$this->template->resourceFolderId = ((!is_object($folder) && $folder == 0) || $folder->getId() == 'new') ? 0 : $folder->getId();
		$this->template->resourceFolderName = ((!is_object($folder) && $folder == 0) || $folder->getId() == 'new') ? "" : $folder->getName();
	}
	public function getTemplateDetails(){
		$template = $this->getTpl();
		$folder = $template->getResourcefolder();
		$foldername = ((!is_object($folder) && $folder == 0) || $folder->getId() == 'new') ? "" : $folder->getName();
		$folderId = ((!is_object($folder) && $folder == 0) || $folder->getId() == 'new') ? 0 : $folder->getId();
		echo json_encode(array('name' => $template->getName(), 'id' => $template->getId(), 'siteId' => $template->getSite()->getId(), 'foldername' => $foldername, 'folderId' => $folderId));
	}
	public function getSpots(){
		$template = $this->getTpl();
		$spots = $template->getSpots();
		$count = 0;
		foreach($spots as $index => $spot){
			if($spot->order() == ''){
				$spot->setProperties(array('order' => $count));
			}
			echo $this->getSpot($spot);
			$count++;
		}
	}
	public function templatemenu(){
		global $varChecker;
		$templates = $this->dRep->getTemplateCollection(array('site' => $varChecker->getValue('siteId')));
		foreach($templates as $tpl){
			include('view/template/templatemenulistitem.template.php');
		}
	}
	public function getSpot($ajax = true){
		global $varChecker;
		$template = $this->getTpl();
		if($ajax === true){
			$tplSpot = $template->getSpot($varChecker->getValue('tplSpotId'));
			$index = $varChecker->getValue('index');
		}else{
			$tplSpot = $ajax;
			$index = $tplSpot->order();
		}
		$returnSpot = array('name' => $tplSpot->getName(), 'id' => $tplSpot->getId(), 'tplSpotId' => $tplSpot->getTplSpotId());
		$template = $this->getTpl();
		$spots = $this->dRep->getSpotCollection(array());
		$showSpotconfig = ($tplSpot instanceof Spot && $tplSpot->hasConfigs()) ? '' : 'hidden';
		ob_start();
		include('view/template/spotrow.template.php');
		$returnSpot['html'] = ob_get_clean();
		if($ajax === true){
			print json_encode($returnSpot);
		}else{
			return $returnSpot['html'];
		}
	}
	public function saveSpotConfig(){
		global $varChecker;
		$tpl= $this->getTpl();
		$spot = $tpl->getSpot($varChecker->getValue('tplSpotId'));
		foreach($spot->getAvailableconfigs() as $configId => $config){
			$spot->setConfigValue($config->getIndex(), $varChecker->getValue($config->getIndex()));
		}
		$tpl->setSpot($spot);
		$this->dRep->saveTemplate($tpl, false);
		echo json_encode(array('success' => 'changedspotconfig'));
	}
	public function spothasconfig(){
		global $varChecker;
		$spotId = $varChecker->getValue('spotId');
		$result = array();
		if($spotId == 0){
			$result['spotconfig'] = false;
		}else{
			$spot = $this->dRep->getSpot($spotId);
			$result['spotconfig'] = ($spot->hasConfigs());		
		}
		echo json_encode($result);
	}
	public function showSpotConfig(){
		global $varChecker;
		$tpl= $this->getTpl();
		$spot = $tpl->getSpot($varChecker->getValue('tplSpotId'));
		if($spot->getId() != $varChecker->getValue('spotId')){
			$configs = $spot->getConfigValues();
			$order = $spot->order();
			$tplSpotId = $spot->getTplSpotId();
			$spot = $this->dRep->getSpot($varChecker->getValue('spotId'));
			$spot->setProperties(array('order' => $order, 'tplSpotId' => $tplSpotId, 'configs' => $configs));
			$tpl->setSpot($spot);
			$this->dRep->saveTemplate($tpl, false);
		}

		ob_start();
		foreach($spot->getAvailableconfigs() as $configId => $config){
			$configName = $config->getName();
			$index = $config->getIndex();
			$value = $spot->getConfigvalue($index);
			include('view/template/configrow.template.php');
		}
		$configRows = ob_get_clean();
		include('view/template/spotconfig.template.php');
		
	}
	public function maketpl(){
		global $varChecker;
		$templateMaker = new TemplateMaker();
		if($varChecker->getValue('id') != 'new'){
			$template = $this->getTpl();
		}else{
			$template = new Template();
			$properties = array('id' => 'new', 'name' => '');
			$template->setProperties($properties);
		}
		$template = $templateMaker->createTemplate($varChecker->getValue('path'), $varChecker->getValue('name'), $template);
		$this->dRep->saveTemplate($template, false);
		$spots = $template->getSpots();
		$newSpots = array();
		foreach($spots as $key => $spot){
			//loop trhough and check if the spot exists, if it does return the id, else return what user is trying to get
			if(is_object($spot)){
				$newSpots[] = array('id' => $spot->getId(), 'tplSpotId' => $spot->getTplSpotId());
			}else{
				$newSpots[] = array('id' => 0, 'tplSpotId' => $key);
			}
		}
		$templateData = array(
			'name' => $template->getName(),
			'spots' => $newSpots
			);
		print json_encode($templateData);
	}
	public function deletetemplate(){
		$tpl = $this->getTpl();
		if($tpl->getId() == 'new'){
			echo json_encode(array('error' => 'del_newtemplate'));
		}
		$this->dRep->delTemplate($tpl);
		echo json_encode(array('success' => 'del_template'));
	}
	public function savetemplate(){
		global $varChecker;
		$template = $this->dRep->getTemplate($varChecker->getValue('id'), false);
		
		if($varChecker->getValue('name') != ''){
			$name = $varChecker->getValue('name');
		}else{
			throw new DataException('nonatemplateme', array('templatename'));
		}
		if($varChecker->getValue('folderId') == 0){
			throw new DataException('noResourceFolder', array('resourcefilefields'));
		}
		$resourceFolder = $this->dRep->getFolder($varChecker->getValue('folderId'));
		if($resourceFolder->getId() == 'new'){
			throw new DataException('folderNotExist');
		}
		//we have the temporary template created, now we need to make it propper
		$spots = $template->getSpots();
		$postedSpots = $varChecker->getValue('spots');
		$count = 0;
		$newSpots = array();
		foreach($postedSpots as $index => $postedSpot){
			$postedSpotArray = explode('-', $postedSpot);
			if(count($postedSpotArray) != 3){
				throw new DataException('errorSaveSpot');	
			}
			$tplSpotId = $postedSpotArray[0];
			$spotId = $postedSpotArray[1];
			$spotName = $postedSpotArray[2];
			if($spotId == 'new' || $spotId == 0){
				throw new DataException('allspotsneedtype');
			}
			if(isset($spots[$tplSpotId])){
				//this spot exists from before - check if it has changed
				$spot = $spots[$tplSpotId];
				if($spot->getId() != $spotId){
					$configs = $spot->getConfigValues();
					$order = $spot->order();
					$tplSpotId = $spot->getTplSpotId();
					$spot = $this->dRep->getSpot($spotId);
					$spot->setProperties(array('order' => $order, 'tplSpotId' => $tplSpotId, 'configs' => $configs));
				}
			}else{
				$spot = $this->dRep->getSpot(array('spotId' => $spotId));
			}
			$spotName = $spot->getConfigvalue('name');
			if($spotName !== false){
				$spot->setConfigvalue($spotName, $spotName);
			}
			$order = array('order' => $index);
			$spot->setProperties($order);
			$newSpots[$tplSpotId] = $spot;
			$count ++;
		}
		$vars = array('name' => $name, 'spots' => $newSpots, 'folder' => $resourceFolder);
		if(!is_object($template->getSite())){
			$vars['site'] = $varChecker->getValue('siteId');
		}else{
			if($template->getSite()->getId() != $varChecker->getValue('siteId')){
				$fm = new FileMaker();
				if(!is_dir('cache/templates/'.$varChecker->getValue('siteId'))){
					$fm->makeDirectory('cache/templates/'.$varChecker->getValue('siteId'));
				}
				$oldPath = 'cache/templates/'.$template->getSite()->getId().'/'.$template->getFilename();
				$newPath = 'cache/templates/'.$varChecker->getValue('siteId').'/'.$template->getFilename();
				$fm->moveFile($oldPath, $newPath);
				$vars['site'] = $varChecker->getValue('siteId');
			}
		}
		$template->setProperties($vars);
		$template = $this->dRep->saveTemplate($template);
		echo json_encode(array('success' => 'tpl_saved', 'id' => $template->getId()));
	}
	private function getTpl(){
		global $varChecker;
		if($varChecker->getValue('id') != ''){
			$template = $this->dRep->getTemplate($varChecker->getValue('id'), false);
			return $template;
		}
		$sites = $this->INK_User->getCustomer()->getSites();
		foreach($sites as $siteId => $site){
			$templates = $this->dRep->getTemplateCollection(array('site' => $site->getId()));
			if(count($templates) > 0){
				break;
			}
		}
		
		if(count($templates) == 0){
			throw new DataException('notemplate');
		}
		return $templates[0];
	}
}
?>