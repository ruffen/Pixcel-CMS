<?php
class CustomerRepository extends MysqlDb{
	private $customers = array();
	public function getCustomer($search){
		global $dRep;
		if(!is_array($search)){
			$id = $search;
			if(isset($this->customers[$id])){
				return $this->customers[$id];	
			}
			$search = array('customerId', $id);	
		}
		$where = $this->sqlBuilder->createWhere($search, 'A', false);
		
		$sql = "SELECT * FROM ink_customer WHERE {$where};";
		$row = $this->runSingleQuery($sql);
		if(!isset($row['customerId'])){
			throw new DataException('nocustomer');
		}
		$properties = array(
			'id' => $row['customerId'],
			'name' => $row['customerName'],
			'subdomain' => $row['subdomain'],
			'newsletter' => $row['newsletter'],
			'sites' => $dRep->getSiteCollection(array('customer' => $row['customerId']))
			);
		$customer = new Customer();
		$customer->setProperties($properties);
		$this->customers[$row['customerId']] = $customer;
		return $customer;
	}
	public function SaveCustomer($customer){
		$sql = "INSERT INTO ink_customer (customerName, subdomain, newsletter) VALUES (?,?,?);";
		$id =  $this->insertValues($sql, array($customer->getName(), $customer->getSubdomain(), $customer->getNewsletter()));
		return $id;
	}
}

?>