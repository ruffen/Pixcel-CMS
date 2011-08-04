<?php
class MessageRepository extends MysqlDb{

	public function getMessage($where){
		$where = $this->sqlBuilder->createWhere($where, '', false);
		$sql = "SELECT * FROM ink_system_messages WHERE {$where};";
		$row = $this->runSingleQuery($sql);

		$message = new Message();
		$properties = array(
			'id' => $row['id'],
			'key' => $row['messagekey'],
			'description' => $row['description'],
			'type' => $row['msgType']
		);
		$message->setProperties($properties);
		return $message;
	}
	public function getMessageCollection($where){
	
	}
}
?>