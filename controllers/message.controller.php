<?php
class MessageController extends BaseController{

	public function index(){
		return 'N/A';
	}
	public function getMessage($messageKey = false){
		global $varChecker;
		//get the message
		$messageKey = ($messageKey === false) ? $varChecker->getValue('key') : $messageKey;
		$message = $this->dRep->getMessage(array('messagekey' => $messageKey));
		$text = ($message->getMessage() == '') ? $messageKey : $message->getMessage();
		switch($message->type()){
			case 'error' : $file = 'error.message.php';break;
			case 'success' : $file = 'success.message.php';break;
			case 'question' : $file = 'question.message.php';break;
			case 'notification' : $file = 'notification.message.php';break;
			default: $file = 'notification.message.php';break;
		}
		//include the file and display it
		require_once('view/message/'.$file);
	}
	public function getLoginMessage($e){
		$type = $e->getMessage();
		$message = array();
		switch($type){
			case 'expired' : 
				$message['css'] = 'notice-box';
				$message['icon'] = 'info';
			break;
			case 'logout' : 
				$message['css'] = 'success-box';
				$message['icon'] = 'accept';	
			break;
			case 'nouser' : 
			default : 
				$message['css'] = 'error-box';
				$message['icon'] = 'remove';
			break;

		}
		
		$msg = $this->dRep->getMessage(array('messagekey' => $type));
		$message['text'] = ($msg->getMessage() == '') ? $type : $msg->getMessage();
		return $message;
	}
}
?>