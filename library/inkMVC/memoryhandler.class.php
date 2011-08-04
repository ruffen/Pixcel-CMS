<?php
class MemoryHandler{
	private static $instance;
	private $segment;
	private $byteswritten = 1;
	private function __construct(){
		$this->createMemory();	
	}
	public function __descruct(){
		fclose($this->segment);
	}
	public static function getInstance(){
		if(!self::$instance){
			self::$instance = new MemoryHandler();
		}
		if(empty(self::$instance->segment)){
			self::$instance->createMemory();	
		}
		return self::$instance;
	}
	private function createMemory(){
		global $smKey , $smSize , $smPermissions;
		$directory = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']);
		if(empty($this->segment)){
			$this->segment = fopen($directory.'cache/locks.txt', 'c+');
		}
	}
	protected function readMemory($key = false){
		$data = fread($this->segment, 8192);
		if(strlen(trim($data)) == 0 && $key !== false){
			return array($key => array());
		}
		$data = unserialize(trim($data));
		if($key !== false){
			return $data[$key];			
		}
		return (is_array($data)) ? $data : array();
	}
	protected function writeMemory($key, $newData){
		$fileData = $this->readMemory();
		$fileData[$key] = $newData;
		//resize the file to 0
		ftruncate($this->segment, 0);
		if(fwrite($this->segment, trim(trim(serialize($fileData)), "\n")) === false){
			fclose($this->segment);
			unset($this->segment);
			throw new SharedMemoryException('Unable write to file');
		}
		fclose($this->segment);
		unset($this->segment);
		return true;
	}
	protected function deleteMemory($key){
		$data = $this->readMemory();
		if(isset($data[$key])){
			unset($data[$key]);		
		}
		if(fwrite($this->segment, serialize($data)) === false){
			fclose($this->segment);
			throw new SharedMemoryException('Unable write to file in delete memory');
		}
		return true;
	}
	public function lockPage($page, $user){
		if($userWithLock = $this->pageLocked($page)){
			return $userWithLock;
		}
		$locks = $this->readMemory('pages');
		foreach($locks as $pageId => $userId){
			//one user can only have one lock
			if($userId == $user->getId()){
				unset($locks[$pageId]);
			}
		}
		$locks = array($page->getId() => $user->getId());
		return $this->writeMemory('pages', $locks);
	}
	public function unlockPage($page){
		$locks = $this->readMemory('pages');
		if(!is_array($locks)){
			$locks = array();
		}
		if(isset($locks[$page->getId()])){
			unset($locks[$page->getId()]);
		}
		return $this->writeMemory('pages', $locks);
	}
	public function pageLocked($page){
		$locks = $this->readMemory('pages');
		if(!is_array($locks)){
			$locks = array();
		}
		if(isset($locks[$page->getId()])){
			return $locks[$page->getId()];
		}
		return false;
	}
}
?>