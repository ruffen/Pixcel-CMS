<?php
class SpotRepository extends MysqlDb{
	public function getSpot($id){
		if(is_array($id)){
			$count = 1;
			$translate = array(
				'sysName' => 'systemName',
				'tplSpotId' => 'tplSpotId'
			);
			foreach($id as $key => $value){
				if(isset($translate[$key])){
					$key = $translate[$key];
				}
				if($key == 'tplSpotId'){
					$innerjoin = " INNER JOIN ink_template_spots B ON (A.spotId = B.spotId)";
					$letter = "B.";
					$tplSpotIdSelector = ", B.tplSpotId";
				}else{
					$innerjoin = "";
					$letter = "A.";	
					$tplSpotIdSelector = "";
				}
				$where = $letter.$key." = '".$value."'";
				$where .= ($count != count($id)) ? ' AND ' : ';';
			}
		}else{
			$where = 'spotId = '.$id.';';	
			$innerjoin = "";
			$tplSpotIdSelector = "";
		}
		
		$sql = "SELECT A.*{$tplSpotIdSelector} FROM ink_spots A {$innerjoin} WHERE {$where}";		
		$row = $this->runSingleQuery($sql);
		if(isset($row['spotId'])){
			$properties = array(
					'id' => $row['spotId'],
					'name' => $row['name'],
					'uservalue' => $row['uservalue'],
					'sysName' => $row['systemName'],
					'availableConfigs' => $this->getAvailableconfigs($row['spotId'])
				);
			if(isset($row['tplSpotId'])){
				$properties['configs'] = $this->getConfigs($row['tplSpotId']);
			}
			$spot = new Spot();
			$spot->setProperties($properties);
			return $spot;
		}
		return false;
	}
	public function getSpotCollection($where){
		
		if(count($where) > 0){
			$where = $this->sqlBuilder->createWhere($where, 'B');
			if($where != ''){
				$where = "WHERE {$where}";
			}
			$innerjoin = "INNER JOIN ink_templates_spots B ON (A.spotId = B.spotId)";
			$bValues = ", B.spotOrder, B.tplSpotId";
			$orderby = 'spotOrder';
		}else{
			$where = '';
			$innerjoin = '';
			$bValues = '';
			$orderby = 'spotId';
		}

		$sql = "SELECT A.*{$bValues} FROM ink_spots A
				{$innerjoin}
				{$where}
				ORDER BY {$orderby};";
		$data = $this->runManyQuery($sql);
		$spots = array();
		foreach($data as $index => $row){
			if(!empty($row['spotId'])){
				$properties = array(
					'id' => $row['spotId'],
					'name' => $row['name'],
					'uservalue' => $row['uservalue'],
					'sysName' => $row['systemName'],
					'availableConfigs' => $this->getAvailableconfigs($row['spotId'])
				);
				if(isset($row['tplSpotId'])){
					$properties['order'] = $row['spotOrder'];
					$properties['tplSpotId'] = $row['tplSpotId'];
					$properties['configs'] = $this->getConfigs($row['tplSpotId']);
				}
				$name = $row['systemName'];		
				$spot = new $name();
				$spot->setProperties($properties);
				if(isset($row['tplSpotId'])){
					$spots[$row['tplSpotId']] = $spot;
				}else{
					$spots[] = $spot;
				}
				
			}
		}
		return $spots;
	}
	private function getAvailableconfigs($spotId){
		$sql = "SELECT A.* FROM spotconfigs A
				INNER JOIN ink_spots_spotconfigs B ON (A.configId = B.configId)
				WHERE B.spotId = ?";
		$data = $this->runManyQuery($sql, array($spotId));
		$spotconfigs = array();
		foreach($data as $index => $row){
			$properties = array(
				'id' => $row['configId'],
				'index' => $row['configIndex'],
				'name' => $row['name'],
				'description' => $row['description']
			);
			$spotconfig = new SpotConfig();
			$spotconfig->setProperties($properties);
			$spotconfigs[$row['configIndex']] = $spotconfig;
		}
		return $spotconfigs;
	}
	private function getconfigs($tplSpotId){
		$sql = "SELECT A.*, B.configIndex FROM ink_templates_spotconfigs A 
				INNER JOIN spotconfigs B ON (A.configId = B.configId)
				WHERE A.tplSpotId = ?;";
		$data = $this->runManyQuery($sql, array($tplSpotId));
		$configs = array();
		foreach($data as $index => $row){
			$configs[$row['configIndex']] = $row['value'];		
		}
		return $configs;
	}
}
?>