<?php
class WHCMS
{
	private $url;
	private $username;
	private $password;
	private $postfields = array();
	public function __construct($url, $username, $password){
		$this->url = $url; # URL to WHMCS API file
		$this->username = $username;
		$this->password = $password;
	}
	public function Send(){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postfields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		
		$data = explode(";",trim($data, ';'));
		foreach ($data as $temp) {
			$temp = explode("=",$temp);
			$results[$temp[0]] = $temp[1];
		}
		return $results;
	}
	public function CreateClient($user){
		$this->postfields = array();
		$this->postfields["username"] = $this->username;
		$this->postfields["password"] = md5($this->password);
		$this->postfields["action"] = "addclient"; 
		$this->postfields["firstname"] = $user->getFirstname();
		$this->postfields["lastname"] = $user->getLastname();
		$this->postfields["companyname"] = $user->getCustomer()->getName();
		$this->postfields["email"] = $user->getEmail();
		$this->postfields["address1"] = "123 Demo Street";
		$this->postfields["city"] = "Demo";
		$this->postfields["state"] = "Florida";
		$this->postfields["postcode"] = "AB123";
		$this->postfields["country"] = "US";
		$this->postfields["phonenumber"] = "123456789";
		$this->postfields["password2"] = $user->getPassword();
		$this->postfields["currency"] = "1";

		$results = $this->Send();

		if ($results["result"]=="success") {
			return $results['clientid'];
		} else {
			throw new WHCMSException($results["message"]);
			# An error occured
		}
	}
	public function AddProducToCustomer($user){
		$this->postfields = array();
		$this->postfields["username"] = $this->username;
		$this->postfields["password"] = md5($this->password);
		$this->postfields["action"] = "addorder";
		$this->postfields["clientid"] = $user->getWhcmsId();
		$this->postfields["pid"] = "2";
		$this->postfields["billingcycle"] = "monthly";
		$this->postfields["regperiod"] = "1";
		$this->postfields["paymentmethod"] = "stgeorge";
		$results = $this->Send();
		if ($results["result"]=="success") {
			$this->AcceptOrder($results['orderid']);
//			$this->ModuleCreate($results['orderid']);
			return true;
		} else {
			throw new WHCMSException($results["message"]);
			# An error occured
		}
	}
	private function AcceptOrder($orderId){
		$this->postfields = array();
		$this->postfields["username"] = $this->username;
		$this->postfields["password"] = md5($this->password);
		$this->postfields["action"] = "acceptorder";
		$this->postfields["orderid"] = $orderId;
		$results = $this->Send();
		if ($results["result"]=="success") {
			return true;
		} else {
			throw new WHCMSException($results["message"]);
			# An error occured
		}
		
	}
	private function ModuleCreate($accountId){
		$this->postfields = array();
		$this->postfields["username"] = $this->username;
		$this->postfields["password"] = md5($this->password);
		$this->postfields["action"] = "modulecreate";
		$this->postfields["accountid"] = $accountId;
		$results = $this->Send();
		if(strlen($results['message']) > 0){
			throw new WHCMSException($results["message"] . " accountId: " . $accountId . " - on module create");
		}else{
			return true;
		}
	}	
}

?>