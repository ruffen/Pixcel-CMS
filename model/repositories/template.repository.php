<?php
class TemplateRepository extends MysqlDb{
	private $templates = array();
	private $where = array(); 
	public function getTemplateCollection(array$where){
		global $dRep;
		$where = $this->sqlBuilder->createWhere($where);
		$sql = "SELECT * FROM ink_templates A WHERE {$where}";
		$data = $this->runManyQuery($sql);
		$templates = array();
		foreach($data as $index => $row){
			$properties = array(
				'id' => $row['templateId'],
				'name' => $row['name'],
				'filename' => $row['filename'],
				'spots' => $dRep->getSpotCollection(array('template' => $row['templateId'])),
				'folder' => $row['resourceFolder'],
				'site' => $row['siteId']
			);
			$template = new Template();
			$template->setProperties($properties);
			$this->templates[$row['templateId']] = $template;
			$templates[] = $template;
		}
		return $templates;
	}
	public function getTemplate($templateId){
		global $dRep;
		if(isset($this->templates[$templateId])){
			return $this->templates[$templateId];	
		}	
		$sql = "SELECT * FROM ink_templates A WHERE templateId = '{$templateId}'";
		$row = $this->runSingleQuery($sql);
		if(empty($row['templateId'])){
			return false;
		}
		$properties = array(
			'id' => $row['templateId'],
			'name' => $row['name'],
			'filename' => $row['filename'],	
			'spots' => $dRep->getSpotCollection(array('template' => $templateId)),
			'folder' => $row['resourceFolder'],
			'site' => $row['siteId']
			);
		$template = new Template();
		$template->setProperties($properties);
		$this->templates[$row['templateId']] = $template;
		return $template;		
	}
	public function saveTemplate($template){
		$sql = "INSERT INTO ink_templates (siteId, name, filename, resourceFolder) VALUES (?, ?, ?, ?);";
		$folder = $template->getResourcefolder();
		$folderId = (is_object($folder) && $folder->getId() != 'new') ? $template->getResourcefolder()->getId() : 0;
		$id = $this->insertValues($sql, array($template->getSite()->getId(), $template->getName(), $template->getFileName(), $folderId));
		foreach($template->getSpots() as $index => $spot){
			$spot = $this->saveSpotTemplate($id, $spot);
			$this->saveSpotConfig($spot, false);			
		}
		return $id;
	}
	public function updateTemplate($template){
		$oldTemplate = $this->getTemplate($template->getId());
		$sql = "UPDATE ink_templates SET name = ?, filename = ?, resourceFolder = ?, siteId = ? WHERE templateId = ?;";
		$folder = $template->getResourcefolder();
		$folderId = (is_object($folder) && $folder->getId() != 'new') ? $template->getResourcefolder()->getId() : 0;
		$foldervalues = array($template->getName(), $template->getFilename(), $folderId, $template->getSite()->getId(), $template->getId());
		$this->insertValues($sql, $foldervalues);
		foreach($template->getSpots() as $tplSpotId => $spot){
			if(is_int($tplSpotId)){
				$this->updateSpotTemplate($spot);
				$this->saveSpotConfig($spot, true);				
			}else{
				$spot = $this->saveSpotTemplate($template->getId(), $spot);
				$this->saveSpotConfig($spot, false);			
			}
		}
		return $template->getId();
	}
	public function deleteTemplate($tpl){
		$spots = $tpl->getSpots();
		foreach($spots as $tplSpotId => $spots){
			$sql = "DELETE FROM ink_templates_spotconfigs WHERE tplSpotId = ?;";
			$this->deleteValues($sql, array($tplSpotId));
		}
		$sql = "DELETE FROM ink_templates_spots WHERE templateId = ?;";
		$this->deleteValues($sql, array($tpl->getId()));
		$sql = "DELETE FROM ink_templates WHERE templateId = ?;";
		$this->deleteValues($sql, array($tpl->getId()));
	}
	private function saveSpotConfig($spot, $update = false){
		$configs = $spot->getConfigValues();
		if(count($configs) == 0){
			return;
		}
		foreach($configs as $config => $value){
			if(!empty($value)){
				$sql = "SELECT * FROM spotconfigs WHERE configIndex = ?";
				$values = $this->runSingleQuery($sql, array($config));
				$id = $values['configId'];
				
				if($update){
					$sqlUpdate = "UPDATE ink_templates_spotconfigs SET value = ? WHERE configId = ? AND tplSpotId = ?;";
					$this->insertValues($sqlUpdate, array($value, $id, $spot->getTplSpotId()));
				}else{
					$sqlInsert = "INSERT INTO ink_templates_spotconfigs (tplSpotId, configId, value) VALUES (?, ?, ?);";
					$this->insertValues($sqlInsert, array($spot->getTplSpotId(), $id, $value));
				}
			}
		}
	}
	private function saveSpotTemplate($templateId, $spot){
		$sql = "INSERT INTO ink_templates_spots (templateId, spotId, spotOrder) VALUES (?, ?, ?);";
		$values = array($templateId, $spot->getId(), $spot->order());
		$tplSpotId = $this->insertValues($sql, $values);
		$tplSpotIdProp = array('tplSpotId' => $tplSpotId);
		$spot->setProperties($tplSpotIdProp);
		return $spot;
	}
	private function updateSpotTemplate($spot){
		$sql = "UPDATE ink_templates_spots SET spotOrder = ? WHERE tplSpotId = ?;";
		return $this->insertValues($sql, array($spot->order(), $spot->getTplSpotId()));
	}
}

?>