<?php
ini_set('display_errors', "1");
ini_set('error_reporting', E_ALL);
$error = '';
/*** include the init.php file ***/
include_once('library/init.php');
try{
/** load the controller **/
	try{
		$route = $varChecker->getValue('rt');
	}catch(DataException $error){
		$route = '';
	}
	
	$router = new Router(trim($route,'/'));
	
	$controller = $router->LoadController($dRep);
	/** check user is logged in and all that **/
	$fido = new Guarddog();
	try{
		try{
			$username = $varChecker->getValue('username');
			$password = $varChecker->getValue('password');
		}catch(DataException $error){
			$username = false;
			$password = false;			
		}		
		$INK_User = $fido->checkUser($username, $password, $controller);
		$hasUser = true;
		$controller->setUser($INK_User);
	}catch(NoUserNeededException $e){
		$hasUser = false;
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
		//include some error file	
	}
}
?>