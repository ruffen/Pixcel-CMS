<?php
class SignupController extends BaseController{
	public function index(){
	}
	public function Signup(){
		global $varChecker;
		if($varChecker->getValue('Password') != $varChecker->getValue('PasswordRepeat')){
			throw new DataException('passwordnotmatching');
		}

		$customerProperties = array(
			'name' => $varChecker->getValue('Company'),
			'timezone' => $varChecker->getValue('Timezone'),
			'newsletter' => ($varChecker->getValue('Newsletter') == 'on'),
			'subdomain' => $varChecker->getValue('Subdomain')
		);
		$userProperties = array(
			'username' => $varChecker->getValue('Username'),
			'password' => $varChecker->getValue('Password'),
			'email' => $varChecker->getValue('Email'),
			'firstname' => $varChecker->getValue('Firstname'),
			'lastname' => $varChecker->getValue('Lastname'),
			'active' => true
		);
		$unique = $this->CheckUniqueFields('subdomain', $customerProperties['subdomain']);
		if(!$unique['unique']){
			throw new DataException('subdomainnotunique');
		}
		$this->checkproperties($customerProperties);
		$this->checkproperties($userProperties);
		
		$customer = new Customer();
		$customer->setProperties($customerProperties);
		$customer = $this->dRep->saveCustomer($customer);
		
		$user = new User();
		$userProperties['customer'] = $customer;
		$user->setProperties($userProperties);
		$user = $this->dRep->saveUser($user);
		
		$users = array($user);
		$modules = $this->dRep->getModuleCollection(array('customer' => $customer->getId()));
		
		$group = new Role();
		$properties = array(
			'name' => 'Administrators',
			'description' => 'Full Access Administrator role',
			'customer' => $customer->getId(),
			'users' => array($user->getId() => true)
		);
		$group->setProperties($properties);
		foreach($modules as $module){
			$group->setAccess($module, true);
			foreach($module->getKids() as $index => $kid){
				$group->setAccess($kid, true);
			}
		}
		$group = $this->dRep->saveRole($group);
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
	private function checkproperties($properties){
		$emptyprops = from('$property')->in($properties)->where('$property == ""')->select('$property');
		if(count($emptyprops) > 0){
			throw new DataException('mandatoryfields');
		}
	}
}
?>