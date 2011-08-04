<?php
class Router{
	private $registry;
	private $route;
	private $controller;
	private $action;
	private $template;
	
	public function __construct($route){
		global $registry;
		
		$this->registry = $registry;
		if(empty($route)){
			$route = $this->findIndex();
		}
		$this->route = explode('/', trim($route, '/'));
	}
	private function findIndex(){
		global $INK_User;
		if(!is_object($INK_User)){
			return '';	
		}
		$modules = $INK_User->getModules();
		//need to get the site
		foreach($modules as $index => $module){
			if($module->isIndex()){
				return str_replace('/?rt=', '', $module->getIndexRoute());
			}	
		}
		throw new RouteException('Could not find a route');
	}
	public function LoadController($repository){
		$controllerName = $this->resolveController($repository).'Controller';
		$actionName = $this->resolveAction();
		if (is_callable(array($controllerName, $actionName)) === false){
			throw new ControllerException('Action '.$actionName.' is not callable on controller '.$controllerName);	
		}
		//create an instance of the controller
		$this->controller = new $controllerName($repository);
		return $this->controller;
	}
	private function runAction(){
		//get controller and action
		$controller = $this->controller;
		$action = $this->resolveAction();
		//run the action method inside controller
		return $controller->$action();	
	}
	public function RunController($hasUser){
		/* AJAX check  */
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$this->template = $this->runAction();
		}else{
			$this->template = $this->getMasterTemplate($this->controller, $hasUser);
		}
	}
	public function printHtml(){
		if(is_object($this->template)){
			$this->template->show('masterpage', 'ink02');
		}elseif(isset($this->template)){
			echo trim($this->template);
		}
	}
	private function resolveController($repository){
		$controller = $this->route[0];
		if(empty($controller)){
			$module = $repository->getModule('index');
			$controller = $module->getRoute();
		}
		
		if($controller == 'logout'){
			throw new AccessException('logout');
		}
		$file = 'controllers/'.$controller.'.controller.php';
		if(is_file($file) === false){
			throw new PathException('File: '.$file.' does not exist');	
		}
		//include the controller
		include_once($file);
		return $controller;
	}
	private function resolveAction(){
		if(count($this->route) > 1){
			return $this->route[1];			
		}
		//index is the standard action
		return 'index';
	}
	private function getMasterTemplate($controller, $hasUser){
		global $INK_User;
		$controllerName = $controller->GetName();
		$javaScripts = new JS($controllerName);
		$cssfiles = new CSS($controllerName);

		//set master statically, we may want to change this to a user controller thing later.
		//May be usefull for access controll as well.
		
		$masterTemplateName = 'ink02';
		$masterTemplate = new ViewTemplate();
		if($hasUser){
			$modules = $INK_User->getModules();
			$sitemodules = from('$module')->in($modules)->where('$module => $module->SystemStatus() == 0')->select('$module');
			$systemmodules = from('$module')->in($modules)->where('$module => $module->SystemStatus() == 1')->select('$module');
			$moduleList = new ModuleList($sitemodules, $controllerName, array('id' => 'navigation'));		
			$masterTemplate->SysModules = $systemmodules;
			$masterTemplate->sites = $INK_User->getRole()->getSites();
			$masterTemplate->INK_User = $INK_User;
			$masterTemplate->modules = $moduleList->getList();
		}
		$masterTemplate->javascripts = $javaScripts->getScripts();
		$masterTemplate->cssfiles = $cssfiles->getFiles();
		$masterTemplate->maincontent = $this->getView();
		$masterTemplate->hasUser = $hasUser;
				
		return $masterTemplate;
	}
	private function getView(){

		//create a new template for the view
		$viewTemplate = new ViewTemplate();

		$this->controller->setTemplate($viewTemplate);
		
		$viewResult = $this->runAction();
				
		//get template back after action has done what it needs
		$viewTemplate = $this->controller->getTemplate();
		
		//show the view and return the result as a string
		ob_start();
		try{
			$viewTemplate->show($this->controller->getName(), $this->resolveAction());
		}catch(Exception $e){
			throw $e;
		}
		return ob_get_clean();
	}
}
?>