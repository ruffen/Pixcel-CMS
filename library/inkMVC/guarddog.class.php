<?php
class Guarddog{
	private $user;
	private $customer;
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
	public function CheckCustomer(){
		global $dRep;
		if(!isset($_SESSION['customer'])){
			$serverdomains = explode('.', $_SERVER['SERVER_NAME']);
			$subdomain = array_shift($serverdomains);
			$this->customer = $dRep->getCustomer(array('subdomain' => $subdomain));
			$_SESSION['customer'] = serialize($this->customer);			
		}
		$this->customer = unserialize($_SESSION['customer']);
		return $this->customer;
	}
	/**
	 * CheckUser sees if we have a user in session, if not tries to login a user
	 *
	 * @param string $username Username entered
	 * @param string $password password entered
	 * @return object User object of matched users, will throw exception if no user is found or if two users are found 
	 *
	 */
	public function CheckUser(){
		global $dRep;
		if(!isset($_SESSION['user'])){
			$this->user = $this->logginUser();
			$_SESSION['user'] = serialize($this->user);
		}else{
			$this->user = unserialize($_SESSION['user']);
		}
		return $this->user;
	}
	public function ResolveUserSite(){
		if(isset($_SESSION['site'])){
			return $site;	
		}
		$site = $this->user->getSite();
		$_SESSION['site'] = serialize($site);
		return $site;
	}
	public function CheckUserAccess(Module$module){
		return ($this->user->HasControllerAccess($module));
	}
	public function updateSite($site){
		$user = unserialize($_SESSION['user']);
		$customer = unserialize($_SESSION['customer']);
		
		$customer->setSite($site);
		$user->setCustomer($customer);

		$_SESSION['user'] = serialize($user);
		$_SESSION['customer'] = serialize($user->getCustomer());
		$_SESSION['site'] = serialize($site);
	}
	public function updateUser($user){
		$_SESSION['user'] = serialize($user);		
	}
	private function logginUser(){
		global $dRep, $varChecker;
		try{
			$username = $varChecker->getValue('username');
			$password = $varChecker->getValue('password');
		}catch(DataException $e){
			throw new AccessException('needusername');
		}
		try{
			$searchParams = array('username' => $username, 'password' => $password, 'customerId' => $this->customer->getId());
			$user = $dRep->getUserCollection($searchParams);
		}catch(DataException $e){
			throw new AccessException('nouser');	
		}
		if(!$user->active()){
			throw new AccessException('inactiveuser');
		}
		return $user;	
	}
}
?>