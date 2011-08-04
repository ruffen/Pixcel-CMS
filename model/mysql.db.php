<?php
class MysqlDb implements Storage{

	protected $connection;
	protected $xmlParser;
	protected $sqlBuilder;

	private $tablename;
	private $objectVars;
	public function __construct(){
		global $db_details, $live_db_details;
		if(strpos($_SERVER['DOCUMENT_ROOT'], 'wamp')){
			$this->connection = $this->connect($db_details);
		}else{
			$this->connection = $this->connect($live_db_details);			
		}
		$this->sqlBuilder = new SQL_builder();
	}
	public function setXmlparser($xmlParser){
		$this->xmlParser = $xmlParser;
	}
	private function connect($connData){
		/* Connect to an ODBC database using driver invocation */
		$dsn = 'mysql:dbname='.$connData['dbName'].';host='.$connData['dbHost'];
		$user = $connData['dbUser'];
		$password = $connData['dbPassword'];
		//if an error occurs, use our error class.		
		try{
			$dbh = new PDO($dsn, $user, $password);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch (PDOException $e){
			throw new DataException('Connection failed'.$e->getMessage());
		}
		return $dbh;
	}
	
	public function selectObject($className, $selectedId){
		$where = array($this->xmlParser->pkClass => $selectedId);
		$row = $this->getValue($this->xmlParser->tableName, $where);
		$object = new $className();
		$object->setProperties($this->populateData($row));
		$object->setProperties($this->getArraysData($row));
		$object->setProperties($this->getObjectData($row));
		return $object;
	}
	public function selectObjects($className, $where, $limit){
		$className = $this->xmlParser->className;
		$data = $this->getValues($this->xmlParser->tableName, $where);
		
		$rows = array();
		foreach($data as $row){
			$object = new $className();
			$object->setProperties($this->populateData($row));
			$object->setProperties($this->getArraysData($row));
			$object->setProperties($this->getObjectData($row));
			$rows[] = $object;
		}
		return $rows;
	}
	public function deleteObject($object, $className){}
	private function getObjectData($row){
		global $dRep;
		$values = array();

		foreach($this->xmlParser->classes as $index => $arrayInfo){
			//create a new repository, so we keep the internal values of the normal repository
			$repository = new Repository();
			//find the right information to send on
			if(isset($row[$arrayInfo['pk']]) && !isset($arrayInfo['join'])){
				$selectedId = $row[$arrayInfo['pk']];
				if($arrayInfo['relation'] == '1'){
					$method = 'get'.$arrayInfo['object'];
				}else if($arrayInfo['relation'] == 'n'){
					$method = 'get'.$arrayInfo['object'].'Collection';				
				}
				$object = $repository->$method($selectedId);
				$values[$arrayInfo['name']] = $object;					
			}else if(isset($arrayInfo['join'])){
				$where = array('join' => $arrayInfo['join'].'.'.$arrayInfo['pk'], $arrayInfo['fk'] => $row[$arrayInfo['fk']]);
				$method = 'get'.$arrayInfo['name'].'Collection';
				$objects = $repository->$method($where);
				$values[$arrayInfo['name']] = $objects;	
			}
		}
		return $values;
	}
	private function getArraysData($row){
		$vars = array();
		foreach($this->xmlParser->arrays as $index => $arrayInfo){
			//we have a foreign key, so we can build a where clause
			if(isset($row[$arrayInfo['fk']])){
				$condition = $this->xmlParser->tableClassMap[$arrayInfo['fk']];
				$value = $row[$arrayInfo['fk']];
				$where = array($condition => $value);
				$data = $this->getValues($arrayInfo['tablename'], $where);
				$tableArrayMap = array_combine($arrayInfo['tablevars'], $arrayInfo['classvars']);
				$lang = array();
				foreach($data as $index => $row){
					$values = array();
					foreach($row as $key => $value){
						if(isset($tableArrayMap[$key])){
							$values[$tableArrayMap[$key]] = $value;	
						}
					}
					$lang[$row[$arrayInfo['pk']]] = $values;
				}
				$vars[$arrayInfo['name']] = $lang;
			}
		}
		return $vars;
	}
	private function populateData($data){
		$values = array();
		foreach($data as $key => $value){
			if(isset($this->xmlParser->tableClassMap[$key])){
				$key = $this->xmlParser->tableClassMap[$key];
				$values[$key] = $value;
			}
		}
		return $values;
	}
	/**
	 * This is method getValues
	 *
	 * @param xmlObject $objectInfo class -> sql mapping
	 * @param array $where information about selectors
	 * @return array resultset. Will use column name as indexes
	 */
	protected function getValues($tablename, $where){
		$sql = $this->sqlBuilder->buildSelectQuery($tablename, $where, $this->xmlParser);
		return $this->runQuery($sql);
	}
	protected function runManyQuery($query, array$values = array()){
		$statement = $this->connection->prepare($query);
		if($statement->execute($values) !== true){
			throw new DataException('Could not run query.'.$query);
		}
		return $statement->fetchAll();		
	}
	
	/**
	 * Method getValue returns one set of results
	 *
	 * @param Object $objectInfo Mapping between sql and classes
	 * @param array $where selector information
	 * @return array resultset uses column as indexes
	 */
	protected function getValue($tablename, $where){
		$sql = $this->sqlBuilder->buildSelectQuery($tablename, $where, $this->xmlParser);
		return $this->runSingleQuery($sql);
	}
	protected function runSingleQuery($query, array$values = array()){
		try{
			$statement = $this->connection->prepare($query);
			$statement->execute($values);
			return $statement->fetch();
		}catch(Exception $e){
			throw $e;
		}
	}
	protected function updateRow($query, array$values = array()){
		$statement = $this->connection->prepare($query);
		$statement->execute($values);
		return true;	
	}
	
	/**
	 * This is method insertValues
	 *
	 * @param string $sql MySQL query
	 * @param arrray $values values to be inserted into query
	 * @return int ID of the inserted row
	 *
	 */
	protected function insertValues($sql, array$values = array()){
		$statement = $this->connection->prepare($sql);
		$statement->execute($values);
		return $this->connection->lastInsertId();
	}
	
	/**
	 * This is method deleteValues
	 *
	 * @param string $sql MySQL query
	 * @return int Number of rows deleted
	 *
	 */
	protected function deleteValues($sql, array$values = array()){
		$statement = $this->connection->prepare($sql);
		$statement->execute($values);
		return $statement->rowCount();	
	}
}

?>