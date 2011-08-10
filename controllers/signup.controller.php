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
		echo json_encode(array('success' => 'signedup'));
	}
}
?>