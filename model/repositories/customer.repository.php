<?php
class CustomerRepository extends MysqlDb{
	private $customers = array();
	public function getCustomer($id){
		global $dRep;
		if(isset($this->customers[$id])){
			return $this->customers[$id];	
		}
		$sql = "SELECT * FROM ink_customer WHERE customerId = '{$id}';";
		$row = $this->runSingleQuery($sql);
		if(isset($row['customerId'])){
			$properties = array(
					'id' => $row['customerId'],
					'name' => $row['customerName'],
					'subdomain' => $row['subdomain'],
					'newsletter' => $row['newsletter'],
					'sites' => $dRep->getSiteCollection(array('customer' => $row['customerId']))
				);
		}
		$customer = new Customer();
		$customer->setProperties($properties);
		$this->customers[$id] = $customer;
		return $customer;
	}
	public function SaveCustomer($customer){
		$sql = "INSERT INTO ink_customer (customerName, subdomain, newsletter) VALUES (?,?,?);";
		$id =  $this->insertValues($sql, array($customer->getName(), $customer->getSubdomain(), $customer->getNewsletter()));
		return $id;
	}
}

?>