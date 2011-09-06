<?php
ini_set('display_errors', "1");
ini_set('error_reporting', E_ALL);
$error = '';
/*** include the init.php file ***/
include_once('library/init.php');
$route = '';
$hasUser = true;
try{
	try{
		$route = $varChecker->getValue('rt');
		$router = new Router(trim($route,'/'));
	}catch(DataException $e){
		$module = $dRep->getModule('index');
	}
	/*** find the module we are looking for ***/
	
	/*** check if this is a customer **/
	/*** redirect to login if we dont have customer and are not trying to access a module where anon access is allowed **/
	$fido = new Guarddog();
	try{
		$customer = $fido->CheckCustomer();
	}catch(DataException $e){
		if($router->getControllername() != ''){
			$module = $dRep->getModule($router->getControllername());
			if(!$module->AllowAnonomousAccess()){
				throw new CustomerException('wrongcustomer');
			}
			$hasUser = false;
		}else{
			throw new CustomerException('nocustomer');
		}
	}
	/*** check if we have a user **/
	if(!isset($module) || (isset($module) && !$module->AllowAnonomousAccess())){
		$INK_User = $fido->CheckUser();
		try{
			$fido->ResolveUserSite();
		}
		catch(SiteException $e)
		{
			//need to find module with no site
			$module = $dRep->getModule(array('cmsIndex' => 2));
		}
		//we have user, check if we have a site, if not, redirect
	}
	if(isset($module)){
		if(!isset($router) || strpos(strtolower($router->getControllername()), strtolower($module->getRoute())) === false){
			//make sure we can access actions on the allowed module by not defaulting to index
			$router = new Router($module->getRoute());
		}	
	}

	$controller = $router->LoadController($dRep);
	if($hasUser){
		$controller->setUser($INK_User);
	}
	
	/*** run the controller ***/
	$router->RunController($hasUser);
	//print out the page
	$router->printHtml($hasUser);
}catch(AccessException $e){
	$message['css'] = 'hidden';
	$message['text'] = '';
	$message['icon'] = '';
	if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false){
		try{
			if($e->getMessage() == 'logout'){
				$fido->kill();
			}
			include_once('controllers/message.controller.php');
			$messageController = new MessageController($dRep);
			$message = $messageController->getLoginMessage($e);
		}catch(Exception $e){
			$message['css'] = 'error-box';
			$message['text'] = $e->getMessage();
		}
	}
	include_once('view/login/login.php');
}catch(PDOException $e){
	print_r($e);
}catch(DataException $e){
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		echo json_encode(array('error' => $e->getMessage(), 'elements' => implode(',', $e->getElements())));
	}else{
		echo 'Message: '.$e->getMessage();	
		echo '<br/>';
		echo 'Error code: '.$e->getCode();
		echo '<br/>';
		echo 'Error happened in file: '.$e->getFile();
		echo '<br/>';
		echo 'Line number: '.$e->getLine();
		echo '<br/>';
		echo 'Trace number: '.$e->getTraceAsString();
		//include some error file	
	}
}catch(Exception $e){
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
		echo json_encode(array('error' => $e->getMessage()));
	}else{
		echo 'Message: '.$e->getMessage();	
		echo '<br/>';
		echo 'Error code: '.$e->getCode();
		echo '<br/>';
		echo 'Error happened in file: '.$e->getFile();
		echo '<br/>';
		echo 'Line number: '.$e->getLine();
		echo '<br/>';
		echo 'Trace number: '.$e->getTraceAsString();
		//include some error file	
	}
}
?>