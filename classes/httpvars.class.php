<?php


/**
 * HttpVars performs cleanup on all get and post variables sent in order to prevent malicious attacks. Access methods are implemented to get variables
 */
class HttpVars{
	private $get;
	private $post;
	public function __construct($get, $post){
		$this->cleanBrowserVariables($get, $post);
	}
	/**
	/**
	 * This is method changeVariable
	 *
	 * @param string $varname Variable name to set
	 * @param mixed $var Value of variable to set in varchecker
	 * @return void 
	 *
	 */
	public function changeVariable($varname, $var){
		$this->post[$varname] = $var;
	}
	/**
	 * @method ReloadVariables reloads get and post variables into varchecker and cleans them
	 * @return void
	 **/
	public function ReloadVariables(){
		$this->cleanBrowserVariables($_GET, $_POST);
	}
	/**
	* Method cleanBrowserVariables cleans variables that is sent by the browser. Cleans for malicious input.
	*
	* @param array $get Browser variables that are sent through method GET
	* @param array $post Browser variables that are sent hrough method POST
	* @return array merged and cleaned array of all variables
	*
	*/
	private function cleanBrowserVariables($get, $post){
		$this->get = $get;
		$this->post = $post;
	}
	
	/**
	/**
	 * This is method getValues
	 *
	 * @return array associative array containing all post and get keys and values
	 *
	 */
	public function getSentVariables($removeRouteInformation = true){
		$vars = array_merge($this->post, $this->get); 
		if($removeRouteInformation){
			if(isset($vars['rt'])){
				unset($vars['rt']);
			}	
		}
		return $vars;
	}
	
	/**
	 * getValue returns a specified get or post variable if it excists
	 *
	 * @param string $variableName Name of variable to get
	 * @return string Value of the variable asked for
	 *
	 */
	public function getValue($variableName){
		if(!isset($this->get[$variableName]) && !isset($this->post[$variableName])){
			throw new DataException("nohttpvar".$variableName);
		}
		if((isset($this->get[$variableName]) && !empty($this->get[$variableName])) || (isset($this->get[$variableName]) && $this->get[$variableName] == 0)){
			return $this->get[$variableName];
		}elseif((isset($this->post[$variableName]) && !empty($this->post[$variableName])) || (isset($this->post[$variableName]) && $this->post[$variableName] == "0")){
			return $this->post[$variableName];
		}
		return false;
	}
}
