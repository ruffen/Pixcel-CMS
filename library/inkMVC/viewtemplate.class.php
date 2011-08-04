<?php

Class ViewTemplate {

	/*
	 * @the registry
	 * @access private
	 */
	private $registry;

	/*
	 * @Variables array
	 * @access private
	 */
	private $vars = array();

	/**
	 *
	 * @constructor
	 *
	 * @access public
	 *
	 * @return void
	 *
	 */
	public function __construct() {
	}


	public function __set($varname, $value) {
		if (isset($this->vars[$varname])){
			throw new Exception('Unable to set var `' . $varname . '. Var already set');
		}
		$this->vars[$varname] = $value;
	}

	public function show($module, $name) {
		$path = __SITE_PATH . '/view/'.$module.'/'.$name.'.'.$module.'.php';

		if (file_exists($path) == false){
			throw new Exception('Template not found in '. $path);
			return false;
		}

		// Load variables
		foreach ($this->vars as $key => $value){
			$$key = $value;
		}
		
		include ($path);               
	}
}

?>
