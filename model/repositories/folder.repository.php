<?php
class FolderRepository extends MysqlDb{
	private $folders;
	public function getFolderCollection($where){
		global $dRep;
		$where = $this->sqlBuilder->createWhere($where, '');
		$sql = "SELECT * FROM ink_folders WHERE {$where} ORDER BY folderOrder;";
		$data = $this->runManyQuery($sql);
		$folders = array();
		foreach($data as $index => $row){
			$folder = new Folder();
			$properties = array(
				'id' => $row['folderId'],
				'name' => $row['folderName'],
				'site' => $row['siteId'],
				'parent' => $dRep->getFolder($row['parentId']),
				'files' => $dRep->getFileCollection(array('folder' => $row['folderId'])),
				'order' => $row['folderOrder']
				);	
			$folder->setProperties($properties);
			$this->folders[$folder->getId()] = $folder;
			$folders[] = $folder;
		}
		return $folders;
	}
	public function deleteFolder($folder){
		global $dRep;
		foreach($folder->getFiles() as $index => $file){
			$dRep->delFile($file);
		}
		$sql = "DELETE FROM ink_folders WHERE folderId = ?;";
		$result = $this->deleteValues($sql, array($folder->getId()));
		$sql = "UPDATE ink_folders SET folderOrder = folderOrder - 1 WHERE folderId IN (SELECT folderId FROM (
					SELECT folderId FROM ink_folders WHERE siteId = ? AND parentId = ? AND folderOrder > ?
				) AS x)";
		$parentId = ($folder->getParent() instanceof Folder) ? $folder->getParent()->getId() : 0;
		$siteid = (is_object($folder->getSite())) ? $folder->getSite()->getId() : $folder->getSite();
		$this->updateRow($sql, array($siteid, $parentId, $folder->getOrder()));
		if(isset($this->folders[$folder->getId()])){
			unset($this->folders[$folder->getId()]);	
		}
		return $result;
	}
	public function getFolder($id){
		if($id == 0){
			return 0;
		}
		if(isset($this->folders[$id])){
			return $this->folders[$id];
		}
		global $dRep;
		$sql = "SELECT * FROM ink_folders WHERE folderId = '{$id}';";
		$row = $this->runSingleQuery($sql);
		if(!isset($row['folderId'])){
			return false;	
		}
		$folder = new Folder();
		$properties = array(
			'id' => $row['folderId'],
			'name' => $row['folderName'],
			'site' => $row['siteId'],
			'parent' => $dRep->getFolder($row['parentId']),
			'files' => $dRep->getFileCollection(array('folder' => $row['folderId'])),
			'order' => $row['folderOrder']
			);	
		$folder->setProperties($properties);
		return $folder;
	}
	public function saveFolder($folder){
		$site = unserialize($_SESSION['site']);
		$sql = "INSERT INTO ink_folders (siteId, parentId, folderName, folderOrder) VALUES (?, ? , ?, 0);";
		$parent = $folder->getParent();
		$parentId = (is_object($parent)) ? $parent->getId() : 0;	
		$values = array($site->getId(), $parentId, $folder->getName());
		return $this->insertValues($sql, $values);
		
	}
	public function updateFolder($folder){
		$sql = "UPDATE ink_folders SET folderName = ?, folderOrder = ?, parentId = ? WHERE folderId = ?;";
		$parentId = ($folder->getParent() instanceof Folder) ? $folder->getParent()->getId() : 0;
		$this->updateRow($sql, array($folder->getName(), $folder->getOrder(), $parentId, $folder->getId()));
		$this->folders[$folder->getId()] = $folder;
		return $folder->getId();
	}
}
?>