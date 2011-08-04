<?php

class Loghandler{

	private $registry;
	private $logableActions;
	public function __construct($registry){
		global $logableactions;
		$this->registry = $registry;
		$this->logableactions = $logableactions;
	}
	public function createactionlog($controller, $action, $entity = 'N/A'){
		if(!$this->islogable($controller, $action)){
			return false;
		}
		$id = md5(uniqid(rand(), true));
		$text = $this->logableactions[$controller][$action] ;
		$values = array(
						$id,
						$text, 
						$this->registry->access->getCurrentUserProperty('username'),
						$entity
						);
		$this->registry->db->insertRow('actionLog', $values, $this->getActionLogFields());
	}
	public function createerrorlog($error){
		
		
	}
	public function getActionLog(){
		return $this->registry->db->selectItems('actionLog', array(), 'ORDER BY logTime DESC LIMIT 100');
	}
	private function islogable($controller, $action){
		foreach($this->logableactions[$controller] as $logAction => $logtext){
			if($action == $logAction){
				return true;			
			}
		}
		return false;
	}
	private function getActionLogFields(){
		return array('logId', 'logText', 'logUser', 'logEntity');
	}
}
?>
