<?php
abstract class BaseController 
{
	/*
	 * @registry object
	 */
	protected $dRep;
	protected $template;
	protected $variables;
	protected $INK_User;
	
	public function __construct($repository) {
		$this->dRep = $repository;
		
	}
	public function setUser($user){
		$this->INK_User = $user;
	}
	/**
	 * This is method getControllerName
	 * 
	 * @return Controllername
	 * 
	 */
	public function GetName(){
		return str_replace('Controller', '', get_class($this));
	}
	
	/**
	 * This is method setTemplate
	 *
	 * @param Template $template Instance of Class template, holds the template definition of this controllers associated view
	 * @return void
	 *
	 */
	public function SetTemplate($template){
		$this->template = $template;
	}
	
	/**
	 * This is method getTemplate
	 *
	 * @return Template $template
	 *
	 */
	public function GetTemplate(){
		return $this->template;	
	}
	/**
	 * @all controllers must contain an index method
	 */
	abstract function index();
	
	
	
	/**
	 * This is method convertMysqlTimestampToEurostyleTimestamp
	 *
	 * @param string $timestamp Timestamp return from MySQL database
	 * @param bool $showTextDay Boolean value to make method return day as text
	 * @param bool $showSeconds Boolean value to make method return seconds
	 * @return string Datetime string formated Euro style
	 *
	 */
	protected function convertMysqlTimestampToEurostyleTimestamp($timestamp, $showTextDay = true, $showSeconds = true){
		$phptime = strtotime($timestamp);
		$format = '';
		if($showTextDay){
			$format .= 'l ';
		}
		$format .= 'd-m-Y';
		if($showSeconds){
			$format .= ' H:i:s';
		}
		return date($format, $phptime);
	}
	/**
	 * This is method sortFolderlistarray
	 * sorts a raw array into a 2D array with files and directories as indexes.
	 * sorts in alphabetical order
	 **/
	protected function sortFolderlistarray($rawList){
		$splitted = $this->splitFilesAndFolders($rawList);
		$splitted['directory'] = $this->bubblesort($splitted['directory']);
		$splitted['file'] = $this->bubblesort($splitted['file']);
		return $splitted;
			
	}
	/* The bubble sort method.  If you don't know how it works it's very
	* simple, values are switched one at a time for each element. */

	protected function bubblesort(array$array){
		$array_size = count($array);
		for($x = 0; $x < $array_size; $x++) {
			for($y = 0; $y < $array_size; $y++) {
				if($array[$x] < $array[$y]) {
					$hold = $array[$x];
					$array[$x] = $array[$y];
					$array[$y] = $hold;
				}
			}
		}
		return $array;
	}
	private function splitFilesAndFolders($array){
		$files = array();
		$folders = array();
		foreach($array as $file){
			if($this->isFolder($file)){
				$folders[] = $file;
			}else{
				$files[] = $file;
			}
		}
		return array('directory' => $folders, 'file' => $files);
	}
	private function isFolder($name){
		if(strpos($name, '.') === false){
			return true;
		}else{
			return false;
		}
	}

}
?>
