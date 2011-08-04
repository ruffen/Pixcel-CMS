<?php
class IOException extends ErrorException {}
class AccessException extends ErrorException{}
class PathException extends ErrorException{}
class ControllerException extends ErrorException{}
class PropertyException extends ErrorException{}
class RouteException extends ErrorException{}
class ModuleException extends ErrorException{}
class ChildError extends ErrorException{}
class SharedMemoryException extends ErrorException{}
class FTPException extends ErrorException{}
class TemplateException extends PathException{}
class LockException extends ErrorException{}
class DataException extends ErrorException{
	private $elements;
	public function __construct($message = "", $elements = array(), $code = 0, $previous = null){
		parent::__construct($message, $code, $previous);
		$this->elements = $elements;
	}
	public function setElements($elements){
		$this->elements = $elements;
	}
	public function getElements(){
		return $this->elements;
	}
}
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
	if(stristr($errstr, 'Non-static method') !== false && stristr($errfile, 'router.class.php') !== false){
		//is_callable tries to call the method statically, if this error happens with this function we want to ignore it
		return true;	
	}
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
if($_SERVER['SERVER_NAME'] != 'localhost'){
	date_default_timezone_set('Australia/ACT');
}

/*** auto load model classes ***/
function __autoload($class_name){
	$directories = array(
			'/classes/',
			'/library/',
			'/library/inkMVC/',
			'/model/',
			'/model/assets/',
			'/controllers/',
			'/spots/',
			'/spots/classes/'
			);
	foreach($directories as $directory){
		$filename = ($directory != '/model/assets/') ? strtolower($class_name).'.class.php' : strtolower($class_name).'.asset.php';
		$filename = (strpos($directory, 'spot') !== false) ? strtolower($class_name).'.spot.php' : $filename;
		$filename = (strpos($directory, 'controllers') !== false) ? str_replace('controller', '', strtolower($class_name)).'/'.str_replace('controller', '', strtolower($class_name)).'.controller.php' : $filename;

		$site_path = str_replace('library', '', realpath(dirname(__FILE__)));
		
		$file = $site_path . $directory . $filename;
		if(file_exists($file)){
			include_once($file);
			return;	
		}
	}
	throw new PathException('Not able to autoload class: '.$class_name);
}
?>