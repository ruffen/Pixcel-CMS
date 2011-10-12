<?php
class Router{
	private $registry;
	private $route;
	private $controller;
	private $action;
	private $view;
	private $ajax;
	
	public function __construct($route){
		global $registry;
		$this->registry = $registry;
		$route = trim(trim($route, '/'));
		$this->route = explode('/', $route);
		$this->setAjax();
	}
	public function getControllername(){
		if(count($this->route) > 0){
			return $this->route[0];
		}else{
			return '';
		}
	}
	private function setAjax(){
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
			$this->ajax = true;
		}else{
			$this->ajax = false;
		}
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
		if($this->ajax) {
			if(is_file('view/'.$this->controller->getName().'/'.$this->resolveAction().'.'.$this->controller->getName().'.php')){
				$this->view = $this->getView();
			}else{
				$this->view = $this->runAction();	
			}
		}else{
			$this->view = $this->getMasterview($this->controller, $hasUser);
		}
	}
	public function printHtml($hasuser){
		if(is_object($this->view) && !$this->ajax){
			$this->view->show('masterpage', ($hasuser) ? 'ink02' : 'ink02nouser');
		}else if(is_object($this->view) && $this->ajax){
			$this->view->show($this->controller->getName(), $this->resolveAction());		
		}elseif(strpos($_SERVER["HTTP_ACCEPT"], 'json') !== false){
			echo json_encode($this->view);
		}else{
			echo trim($this->view);
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
	private function getMasterview($controller, $hasUser){
		global $INK_User;
		$controllerName = $controller->GetName();
		$javaScripts = new JS($controllerName);
		$cssfiles = new CSS($controllerName);

		//set master statically, we may want to change this to a user controller thing later.
		//May be usefull for access controll as well.
		$masterview = new ViewTemplate();
		if($hasUser){
			$modules = $INK_User->getModules();
			$customermodules = from('$module')->in($modules)->where('$module => $module->SystemStatus() == 0')->select('$module');
			$systemmodules = from('$module')->in($modules)->where('$module => $module->SystemStatus() == 1')->select('$module');
			$moduleList = new ModuleList($customermodules, $controllerName, array('id' => 'navigation'));		
			$masterview->SysModules = $systemmodules;
			$masterview->sites = $INK_User->getRole()->getSites();
			$masterview->INK_User = $INK_User;
			$masterview->modules = $moduleList->getList();
		}
		$masterview->javascripts = $javaScripts->getScripts();
		$masterview->cssfiles = $cssfiles->getFiles();
		$masterview->hasUser = $hasUser;

		//show the view and return the result as a string
		$viewview = $this->getView();
		ob_start();
		try{
			$viewview->show($this->controller->getName(), $this->resolveAction());
		}catch(Exception $e){
			throw $e;
		}
		$masterview->maincontent = ob_get_clean();
		return $masterview;
	}
	private function getView(){

		//create a new view for the view
		$viewview = new ViewTemplate();

		$this->controller->setTemplate($viewview);
		
		$viewResult = $this->runAction();
				
		//get view back after action has done what it needs
		$viewview = $this->controller->getTemplate();
		return $viewview;		
	}
}
?>