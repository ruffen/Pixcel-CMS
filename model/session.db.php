<?php
class SessionHandler implements Storage{
	
	public function __construct(){
	}
	public function selectObject($className, $selectedId){
		$selectedId = (!empty($selectedId) && $selectedId != '') ? $selectedId : 'new';
		if(isset($_SESSION[$className][$selectedId])){
			return unserialize($_SESSION[$className][$selectedId]);	
		}
		return false;
	}
	public function selectObjects($className, $where, $limit){}
	
	public function saveObject($object, $className){	
		$id = ($object->getId() != '') ? $object->getId() : 'new';
		$_SESSION[$className][$id] = serialize($object);
		return true;
	}
	public function deleteObject($object, $className){
		$sessionObject = unserialize($_SESSION[$className]);
		if($sessionObject->getId() == $object->getId()){
			unset($_SESSION[$className]);
			return true;	
		}
		return false;
	}
}
?>