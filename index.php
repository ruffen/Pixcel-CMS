<?php
$error = '';
/*** include the init.php file ***/
include_once('library/init.php');
try{
/** load the controller **/
	$router = new Router(trim($varChecker->getValue('rt'),'/'));
	
	$controller = $router->LoadController($dRep);
	/** check user is logged in and all that **/
	$fido = new Guarddog();
	try{
		$INK_User = $fido->checkUser($varChecker->getValue('username'), $varChecker->getValue('password'), $controller);
		$hasUser = true;
		$controller->setUser($INK_User);
	}catch(NoUserNeededException $e){
		$hasUser = false;
	}
	
	/*** run the controller ***/
	$router->RunController($hasUser);
	
	//print out the page
	$router->printHtml();
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
	if(strpos(trim($e->getMessage()), " ") === false){
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
	if(strpos(trim($e->getMessage()), " ") === false){
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