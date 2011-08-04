<?php
class SpotMaker{
	public function __construct(){
		
	}
	public function createSpots($configs, $template){
		$spots = $template->getSpots();
		$counter = 0;
		foreach($configs as $index => $config){
			if(count($spots) > $index){
				$counter = 0;
				if(isset($oldSpot)){
					unset($oldSpot);
				}
				foreach($spots as $tplSpotId => $spot){
					if($counter == $index){
						$oldSpot = $spot;
					}
				}
				if(isset($oldSpot)){
					$spot = $oldSpot;	
				}else{
					$spot = false;		
				}
			}else{
				$spot = false;
			}
			if($spot === false){
				$spots['new'.$counter] = $this->configureSpots($config, 'new'.$counter);
			}else{
				$spots[$spot->getTplSpotId()] = $this->configureSpots($config, $spot->getTplSpotId());
			}
			$counter++;
		}
		return $spots;
	}
	private function configureSpots($configArray, $tplSpotId){
		global $dRep;
		if(!is_array($configArray) || count($configArray) == 0){
			throw new DataException('nospot');
		}

		$spotName = array_shift($configArray);		
		//find asset in db by name
		$spot = $dRep->getSpot(array('sysName' => $spotName));
		if($spot->getId() == 'new'){
			$configArray['name'] = $spotName;
		}
		$spot->setProperties(array('tplSpotId' => $tplSpotId));
		$spot->setConfigString($configArray);
		return $spot;
	}
}
