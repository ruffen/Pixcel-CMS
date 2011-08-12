<?php
class Guarddog{
	private $user;
	public function __construct(){
		$this->time = time();
		$this->startSession();
	}
	public function kill(){
		session_destroy();
	}
	/*
		Start session initializes session object, and checks if 
		user has already logged in. Sets the session variables 
		accordingly.	
	*/
	private function startSession(){
		/*set new session id*/
		session_name("INKADVERTISING");
		if(!isset($_SESSION)){
			session_start();
		}
	}
	
	/**
	 * Chekuser tries to find a user matching username and password
	 *
	 * @param string $username Username entered
	 * @param string $password password entered
	 * @return object User object of matched users, will throw exception if no user is found or if two users are found 
	 *
	 */
	public function CheckUser($username, $password, $controller){
		global $dRep;
		$module = $dRep->getModule($controller->getName());
		if($module->SystemStatus() == 3){
			throw new NoUserNeededException($controller->getName() . 'does not need controller');
		}
		if($username !== false){
			$user = $this->loggin($username, $password);
		}elseif(isset($_SESSION['user'])){
			$user = unserialize($_SESSION['user']);
		}else{
			throw new AccessException('expired');	
		}
		
		$site = $user->getRole()->getSite();
		if(!$user->HasControllerAccess($module, $site)){
			throw new AccessException('noaccess');
		}
		
		//set session vars
		$_SESSION['user'] = serialize($user);
		$_SESSION['customer'] = serialize($user->getCustomer());
		$_SESSION['site'] = serialize($site);

		return $user;
	}
	public function updateSite($site){
		$user = unserialize($_SESSION['user']);
		$customer = unserialize($_SESSION['customer']);
		
		$customer->setSite($site);
		$user->setCustomer($customer);

		$_SESSION['user'] = serialize($user);
		$_SESSION['customer'] = serialize($user->getCustomer());
		$_SESSION['site'] =serialize($site);
	}
	public function updateUser($user){
		$_SESSION['user'] = serialize($user);		
	}
	private function loggin($username, $password){
		global $dRep;
		try{
			if(!isset($_SESSION['customer'])){
				$serverdomains = explode('.', $_SERVER['SERVER_NAME']);
				$subdomain = array_shift($serverdomains);
				$customer = $dRep->getCustomer(array('subdomain' => $subdomain));
			}else{
				$customer = unserialize($_SESSION['customer']);
			}
			$user = $dRep->getUserCollection(array('username' => $username, 'password' => $password, 'customerId' => $customer->getId()));
			if($user === false){
				throw new AccessException('nouser');	
			}
			if(!$user->active()){
				throw new AccessException('inactiveuser');
			}
			return $user;	
		}catch(DataException $e){
			if($e->getMessage() == 'nocustomer'){
				throw new PathException('notsubdomain');
			}
			throw new AccessException('nouser');			
		}
	}
}
?>