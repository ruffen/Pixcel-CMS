<?php
class SignupController extends BaseController{
	public function index(){
	}
	public function Signup(){
		global $varChecker;
		
		$customerProperties = array(
			'name' => $varChecker->getValue('Company'),
			'timezone' => $varChecker->getValue('Timezone'),
			'newsletter' => ($varChecker->getValue('Newsletter') == 'on'),
			'subdomain' => $varChecker->getValue('Subdomain')
		);
		$unique = $this->CheckUniqueFields('subdomain', $customerProperties);
		if(!$unique['unique']){
			throw new DataException('subdomainnotunique');
		}
		$customer = new Customer();
		$customer->setProperties($customerProperties);
		$customer = $this->dRep->saveCustomer($customer);

		$userProperties = array(
			'username' => $varChecker->getValue('Username'),
			'password' => $varChecker->getValue('Password'),
			'email' => $varChecker->getValue('Email'),
			'firstname' => $varChecker->getValue('Firstname'),
			'lastname' => $varChecker->getValue('Lastname'),
			'customer' => $customer,
			'active' => true
		);
		$user = new User();
		$user->setProperties($userProperties);
		
		$this->dRep->saveUser($user);
	}
	public function CheckUniqueFields($fieldname = false, $value = false){
		global $varChecker;
		if(!$fieldname || !$value){
			$value = $varChecker->getValue('value');
			$fieldname = $varChecker->getValue('fieldname');		
		}
		try{
			if(method_exists('Customer', 'get'.$fieldname)){
				$customer = $this->dRep->getCustomer(array($fieldname => $value));
			}else{
				$user = $this->dRep->getUser(array($fieldname => $value));
			}		
			return array('unique' => false);
		}catch(DataException $e){
			return array('unique' => true);
		}
	}
}
?>