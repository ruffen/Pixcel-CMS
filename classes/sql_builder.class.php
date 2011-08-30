<?php
class SQL_builder{

	var $xmlParser;
	public function __construct(){
	}
	public function buildSelectQuery($tablename, $where, $xmlParser){
		$this->xmlParser = $xmlParser;
		$sql = 'SELECT * FROM '.$tablename.' A ';
		$join = false;
		$alphabet = array('A', 'B', 'C', 'D', 'E');
		$lastLetter = 'A';
		if(isset($where['join'])){
			foreach($where as $index => $value){
				if($index == 'join'){	
					$table2 = explode('.', $where['join']);
					$joins = explode(',', $table2[0]);
					$clauses = explode(',', $table2[1]);
					foreach($joins as $index => $join){
						$sql .= 'INNER JOIN '.$join.' '.$alphabet[$index + 1].' ON ('.$alphabet[$index].'.'.$clauses[$index].' = '.$alphabet[$index + 1].'.'.$clauses[$index].') ';
						$lastLetter = $alphabet[$index + 1];
					}
				}else{
					$where = array($index => $value);					
				}
			}
			$join = true;
		}
		$sql .= 'WHERE ';
		$count = 1;
		$max = count($where);
		foreach($where as $condition => $value){
			if(!$join && !isset($this->xmlParser->classTableMap[$condition])){
				throw new DataException('You have made a where condition with data not available in the db for this class');
			}
			$condition = (!$join) ? $this->xmlParser->classTableMap[$condition] : $condition;
			$letter = ($join) ? $lastLetter.'.' : 'A.';
			$sql .= $letter.$condition." = '".$value."'";
			$sql .= ($count != $max) ? ' AND ' : '';
			$count++;
		}
		$sql .= ';';

		return $sql;
	}
	public function buildInsertQuery($tablename, $values, $xmlParser){
		
	}
	public function createWhere($where, $tablename = '', $translate = true){
		if($translate){
			$where = $this->translateWhere($where, $tablename);	
		}
		$count = 1;
		$whereString = '';
		foreach($where as $key => $value){
			$whereString .= $key." = '".$value."'";
			$whereString .= ($count != count($where)) ? ' AND ' : '';
			$count++;
		}
		return $whereString;
	}
	private function translateWhere(array$where, $table = ''){
		//may want to use the xml files here, but for now use manual translation
		$newWhere = array();
		$noId = array('published', 'resourcefolder', 'cmsIndex');
		$count = 0;
		foreach($where as $key => $value){
			if(is_array($table) && isset($table[0])){
				$key = (isset($table[$count])) ? $table[$count].'.'.$key : $table[0].'.'.$key;
			}else if(stripos($key, 'id') === false && !in_array($key, $noId)){
				$key = $key.'Id';
			}else{
				$key = ($table != '') ? $table.'.'.$key : $key;			
			}
			$newWhere[$key] = $value;
			$count++;
		}
		return $newWhere;
	}
}
