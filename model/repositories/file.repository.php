<?php
class FileRepository extends MysqlDb{
	private $fileCache = array();
	public function getFile($id){
		global $dRep;
		if(isset($this->fileCache[$id])){
			return $this->fileCache[$id];
		}
		$sql = "SELECT * FROM ink_files WHERE fileId = {$id};";
		$data = $this->runSingleQuery($sql);

		$file = new File();
		$properties = array(
			'id' => $data ['fileId'],
			'siteId' => $data ['siteId'],
			'filename' => $data ['filename'],
			'folderId' => $data ['folderId'],
			'size' => $data ['filesize'],
			'type' => $dRep->getFiletype($data ['filetypeId']),
			'timestamp' => $data['uploaded']
		);
		$file->setProperties($properties);
		$file = $this->getFiletext($file);
		$this->fileCache[$file->getId()] = $file;
		return $file;
	}
	public function getFileCollection($where){
		global $dRep;
		$where = $this->sqlBuilder->createWhere($where, '');
		$sql = "SELECT * FROM ink_files WHERE {$where};";
		$data = $this->runManyQuery($sql);
		$files = array();
		foreach($data as $index => $row){
			$file = new File();
			$properties = array(
				'id' => $row['fileId'],
				'siteId' => $row['siteId'],
				'filename' => $row['filename'],
				'folderId' => $row['folderId'],
				'size' => $row['filesize'],
				'type' => $dRep->getFiletype($row['filetypeId']),
				'timestamp' => $row['uploaded']
			);
			$file->setProperties($properties);
//			$file = $this->getFiletext($file);
			$this->fileCache[$file->getId()] = $file;
			$files[] = $file;
		}
		return $files;
	}
	public function saveFile($file){
		$customer = unserialize($_SESSION['customer']);
		$sql = "INSERT INTO ink_files 
					(siteId, customerId, folderId, filetypeId, filename, filesize, uploaded) 
				VALUES (?, ?, ?, ?, ?, ?, ?);";
		$values = array($customer->getId(),$customer->getSite()->getId(), $file->getFolder(), $file->getFiletype()->getId(), $file->getFilename(), $file->getSize(), time());
		$id = $this->insertValues($sql, $values);
		$idProp = array('id' => $id);
		$file->setProperties($idProp);
		$this->fileCache[$file->getId()] = $file;
		return $id;
	}
	public function deleteFile($file){
		$sql= "DELETE FROM ink_files WHERE fileId = ?";
		$this->deleteValues($sql, array($file->getId()));
	}
	private function getFiletext($file){
		$sql = "SELECT * FROM ink_files_languages WHERE fileID = '{$file->getId()}';";
		$data = $this->runManyQuery($sql);
		foreach($data as $index => $row){
			$file->setLang($row['languageId'], 'name', $row['filename']);				
		}
		return $file;
	}
}
?>