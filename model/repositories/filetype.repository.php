<?php
class FiletypeRepository extends MysqlDb{
	
	public function getFiletype($id){
		if(!is_numeric($id)){
			return $this->searchFiletype($id);	
		}
		$sql = "SELECT * FROM ink_filetypes WHERE filetypeId = '{$id}';";
		$row = $this->runSingleQuery($sql);
		if(!isset($row['filetypeId'])){
			return false;	
		}
		$type = new Filetype();
		$properties = array(
			'id' => $row['filetypeId'],
			'name' => $row['fileType'],
			'thumbnail' => $row['thumbname'],
			'description' => $row['description']
			);
		$type->setProperties($properties);
		return $type;
	}
	public function searchFiletype($extension){
		$sql = "SELECT * FROM ink_filetypes WHERE extension LIKE '%{$extension}%';";
		$row = $this->runSingleQuery($sql);
		if(!isset($row['filetypeId'])){
			return false;	
		}
		$type = new Filetype();
		$properties = array(
			'id' => $row['filetypeId'],
			'name' => $row['fileType'],
			'thumbnail' => $row['thumbname'],
			'description' => $row['description']
		);
		$type->setProperties($properties);
		return $type;
	}	
	
}
