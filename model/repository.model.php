<?php

class Repository{
	protected static $data;
	private static $childInstances = array();
	private $db;
	private $useORM = false;
	private $repositories = array();
	private $parsers;
	public function __construct(){
		$this->setMysqlDb();
	}
	private function setMysqlDb(){
		$db = new MysqlDb();
		$this->db = $db;	
	}
	/**
	 * This is method getObject
	 *
	 * @param string $className Name of class to get object of
	 * @return mixed This is the return value description
	 *
	 */
	private function getObject($className, $id, $session = false){
		if($this->useORM || $session){
			$object = $this->db->selectObject($className, $id);
			
			if(!$object instanceof $className){
				//if object not in session, try getting it from db.
				$method = 'get'.$className;
				$object = $this->$method($id);	
			}
			return $object;
		}
		//include the repository
		$this->loadRepository($className);
		$method = 'get'.$className;
		$object = $this->repositories[$className]->$method($id);
		
		return (is_object($object)) ? $object : $this->createObject($className);
	}
	/**
	 * This is method deleteObject
	 * @param mixed className
	 * @param mixed 
	 *
	 */
	private function deleteObject($className, $object, $revision, $session = false){
		if($this->useORM){
			//never implemented
			$this->db->deleteObjects();
		}
		$this->loadRepository($className);		
		$method = 'delete'.$className;
		$object = (is_object($object)) ? $object : $this->getObject($className, $id);
		return $this->repositories[$className]->$method($object, $revision);
	}
	
	/**
	 * This is method getObjects
	 *
	 * @param mixed $className This is a description
	 * @return mixed This is the return value description
	 *
	 */
	private function getObjects($className, $whereconditions, $order, $limit){
		if($this->useORM !== false){
			return $this->db->selectObjects($className, $whereconditions, $order, $limit);
		}
		$this->loadRepository($className);
		$method = 'get'.$className.'Collection';
		return $this->repositories[$className]->$method($whereconditions, $order, $limit);
	}
	private function changeRevision($className, $object, $revisionId){
		if($this->useORM !== false){
			throw new ImplementationException('Revision change in ORM not implemented');
		}
		$this->loadRepository($className);
		$method = 'changeRevision';

		return $this->repositories[$className]->$method($object, $revisionId);	
	}	
	private function loadRepository($className){
		if(!isset($this->repositories[$className])){
			include_once('model/repositories/'.strtolower($className).'.repository.php');
			$repName = $className.'Repository';
			$rep = new $repName();
			$this->repositories[$className] = $rep;		
		}	
	}
	/**
	 * Method createObject
	 *
	 * @param string $className Classname to create object of
	 * @return object returns object of type classname
	 *
	 */
	private function createObject($className){
		$object = new $className();
		$vals = array('id' => 'new');
		$object->setProperties($vals);
		return $object;
	}
	private function updateObject($className, $object, $fieldName, $fieldValue){
		if($this->useORM !== false){
			$result = $this->db->saveObject($object, $className);
			return $result;
		}else if($this->db instanceof SessionHandler){
			$result = $this->db->saveObject($object, $className);
			return $result;			
		}		
		$this->loadRepository($className);
		$method = 'update'.$className;
		$result = $this->repositories[$className]->$method($object, $fieldName, $fieldValue);
	}
	/**
	 * method saveobject
	 *
	 * @param object $className objet to be save
	 * @param bool $session indicates wheter we want it to be saved to db or just to store it in session
	 * @return object saved object with ID
	 *
	 */
	private function saveObject($object, $className){
		if($this->useORM !== false){
			$result = $this->db->saveObject($object, $className);
			return $result;
		}else if($this->db instanceof SessionHandler){
			$result = $this->db->saveObject($object, $className);
			return $result;			
		}
		$this->loadRepository($className);
		$method = ($object->getId() != '' && $object->getId() != 'new') ? 'update' : 'save';
		$method .= $className;
		$id = $this->repositories[$className]->$method($object);
		if($id != 0){ 
			$method = 'get'.$className;
			$object = $this->repositories[$className]->$method($id);
		}else if(!$object instanceof Page){
			throw new Exception('sqlinsert_error');	
		}
		return $object;
	}
	private function setMap($className){
		if(isset($this->parsers[$className])){
			$this->db->setXmlParser($this->parsers[$className]);
		}
		$path = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']);
		$file = strtolower($path.'model/datamaps/'.$className.'.map.xml');
		$xmlParser = new xmlDataParser($file);
		$xmlParser->my_xml2array();
		$this->parsers[$className] = $xmlParser;
		$this->db->setXmlparser($xmlParser);
	}
	
	/**
	 * This is method __call
	 *
	 * @return mixed This is the return value description
	 *
	 */
	public function __call($method_name, $arguments){		
		if($this->db instanceof SessionHandler){
			$this->setMysqlDb();
		}
		$prefix = substr($method_name, 0, 3);
		$suffix = substr($method_name, 3);
		if ($prefix === 'get') {
			if (substr($suffix, -10) === 'Collection') {
				$class = substr($suffix, 0, -10);
				//set datamap
				$this->setMap($class);
				if (!isset($arguments[0])) $arguments[0] = null;	//where conditions
				if (!isset($arguments[1])) $arguments[1] = null;	//ordering
				if (!isset($arguments[2])) $arguments[2] = null;	//limit
				if (!isset($arguments[3])) $arguments[3] = null;
				return $this->getObjects($class, $arguments[0], $arguments[1], $arguments[2], $arguments[3]);
			}else {
				//set datamap
				$this->setMap($suffix);
				if(isset($arguments[1]) && $arguments[1] === false){
					$oldDb = $this->db;
					$this->db = new SessionHandler();
					$object = $this->getObject($suffix, $arguments[0], true);
					$this->db = $oldDb;
					return $object;
				}
				return $this->getObject($suffix, $arguments[0]);
			}
		}elseif($prefix === 'new'){
			//set datamap
			$this->setMap($suffix);
			$object = $this->createObject($suffix);
			return $object;
		}elseif($prefix == 'sav'){
			$suffix = substr($method_name, 4);
			//set datamap
			$this->setMap($suffix);

			$suffix = substr($method_name, 4);
			if(isset($arguments[1]) && $arguments[1] === false){
				$oldDb = $this->db;
				$this->db = new SessionHandler();
				$return = $this->saveObject($arguments[0], $suffix);
				$this->db = $oldDb;
				return $return;
			}
			return $this->saveObject($arguments[0], $suffix);
				
		}elseif($prefix == 'upd'){
			$suffix = substr($method_name, 6);
			$this->setMap($suffix);
			if (!isset($arguments[0])) $arguments[0] = null;	//object
			if (!isset($arguments[1])) $arguments[1] = null;	//object fieldname
			if (!isset($arguments[2])) $arguments[2] = null;	//object value
			$result = $this->updateObject($suffix, $arguments[0], $arguments[1], $arguments[2]);
			return 	$result;		
		}elseif($prefix == 'del'){
			//set datamap
			$this->setMap($suffix);
			if (!isset($arguments[0])) $arguments[0] = null;	//object ID
			if (!isset($arguments[1])) $arguments[1] = null;	//object revision			
			$result = $this->deleteObject($suffix, $arguments[0], $arguments[1]);						
			return $result;
		}elseif($prefix == 'cha'){
			$prefix = substr($method_name, 0, 6);
			$suffix = substr($method_name, 6);
			$className = substr($suffix, 0, -8);
			return $this->changeRevision($className, $arguments[0], $arguments[1]);
		}
		throw new Exception('Un callable method '.$method_name);
	}	
}
?>