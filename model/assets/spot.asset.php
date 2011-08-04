<?php
class Spot extends Asset{
	protected $name;
	protected $order;
	protected $sysName;
	protected $uservalue;
	protected $tplSpotId;
	protected $configs = array();
	protected $availableConfigs = array();
	protected $configString;
	public function __construct(){
	}
	public function order(){
		return $this->order;
	}
	public function getType(){
		return $this->name;
	}
	public function getTplSpotId(){
		return $this->tplSpotId;
	}
	public function getFullname(){
		return $this->getConfigvalue('name').' ('.$this->name.')';
	}
	public function getName(){
		if($this->getConfigvalue('name') != ''){
			return $this->getConfigvalue('name');
		}
		return $this->name;	
	}
	public function systemName(){
		return $this->sysName;
	}
	public function getDescription(){
		return 'bla';	
	}
	public function uservalue(){
		return ($this->uservalue == 1) ? true : false;
	}
	public function getButtonimage(){
		return $this->sysName.'jpg';	
	}
	public function setConfigString($string)
	{
		if(is_array($string) && count($string) > 0){
			$setAgain = false;
			$backupArray = array();
			foreach($string as $config => $value){				
				if(is_int($config)){
					$index = $config;
					$config = explode('::', $value);	
					$backupArray[$config[0]] = $config[1];
					$setAgain = true;
				}else{
					$backupArray[$config] = $value;
					$this->setConfigValue($config, $value);
				}
			}
			if($setAgain){
				$this->setConfigString($backupArray);
			}
		}else if(!is_array($string)){
			$this->setConfigString(explode('-', $string));
			
			$this->configString = $string;
		}
	}
	public function getAvailableconfigs(){
		return $this->availableConfigs;
	}
	public function hasConfigs(){
		return (count($this->availableConfigs) > 0);
	}
	public function setConfigValue($config, $value){
		if(isset($this->availableConfigs[$config]) || ($this->id == 'new' && $config == 'name')){
			$this->configs[$config] = $value;
		}
	}
	public function getConfigString(){
		return $this->configString;
	}
	public function getConfigValues(){
		return $this->configs;
	}
	public function getConfigvalue($value){
		if(isset($this->configs[$value])){
			return $this->configs[$value];
		}
		return false;
	}
}
?>
