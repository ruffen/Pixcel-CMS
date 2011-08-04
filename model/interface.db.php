<?php
interface Storage{
	public function selectObject($className, $selectedId);
	public function selectObjects($className, $where, $limit);
	public function deleteObject($object, $className);
	
}
?>