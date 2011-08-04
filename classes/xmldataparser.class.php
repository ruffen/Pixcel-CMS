<?php
class xmlDataParser{

	private $path;
	private $xmlArray = array();
	private $className;
	private $tableName;
	private $classVars = array();
	private $tableVars = array();
	private $classTableMap = array();
	private $tableClassMap = array();
	private $classes = array();
	private $arrays = array();
	private $pkTable;
	private $pkClass;
	public function __construct($filepath){
		$this->path = $filepath;
		$this->xmlArray = $this->my_xml2array();
		$this->getObjectProperties();
		
	}
	public function getXmlArray(){
		if(!isset($this->xmlArray['name'])){
			return $this->xmlArray[0];
		}
		return $this->xmlArray;
	}
	private function getObjectProperties(){
		$array = $this->getXmlArray();
		$this->className = $array['name'];
		$this->tableName = $array[0]['name'];
		$this->getVariables();
		$this->classTableMap = array_combine($this->classVars, $this->tableVars);
		$this->tableClassMap = array_combine($this->tableVars, $this->classVars);
	}
	private function getVariables(){
		$array = $this->getXmlArray();
		foreach($array[0] as $index => $value){
			if(is_int($index)){
				$info = array();
				if($index == 0){
					$this->pkTable = $value['value'];
					$this->pkClass = $value['name'];
				}
				if(isset($value['value'])){
					$this->classVars[] = $value['name'];
					$this->tableVars[] = $value['value'];
				}else if($value['attributes']['type'] == 'object'){
					$info = array();
					$info['name'] = $value['name'];
					$info['relation'] = $value[0]['attributes']['relation'];
					$info['object'] = $value[0]['value'];
					$info['pk'] = $value['attributes']['pk'];	
					$this->classes[] = $info;
				}else if($value['attributes']['type'] == 'array' && !isset($value['attributes']['join'])){
					$info['name'] = $value['name'];
					$info['tablename'] = $value[0]['name'];
					$info['pk'] = $value['attributes']['pk'];
					$info['fk'] = $value['attributes']['fk'];
					$classVars = array();
					$tableVars = array();
					foreach($value[0] as $index => $val){
						if(is_int($index)){
							$classVars[] = $val['name'];
							$tableVars[] = $val['value'];	
						}	
					}
					$info['classvars'] = $classVars;
					$info['tablevars'] = $tableVars;
					$this->arrays[] = $info;
				}else if($value['attributes']['type'] == 'array' && isset($value['attributes']['join'])){
					$info['join'] = $value['attributes']['join'];
					$info['pk'] = $value['attributes']['pk'];
					$info['fk'] = $value['attributes']['fk'];
					$info['name'] = $value['name'];
					$this->classes[] = $info;
				}	
			}
		}
	}	
	public function my_xml2array(){
		$xml_values = array();
		$contents = file_get_contents($this->path);
		$parser = xml_parser_create('');
		if(!$parser)
			return false;

		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($contents), $xml_values);
		xml_parser_free($parser);
		if (!$xml_values)
			return array();
	   
		$xml_array = array();
		$last_tag_ar =& $xml_array;
		$parents = array();
		$last_counter_in_tag = array(1=>0);
		foreach ($xml_values as $data)
		{
			switch($data['type'])
			{
				case 'open':
					$last_counter_in_tag[$data['level']+1] = 0;
					$new_tag = array('name' => $data['tag']);
					if(isset($data['attributes']))
						$new_tag['attributes'] = $data['attributes'];
					if(isset($data['value']) && trim($data['value']))
						$new_tag['value'] = trim($data['value']);
					$last_tag_ar[$last_counter_in_tag[$data['level']]] = $new_tag;
					$parents[$data['level']] =& $last_tag_ar;
					$last_tag_ar =& $last_tag_ar[$last_counter_in_tag[$data['level']]++];
					break;
				case 'complete':
					$new_tag = array('name' => $data['tag']);
					if(isset($data['attributes']))
						$new_tag['attributes'] = $data['attributes'];
					if(isset($data['value']) && trim($data['value']))
						$new_tag['value'] = trim($data['value']);

					$last_count = count($last_tag_ar)-1;
					$last_tag_ar[$last_counter_in_tag[$data['level']]++] = $new_tag;
					break;
				case 'close':
					$last_tag_ar =& $parents[$data['level']];
					break;
				default:
					break;
			};
		}
		return $xml_array;
	}
	//
	// use this to get node of tree by path with '/' terminator
	//
	public function get_value_by_path($__tag_path)
	{
		$tmp_arr =& $this->xmlArray;
		$tag_path = explode('/', $__tag_path);
		foreach($tag_path as $tag_name)
		{
			$res = false;
			foreach($tmp_arr as $key => $node)
			{
				if(is_int($key) && $node['name'] == $tag_name)
				{
					$tmp_arr = $node;
					$res = true;
					break;
				}
			}
			if(!$res)
				return false;
		}
		return $tmp_arr;
	}
	public function __get($var){
		if(isset($this->$var)){
			return $this->$var;	
		}
		throw new DataException('Could not get variable'.$var);
	}
}

?>

